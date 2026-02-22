<?php
require_once 'config.php';

if (!isLoggedIn()) {
    redirect('login.php');
}

try {
    // Charger les données utilisateur depuis XML
    $doc = loadXML('users.xml');
    $xpath_query = "//user[id='" . $_SESSION['user_id'] . "']";
    $result = executeXPath($doc, $xpath_query);
    
    $user = [];
    if ($result->length > 0) {
        $user_node = $result->item(0);
        foreach ($user_node->childNodes as $child) {
            if ($child->nodeType === XML_ELEMENT_NODE) {
                $user[$child->nodeName] = $child->nodeValue;
            }
        }
    }
    
    // Charger les secrets de l'utilisateur
    $secrets_doc = loadXML('secrets.xml');
    $secrets_query = "//secret[user_id='" . $_SESSION['user_id'] . "']";
    $secrets_result = executeXPath($secrets_doc, $secrets_query);
    $secrets = nodeListToArray($secrets_result);
    
} catch (Exception $e) {
    $error = "Erreur lors du chargement des données: " . $e->getMessage();
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - XPath Injection Lab</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="container">
        <header>
            <h1> Dashboard</h1>
            <p class="subtitle">Connecté en tant que: <strong><?php echo htmlspecialchars($_SESSION['username']); ?></strong></p>
        </header>

        <nav class="navigation">
            <a href="dashboard.php" class="active">Dashboard</a>
            <a href="search.php">Recherche</a>
            <a href="products.php">Produits</a>
            <?php if (isAdmin()): ?>
                <a href="admin.php">Admin Panel</a>
            <?php endif; ?>
            <a href="logout.php">Déconnexion</a>
        </nav>

        <div class="content">
            <div class="success-banner">
                <h2> Connexion réussie !</h2>
                <p>Félicitations ! Vous avez réussi à vous connecter via XPath.</p>
                <?php if (isAdmin()): ?>
                    <p class="admin-badge">🎉 Vous êtes administrateur ! Vous avez contourné l'authentification XPath.</p>
                <?php endif; ?>
            </div>

            <div class="card">
                <h2> Informations du compte</h2>
                <table class="info-table">
                    <tr>
                        <th>ID</th>
                        <td><?php echo htmlspecialchars($user['id'] ?? 'N/A'); ?></td>
                    </tr>
                    <tr>
                        <th>Nom d'utilisateur</th>
                        <td><?php echo htmlspecialchars($user['username'] ?? 'N/A'); ?></td>
                    </tr>
                    <tr>
                        <th>Email</th>
                        <td><?php echo htmlspecialchars($user['email'] ?? 'N/A'); ?></td>
                    </tr>
                    <tr>
                        <th>Rôle</th>
                        <td>
                            <span class="badge badge-<?php echo $user['role'] ?? 'user'; ?>">
                                <?php echo htmlspecialchars($user['role'] ?? 'N/A'); ?>
                            </span>
                        </td>
                    </tr>
                </table>
            </div>

            <div class="card">
                <h2> Vos secrets</h2>
                <?php if (!empty($secrets)): ?>
                    <?php foreach ($secrets as $secret): ?>
                        <div class="secret-box">
                            <p><?php echo htmlspecialchars($secret['secret_data']); ?></p>
                            <small>Catégorie: <?php echo htmlspecialchars($secret['category']); ?></small>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <p>Aucun secret enregistré</p>
                <?php endif; ?>
            </div>

            <?php if (isAdmin()): ?>
            <div class="card admin-section">
                <h2> Section Administrateur</h2>
                <p>Vous avez trouvé le FLAG !</p>
                <div class="flag-box">
                    <code>FLAG{XP4th_1nj3ct10n5_4r3_D4ng3r0u5}</code>
                </div>
                <p>Bravo ! Vous avez réussi à contourner l'authentification en utilisant une injection XPath.</p>
                
                <div class="next-steps">
                    <h3> Prochaines étapes</h3>
                    <ul>
                        <li>Essayez le <a href="search.php">Scénario 2: Data Extraction</a></li>
                        <li>Explorez le <a href="products.php">Scénario 3: Blind XPath</a></li>
                        <li>Examinez le code source pour comprendre la vulnérabilité</li>
                    </ul>
                </div>
            </div>
            <?php endif; ?>

            <div class="card learning-section">
                <h2> Apprentissage</h2>
                <h3>Comment l'attaque a fonctionné ?</h3>
                <p>L'attaque par injection XPath exploite la concaténation de chaînes dans les requêtes XPath:</p>
                
                <div class="code-example">
                    <h4>Code vulnérable:</h4>
                    <pre>$xpath_query = "//user[username='$username' and password='$password']";</pre>
                </div>

                <div class="code-example">
                    <h4>Payload utilisé:</h4>
                    <pre>username: admin' or '1'='1
password: n'importe quoi</pre>
                </div>

                <div class="code-example">
                    <h4>Requête résultante:</h4>
                    <pre>//user[username='admin' or '1'='1' and password='...']</pre>
                    <p class="explanation">↑ La condition '1'='1' est toujours vraie, donc tous les utilisateurs sont sélectionnés !</p>
                </div>

                <h3> Comment corriger ?</h3>
                <div class="code-example correct">
                    <h4>Code sécurisé avec échappement:</h4>
                    <pre>// Échapper les caractères spéciaux
$username = str_replace("'", "&apos;", $username);
$password = str_replace("'", "&apos;", $password);

// Ou mieux: valider strictement le format
if (!preg_match('/^[a-zA-Z0-9_-]+$/', $username)) {
    die("Format invalide");
}</pre>
                    <p class="explanation">Les apostrophes sont échappées, empêchant l'injection</p>
                </div>
            </div>
        </div>

        <footer>
            <p>Lab DIABLE v3.0 - DSI ISFA 2025-2026</p>
        </footer>
    </div>
</body>
</html>
