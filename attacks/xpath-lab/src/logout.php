<?php
require_once 'config.php';

// Détruire la session
session_destroy();

// Rediriger vers l'accueil
redirect('index.php');
?>
