<?php
/**
 * Reset Endpoint - Réinitialise les données XML
 * Lab DIABLE - XPath Injection
 */

$output = "";

ob_start();
include 'init_data.php';
$output = ob_get_clean();

?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset - XPath Injection Lab</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="container">
        <header>
            <h1>🔄 Réinitialisation</h1>
            <p class="subtitle">Reset du Lab XPath Injection</p>
        </header>

        <div class="content">
            <div class="card">
                <h2>Données réinitialisées avec succès !</h2>
                <div class="success">
                     Les fichiers XML ont été réinitialisés à leur état initial.
                </div>

                <div class="debug">
                    <strong>Sortie de l'initialisation:</strong>
                    <pre><?php echo htmlspecialchars($output); ?></pre>
                </div>

                <div style="margin-top: 30px;">
                    <a href="index.php" class="btn btn-primary">← Retour à l'accueil</a>
                    <a href="dashboard.php" class="btn btn-secondary">Dashboard</a>
                </div>
            </div>

            <div class="card">
                <h3> État des fichiers</h3>
                <ul>
                    <li> users.xml - 5 utilisateurs</li>
                    <li> secrets.xml - 5 secrets</li>
                    <li> products.xml - 5 produits</li>
                </ul>
            </div>
        </div>

        <footer>
            <p>Lab DIABLE v3.0 - DSI ISFA 2025-2026</p>
        </footer>
    </div>
</body>
</html>
