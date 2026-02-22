<?php
require_once 'config.php';

if (!isLoggedIn()) {
    redirect('login.php');
}

if (!isAdmin()) {
    die("Accès refusé : vous devez être administrateur");
}

$db = getDB();

// Récupérer tous les utilisateurs
$users = $db->query("SELECT * FROM users ORDER BY id")->fetchAll(PDO::FETCH_ASSOC);

// Récupérer tous les secrets
$secrets = $db->query("
    SELECT secrets.*, users.username 
    FROM secrets 
    JOIN users ON secrets.user_id = users.id 
    ORDER BY secrets.id
")->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Panel - SQL Injection Lab</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="container">
        <header>
            <h1> Administration</h1>
            <p class="subtitle">Panel administrateur</p>
        </header>

        <nav class="navigation">
            <a href="dashboard.php">Dashboard</a>
            <a href="search.php">Recherche</a>
            <a href="profile.php">Profil</a>
            <a href="admin.php" class="active">Admin Panel</a>
            <a href="logout.php">Déconnexion</a>
        </nav>

        <div class="content">
            <div class="card admin-section">
                <h2>🎉 Félicitations !</h2>
                <p>Vous avez réussi à accéder au panel administrateur !</p>
                
                <div class="flag-box">
                    <h3>🚩 FLAG</h3>
                    <code>FLAG{C0mm3nt5_4r3_D4ng3r0u5}</code>
                </div>
            </div>

            <div class="card">
                <h2> Gestion des utilisateurs (<?php echo count($users); ?>)</h2>
                <table class="results-table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Utilisateur</th>
                            <th>Email</th>
                            <th>Rôle</th>
                            <th>Créé le</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($users as $user): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($user['id']); ?></td>
                                <td><strong><?php echo htmlspecialchars($user['username']); ?></strong></td>
                                <td><?php echo htmlspecialchars($user['email']); ?></td>
                                <td>
                                    <span class="badge badge-<?php echo $user['role']; ?>">
                                        <?php echo htmlspecialchars($user['role']); ?>
                                    </span>
                                </td>
                                <td><?php echo htmlspecialchars($user['created_at']); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>

            <div class="card">
                <h2> Base de secrets (<?php echo count($secrets); ?>)</h2>
                <table class="results-table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Utilisateur</th>
                            <th>Secret</th>
                            <th>Date</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($secrets as $secret): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($secret['id']); ?></td>
                                <td><?php echo htmlspecialchars($secret['username']); ?></td>
                                <td>
                                    <div class="secret-box">
                                        <?php echo htmlspecialchars($secret['secret_data']); ?>
                                    </div>
                                </td>
                                <td><?php echo htmlspecialchars($secret['created_at']); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>

            <div class="card learning-section">
                <h2> Statistiques du Lab</h2>
                <table class="info-table">
                    <tr>
                        <th>Utilisateurs totaux</th>
                        <td><?php echo count($users); ?></td>
                    </tr>
                    <tr>
                        <th>Secrets stockés</th>
                        <td><?php echo count($secrets); ?></td>
                    </tr>
                    <tr>
                        <th>Base de données</th>
                        <td>SQLite</td>
                    </tr>
                    <tr>
                        <th>Mode Debug</th>
                        <td><?php echo DEBUG_MODE ? ' Activé' : ' Désactivé'; ?></td>
                    </tr>
                </table>
            </div>

            <div class="card">
                <h2> Ce que vous avez appris</h2>
                <div class="next-steps">
                    <h3>Techniques explorées</h3>
                    <ul>
                        <li> Injection SQL basée sur les commentaires</li>
                        <li> Bypass d'authentification</li>
                        <li> Exploitation de concaténation de chaînes</li>
                        <li> Compréhension des requêtes vulnérables</li>
                    </ul>

                    <h3>Prochaines étapes suggérées</h3>
                    <ul>
                        <li>Essayez le <a href="search.php">Scénario 2: Data Extraction avec UNION</a></li>
                        <li>Examinez le code source pour comprendre la vulnérabilité</li>
                        <li>Apprenez à sécuriser avec des requêtes préparées</li>
                        <li>Explorez d'autres labs DIABLE</li>
                    </ul>
                </div>
            </div>
        </div>

        <footer>
            <p>Lab DIABLE v3.0 - DSI ISFA 2025-2026</p>
        </footer>
    </div>
</body>
</html>