<?php
/**
 * Script de test pour vérifier le mapping JWT
 * URL de test : http://localhost:8080/test-jwt-mapping.php?id_token=eyJhbGciOiJub25lIiwidHlwIjoiSldUIn0.eyJzdWIiOiJhZG1pbiIsImVtYWlsIjoiYWRtaW5AZXhhbXBsZS5jb20iLCJlbWFpbF92ZXJpZmllZCI6dHJ1ZX0.
 */

// Charger WordPress
require_once __DIR__ . '/wp-load.php';

echo "<h1>Test de mapping JWT</h1>";

if (!isset($_GET['id_token'])) {
    echo "<p style='color:red;'>Paramètre id_token manquant</p>";
    echo "<p>Exemple d'URL : <code>?id_token=eyJhbGciOiJub25lIiwidHlwIjoiSldUIn0.eyJzdWIiOiJhZG1pbiIsImVtYWlsIjoiYWRtaW5AZXhhbXBsZS5jb20iLCJlbWFpbF92ZXJpZmllZCI6dHJ1ZX0.</code></p>";
    exit;
}

$id_token = $_GET['id_token'];
echo "<h2>1. Token reçu</h2>";
echo "<pre>" . htmlspecialchars($id_token) . "</pre>";

// Décoder le JWT
$parts = explode('.', $id_token);
if (count($parts) < 2) {
    echo "<p style='color:red;'>Format JWT invalide</p>";
    exit;
}

echo "<h2>2. Header JWT décodé</h2>";
$header = json_decode(base64_decode($parts[0]), true);
echo "<pre>" . print_r($header, true) . "</pre>";

echo "<h2>3. Payload JWT décodé</h2>";
$token_payload = json_decode(base64_decode($parts[1]), true);
echo "<pre>" . print_r($token_payload, true) . "</pre>";

echo "<h2>4. Recherche d'utilisateur par email</h2>";
$email = $token_payload['email'] ?? null;
if ($email) {
    echo "<p>Email recherché : <strong>" . htmlspecialchars($email) . "</strong></p>";
    
    $user = get_user_by('email', $email);
    
    if ($user) {
        echo "<p style='color:green;'>Utilisateur trouvé !</p>";
        echo "<pre>";
        echo "ID: " . $user->ID . "\n";
        echo "Login: " . $user->user_login . "\n";
        echo "Email: " . $user->user_email . "\n";
        echo "Display Name: " . $user->display_name . "\n";
        echo "Roles: " . implode(', ', $user->roles) . "\n";
        echo "</pre>";
    } else {
        echo "<p style='color:orange;'>Aucun utilisateur trouvé avec cet email</p>";
        
        // Lister tous les utilisateurs
        echo "<h3>Utilisateurs existants dans la base :</h3>";
        $all_users = get_users(['fields' => ['ID', 'user_login', 'user_email']]);
        echo "<ul>";
        foreach ($all_users as $u) {
            echo "<li>ID: {$u->ID}, Login: {$u->user_login}, Email: {$u->user_email}</li>";
        }
        echo "</ul>";
    }
} else {
    echo "<p style='color:red;'>Pas d'email dans le payload JWT</p>";
}

echo "<h2>5. Test de mapping avec username</h2>";
$username = $token_payload['sub'] ?? $token_payload['username'] ?? null;
if ($username) {
    echo "<p>Username recherché : <strong>" . htmlspecialchars($username) . "</strong></p>";
    
    $user = get_user_by('login', $username);
    
    if ($user) {
        echo "<p style='color:green;'>Utilisateur trouvé par username !</p>";
        echo "<pre>";
        echo "ID: " . $user->ID . "\n";
        echo "Login: " . $user->user_login . "\n";
        echo "Email: " . $user->user_email . "\n";
        echo "</pre>";
    } else {
        echo "<p style='color:orange;'>Aucun utilisateur trouvé avec ce username</p>";
    }
}
