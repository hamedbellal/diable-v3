<?php
require_once 'config.php';

$error = '';
$query_displayed = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';
    
    try {
        // Charger le fichier XML des utilisateurs
        $doc = loadXML('users.xml');
        
        // VULNERABLE: Concaténation directe dans la requête XPath
        // Ne JAMAIS faire ça en production !
        $xpath_query = "//user[username='$username' and password='$password']";
        
        logXPath($xpath_query);
        
        // Exécuter la requête XPath
        $result = executeXPath($doc, $xpath_query);
        
        if ($result->length > 0) {
            // Connexion réussie - récupérer les infos utilisateur
            $user_node = $result->item(0);
            $user = [];
            
            foreach ($user_node->childNodes as $child) {
                if ($child->nodeType === XML_ELEMENT_NODE) {
                    $user[$child->nodeName] = $child->nodeValue;
                }
            }
            
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['role'] = $user['role'];
            $_SESSION['email'] = $user['email'];
            
            redirect('dashboard.php');
        } else {
            $error = "Identifiants incorrects";
        }
        
        if (DEBUG_MODE) {
            $query_displayed = $xpath_query;
        }
        
    } catch (Exception $e) {
        $error = showError("Erreur de connexion", $e->getMessage());
        if (DEBUG_MODE) {
            $query_displayed = $xpath_query ?? '';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - XPath Injection Lab</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="container">
        <header>
            <h1>Connexion</h1>
            <p class="subtitle">Scénario 1: Login Bypass via XPath Injection</p>
        </header>

        <div class="content">
            <div class="card login-card">
                <h2>Authentification</h2>
                
                <?php if ($error): ?>
                    <?php echo $error; ?>
                <?php endif; ?>

                <?php if ($query_displayed): ?>
                    <div class="debug">
                        <strong>Requête XPath exécutée:</strong>
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
                        <summary>Indice: Structure de la requête XPath</summary>
                        <p>La requête XPath ressemble à ceci:</p>
                        <pre>//user[username='[votre_input]' and password='[votre_password]']</pre>
                        <p>Cette requête cherche un nœud <code>&lt;user&gt;</code> dont le <code>username</code> et le <code>password</code> correspondent.</p>
                    </details>
                    
                    <details>
                        <summary>Indice: Pas de commentaires en XPath</summary>
                        <p>⚠️ Contrairement à SQL, XPath ne supporte PAS les commentaires (<code>--</code> ou <code>#</code>)</p>
                        <p>Il faut utiliser une autre technique : rendre une partie de la condition toujours vraie</p>
                    </details>
                    
                    <details>
                        <summary>Indice: Opérateur OR</summary>
                        <p>En XPath, vous pouvez utiliser <code>or</code> pour créer une condition alternative</p>
                        <p>Exemple : <code>condition1 or condition2</code></p>
                        <p>Si <code>condition2</code> est toujours vraie, la requête retournera toujours des résultats</p>
                    </details>
                    
                    <details>
                        <summary>💡 Solution complète</summary>
                        <div class="solution-box">
                            <p><strong>Username:</strong> <code>admin' or '1'='1</code></p>
                            <p><strong>Password:</strong> <code>n'importe quoi</code></p>
                            <p><strong>Requête résultante:</strong></p>
                            <pre>//user[username='admin' or '1'='1' and password='n'importe quoi']</pre>
                            <p>La condition <code>'1'='1'</code> est toujours vraie, donc la requête retourne tous les utilisateurs dont le username est 'admin' OU pour lesquels '1'='1' (donc tous).</p>
                            <p>Comme 'admin' est le premier utilisateur correspondant, vous êtes connecté comme admin !</p>
                        </div>
                    </details>
                </div>

                <div class="test-accounts">
                    <h4>Comptes de test valides</h4>
                    <ul>
                        <li><code>user</code> : <code>password</code></li>
                        <li><code>alice</code> : <code>alice2024</code></li>
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
