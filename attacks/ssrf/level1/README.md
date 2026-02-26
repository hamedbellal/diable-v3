# 🌐 SSRF Level 1 — Internal Service Access

A lab demonstrating Server-Side Request Forgery (SSRF) allowing access to internal Docker services.

---

## 📁 Project Structure

    level1/
    ├── Dockerfile
    ├── README.md
    └── src/
        ├── index.php
        ├── fetch.php
        ├── config.php
        ├── health.php
        ├── reset.php
        └── style.css

---

## 🐳 Run with Docker (Dockerfile only)

Build:

    cd labs/ssrf/level1
    docker build -t diable-ssrf-l1 .

Run:

    docker run --rm -d --name diable-ssrf-l1 -p 8083:80 diable-ssrf-l1

Open:

    http://localhost:8083

Stop:

    docker stop diable-ssrf-l1

---

## 🧪 Internal Service (for SSRF demo)

This lab is meant to reach an internal-only service (not exposed to the host).
Create a dedicated Docker network and run an internal API on it:

    docker network create ssrf-net 2>/dev/null || true

    docker rm -f internal-api 2>/dev/null
    docker run -d --name internal-api --network ssrf-net hashicorp/http-echo:0.2.3 \
      -listen=:9000 -text="INTERNAL_OK"

Re-run the lab container on the same network:

    docker rm -f diable-ssrf-l1 2>/dev/null
    docker run --rm -d --name diable-ssrf-l1 --network ssrf-net -p 8083:80 diable-ssrf-l1

---

## 🎯 Endpoint

    GET /fetch.php?url=

Example:

    http://localhost:8083/fetch.php?url=http://example.com

---

## 🚨 The Vulnerability

The server fetches user-supplied URLs without validation.
The request is executed server-side, enabling access to internal Docker services that are not publicly exposed.

---

## ✅ Example SSRF Attack (Level 1)

With the internal API running:

    http://localhost:8083/fetch.php?url=http://internal-api:9000

Expected output contains:

    INTERNAL_OK

---

## 🛡 The Fix (implemented in Level 2)

- Validate allowed schemes (http/https only)
- Resolve hostname to IP and block private ranges
- Use strict allowlists
- Restrict outbound network access

---

## ⚠ Disclaimer

Educational use only.
