<?php
/**
 * Reset Lab - Réinitialise la base de données
 */

// Exécuter le script d'initialisation
$output = [];
$return_var = 0;

exec('php /var/www/html/init_db.php 2>&1', $output, $return_var);

$success = ($return_var === 0);
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset - SQL Injection Lab</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="container">
        <header>
            <h1>🔄 Réinitialisation du Lab</h1>
        </header>

        <div class="content">
            <div class="card">
                <?php if ($success): ?>
                    <div class="success">
                        <h2> Lab réinitialisé avec succès</h2>
                        <p>La base de données a été réinitialisée aux valeurs par défaut.</p>
                    </div>
                <?php else: ?>
                    <div class="error">
                        <h2>❌ Erreur lors de la réinitialisation</h2>
                        <p>Une erreur s'est produite lors de la réinitialisation.</p>
                    </div>
                <?php endif; ?>

                <?php if (!empty($output)): ?>
                    <div class="debug">
                        <h3>Sortie du script:</h3>
                        <pre><?php echo htmlspecialchars(implode("\n", $output)); ?></pre>
                    </div>
                <?php endif; ?>

                <div class="actions">
                    <a href="index.php" class="btn btn-primary">← Retour à l'accueil</a>
                </div>
            </div>
        </div>

        <footer>
            <p>Lab DIABLE v3.0 - DSI ISFA 2025-2026</p>
        </footer>
    </div>
</body>
</html>