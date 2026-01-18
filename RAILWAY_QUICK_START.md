# 🚂 Railway - Guide de démarrage rapide

## ✅ Configuration actuelle

Votre projet est **déjà configuré** pour Railway avec :
- ✅ `railway.json` - Configuration optimisée
- ✅ `Procfile` - Alternative avec Apache
- ✅ `public/router.php` - Router PHP

## 🚀 Déploiement en 5 minutes

### 1. Créer un compte Railway
👉 [railway.app](https://railway.app) → "Start a New Project" → Connectez GitHub

### 2. Créer un projet
- "New Project" → "Deploy from GitHub repo"
- Sélectionnez : `SMATH05/bricolage`
- Railway détectera automatiquement PHP

### 3. Ajouter PostgreSQL
- "+ New" → "Database" → "Add PostgreSQL"
- Créé automatiquement ✅

### 4. Configurer les variables
Dans votre service Web → "Variables" :

```
APP_ENV=prod
APP_DEBUG=0
APP_SECRET=<générer un secret>
DATABASE_URL=<automatique depuis PostgreSQL>
```

### 5. Générer l'URL
- Settings → Networking → "Generate Domain"
- URL générée : `https://votre-projet.up.railway.app`

### 6. Exécuter les migrations
Dans le terminal Railway :
```bash
php bin/console doctrine:migrations:migrate --no-interaction
```

### 7. Créer les dossiers uploads
```bash
mkdir -p public/uploads/annonces public/uploads/profiles
chmod -R 755 public/uploads
```

## 📋 Informations importantes

### URL de votre site
Railway génère automatiquement une URL comme :
```
https://bricolage-production.up.railway.app
```

### Variables d'environnement automatiques
Railway fournit automatiquement :
- `PORT` - Port d'écoute
- `DATABASE_URL` - Si PostgreSQL connecté
- `RAILWAY_ENVIRONMENT` - Environnement

### Commandes de build/démarrage
**Build :**
```bash
composer install --no-dev --optimize-autoloader && php bin/console cache:clear --env=prod && php bin/console cache:warmup --env=prod
```

**Start :**
```bash
vendor/bin/heroku-php-apache2 public/
```

## 🔧 Dépannage rapide

| Problème | Solution |
|----------|----------|
| 404 Error | Vérifiez `startCommand` dans Settings |
| Database Error | Vérifiez `DATABASE_URL` et migrations |
| Site ne démarre pas | Vérifiez les logs dans Railway |

## 📖 Guide complet
Consultez `RAILWAY_SETUP.md` pour le guide détaillé.
