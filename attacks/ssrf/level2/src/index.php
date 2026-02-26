<?php require_once "config.php"; ?>
<!doctype html>
<html>
<head>
  <meta charset="utf-8" />
  <title>DIABLE - SSRF Lab</title>
  <link rel="stylesheet" href="style.css">
</head>
<body>
  <div class="card">
    <h1>Lab DIABLE - SSRF</h1>
    <p>Endpoint vulnérable : <code>fetch.php?url=...</code> récupère une URL côté serveur sans filtre.</p>

    <form method="GET" action="fetch.php">
      <label>URL à récupérer</label>
      <input name="url" value="http://example.com" />
      <button class="btn" type="submit">Fetch</button>
    </form>

    <hr>
    <p><b>Scénario SSRF (interne Docker) :</b></p>
    <p>Teste : <code>http://internal-api:9000</code> (service interne non exposé vers l’hôte)</p>
    <p>Donc : <a href="fetch.php?url=http://internal-api:9000">fetch.php?url=http://internal-api:9000</a></p>

    <a class="btn" href="reset.php">Reset</a>
  </div>
</body>
</html>

