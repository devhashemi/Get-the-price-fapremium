<?php
declare(strict_types=1);

namespace FaScrape;

final class Parser {

  /**
   * از HTML صفحه، JSON-LD نوع Product → AggregateOffer → offers را می‌خواند.
   * خروجی premium: آرایه‌ای از {id,label,price,currency}
   */
  public static function parsePremiumFromJsonLd(string $html): array {
    $premium = [];
    // جمع‌آوری همه <script type="application/ld+json">
    if (!preg_match_all('#<script[^>]+type=["\']application/ld\+json["\'][^>]*>(.*?)</script>#is', $html, $m)) {
      return $premium;
    }
    foreach ($m[1] as $block) {
      $block = html_entity_decode($block, ENT_QUOTES | ENT_HTML5, 'UTF-8');
      $json = json_decode(trim($block), true);
      if (!$json) continue;

      // ممکنه آرایه‌ای از آبجکت‌ها باشه
      $candidates = isset($json[0]) ? $json : [$json];

      foreach ($candidates as $j) {
        if (!is_array($j)) continue;
        $type = $j['@type'] ?? '';
        if ($type !== 'Product') continue;

        $offers = $j['offers'] ?? null;
        // Offers ممکنه AggregateOffer باشد
        if (isset($offers['@type']) && $offers['@type'] === 'AggregateOffer' && !empty($offers['offers']) && is_array($offers['offers'])) {
          foreach ($offers['offers'] as $offer) {
            $name  = $offer['name'] ?? '';
            $price = $offer['price'] ?? $offer['priceSpecification']['price'] ?? null;
            $cur   = $offer['priceCurrency'] ?? 'IRR';
            $url   = $offer['url'] ?? '';
            $id = null;
            if (preg_match('##buy-(\w+)#u', $url, $mm)) {
              $id = $mm[1]; // مثل 1m/3m/6m/12m
            } else {
              // fallback از نام
              if (str_contains($name,'۱ ماه') || str_contains($name,'1 ماه') || str_contains($name,'1m')) $id='1m';
              elseif (str_contains($name,'۳ ماه') || str_contains($name,'3 ماه') || str_contains($name,'3m')) $id='3m';
              elseif (str_contains($name,'۶ ماه') || str_contains($name,'6 ماه') || str_contains($name,'6m')) $id='6m';
              elseif (str_contains($name,'۱۲ ماه')|| str_contains($name,'12 ماه')|| str_contains($name,'12m')) $id='12m';
            }
            if ($price !== null && $id) {
              $premium[] = [
                'id'       => $id,
                'label'    => $name ?: $id,
                'price'    => (int)$price,
                'currency' => $cur,
              ];
            }
          }
        }
      }
    }
    // مرتب‌سازی بر اساس مدت
    $order = ['1m'=>1,'3m'=>3,'6m'=>6,'12m'=>12,'1y'=>12];
    usort($premium, function($a,$b) use($order){
      return ($order[$a['id']] ?? 999) <=> ($order[$b['id']] ?? 999);
    });
    return $premium;
  }

  /**
   * استخراج پلن‌های Stars از آبجکت JS: plansData.stars
   */
  public static function parseStarsFromJs(string $html): array {
    $stars = [];
    if (!preg_match('#const\s+plansData\s*=\s*\{(.+?)\};#is', $html, $m)) {
      return $stars;
    }
    $js = $m[1];

    // بخش stars را جدا کنیم
    if (!preg_match('#stars\s*:\s*\[(.+?)\]#is', $js, $m2)) {
      return $stars;
    }
    $arr = $m2[1];

    // هر آبجکت: { id: '50', price: 110000, label: '50 استارز' }
    if (preg_match_all('#\{\s*id:\s*[\'"]([^\'"]+)[\'"]\s*,\s*price:\s*([0-9]+)\s*,\s*label:\s*[\'"]([^\'"]+)[\'"]#u', $arr, $mm, PREG_SET_ORDER)) {
      foreach ($mm as $row) {
        $stars[] = [
          'id'       => $row[1],
          'label'    => $row[3],
          'price'    => (int)$row[2],
          'currency' => 'IRR',
        ];
      }
    }
    // مرتب‌سازی عددی
    usort($stars, fn($a,$b) => (int)$a['id'] <=> (int)$b['id']);
    return $stars;
  }
}
