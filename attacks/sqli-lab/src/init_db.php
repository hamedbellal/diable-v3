<?php
/**
 * Script d'initialisation de la base de données
 * Lab DIABLE - SQL Injection based on Comments
 */

$db_path = getenv('DB_PATH') ?: '/var/www/html/database.db';

try {
    // Suppression de l'ancienne base si elle existe
    if (file_exists($db_path)) {
        unlink($db_path);
    }

    // Création de la connexion
    $db = new PDO("sqlite:$db_path");
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    echo "Création de la base de données...\n";

    // Création de la table users
    $db->exec("
        CREATE TABLE IF NOT EXISTS users (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            username TEXT UNIQUE NOT NULL,
            password TEXT NOT NULL,
            email TEXT NOT NULL,
            role TEXT NOT NULL DEFAULT 'user',
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP
        )
    ");

    echo "Table 'users' créée\n";

    // Création de la table secrets
    $db->exec("
        CREATE TABLE IF NOT EXISTS secrets (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            user_id INTEGER NOT NULL,
            secret_data TEXT NOT NULL,
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (user_id) REFERENCES users(id)
        )
    ");

    echo "Table 'secrets' créée\n";

    // Insertion des utilisateurs de test
    $users = [
        ['admin', 'admin123', 'admin@diable.lab', 'admin'],
        ['user', 'password', 'user@diable.lab', 'user'],
        ['alice', 'alice2024', 'alice@diable.lab', 'user'],
        ['bob', 'bobsecure', 'bob@diable.lab', 'user'],
        ['charlie', 'charlie!pass', 'charlie@diable.lab', 'moderator']
    ];

    $stmt = $db->prepare("
        INSERT INTO users (username, password, email, role) 
        VALUES (?, ?, ?, ?)
    ");

    foreach ($users as $user) {
        $stmt->execute($user);
        echo "👤 Utilisateur créé: {$user[0]} (role: {$user[3]})\n";
    }

    // Insertion de secrets pour chaque utilisateur
    $secrets = [
        [1, 'FLAG{C0mm3nt5_4r3_D4ng3r0u5}'],
        [2, 'My secret: I love SQL injections for learning!'],
        [3, 'Alice\'s password for bank: not_here_silly'],
        [4, 'Bob\'s API key: sk-1234567890abcdef'],
        [5, 'Charlie\'s backup codes: 111-222-333']
    ];

    $stmt = $db->prepare("
        INSERT INTO secrets (user_id, secret_data) 
        VALUES (?, ?)
    ");

    foreach ($secrets as $secret) {
        $stmt->execute($secret);
    }

    echo "Secrets insérés\n";

    // Permissions
    chmod($db_path, 0666);

    echo "\n Base de données initialisée avec succès!\n";
    echo "Statistiques:\n";
    echo "   - " . count($users) . " utilisateurs\n";
    echo "   - " . count($secrets) . " secrets\n";
    echo "\n Comptes de test:\n";
    echo "   - admin:admin123 (administrateur)\n";
    echo "   - user:password (utilisateur standard)\n";

} catch (PDOException $e) {
    echo "❌ Erreur: " . $e->getMessage() . "\n";
    exit(1);
}
?>