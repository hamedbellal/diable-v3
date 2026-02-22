<?php
session_start();
session_destroy();
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Déconnexion - SQL Injection Lab</title>
    <link rel="stylesheet" href="style.css">
    <meta http-equiv="refresh" content="3;url=index.php">
</head>
<body>
    <div class="container">
        <header>
            <h1> Déconnexion</h1>
        </header>

        <div class="content">
            <div class="card">
                <div class="success">
                    <h2> Vous avez été déconnecté</h2>
                    <p>Redirection vers la page d'accueil dans 3 secondes...</p>
                </div>

                <div class="actions">
                    <a href="index.php" class="btn btn-primary">Retour à l'accueil</a>
                </div>
            </div>
        </div>

        <footer>
            <p>Lab DIABLE v3.0 - DSI ISFA 2025-2026</p>
        </footer>
    </div>
</body>
</html>