# 🔐 CSRF Level 2 — Proper Session-Based Protection

A lab demonstrating a correct CSRF protection implementation using a session-bound random token.

---

## 📁 Project Structure

    level2/
    ├── Dockerfile
    ├── README.md
    └── src/
        ├── index.php
        ├── login.php
        ├── transfer.php
        ├── attacker.html
        └── config.php

---

## 🐳 Run with Docker (Dockerfile only)

Build:

    docker build -t diable-csrf-l2 .

Run:

    docker run --rm -d --name diable-csrf-l2 -p 8084:80 diable-csrf-l2

Open:

    http://localhost:8084

Stop:

    docker stop diable-csrf-l2

---

## 🎯 Endpoints

- GET / → Transfer form (protected)
- POST /transfer.php → Protected action
- GET /attacker.html → CSRF proof of concept (should fail)
- GET /reset.php → Reset session

---

## 🛡 Protection Mechanism

This level implements a proper CSRF defense:

- A random token is generated per session
- The token is stored server-side in the session
- The token is embedded in the form as a hidden field
- The server validates the token using constant-time comparison

If the token is missing or invalid, the request is rejected.

---

## 🧪 Expected Behavior

1. Log in (user / password)
2. Perform a transfer from the form → works
3. Open attacker.html in another tab → request is rejected
4. No balance change
5. No flag appears

---

## ⚠ Disclaimer

Educational use only.
