# SQL Injection basée sur des Commentaires - Lab DIABLE

> Conteneur pédagogique pour apprendre et pratiquer les injections SQL utilisant les commentaires

##  Vue d'ensemble

Ce conteneur fait partie du projet **Lab DIABLE v3.0** (DSI Infrastructure Allocated for Bench Local Environment). Il propose un environnement d'apprentissage sécurisé pour comprendre et exploiter les vulnérabilités d'injection SQL basées sur les commentaires.

### Métadonnées

- **Image**: `diable/sqli-comments-lab`
- **Tag**: DB
- **Difficulté**: Moyen
- **Port**: 80 (à mapper sur 8080 ou autre)
- **Technologies**: PHP 8.1, SQLite, Apache

##  Objectifs pédagogiques

1. **Comprendre** comment les commentaires SQL peuvent être exploités
2. **Bypass** d'authentification en commentant la vérification du mot de passe
3. **Extraire** des données sensibles via UNION-based injection
4. **Modifier** des privilèges utilisateur
5. **Apprendre** les bonnes pratiques de sécurisation

##  Démarrage rapide

### Option 1: Docker Run

```bash
docker build -t diable/sqli-comments-lab .
docker run -d -p 8080:80 --name sqli-lab diable/sqli-comments-lab
```

Accédez au lab: http://localhost:8080

### Option 2: Docker Compose

```bash
docker-compose up -d
```

### Variables d'environnement

| Variable | Description | Défaut | Valeurs |
|----------|-------------|--------|---------|
| `DEBUG_MODE` | Affiche les requêtes SQL | `false` | `true`/`false` |
| `DB_PATH` | Chemin de la base de données | `/var/www/html/database.db` | Chemin absolu |

Exemple avec debug activé:
```bash
docker run -d -p 8080:80 -e DEBUG_MODE=true diable/sqli-comments-lab
```

##  Scénarios d'attaque

### Scénario 1: Login Bypass (Facile)

**Objectif**: Se connecter en tant qu'admin sans connaître le mot de passe

**Endpoint**: `/login.php`

**Technique**: Utilisation des commentaires SQL (`--`) pour neutraliser la vérification du mot de passe

**Payload**:
```
Username: admin'--
Password: [n'importe quoi]
```

**Requête résultante**:
```sql
SELECT * FROM users WHERE username = 'admin'-- ' AND password = '...'
```

La partie après `--` est commentée et ignorée !

### Scénario 2: Data Extraction (Moyen)

**Objectif**: Extraire tous les secrets de la base de données

**Endpoint**: `/search.php`

**Technique**: UNION-based SQL injection avec commentaires

**Payload**:
```
' UNION SELECT username, secret_data, 'extracted' FROM secrets JOIN users ON secrets.user_id = users.id--
```

### Scénario 3: Privilege Escalation (Avancé)

**Objectif**: Modifier son rôle pour devenir administrateur

**Endpoint**: `/profile.php` (à implémenter)

**Technique**: UPDATE injection avec commentaires

##  Structure de la base de données

### Table `users`
```sql
CREATE TABLE users (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    username TEXT UNIQUE NOT NULL,
    password TEXT NOT NULL,
    email TEXT NOT NULL,
    role TEXT NOT NULL DEFAULT 'user',
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP
)
```

### Table `secrets`
```sql
CREATE TABLE secrets (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    user_id INTEGER NOT NULL,
    secret_data TEXT NOT NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id)
)
```

### Comptes de test

| Username | Password | Rôle | Notes |
|----------|----------|------|-------|
| `admin` | `admin123` | admin | Compte cible principal |
| `user` | `password` | user | Compte standard |
| `alice` | `alice2024` | user | Utilisateur test |
| `bob` | `bobsecure` | user | Utilisateur test |
| `charlie` | `charlie!pass` | moderator | Modérateur |

##  API Endpoints

### Health Check
```
GET /health.php
```

Retourne le statut du container au format JSON:
```json
{
  "status": "ok",
  "service": "sqli-comments-lab",
  "timestamp": "2025-01-30 12:00:00",
  "checks": {
    "database": {
      "status": "ok",
      "users_count": 5
    },
    "files": {
      "status": "ok"
    }
  }
}
```

### Reset
```
GET /reset.php
```

Réinitialise la base de données aux valeurs par défaut.

##  Théorie: Les commentaires SQL

### Types de commentaires SQL

1. **Commentaire sur une ligne (`--`)**
   - Standard SQL
   - Nécessite un espace après `--`
   - Exemple: `SELECT * FROM users WHERE id = 1-- commentaire`

2. **Commentaire sur une ligne (`#`)**
   - Spécifique à MySQL
   - Exemple: `SELECT * FROM users WHERE id = 1# commentaire`

3. **Commentaire multi-lignes (`/* */`)**
   - Tous les SGBD
   - Exemple: `SELECT * FROM users /* commentaire */ WHERE id = 1`

### Pourquoi c'est dangereux ?

Les commentaires permettent de:
- **Neutraliser** des parties de requêtes (comme la vérification de mot de passe)
- **Éviter** les erreurs de syntaxe en commentant les guillemets supplémentaires
- **Contourner** des filtres de sécurité basiques
- **Simplifier** l'exploitation d'autres techniques (UNION, UPDATE, etc.)

##  Protection et Correction

### 1. Requêtes préparées (PRIORITÉ 1)

