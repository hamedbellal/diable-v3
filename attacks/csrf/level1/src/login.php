<?php require_once "config.php";

$error = "";

// Si déjà connecté, aller directement à l’accueil
if (isset($_SESSION["logged_in"]) && $_SESSION["logged_in"] === true) {
  header("Location: index.php");
  exit;
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
  $u = trim($_POST["username"] ?? "");
  $p = (string)($_POST["password"] ?? "");

  if ($u === DEFAULT_USER && $p === DEFAULT_PASS) {
    session_regenerate_id(true); // bonne pratique après login
    $_SESSION["logged_in"] = true;
    header("Location: index.php");
    exit;
  } else {
    $error = "Identifiants invalides.";
  }
}
?>
<!doctype html>
<html lang="fr">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <link rel="stylesheet" href="style.css">
  <title>Login</title>
</head>
<body>
  <div class="card">
    <h1>Connexion</h1>

    <?php if ($error): ?>
      <p class="error"><?php echo htmlspecialchars($error, ENT_QUOTES, "UTF-8"); ?></p>
    <?php endif; ?>

    <form method="POST" autocomplete="off">
      <label>Username</label>
      <input name="username" placeholder="user" required />

      <label>Password</label>
      <input name="password" placeholder="password" type="password" required />

      <button class="btn" type="submit">Login</button>
    </form>

    <p><a href="index.php">Retour</a></p>
  </div>
</body>
</html>
