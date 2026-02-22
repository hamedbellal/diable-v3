Set-Content -Path src\health.php -Value '<?php
header("Content-Type: application/json");
$db_path = getenv("DB_PATH") ?: "/var/www/html/database.db";
$health = ["status" => "ok", "service" => "sqli-comments-lab", "timestamp" => date("Y-m-d H:i:s")];
try {
    if (!file_exists($db_path)) throw new Exception("Database file not found");
    $db = new PDO("sqlite:$db_path");
    $result = $db->query("SELECT COUNT(*) as count FROM users");
    $count = $result->fetch(PDO::FETCH_ASSOC);
    $health["checks"] = ["database" => ["status" => "ok", "users_count" => $count["count"]]];
} catch (Exception $e) {
    $health["status"] = "error";
    $health["checks"] = ["database" => ["status" => "error", "message" => $e->getMessage()]];
    http_response_code(503);
}
echo json_encode($health, JSON_PRETTY_PRINT);
?>'