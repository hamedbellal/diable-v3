<?php
/**
 * Configuration du Lab
 * Lab DIABLE - SQL Injection based on Comments
 */

// Configuration de la base de données
define('DB_PATH', getenv('DB_PATH') ?: '/var/www/html/database.db');

// Mode debug (affiche les requêtes SQL)
define('DEBUG_MODE', getenv('DEBUG_MODE') === 'true');

// Configuration de session
ini_set('session.cookie_httponly', 1);
session_start();

/**
 * Obtenir la connexion à la base de données
 */
function getDB() {
    try {
        $db = new PDO("sqlite:" . DB_PATH);
        $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        return $db;
    } catch (PDOException $e) {
        die("Erreur de connexion à la base de données");
    }
}

/**
 * Logger les requêtes SQL en mode debug
 */
function logQuery($query) {
    if (DEBUG_MODE) {
        error_log("[SQL DEBUG] " . $query);
    }
}

/**
 * Afficher un message d'erreur (potentiellement avec détails SQL en mode debug)
 */
function showError($message, $sqlError = null) {
    $output = "<div class='error'>" . htmlspecialchars($message) . "</div>";
    
    if (DEBUG_MODE && $sqlError) {
        $output .= "<div class='debug'><strong>SQL Error:</strong> " . htmlspecialchars($sqlError) . "</div>";
    }
    
    return $output;
}

/**
 * Vérifier si l'utilisateur est connecté
 */
function isLoggedIn() {
    return isset($_SESSION['user_id']) && isset($_SESSION['username']);
}

/**
 * Vérifier si l'utilisateur est admin
 */
function isAdmin() {
    return isset($_SESSION['role']) && $_SESSION['role'] === 'admin';
}

/**
 * Rediriger vers une page
 */
function redirect($url) {
    header("Location: $url");
    exit();
}
?>