<?php
require_once 'config.php';

if (!isLoggedIn()) {
    redirect('login.php');
}

$db = getDB();

// Récupérer les infos de l'utilisateur
$stmt = $db->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$_SESSION['user_id']]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

// Récupérer les secrets de l'utilisateur
$stmt = $db->prepare("SELECT * FROM secrets WHERE user_id = ?");
$stmt->execute([$_SESSION['user_id']]);
$secrets = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - SQL Injection Lab</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="container">
        <header>
            <h1> Dashboard</h1>
            <p class="subtitle">Connecté en tant que: <strong><?php echo htmlspecialchars($user['username']); ?></strong></p>
        </header>

        <nav class="navigation">
            <a href="dashboard.php" class="active">Dashboard</a>
            <a href="search.php">Recherche</a>
            <a href="profile.php">Profil</a>
            <?php if (isAdmin()): ?>
                <a href="admin.php">Admin Panel</a>
            <?php endif; ?>
            <a href="logout.php">Déconnexion</a>
        </nav>

        <div class="content">
            <div class="success-banner">
                <h2>✅ Connexion réussie !</h2>
                <p>Félicitations ! Vous avez réussi à vous connecter.</p>
                <?php if (isAdmin()): ?>
                    <p class="admin-badge">🎉 Vous êtes administrateur ! Vous avez accès à toutes les fonctionnalités.</p>
                <?php endif; ?>
            </div>

            <div class="card">
                <h2> Informations du compte</h2>
                <table class="info-table">
                    <tr>
                        <th>ID</th>
                        <td><?php echo htmlspecialchars($user['id']); ?></td>
                    </tr>
                    <tr>
                        <th>Nom d'utilisateur</th>
                        <td><?php echo htmlspecialchars($user['username']); ?></td>
                    </tr>
                    <tr>
                        <th>Email</th>
                        <td><?php echo htmlspecialchars($user['email']); ?></td>
                    </tr>
                    <tr>
                        <th>Rôle</th>
                        <td>
                            <span class="badge badge-<?php echo $user['role']; ?>">
                                <?php echo htmlspecialchars($user['role']); ?>
                            </span>
                        </td>
                    </tr>
                    <tr>
                        <th>Créé le</th>
                        <td><?php echo htmlspecialchars($user['created_at']); ?></td>
                    </tr>
                </table>
            </div>

            <div class="card">
                <h2> Vos secrets</h2>
                <?php if (count($secrets) > 0): ?>
                    <?php foreach ($secrets as $secret): ?>
                        <div class="secret-box">
                            <p><?php echo htmlspecialchars($secret['secret_data']); ?></p>
                            <small>Créé le: <?php echo htmlspecialchars($secret['created_at']); ?></small>
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
                    <code>FLAG{C0mm3nt5_4r3_D4ng3r0u5}</code>
                </div>
                <p>Bravo ! Vous avez réussi à contourner l'authentification en utilisant les commentaires SQL.</p>
                
                <div class="next-steps">
                    <h3> Prochaines étapes</h3>
                    <ul>
                        <li>Essayez le <a href="search.php">Scénario 2: Data Extraction</a></li>
                        <li>Explorez le <a href="profile.php">Scénario 3: Privilege Escalation</a></li>
                        <li>Examinez le code source pour comprendre la vulnérabilité</li>
                    </ul>
                </div>
            </div>
            <?php endif; ?>

            <div class="card learning-section">
                <h2> Apprentissage</h2>
                <h3>Comment l'attaque a fonctionné ?</h3>
                <p>L'attaque par commentaires SQL exploite la concaténation de chaînes dans les requêtes SQL:</p>
                
                <div class="code-example">
                    <h4>Code vulnérable:</h4>
                    <pre>$query = "SELECT * FROM users WHERE username = '$username' AND password = '$password'";</pre>
                </div>

                <div class="code-example">
                    <h4>Payload utilisé:</h4>
                    <pre>username: admin'--
password: n'importe quoi</pre>
                </div>

                <div class="code-example">
                    <h4>Requête résultante:</h4>
                    <pre>SELECT * FROM users WHERE username = 'admin'-- ' AND password = '...'</pre>
                    <p class="explanation">↑ La partie après <code>--</code> est commentée et ignorée !</p>
                </div>

                <h3> Comment corriger ?</h3>
                <div class="code-example correct">
                    <h4>Code sécurisé avec requêtes préparées:</h4>
                    <pre>$stmt = $db->prepare("SELECT * FROM users WHERE username = ? AND password = ?");
$stmt->execute([$username, $password]);</pre>
                    <p class="explanation">Les paramètres sont échappés automatiquement, empêchant l'injection</p>
                </div>
            </div>
        </div>

        <footer>
            <p>Lab DIABLE v3.0 - DSI ISFA 2025-2026</p>
        </footer>
    </div>
</body>
</html>