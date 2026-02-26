<?php require_once "config.php";

if (!isset($_SESSION["logged_in"]) || $_SESSION["logged_in"] !== true) {
  http_response_code(401);
  echo "Not logged in";
  exit;
}

$to = trim($_POST["to"] ?? "unknown");
$amount_raw = $_POST["amount"] ?? 0;

// validation simple
if (!is_numeric($amount_raw)) {
  http_response_code(400);
  echo "Invalid amount";
  exit;
}

$amount = (int)$amount_raw;
if ($amount <= 0) {
  http_response_code(400);
  echo "Invalid amount";
  exit;
}

// VULN (volontaire - level1): aucune protection CSRF,
// donc une page externe peut POST à la place de la victime.
$_SESSION["balance"] = max(0, (int)$_SESSION["balance"] - $amount);

// CTF: condition de victoire (flag)
if ($to === CTF_TARGET_USER && $amount >= CTF_MIN_AMOUNT) {
  $_SESSION["flag_csrf_level1"] = FLAG_CSRF_LEVEL1;
}

header("Location: index.php");
exit;
