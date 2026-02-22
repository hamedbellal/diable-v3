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
    <title>SQL Injection Lab - Comments Based</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="container">
        <header>
            <h1> Lab DIABLE - SQL Injection</h1>
            <p class="subtitle">Injection SQL basée sur des Commentaires</p>
        </header>

        <div class="content">
            <div class="card welcome-card">
                <h2>Bienvenue dans le Lab</h2>
                <p>Ce lab vous permet d'explorer les vulnérabilités liées aux injections SQL, 
                   en particulier la technique basée sur les <strong>commentaires SQL</strong>.</p>
                
                <div class="info-box">
                    <h3> Objectifs pédagogiques</h3>
                    <ul>
                        <li>Comprendre comment les commentaires SQL peuvent être exploités</li>
                        <li>Bypass d'authentification</li>
                        <li>Extraction de données sensibles</li>
                        <li>Modification de privilèges</li>
                    </ul>
                </div>

                <div class="info-box">
                    <h3> Commentaires SQL</h3>
                    <p>En SQL, il existe plusieurs façons de créer des commentaires :</p>
                    <ul>
                        <li><code>--</code> (commentaire sur une ligne - nécessite un espace après)</li>
                        <li><code>#</code> (commentaire sur une ligne - MySQL)</li>
                        <li><code>/* */</code> (commentaire multi-lignes)</li>
                    </ul>
                </div>

                <div class="scenarios">
                    <h3> Scénarios disponibles</h3>
                    <div class="scenario-grid">
                        <div class="scenario-card">
                            <h4>Scénario 1: Login Bypass</h4>
                            <p>Utilisez les commentaires pour contourner la vérification du mot de passe</p>
                            <a href="login.php" class="btn btn-primary">Commencer →</a>
                        </div>
                        <div class="scenario-card">
                            <h4>Scénario 2: Data Extraction</h4>
                            <p>Extrayez des données auxquelles vous ne devriez pas avoir accès</p>
                            <a href="search.php" class="btn btn-secondary">Explorer →</a>
                        </div>
                        <div class="scenario-card">
                            <h4>Scénario 3: Privilege Escalation</h4>
                            <p>Modifiez vos privilèges pour devenir administrateur</p>
                            <a href="profile.php" class="btn btn-secondary">Profil →</a>
                        </div>
                    </div>
                </div>

                <div class="hints-box">
                    <h3>💡 Indices</h3>
                    <details>
                        <summary>Indice 1: Regardez les réponses d'erreur</summary>
                        <p>Le serveur peut révéler des informations utiles dans les messages d'erreur SQL</p>
                    </details>
                    <details>
                        <summary>Indice 2: Testez différents commentaires</summary>
                        <p>Essayez <code>--</code>, <code>#</code>, et <code>/* */</code> dans vos inputs</p>
                    </details>
                    <details>
                        <summary>Indice 3: Pensez à la logique SQL</summary>
                        <p>Comment pourriez-vous faire en sorte que la condition soit toujours vraie ?</p>
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