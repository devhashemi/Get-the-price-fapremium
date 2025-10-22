<?php
declare(strict_types=1);

require_once __DIR__ . '/../../config/bootstrap.php';

use FaScrape\Http;
use FaScrape\Cache;
use FaScrape\Parser;

header('Content-Type: application/json; charset=UTF-8');

$SOURCE = 'https://fapremium.ir/store/';
$TTL    = 600; // ثانیه (10 دقیقه)
$cache  = new Cache();

if ($cached = $cache->get('prices', $TTL)) {
  $cached['cache'] = 'HIT';
  echo json_encode($cached, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
  exit;
}

try {
  $html = Http::get($SOURCE, 12);

  // 1) Premium از JSON-LD Product/AggregateOffer
  $premium = Parser::parsePremiumFromJsonLd($html);

  // 2) Stars از plansData.stars
  $stars = Parser::parseStarsFromJs($html);

  if (empty($premium) && empty($stars)) {
    throw new RuntimeException('Failed to parse prices from source.');
  }

  $payload = [
    'ok'         => true,
    'source_url' => $SOURCE,
    'updated_at' => date('c'),
    'premium'    => array_values($premium),
    'stars'      => array_values($stars),
    'cache'      => 'MISS',
  ];

  $cache->set('prices', $payload);
  echo json_encode($payload, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
} catch (\Throwable $e) {
  http_response_code(500);
  echo json_encode([
    'ok'    => false,
    'error' => 'FETCH_OR_PARSE_FAILED',
    'msg'   => $e->getMessage(),
  ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
}
