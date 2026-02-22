<?php
require_once 'config.php';

if (!isLoggedIn()) {
    redirect('login.php');
}

if (!isAdmin()) {
    die("<h1>Accès refusé</h1><p>Vous n'êtes pas administrateur.</p><a href='dashboard.php'>← Retour</a>");
}

// Charger tous les utilisateurs
$users_xml = loadXML('users.xml');
$users_result = executeXPath($users_xml, "//user");
$all_users = nodeListToArray($users_result);

// Charger tous les secrets
$secrets_xml = loadXML('secrets.xml');
$secrets_result = executeXPath($secrets_xml, "//secret");
$all_secrets = nodeListToArray($secrets_result);

// Charger tous les produits
$products_xml = loadXML('products.xml');
$products_result = executeXPath($products_xml, "//product");
$all_products = nodeListToArray($products_result);
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Panel - XPath Injection Lab</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="container">
        <header>
            <h1> Admin Panel</h1>
            <p class="subtitle">Gestion des données XML</p>
        </header>

        <nav class="navigation">
            <a href="dashboard.php">Dashboard</a>
            <a href="search.php">Recherche</a>
            <a href="products.php">Produits</a>
            <a href="admin.php" class="active">Admin Panel</a>
            <a href="logout.php">Déconnexion</a>
        </nav>

        <div class="content">
            <div class="success-banner admin-banner">
                <h2> Accès Administrateur</h2>
                <p>Vous avez réussi à obtenir les privilèges administrateur !</p>
                <div class="flag-box">
                    <code>FLAG{XP4th_1nj3ct10n5_4r3_D4ng3r0u5}</code>
                </div>
            </div>

            <div class="card admin-section">
                <h2> Tous les utilisateurs (<?php echo count($all_users); ?>)</h2>
                <table>
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Username</th>
                            <th>Password</th>
                            <th>Email</th>
                            <th>Role</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($all_users as $user): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($user['id']); ?></td>
                            <td><?php echo htmlspecialchars($user['username']); ?></td>
                            <td><code><?php echo htmlspecialchars($user['password']); ?></code></td>
                            <td><?php echo htmlspecialchars($user['email']); ?></td>
                            <td>
                                <span class="badge badge-<?php echo $user['role']; ?>">
                                    <?php echo htmlspecialchars($user['role']); ?>
                                </span>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>

            <div class="card admin-section">
                <h2> Tous les secrets (<?php echo count($all_secrets); ?>)</h2>
                <table>
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>User ID</th>
                            <th>Secret Data</th>
                            <th>Category</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($all_secrets as $secret): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($secret['id']); ?></td>
                            <td><?php echo htmlspecialchars($secret['user_id']); ?></td>
                            <td><code><?php echo htmlspecialchars($secret['secret_data']); ?></code></td>
                            <td><?php echo htmlspecialchars($secret['category']); ?></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>

            <div class="card admin-section">
                <h2> Tous les produits (<?php echo count($all_products); ?>)</h2>
                <table>
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Name</th>
                            <th>Price</th>
                            <th>Stock</th>
                            <th>Category</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($all_products as $product): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($product['id']); ?></td>
                            <td><?php echo htmlspecialchars($product['name']); ?></td>
                            <td><?php echo htmlspecialchars($product['price']); ?> €</td>
                            <td><?php echo htmlspecialchars($product['stock']); ?></td>
                            <td><?php echo htmlspecialchars($product['category']); ?></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>

            <div class="card">
                <h2> Actions administrateur</h2>
                <p>
                    <a href="reset.php" class="btn btn-secondary">🔄 Réinitialiser les données</a>
                    <a href="health.php" class="btn btn-secondary">❤️ Health Check</a>
                </p>
            </div>
        </div>

        <footer>
            <p>Lab DIABLE v3.0 - DSI ISFA 2025-2026</p>
        </footer>
    </div>
</body>
</html>
