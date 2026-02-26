<?php
require_once "config.php";

$url = $_GET["url"] ?? "";
if (!$url) {
  http_response_code(400);
  echo "Missing url";
  exit;
}

/*
  SSRF Level 2: pseudo-protection (naïve)
  On bloque le hostname "internal-api" dans l'URL.
  => Bypass: utiliser l'alias Docker "svc".
*/
if (stripos($url, "internal-api") !== false) {
  http_response_code(403);
  echo "Blocked host: internal-api\n";
  exit;
}

// SSRF via curl (server-side)
$cmd = "curl -sS -D - --max-time 3 " . escapeshellarg($url) . " 2>&1";
$out = shell_exec($cmd);

header("Content-Type: text/plain; charset=utf-8");
echo $out ?: "No response (curl returned empty output)";
