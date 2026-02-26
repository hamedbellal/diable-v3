<?php require_once "config.php"; ?>
<!doctype html>
<html>
<head>
  <meta charset="utf-8" />
  <title>DIABLE - CSRF Lab</title>
  <link rel="stylesheet" href="style.css">
</head>
<body>
  <div class="card">
    <h1>Lab DIABLE - CSRF</h1>
    <p>Objectif : montrer un transfert bancaire déclenchable sans consentement via CSRF.</p>

    <?php if (!isset($_SESSION["logged_in"])): ?>
      <p><b>Tu n’es pas connecté.</b></p>
      <a class="btn" href="login.php">Se connecter</a>
    <?php else: ?>
     <p><b>Connecté</b> en tant que user — Solde: <b><?php echo (int)$_SESSION["balance"]; ?>€</b></p>

       <?php if (isset($_SESSION["flag_csrf_level1"])): ?>
         <p class="flag"><b>FLAG:</b> <code><?php echo htmlspecialchars($_SESSION["flag_csrf_level1"]); ?></code></p>
       <?php endif; ?> 

      <form method="POST" action="transfer.php">
        <label>Destinataire</label>
        <input name="to" value="attacker" />
        <label>Montant</label>
        <input name="amount" value="100" />
        <button class="btn" type="submit">Faire un virement</button>
      </form>

      <hr>
      <p><b>Scénario CSRF :</b> ouvre <code>attacker.html</code> dans un autre onglet (ou copie son contenu sur une page externe).
      Si la victime est connectée, le virement part sans confirmation.</p>

      <a class="btn" href="reset.php">Reset</a>
    <?php endif; ?>
  </div>
</body>
</html>
