<?php
require_once 'config.php';

if (!isLoggedIn()) {
    redirect('login.php');
}

$results = [];
$error = '';
$query_displayed = '';

if (isset($_GET['search'])) {
    $search = $_GET['search'];
    
    try {
        // Créer un document XML combiné contenant users ET secrets
        $combined_xml = new DOMDocument('1.0', 'UTF-8');
        $root = $combined_xml->createElement('data');
        $combined_xml->appendChild($root);
        
        // Charger et ajouter les users
        $users_doc = loadXML('users.xml');
        $users_root = $users_doc->documentElement;
        foreach ($users_root->childNodes as $user) {
            if ($user->nodeType === XML_ELEMENT_NODE) {
                $imported = $combined_xml->importNode($user, true);
                $root->appendChild($imported);
            }
        }
        
        // Charger et ajouter les secrets
        $secrets_doc = loadXML('secrets.xml');
        $secrets_root = $secrets_doc->documentElement;
        foreach ($secrets_root->childNodes as $secret) {
            if ($secret->nodeType === XML_ELEMENT_NODE) {
                $imported = $combined_xml->importNode($secret, true);
                $root->appendChild($imported);
            }
        }
        
        // VULNERABLE: Concaténation directe dans XPath
        $query = "//user[contains(username, '$search')]";
        
        logXPath($query);
        
        // Exécuter la requête XPath sur le document combiné
        $result = executeXPath($combined_xml, $query);
        $results = nodeListToArray($result);
        
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
    <title>Recherche - XPath Injection Lab</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="container">
        <header>
            <h1> Recherche d'utilisateurs</h1>
            <p class="subtitle">Scénario 2: Data Extraction via XPath Injection</p>
        </header>

        <nav class="navigation">
            <a href="dashboard.php">Dashboard</a>
            <a href="search.php" class="active">Recherche</a>
            <a href="products.php">Produits</a>
            <?php if (isAdmin()): ?>
                <a href="admin.php">Admin Panel</a>
            <?php endif; ?>
            <a href="logout.php">Déconnexion</a>
        </nav>

        <div class="content">
            <div class="card">
                <h2>Rechercher un utilisateur</h2>
                
                <?php if ($error): ?>
                    <?php echo $error; ?>
                <?php endif; ?>

                <?php if ($query_displayed): ?>
                    <div class="debug">
                        <strong>Requête XPath exécutée:</strong>
                        <pre><?php echo htmlspecialchars($query_displayed); ?></pre>
                    </div>
                <?php endif; ?>

                <form method="GET" action="">
                    <div class="form-group">
                        <label for="search">Rechercher par nom d'utilisateur</label>
                        <input 
                            type="text" 
                            id="search" 
                            name="search" 
                            placeholder="Entrez un nom d'utilisateur"
                            value="<?php echo htmlspecialchars($_GET['search'] ?? ''); ?>"
                        >
                    </div>
                    <button type="submit" class="btn btn-primary">Rechercher</button>
                </form>
            </div>

            <?php if (!empty($results)): ?>
            <div class="card">
                <h2>Résultats (<?php echo count($results); ?>)</h2>
                <table>
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Utilisateur / Secret</th>
                            <th>Email / Data</th>
                            <th>Rôle / Catégorie</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($results as $item): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($item['id'] ?? 'N/A'); ?></td>
                            <td><?php echo htmlspecialchars($item['username'] ?? $item['user_id'] ?? 'N/A'); ?></td>
                            <td><?php echo htmlspecialchars($item['email'] ?? $item['secret_data'] ?? 'N/A'); ?></td>
                            <td>
                                <?php if (isset($item['role'])): ?>
                                    <span class="badge badge-<?php echo $item['role']; ?>">
                                        <?php echo htmlspecialchars($item['role']); ?>
                                    </span>
                                <?php else: ?>
                                    <?php echo htmlspecialchars($item['category'] ?? 'N/A'); ?>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            <?php elseif (isset($_GET['search'])): ?>
            <div class="card">
                <p>Aucun utilisateur trouvé</p>
            </div>
            <?php endif; ?>

            <div class="card hints-section">
                <h3> Objectif</h3>
                <p>Extraire des données du document XML en exploitant la requête XPath avec l'opérateur union <code>|</code></p>

                <details>
                    <summary>Indice 1: Structure de la requête</summary>
                    <p>La requête XPath actuelle recherche dans les utilisateurs:</p>
                    <pre>//user[contains(username, '[votre_recherche]')]</pre>
                    <p>Elle cherche les utilisateurs dont le username contient votre recherche.</p>
                </details>

                <details>
                    <summary>Indice 2: Union XPath avec |</summary>
                    <p>En XPath, l'opérateur <code>|</code> (pipe) permet de combiner plusieurs requêtes.</p>
                    <p>Par exemple: <code>//user | //secret</code> retourne à la fois les users ET les secrets</p>
                    <p>Vous devez fermer la fonction <code>contains()</code> et le crochet, puis ajouter votre union</p>
                </details>

                <details>
                    <summary>Indice 3: Fermer proprement les crochets</summary>
                    <p>Pour injecter votre propre requête, vous devez :</p>
                    <ul>
                        <li>Fermer la parenthèse de <code>contains()</code> avec <code>')]</code></li>
                        <li>Ajouter votre union avec <code>|//secret</code></li>
                        <li>Rouvrir un crochet factice avec <code>[contains(username,'</code></li>
                    </ul>
                    <p>Cela donne : <code>')]|//secret[contains(secret_data,'</code></p>
                </details>

                <details>
                    <summary>💡 Solution complète</summary>
                    <div class="solution-box">
                        <p><strong>Payload à entrer dans la recherche:</strong></p>
                        <pre>')]|//secret[contains(secret_data,'</pre>
                        <p><strong>Requête résultante:</strong></p>
                        <pre>//user[contains(username, '')]|//secret[contains(secret_data, '')]</pre>
                        <p>Cette requête retourne à la fois :</p>
                        <ul>
                            <li>Tous les utilisateurs (recherche vide)</li>
                            <li>Tous les secrets (recherche vide)</li>
                        </ul>
                        <p>Les secrets s'afficheront dans le tableau avec leurs données !</p>
                    </div>
                </details>
            </div>

            <div class="card learning-section">
                <h2> Technique: XPath Union Injection</h2>
                <p>Cette technique exploite l'opérateur <code>|</code> pour combiner plusieurs requêtes XPath et accéder à des données non prévues.</p>

                <div class="code-example">
                    <h4>Code vulnérable:</h4>
                    <pre>$query = "//user[contains(username, '$search')]";</pre>
                </div>

                <div class="code-example">
                    <h4>Payload:</h4>
                    <pre>')]|//secret[contains(secret_data,'</pre>
                </div>

                <div class="code-example">
                    <h4>Requête résultante:</h4>
                    <pre>//user[contains(username, '')]|//secret[contains(secret_data, '')]</pre>
                    <p class="explanation">↑ L'opérateur <code>|</code> combine les résultats. Tous les secrets sont extraits !</p>
                </div>

                <h3> Comment corriger ?</h3>
                <div class="code-example correct">
                    <h4>Code sécurisé avec validation:</h4>
                    <pre>// Valider strictement le format
if (!preg_match('/^[a-zA-Z0-9_-]+$/', $search)) {
    die("Format invalide");
}

// Ou échapper les caractères spéciaux
$search = str_replace(["'", "]", "[", "|", "(", ")"], "", $search);</pre>
                    <p class="explanation">Les caractères spéciaux sont supprimés ou échappés</p>
                </div>
            </div>
        </div>

        <footer>
            <p>Lab DIABLE v3.0 - DSI ISFA 2025-2026</p>
        </footer>
    </div>
</body>
</html>