<?php
/**
 * Script d'initialisation des données XML
 * Lab DIABLE - XPath Injection
 */

$data_dir = getenv('DATA_PATH') ?: '/var/www/html/data';

// Créer le dossier data s'il n'existe pas
if (!is_dir($data_dir)) {
    mkdir($data_dir, 0755, true);
}

echo " Création des fichiers XML...\n";

// Fichier users.xml - Base de données utilisateurs
$users_xml = new DOMDocument('1.0', 'UTF-8');
$users_xml->formatOutput = true;

$root = $users_xml->createElement('users');
$users_xml->appendChild($root);

$users_data = [
    ['id' => '1', 'username' => 'admin', 'password' => 'admin123', 'email' => 'admin@diable.lab', 'role' => 'admin'],
    ['id' => '2', 'username' => 'user', 'password' => 'password', 'email' => 'user@diable.lab', 'role' => 'user'],
    ['id' => '3', 'username' => 'alice', 'password' => 'alice2024', 'email' => 'alice@diable.lab', 'role' => 'user'],
    ['id' => '4', 'username' => 'bob', 'password' => 'bobsecure', 'email' => 'bob@diable.lab', 'role' => 'user'],
    ['id' => '5', 'username' => 'charlie', 'password' => 'charlie!pass', 'email' => 'charlie@diable.lab', 'role' => 'moderator']
];

foreach ($users_data as $user_data) {
    $user = $users_xml->createElement('user');
    
    foreach ($user_data as $key => $value) {
        $element = $users_xml->createElement($key, htmlspecialchars($value, ENT_XML1, 'UTF-8'));
        $user->appendChild($element);
    }
    
    $root->appendChild($user);
    echo " Utilisateur créé: {$user_data['username']} (role: {$user_data['role']})\n";
}

$users_xml->save("$data_dir/users.xml");
echo " Fichier users.xml créé\n";

// Fichier secrets.xml - Données sensibles
$secrets_xml = new DOMDocument('1.0', 'UTF-8');
$secrets_xml->formatOutput = true;

$secrets_root = $secrets_xml->createElement('secrets');
$secrets_xml->appendChild($secrets_root);

$secrets_data = [
    ['id' => '1', 'user_id' => '1', 'secret_data' => 'FLAG{XP4th_1nj3ct10n5_4r3_D4ng3r0u5}', 'category' => 'flag'],
    ['id' => '2', 'user_id' => '2', 'secret_data' => 'My secret: I love XPath injections for learning!', 'category' => 'personal'],
    ['id' => '3', 'user_id' => '3', 'secret_data' => 'Alice\'s password for bank: not_here_silly', 'category' => 'credential'],
    ['id' => '4', 'user_id' => '4', 'secret_data' => 'Bob\'s API key: sk-9876543210fedcba', 'category' => 'api_key'],
    ['id' => '5', 'user_id' => '5', 'secret_data' => 'Charlie\'s backup codes: 999-888-777', 'category' => 'backup']
];

foreach ($secrets_data as $secret_data) {
    $secret = $secrets_xml->createElement('secret');
    
    foreach ($secret_data as $key => $value) {
        $element = $secrets_xml->createElement($key, htmlspecialchars($value, ENT_XML1, 'UTF-8'));
        $secret->appendChild($element);
    }
    
    $secrets_root->appendChild($secret);
}

$secrets_xml->save("$data_dir/secrets.xml");
echo "Fichier secrets.xml créé\n";

// Fichier products.xml - Catalogue de produits
$products_xml = new DOMDocument('1.0', 'UTF-8');
$products_xml->formatOutput = true;

$products_root = $products_xml->createElement('products');
$products_xml->appendChild($products_root);

$products_data = [
    ['id' => '1', 'name' => 'Laptop Pro', 'price' => '1299.99', 'stock' => '15', 'category' => 'electronics'],
    ['id' => '2', 'name' => 'Wireless Mouse', 'price' => '29.99', 'stock' => '50', 'category' => 'accessories'],
    ['id' => '3', 'name' => 'USB-C Cable', 'price' => '19.99', 'stock' => '100', 'category' => 'accessories'],
    ['id' => '4', 'name' => 'Monitor 27"', 'price' => '399.99', 'stock' => '8', 'category' => 'electronics'],
    ['id' => '5', 'name' => 'Keyboard Mechanical', 'price' => '149.99', 'stock' => '25', 'category' => 'accessories']
];

foreach ($products_data as $product_data) {
    $product = $products_xml->createElement('product');
    
    foreach ($product_data as $key => $value) {
        $element = $products_xml->createElement($key, htmlspecialchars($value, ENT_XML1, 'UTF-8'));
        $product->appendChild($element);
    }
    
    $products_root->appendChild($product);
}

$products_xml->save("$data_dir/products.xml");
echo "Fichier products.xml créé\n";

// Permissions
chmod("$data_dir/users.xml", 0666);
chmod("$data_dir/secrets.xml", 0666);
chmod("$data_dir/products.xml", 0666);

echo "\nInitialisation terminée avec succès!\n";
echo " Statistiques:\n";
echo "   - " . count($users_data) . " utilisateurs\n";
echo "   - " . count($secrets_data) . " secrets\n";
echo "   - " . count($products_data) . " produits\n";
echo "\n Comptes de test:\n";
echo "   - admin:admin123 (administrateur)\n";
echo "   - user:password (utilisateur standard)\n";
?>