**Code vulnérable**:
```php
$query = "SELECT * FROM users WHERE username = '$username' AND password = '$password'";
$result = $db->query($query);
```

**Code sécurisé**:
```php
$stmt = $db->prepare("SELECT * FROM users WHERE username = ? AND password = ?");
$stmt->execute([$username, $password]);
```

### 2. ORM (Object-Relational Mapping)

Utilisez des ORM comme:
- **PHP**: Doctrine, Eloquent
- **Python**: SQLAlchemy, Django ORM
- **Node.js**: Sequelize, TypeORM

### 3. Validation des entrées

```php
// Validation du format
if (!preg_match('/^[a-zA-Z0-9_-]{3,20}$/', $username)) {
    die("Format invalide");
}

// Limitation de longueur
if (strlen($username) > 50) {
    die("Trop long");
}
```

⚠️ **Important**: La validation est un complément, PAS un remplacement des requêtes préparées !

### 4. Principe du moindre privilège

Configurez votre utilisateur de base de données avec le minimum de droits:
```sql
-- Créer un utilisateur avec droits limités
CREATE USER 'webapp'@'localhost' IDENTIFIED BY 'password';
GRANT SELECT, INSERT, UPDATE ON mydb.users TO 'webapp'@'localhost';
-- PAS de DROP, DELETE sur tables sensibles
```

### 5. Désactiver les messages d'erreur détaillés

En production:
```php
// php.ini
display_errors = Off
log_errors = On
error_log = /var/log/php_errors.log
```

### 6. WAF (Web Application Firewall)

En complément (mais pas à la place !):
- ModSecurity
- AWS WAF
- Cloudflare WAF

##  Tests et Développement

### Tester le container localement

```bash
# Build
docker build -t diable/sqli-comments-lab .

# Run avec debug
docker run -d -p 8080:80 -e DEBUG_MODE=true diable/sqli-comments-lab

# Vérifier les logs
docker logs -f [container_id]

# Health check
curl http://localhost:8080/health.php
```

### Réinitialiser le lab

```bash
curl http://localhost:8080/reset.php
# ou
docker exec [container_id] php /var/www/html/init_db.php
```

### Accéder au shell du container

```bash
docker exec -it [container_id] bash
```

##  Structure du projet

```
sqli-comments-lab/
├── Dockerfile              # Image Docker
├── docker-compose.yml      # Configuration Docker Compose
├── README.md              # Cette documentation
├── src/
│   ├── index.php          # Page d'accueil
│   ├── login.php          # Login vulnérable (Scénario 1)
│   ├── dashboard.php      # Dashboard après connexion
│   ├── search.php         # Recherche vulnérable (Scénario 2)
│   ├── profile.php        # Profil (Scénario 3) 
│   ├── admin.php          # Panel admin
│   ├── config.php         # Configuration
│   ├── init_db.php        # Initialisation BDD
│   ├── health.php         # Health check
│   ├── reset.php          # Reset du lab
│   ├── logout.php         # Déconnexion
│   └── style.css          # Styles CSS
└── docs/
    
```

## 🔗 Intégration avec DIABLE

### Pour WP1 (Architecture & API)

Ce conteneur expose les endpoints suivants pour l'intégration:

- `GET /health.php` - Health check
- `GET /` - Page d'accueil du lab
- `POST /login.php` - Endpoint vulnérable
- `GET /reset.php` - Réinitialisation

### Métadonnées pour l'API

```json
{
  "id": "sqli-comments",
  "title": "SQL Injection basée sur Commentaires",
  "difficulty": "medium",
  "tag": "DB",
  "image": "diable/sqli-comments-lab",
  "port": 80,
  "scenarios": [
    {
      "id": 1,
      "title": "Login Bypass",
      "difficulty": "easy",
      "endpoint": "/login.php"
    },
    {
      "id": 2,
      "title": "Data Extraction",
      "difficulty": "medium",
      "endpoint": "/search.php"
    }
  ]
}
```

##  Ressources

### Documentation
- [OWASP SQL Injection](https://owasp.org/www-community/attacks/SQL_Injection)
- [SQL Injection Cheat Sheet](https://portswigger.net/web-security/sql-injection/cheat-sheet)
- [OWASP Top 10 A03:2021 - Injection](https://owasp.org/Top10/A03_2021-Injection/)

### Outils recommandés
- **Burp Suite**: Interception et modification de requêtes
- **OWASP ZAP**: Scanner de vulnérabilités web
- **SQLMap**: Automatisation d'exploitation SQL (après compréhension manuelle)
- **Browser DevTools**: Observation du trafic réseau

##  Contribution

Ce container a été développé dans le cadre du projet Lab DIABLE v3.0.

**Auteur**: Kennedy  
**Promo**: DSI ISFA 2025-2026  
**Work Package**: WP3 - Containers

### Améliorations possibles
- [ ] Ajouter le Scénario 3 (Privilege Escalation)
- [ ] Support de MySQL en plus de SQLite
- [ ] Mode "guided" avec hints progressifs
- [ ] Scoring system
- [ ] Export de rapports d'exploitation

## 📄 Licence

Ce projet est développé à des fins pédagogiques dans le cadre du Lab DIABLE v3.0.

---

**⚠️ Avertissement**: Ce lab contient des vulnérabilités intentionnelles à des fins éducatives. Ne JAMAIS déployer en production ou sur un réseau accessible publiquement.