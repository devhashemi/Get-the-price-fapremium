<?php
declare(strict_types=1);

namespace FaScrape;

final class Http {
  public static function get(string $url, int $timeout = 12): string {
    $ch = curl_init($url);
    curl_setopt_array($ch, [
      CURLOPT_RETURNTRANSFER => true,
      CURLOPT_FOLLOWLOCATION => true,
      CURLOPT_MAXREDIRS => 5,
      CURLOPT_CONNECTTIMEOUT => 6,
      CURLOPT_TIMEOUT => $timeout,
      CURLOPT_USERAGENT => 'FaPremiumPriceBot/1.0 (+https://github.com/fapremium)',
      CURLOPT_SSL_VERIFYPEER => true,
      CURLOPT_SSL_VERIFYHOST => 2,
      CURLOPT_HTTPHEADER => [
        'Accept: text/html,application/xhtml+xml,application/json',
      ],
    ]);
    $res = curl_exec($ch);
    if ($res === false) {
      $err = curl_error($ch);
      curl_close($ch);
      throw new \RuntimeException('HTTP error: ' . $err);
    }
    $code = curl_getinfo($ch, CURLINFO_RESPONSE_CODE);
    curl_close($ch);
    if ($code >= 400) {
      throw new \RuntimeException('HTTP status ' . $code);
    }
    return $res;
  }
}
