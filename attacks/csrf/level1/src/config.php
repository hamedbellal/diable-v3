<?php
session_start();

define("APP_NAME", "csrf-transfer-lab");

define("DEFAULT_USER", "user");
define("DEFAULT_PASS", "password");

// “Solde” stocké en session (reset remet à l’état initial)
if (!isset($_SESSION["balance"])) {
  $_SESSION["balance"] = 1000;
}


define("FLAG_CSRF_LEVEL1", "flag{csrf_level1_transfer_to_attacker}");
define("CTF_TARGET_USER", "attacker");
define("CTF_MIN_AMOUNT", 200);
