<?php
require_once 'config.php';

// Rediriger si déjà connecté
if (isLoggedIn()) {
    redirect('dashboard.php');
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>XPath Injection Lab - DIABLE</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="container">
        <header>
            <h1> Lab DIABLE - XPath Injection</h1>
            <p class="subtitle">Injection XPath dans les applications XML</p>
        </header>

        <div class="content">
            <div class="card welcome-card">
                <h2>Bienvenue dans le Lab</h2>
                <p>Ce lab vous permet d'explorer les vulnérabilités liées aux <strong>injections XPath</strong>, 
                   une technique similaire aux injections SQL mais ciblant les bases de données XML.</p>
                
                <div class="info-box">
                    <h3> Objectifs pédagogiques</h3>
                    <ul>
                        <li>Comprendre le fonctionnement de XPath et sa syntaxe</li>
                        <li>Identifier les vulnérabilités dans les requêtes XPath</li>
                        <li>Bypass d'authentification via injection XPath</li>
                        <li>Extraction de données XML sensibles</li>
                        <li>Énumération de structure XML</li>
                    </ul>
                </div>

                <div class="info-box">
                    <h3> Qu'est-ce que XPath ?</h3>
                    <p>XPath (XML Path Language) est un langage de requête pour naviguer dans les documents XML. Il permet de sélectionner des nœuds spécifiques basés sur des critères.</p>
                    <p><strong>Exemple de requête XPath :</strong></p>
                    <pre><code>//user[username='admin' and password='secret']</code></pre>
                    <p>Cette requête sélectionne les utilisateurs dont le username est 'admin' et le password est 'secret'.</p>
                </div>

                <div class="info-box">
                    <h3>⚠️ Vulnérabilité XPath Injection</h3>
                    <p>Comme pour SQL, si les entrées utilisateur sont directement concaténées dans une requête XPath, un attaquant peut :</p>
                    <ul>
                        <li>Modifier la logique de la requête</li>
                        <li>Accéder à des données non autorisées</li>
                        <li>Contourner l'authentification</li>
                        <li>Énumérer la structure du document XML</li>
                    </ul>
                </div>

                <div class="scenarios">
                    <h3> Scénarios disponibles</h3>
                    <div class="scenario-grid">
                        <div class="scenario-card">
                            <h4>Scénario 1: Login Bypass</h4>
                            <p>Contournez l'authentification XPath pour accéder au compte admin</p>
                            <a href="login.php" class="btn btn-primary">Commencer →</a>
                        </div>
                        <div class="scenario-card">
                            <h4>Scénario 2: Data Extraction</h4>
                            <p>Extrayez des données sensibles du fichier XML</p>
                            <a href="search.php" class="btn btn-secondary">Explorer →</a>
                        </div>
                        <div class="scenario-card">
                            <h4>Scénario 3: Blind XPath</h4>
                            <p>Énumérez la structure XML via des injections aveugles</p>
                            <a href="products.php" class="btn btn-secondary">Découvrir →</a>
                        </div>
                    </div>
                </div>

                <div class="hints-box">
                    <h3>💡 Indices généraux</h3>
                    <details>
                        <summary>Indice 1: Syntaxe XPath de base</summary>
                        <p>En XPath, vous pouvez utiliser :</p>
                        <ul>
                            <li><code>or</code> pour créer une condition toujours vraie</li>
                            <li><code>'</code> pour fermer une chaîne</li>
                            <li><code>|</code> pour combiner plusieurs requêtes (union)</li>
                        </ul>
                    </details>
                    <details>
                        <summary>Indice 2: Condition toujours vraie</summary>
                        <p>En XPath, <code>1=1</code> ou <code>'1'='1'</code> est toujours vrai</p>
                        <p>Exemple : <code>' or '1'='1</code></p>
                    </details>
                    <details>
                        <summary>Indice 3: Commentaires XPath</summary>
                        <p>⚠️ XPath ne supporte PAS les commentaires comme SQL !</p>
                        <p>Il faut donc fermer les conditions différemment</p>
                    </details>
                </div>

                <div class="test-accounts">
                    <h3> Comptes de test</h3>
                    <table>
                        <tr>
                            <th>Utilisateur</th>
                            <th>Mot de passe</th>
                            <th>Rôle</th>
                        </tr>
                        <tr>
                            <td><code>user</code></td>
                            <td><code>password</code></td>
                            <td>Utilisateur</td>
                        </tr>
                        <tr>
                            <td><code>admin</code></td>
                            <td><code>???</code></td>
                            <td>Administrateur</td>
                        </tr>
                    </table>
                    <p class="note">💡 Votre objectif : accéder au compte admin sans connaître le mot de passe</p>
                </div>
            </div>
        </div>

        <footer>
            <p>Lab DIABLE v3.0 - DSI ISFA 2025-2026</p>
            <p><a href="reset.php">🔄 Réinitialiser le lab</a> | <a href="health.php">❤️ Health Check</a></p>
        </footer>
    </div>
</body>
</html>
