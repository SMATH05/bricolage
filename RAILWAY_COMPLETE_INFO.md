# 🚂 Railway - Informations complètes de déploiement

## ✅ État actuel de votre projet

Votre projet est **100% prêt** pour Railway avec :

### Fichiers de configuration ✅
- ✅ `railway.json` - Configuration Railway optimisée
- ✅ `Procfile` - Configuration Apache (alternative)
- ✅ `public/router.php` - Router PHP pour serveur intégré
- ✅ Dossiers d'upload créés avec `.gitkeep`

### Configuration actuelle

**Build Command :**
```bash
composer install --no-dev --optimize-autoloader && php bin/console cache:clear --env=prod && php bin/console cache:warmup --env=prod
```

**Start Command :**
```bash
vendor/bin/heroku-php-apache2 public/
```

**Alternative (PHP built-in server) :**
```bash
php -S 0.0.0.0:$PORT -t public public/router.php
```

---

## 📋 Étapes de déploiement détaillées

### ÉTAPE 1 : Créer un compte Railway

1. Allez sur **[railway.app](https://railway.app)**
2. Cliquez sur **"Start a New Project"**
3. Connectez-vous avec votre compte **GitHub**
4. Autorisez Railway à accéder à vos dépôts

### ÉTAPE 2 : Créer un nouveau projet

1. Cliquez sur **"New Project"**
2. Sélectionnez **"Deploy from GitHub repo"**
3. Si c'est la première fois, autorisez Railway à accéder à GitHub
4. Sélectionnez votre dépôt : **`SMATH05/bricolage`**
5. Railway détectera automatiquement que c'est un projet PHP

### ÉTAPE 3 : Ajouter PostgreSQL

1. Dans votre projet Railway, cliquez sur **"+ New"**
2. Sélectionnez **"Database"**
3. Choisissez **"Add PostgreSQL"**
4. Railway créera automatiquement une base de données PostgreSQL
5. **Notez le nom** (ex: `PostgreSQL` ou `railway`)

### ÉTAPE 4 : Configurer les variables d'environnement

Dans votre service Web → **"Variables"** → Ajoutez :

#### Variables essentielles :

```env
APP_ENV=prod
APP_DEBUG=0
APP_SECRET=<générez un secret>
```

**Pour générer APP_SECRET :**
```bash
php -r "echo bin2hex(random_bytes(32));"
```

Ou laissez Railway le générer automatiquement.

#### DATABASE_URL (automatique ou manuel)

**Option 1 : Automatique (recommandé)**
- Si PostgreSQL est connecté au service Web, Railway fournit automatiquement `DATABASE_URL`
- Vérifiez dans Variables → `DATABASE_URL` devrait être là

**Option 2 : Manuel**
1. Allez dans votre service PostgreSQL
2. Variables → Copiez `DATABASE_URL` ou `POSTGRES_URL`
3. Collez dans les variables de votre service Web

#### Variables optionnelles (si vous utilisez Google OAuth) :

```env
GOOGLE_CLIENT_ID=votre_client_id
GOOGLE_CLIENT_SECRET=votre_client_secret
```

### ÉTAPE 5 : Vérifier la configuration

1. Cliquez sur votre service Web
2. Allez dans **"Settings"**
3. Vérifiez :
   - **Start Command:** `vendor/bin/heroku-php-apache2 public/`
   - **Build Command:** (dans railway.json, automatique)

### ÉTAPE 6 : Déployer

1. Railway déploiera automatiquement votre code
2. Attendez que le déploiement soit terminé (icône verte ✅)
3. Vérifiez les logs pour voir le processus

### ÉTAPE 7 : Exécuter les migrations

**Méthode 1 : Via le terminal Railway**

1. Cliquez sur votre service Web
2. Allez dans **"Deployments"** → Cliquez sur le dernier déploiement
3. Utilisez **"Run Command"** ou **"Shell"**
4. Exécutez :
   ```bash
   php bin/console doctrine:migrations:migrate --no-interaction
   ```

**Méthode 2 : Via les logs**

1. Ouvrez les logs de votre service
2. Utilisez le terminal intégré
3. Exécutez la commande de migration

### ÉTAPE 8 : Créer les dossiers d'upload

Dans le terminal Railway :

```bash
mkdir -p public/uploads/annonces
mkdir -p public/uploads/profiles
chmod -R 755 public/uploads
```

### ÉTAPE 9 : Générer une URL publique

1. Dans votre service Web → **"Settings"**
2. Allez dans **"Networking"** ou **"Domains"**
3. Cliquez sur **"Generate Domain"**
4. Railway générera une URL comme :
   ```
   https://bricolage-production.up.railway.app
   ```
5. **Copiez cette URL** - c'est l'URL de votre site !

### ÉTAPE 10 : Tester votre site

1. Ouvrez l'URL générée dans votre navigateur
2. Votre site devrait être accessible !
3. Testez les fonctionnalités principales

---

## 🔧 Configuration avancée

### Utiliser un domaine personnalisé

1. Dans **Settings** → **Networking**
2. Cliquez sur **"Custom Domain"**
3. Ajoutez votre domaine (ex: `monsite.com`)
4. Suivez les instructions DNS :
   - Ajoutez un enregistrement CNAME
   - Pointez vers l'URL Railway fournie

### Monitoring et logs

**Voir les logs en temps réel :**
1. Cliquez sur votre service Web
2. Onglet **"Logs"**
3. Vous verrez tous les logs en temps réel

**Voir les métriques :**
- CPU, RAM, Network dans l'onglet **"Metrics"**

### Redéploiement

**Automatique :**
- Railway redéploie automatiquement quand vous poussez sur GitHub

**Manuel :**
1. Service Web → **"Deployments"**
2. Cliquez sur **"Redeploy"** → **"Redeploy Latest"**

---

## 🐛 Dépannage

### Erreur 404

**Problème :** Le site affiche 404

**Solutions :**
1. Vérifiez que `startCommand` est correct dans Settings
2. Vérifiez que `public/router.php` existe
3. Vérifiez les logs pour les erreurs

### Erreur de base de données

**Problème :** Erreur de connexion à la base de données

**Solutions :**
1. Vérifiez que `DATABASE_URL` est correct dans Variables
2. Vérifiez que le service PostgreSQL est démarré
3. Vérifiez que les migrations sont exécutées
4. Testez la connexion dans le terminal :
   ```bash
   php bin/console doctrine:database:create
   ```

### Site ne démarre pas

**Problème :** Le service ne démarre pas

**Solutions :**
1. Vérifiez les logs dans Railway
2. Vérifiez que toutes les variables d'environnement sont définies
3. Vérifiez que `composer install` s'est bien exécuté
4. Vérifiez que le port `$PORT` est utilisé

### Erreur de permissions (uploads)

**Problème :** Impossible d'uploader des fichiers

**Solutions :**
```bash
chmod -R 755 public/uploads
```

---

## 📊 Informations techniques

### Structure de l'URL Railway

```
https://[nom-du-projet]-[environnement].up.railway.app
```

Exemples :
- `https://bricolage-production.up.railway.app`
- `https://bricolage-staging.up.railway.app`

### Variables d'environnement automatiques

Railway fournit automatiquement (ne pas ajouter manuellement) :
- `PORT` - Port sur lequel votre application doit écouter
- `RAILWAY_ENVIRONMENT` - Environnement (production, etc.)
- `RAILWAY_PROJECT_ID` - ID du projet
- `RAILWAY_SERVICE_ID` - ID du service
- `DATABASE_URL` - Si PostgreSQL est connecté

### Commandes utiles Railway

Dans le terminal Railway :

```bash
# Vérifier les routes Symfony
php bin/console debug:router

# Exécuter les migrations
php bin/console doctrine:migrations:migrate --no-interaction

# Vérifier le statut des migrations
php bin/console doctrine:migrations:status

# Vider le cache
php bin/console cache:clear --env=prod

# Réchauffer le cache
php bin/console cache:warmup --env=prod

# Vérifier la configuration
php bin/console debug:container --env=prod

# Vérifier la connexion à la base de données
php bin/console doctrine:database:create --if-not-exists
```

---

## ✅ Checklist de déploiement

- [ ] Compte Railway créé
- [ ] Projet créé et connecté à GitHub (`SMATH05/bricolage`)
- [ ] Base de données PostgreSQL créée
- [ ] Variables d'environnement configurées :
  - [ ] `APP_ENV=prod`
  - [ ] `APP_DEBUG=0`
  - [ ] `APP_SECRET` (généré)
  - [ ] `DATABASE_URL` (automatique ou manuel)
- [ ] Service Web configuré
- [ ] Déploiement réussi (icône verte ✅)
- [ ] Migrations exécutées
- [ ] Dossiers d'upload créés
- [ ] URL publique générée
- [ ] Site accessible et fonctionnel

---

## 🎯 Résumé rapide

1. **Créer compte** → [railway.app](https://railway.app)
2. **Créer projet** → Connecter GitHub → Sélectionner `SMATH05/bricolage`
3. **Ajouter PostgreSQL** → "+ New" → "Database" → "PostgreSQL"
4. **Configurer variables** → `APP_ENV=prod`, `APP_DEBUG=0`, `APP_SECRET`
5. **Générer URL** → Settings → Networking → "Generate Domain"
6. **Exécuter migrations** → Terminal → `php bin/console doctrine:migrations:migrate`
7. **Créer dossiers** → `mkdir -p public/uploads/annonces public/uploads/profiles`
8. **Tester** → Ouvrir l'URL dans le navigateur

---

## 📞 Support et ressources

- **Documentation Railway :** [docs.railway.app](https://docs.railway.app)
- **Support Railway :** [railway.app/support](https://railway.app/support)
- **Guides détaillés :** 
  - `RAILWAY_SETUP.md` - Guide complet
  - `RAILWAY_QUICK_START.md` - Démarrage rapide
  - `RAILWAY_ENV_VARS.md` - Variables d'environnement

---

## 🎉 C'est tout !

Votre site Symfony est maintenant prêt à être déployé sur Railway !

**Prochaine étape :** Suivez les étapes ci-dessus pour déployer votre site.

**URL finale :** `https://votre-projet-production.up.railway.app` (générée par Railway)
