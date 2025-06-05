---

```markdown
# ⚙️ Symfony 7.3 REST API Starter Template

Starter pack Symfony 7.3 pour développer et déployer une API REST rapidement en environnement Docker, avec CI/CD GitHub Actions, Ansible pour le provisioning, et Makefile pour simplifier l'exécution des commandes.

---

## 🚀 Fonctionnalités principales

- 🔧 **Symfony 7.3** préconfiguré pour une API REST
- 🐳 **Environnement Docker** complet :
    - **Dev** : MySQL 8.0, phpMyAdmin, PHP-FPM 8.3, Caddy
    - **Prod** : MySQL 8.0, Adminer, PHP-FPM 8.3, Caddy
- 📦 **PHPStan niveau 8** intégré :
    - `composer phpstan`
    - `composer phpstan-clean`
    - `composer phpstan-baseline`
- 📨 **Messenger & Scheduler** : déjà configurés mais **commentés** (décommenter si besoin)
- ⚙️ **CI/CD GitHub Actions** avec déploiement sur VPS via SSH
- 🧰 **Ansible** pour provisionner un VPS proprement
- 🛠️ **Makefile** pour automatiser le développement, la build, la mise en prod et le provisionning Ansible

---

## 🔧 Configuration de l'environnement

### ⚙️ Variables d'environnement

Les variables de connexion à la base de données sont stockées dans le fichier `.env`.  
**En production**, copier ce fichier vers `.env.local` et adapter les valeurs :

```bash
cp .env .env.local
# Modifier les valeurs DB_...
```

---

## 📂 Structure Docker

### 🧪 Environnement de développement

- `PHP-FPM 8.3`
- `MySQL 8.0`
- `phpMyAdmin`
- `Caddy (reverse proxy)`

### 🚀 Environnement de production

- `PHP-FPM 8.3`
- `MySQL 8.0`
- `Adminer` (plus léger que phpMyAdmin)
- `Caddy`

---

## 🔄 CI/CD GitHub Actions

Un workflow GitHub est déjà prêt dans `.github/workflows/deploy.yml`.
Le workflow ne se déclanchera uniquement l'or d'un push sur la branch "PROD".

### ✅ À modifier :
- Changer la ligne du `git clone` dans le workflow pour votre repo
- Ajouter les **secrets GitHub** suivants à votre repo :

| Clé          | Description                         |
|--------------|-------------------------------------|
| `VPS_HOST`   | IP ou nom de domaine du VPS         |
| `VPS_USER`   | Utilisateur SSH                     |
| `VPS_PASSWORD` | Mot de passe SSH (ou clé privée si configurée) |

### 🔐 Ajouter votre clé SSH sur GitHub et sur le VPS :

1. Configurer les globals GIT (sur le serveur) :
   ```bash
    git config --global user.name "name"
    git config --global user.email "email@example.com"
   ```

1. Générer une clé SSH :
   ```bash
   ssh-keygen -t rsa -b 4096 -C "your_email@example.com"
   ```

2. Copier la clé publique sur le VPS :
   ```bash
   ssh-copy-id -i ~/.ssh/id_rsa.pub user@your-vps-ip
   ```

3. Ajouter la clé privée dans GitHub :
    - Dans **Settings > Secrets > Actions**, ajouter :
        - `VPS_SSH_KEY` → contenu de `~/.ssh/id_rsa`

---

## 📦 Ansible – Provisioning du VPS

Permet d'installer et configurer automatiquement :

- Docker & Docker Compose
- PHP
- Fail2ban
- UFW (Firewall) :
    - ports 8000 et 443 ouverts (TCP/UDP)
    - SSH autorisé
- Caddy en reverse proxy, configuré depuis `ansible/templates/Caddyfile.j2`

### 🔧 Configuration Ansible

Modifier ces deux fichiers avant d’exécuter :

- `ansible/inventory.yaml` → IP/hostname du VPS
- `ansible/vars/main.yaml` → Infos de connexion SSH et variables projet

---

## 🛠️ Makefile

Des commandes simples pour automatiser les tâches :

```bash
make up              # Lance l'environnement Docker en dev
make build           # Rebuild les containers
make prod            # Build la version prod (utilisé par GitHub Actions)
make deploy-ansible  # Configure automatiquement le VPS via Ansible
```

---

## 📜 Notes

- Symfony est prêt à l'emploi pour une API REST (pas de front)
- Le scheduler et Messenger sont commentés mais **fonctionnels**
- Prise en charge native des déploiements CI/CD sur un VPS
- Caddy est configuré automatiquement en reverse proxy par Ansible
- Le worflow se déclanche uniquement sur la branch "PROD"

---

## 📄 Licence

MIT – Utilisation libre et modifications autorisées.
