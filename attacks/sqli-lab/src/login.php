<?php
require_once 'config.php';

$error = '';
$query_displayed = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';
    
    // VULNERABLE: Concaténation directe dans la requête SQL
    // Ne JAMAIS faire ça en production !
    $query = "SELECT * FROM users WHERE username = '$username' AND password = '$password'";
    
    // Log de la requête en mode debug
    logQuery($query);
    
    try {
        $db = getDB();
        $result = $db->query($query);
        $user = $result->fetch(PDO::FETCH_ASSOC);
        
        if ($user) {
            // Connexion réussie
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['role'] = $user['role'];
            
            redirect('dashboard.php');
        } else {
            $error = "Identifiants incorrects";
        }
        
        if (DEBUG_MODE) {
            $query_displayed = $query;
        }
        
    } catch (PDOException $e) {
        $error = showError("Erreur de connexion", $e->getMessage());
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
    <title>Login - SQL Injection Lab</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="container">
        <header>
            <h1> Connexion</h1>
            <p class="subtitle">Scénario 1: Login Bypass</p>
        </header>

        <div class="content">
            <div class="card login-card">
                <h2>Authentification</h2>
                
                <?php if ($error): ?>
                    <?php echo $error; ?>
                <?php endif; ?>

                <?php if ($query_displayed): ?>
                    <div class="debug">
                        <strong> Requête SQL exécutée:</strong>
                        <pre><?php echo htmlspecialchars($query_displayed); ?></pre>
                    </div>
                <?php endif; ?>

                <form method="POST" action="">
                    <div class="form-group">
                        <label for="username">Nom d'utilisateur</label>
                        <input 
                            type="text" 
                            id="username" 
                            name="username" 
                            placeholder="Entrez votre nom d'utilisateur"
                            value="<?php echo htmlspecialchars($_POST['username'] ?? ''); ?>"
                            required
                        >
                    </div>

                    <div class="form-group">
                        <label for="password">Mot de passe</label>
                        <input 
                            type="password" 
                            id="password" 
                            name="password" 
                            placeholder="Entrez votre mot de passe"
                            required
                        >
                    </div>

                    <button type="submit" class="btn btn-primary">Se connecter</button>
                </form>

                <div class="hints-section">
                    <h3>💡 Objectif</h3>
                    <p>Connectez-vous en tant qu'<strong>admin</strong> sans connaître le mot de passe</p>
                    
                    <details>
                        <summary>Indice: Structure de la requête</summary>
                        <p>La requête SQL ressemble à ceci:</p>
                        <pre>SELECT * FROM users WHERE username = '[votre_input]' AND password = '[votre_password]'</pre>
                    </details>
                    
                    <details>
                        <summary>Indice: Les commentaires SQL</summary>
                        <p>En SQL, le symbole <code>--</code> permet de commenter le reste de la ligne</p>
                        <p>Exemple: <code>SELECT * FROM users WHERE id = 1 -- ceci est un commentaire</code></p>
                    </details>
                    
                    <details>
                        <summary>Indice: Que se passe-t-il si...</summary>
                        <p>Que se passe-t-il si vous entrez <code>admin'--</code> comme nom d'utilisateur ?</p>
                        <p>La requête devient: <code>SELECT * FROM users WHERE username = 'admin'--' AND password = '...'</code></p>
                        <p>Tout ce qui suit <code>--</code> est ignoré !</p>
                    </details>
                </div>

                <div class="test-accounts">
                    <h4>Comptes de test valides</h4>
                    <ul>
                        <li><code>user</code> : <code>alice</code></li>
                        <li><code>password</code> : <code>alice2024</code></li>
                    </ul>
                    <p class="note">Essayez d'abord avec un compte valide pour voir le comportement normal</p>
                </div>
            </div>
        </div>

        <footer>
            <p><a href="index.php">← Retour à l'accueil</a></p>
        </footer>
    </div>
</body>
</html>