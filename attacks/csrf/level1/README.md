# 🔓 CSRF Level 1 — Basic Cross-Site Request Forgery

A hands-on educational lab to understand and exploit a basic Cross-Site Request Forgery (CSRF) vulnerability in a controlled Docker environment.

---

## 📁 Project Structure

    level1/
    ├── Dockerfile
    ├── README.md
    └── src/
        ├── index.php
        ├── login.php
        ├── transfer.php
        ├── attacker.html
        ├── reset.php
        └── config.php

---

## 🐳 Run with Docker (Dockerfile only)

Build the image:

    docker build -t diable-csrf-l1 .

Run the container:

    docker run --rm -d --name diable-csrf-l1 -p 8081:80 diable-csrf-l1

Open:

    http://localhost:8081

Stop:

    docker stop diable-csrf-l1

---

## 🎯 Endpoints

- GET / → Transfer form
- POST /transfer.php → Vulnerable action
- GET /attacker.html → CSRF proof of concept
- GET /reset.php → Reset session

---

## 🚨 The Vulnerability

The transfer endpoint does not validate any CSRF token.
If a user is authenticated, the browser automatically includes session cookies.
An attacker can force a victim to submit a transfer request without consent.

---

## 🧪 Example Attack

1. Log in (user / password)
2. Open /attacker.html
3. Transfer is executed automatically
4. Flag appears:

    flag{csrf_level1_transfer_to_attacker}

---

## 🛡 The Fix

- Generate random token per session
- Validate server-side
- Use SameSite cookies
- Validate Origin/Referer

---

## ⚠ Disclaimer

Educational use only.
