<?php
declare(strict_types=1);

namespace FaScrape;

final class Cache {
  private string $dir;
  public function __construct(string $dir = __DIR__ . '/../.cache') {
    $this->dir = $dir;
    if (!is_dir($dir)) @mkdir($dir, 0775, true);
  }
  private function path(string $key): string { return $this->dir . '/' . $key . '.json'; }

  public function get(string $key, int $ttl): ?array {
    $f = $this->path($key);
    if (!is_file($f)) return null;
    if (time() - filemtime($f) > $ttl) return null;
    $json = file_get_contents($f);
    $data = json_decode($json, true);
    return is_array($data) ? $data : null;
  }

  public function set(string $key, array $data): void {
    $f = $this->path($key);
    file_put_contents($f, json_encode($data, JSON_UNESCAPED_UNICODE|JSON_PRETTY_PRINT));
  }
}
