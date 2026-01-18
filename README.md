# 🛠️ Projet Bricolage - Guide de Déploiement

Ce projet est une plateforme premium de mise en relation pour le bricolage, construite avec **Symfony 7**.

## 🚀 Étapes pour l'Hébergement

### 1. Prérequis
- **PHP 8.2+** avec les extensions habituelles (pdo_mysql, intl, etc.)
- **MySQL** ou MariaDB
- **Composer**

### 2. Configuration (`.env`)
Créez ou modifiez le fichier `.env.local` sur votre serveur :

```env
DATABASE_URL="mysql://username:password@127.0.0.1:3306/bricolage?serverVersion=8.0.32&charset=utf8mb4"
APP_ENV=prod
APP_SECRET=votre_secret_ici

# Google Auth (Obligatoire pour la connexion Google)
GOOGLE_CLIENT_ID=votre_id_client
GOOGLE_CLIENT_SECRET=votre_secret_client
```

### 3. Installation
Exécutez les commandes suivantes dans le dossier racine :

```bash
# Installation des dépendances
composer install --no-dev --optimize-autoloader

# Création de la base de données et migrations
php bin/console doctrine:database:create
php bin/console doctrine:migrations:migrate --no-interaction

# Compilation des assets (AssetMapper)
php bin/console asset-map:compile
```

### 4. Configuration Apache / Nginx
Le document root doit pointer vers le dossier `public/`.
Le fichier `.htaccess` est déjà inclus pour gérer les redirections.

### 5. Droits d'accès
Assurez-vous que le serveur web a les droits d'écriture sur :
- `var/`
- `public/uploads/`

## ✨ Fonctionnalités Incluses
- **Dashboard Admin** : Gestion totale des annonces, chercheurs et recruteurs.
- **Profils Premium** : Design moderne avec upload de photos.
- **Connexion Google** : Authentification simplifiée.
- **Design Card-Based** : Une interface fluide et réactive.

---
*Projet optimisé par Antigravity.*
