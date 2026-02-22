<?php
/**
 * Configuration du Lab XPath Injection
 * Lab DIABLE - XPath Injection
 */

// Configuration des données
define('DATA_PATH', getenv('DATA_PATH') ?: '/var/www/html/data');

// Mode debug (affiche les requêtes XPath)
define('DEBUG_MODE', getenv('DEBUG_MODE') === 'true');

// Configuration de session
ini_set('session.cookie_httponly', 1);
session_start();

/**
 * Charger un document XML
 */
function loadXML($filename) {
    $filepath = DATA_PATH . '/' . $filename;
    
    if (!file_exists($filepath)) {
        throw new Exception("Fichier XML non trouvé: $filename");
    }
    
    $doc = new DOMDocument();
    $doc->load($filepath);
    return $doc;
}

/**
 * Exécuter une requête XPath (VULNERABLE - à des fins pédagogiques)
 */
function executeXPath($doc, $query) {
    if (DEBUG_MODE) {
        error_log("[XPATH DEBUG] " . $query);
    }
    
    $xpath = new DOMXPath($doc);
    return $xpath->query($query);
}

/**
 * Logger les requêtes XPath en mode debug
 */
function logXPath($query) {
    if (DEBUG_MODE) {
        error_log("[XPATH] " . $query);
    }
}

/**
 * Afficher un message d'erreur
 */
function showError($message, $xpathError = null) {
    $output = "<div class='error'>" . htmlspecialchars($message) . "</div>";
    
    if (DEBUG_MODE && $xpathError) {
        $output .= "<div class='debug'><strong>XPath Error:</strong> " . htmlspecialchars($xpathError) . "</div>";
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

/**
 * Convertir un NodeList en tableau associatif
 */
function nodeListToArray($nodeList) {
    $results = [];
    
    foreach ($nodeList as $node) {
        $item = [];
        foreach ($node->childNodes as $child) {
            if ($child->nodeType === XML_ELEMENT_NODE) {
                $item[$child->nodeName] = $child->nodeValue;
            }
        }
        if (!empty($item)) {
            $results[] = $item;
        }
    }
    
    return $results;
}
?>
