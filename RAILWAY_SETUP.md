# 🚂 Guide de déploiement Railway - Configuration complète

## ✅ Votre projet est maintenant prêt pour Railway !

Tous les fichiers nécessaires ont été créés et configurés.

## 📋 Fichiers de configuration

- ✅ `railway.json` - Configuration Railway optimisée
- ✅ `Procfile` - Configuration Apache (alternative)
- ✅ `public/router.php` - Router pour PHP built-in server

## 🚀 Étapes de déploiement sur Railway

### Étape 1 : Créer un compte Railway

1. Allez sur [railway.app](https://railway.app)
2. Cliquez sur **"Start a New Project"**
3. Connectez-vous avec votre compte **GitHub**

### Étape 2 : Créer un nouveau projet

1. Cliquez sur **"New Project"**
2. Sélectionnez **"Deploy from GitHub repo"**
3. Autorisez Railway à accéder à votre GitHub si nécessaire
4. Sélectionnez votre dépôt : `SMATH05/bricolage`
5. Railway va automatiquement détecter que c'est un projet PHP

### Étape 3 : Ajouter une base de données PostgreSQL

1. Dans votre projet Railway, cliquez sur **"+ New"**
2. Sélectionnez **"Database"** → **"Add PostgreSQL"**
3. Railway créera automatiquement une base de données PostgreSQL
4. **Notez le nom de la base de données** (ex: `railway`)

### Étape 4 : Configurer les variables d'environnement

1. Cliquez sur votre service PHP/Web
2. Allez dans l'onglet **"Variables"**
3. Ajoutez les variables suivantes :

#### Variables requises :

```
APP_ENV=prod
APP_DEBUG=0
APP_SECRET=<généré automatiquement ou créez-en un>
DATABASE_URL=<copié automatiquement depuis PostgreSQL>
```

#### Comment obtenir DATABASE_URL :

1. Cliquez sur votre service **PostgreSQL**
2. Allez dans l'onglet **"Variables"**
3. Copiez la variable **`DATABASE_URL`** ou **`POSTGRES_URL`**
4. Collez-la dans les variables de votre service Web

**Ou Railway le fait automatiquement :**
- Railway connecte automatiquement les services
- La variable `DATABASE_URL` peut être automatiquement disponible

#### Variables optionnelles (si vous utilisez Google OAuth) :

```
GOOGLE_CLIENT_ID=votre_client_id
GOOGLE_CLIENT_SECRET=votre_client_secret
```

### Étape 5 : Configurer le service Web

Railway devrait détecter automatiquement `railway.json`, mais vérifiez :

1. Cliquez sur votre service Web
2. Allez dans **"Settings"**
3. Vérifiez **"Start Command"** :
   ```
   vendor/bin/heroku-php-apache2 public/
   ```
   Ou si vous utilisez PHP built-in server :
   ```
   php -S 0.0.0.0:$PORT -t public public/router.php
   ```

4. Vérifiez **"Build Command"** (dans railway.json) :
   ```
   composer install --no-dev --optimize-autoloader && php bin/console cache:clear --env=prod && php bin/console cache:warmup --env=prod
   ```

### Étape 6 : Déployer

1. Railway déploiera automatiquement votre code
2. Attendez que le déploiement soit terminé (icône verte ✅)
3. Vérifiez les logs pour voir si tout fonctionne

### Étape 7 : Exécuter les migrations

1. Dans votre service Web, cliquez sur l'onglet **"Deployments"**
2. Cliquez sur le dernier déploiement
3. Cliquez sur **"View Logs"** ou utilisez **"Run Command"**
4. Exécutez :
   ```bash
   php bin/console doctrine:migrations:migrate --no-interaction
   ```

**Ou via le terminal Railway :**
1. Cliquez sur votre service Web
2. Allez dans **"Deployments"** → **"Latest"**
3. Utilisez le bouton **"Run Command"** ou **"Shell"**
4. Exécutez la commande de migration

### Étape 8 : Créer les dossiers d'upload

Dans le terminal Railway, exécutez :

```bash
mkdir -p public/uploads/annonces
mkdir -p public/uploads/profiles
chmod -R 755 public/uploads
```

### Étape 9 : Générer une URL publique

1. Dans votre service Web, allez dans **"Settings"**
2. Allez dans l'onglet **"Networking"** ou **"Domains"**
3. Cliquez sur **"Generate Domain"**
4. Railway générera une URL comme : `https://votre-projet-production.up.railway.app`
5. Copiez cette URL

### Étape 10 : Accéder à votre site

1. Ouvrez l'URL générée dans votre navigateur
2. Votre site devrait être accessible !

## 🔧 Configuration avancée

### Utiliser un domaine personnalisé

1. Dans **"Settings"** → **"Networking"**
2. Cliquez sur **"Custom Domain"**
3. Ajoutez votre domaine (ex: `monsite.com`)
4. Suivez les instructions pour configurer les DNS

### Vérifier les logs

1. Cliquez sur votre service Web
2. Allez dans l'onglet **"Logs"**
3. Vous verrez tous les logs en temps réel

### Redéployer après un changement

Railway redéploie automatiquement quand vous poussez du code sur GitHub.

Ou manuellement :
1. Allez dans votre service Web
2. Cliquez sur **"Redeploy"** → **"Redeploy Latest"**

## 🐛 Dépannage

### Erreur 404
- Vérifiez que `startCommand` est correct
- Vérifiez les logs dans Railway
- Vérifiez que `public/router.php` existe

### Erreur de base de données
- Vérifiez que `DATABASE_URL` est correct
- Vérifiez que les migrations sont exécutées
- Vérifiez que le service PostgreSQL est démarré

### Erreur de permissions (uploads)
- Exécutez dans le terminal : `chmod -R 755 public/uploads`

### Site ne démarre pas
- Vérifiez les logs
- Vérifiez que toutes les variables d'environnement sont définies
- Vérifiez que `composer install` s'est bien exécuté

## ✅ Checklist finale

- [ ] Compte Railway créé
- [ ] Projet créé et connecté à GitHub
- [ ] Base de données PostgreSQL créée
- [ ] Variables d'environnement configurées
- [ ] Service Web configuré
- [ ] Migrations exécutées
- [ ] Dossiers d'upload créés
- [ ] URL publique générée
- [ ] Site accessible

## 📝 Informations importantes

### Structure de l'URL Railway

```
https://[nom-du-projet]-[environnement].up.railway.app
```

Exemple :
```
https://bricolage-production.up.railway.app
```

### Variables d'environnement automatiques Railway

Railway fournit automatiquement :
- `PORT` - Port sur lequel votre application doit écouter
- `RAILWAY_ENVIRONMENT` - Environnement (production, etc.)
- `DATABASE_URL` - Si vous avez une base de données PostgreSQL connectée

### Commandes utiles Railway

Dans le terminal Railway, vous pouvez exécuter :

```bash
# Vérifier les routes
php bin/console debug:router

# Exécuter les migrations
php bin/console doctrine:migrations:migrate --no-interaction

# Vider le cache
php bin/console cache:clear --env=prod

# Vérifier la configuration
php bin/console debug:container --env=prod
```

## 🎉 C'est tout !

Votre site Symfony est maintenant déployé sur Railway !

**URL de votre site :** `https://votre-projet-production.up.railway.app` (ou l'URL générée par Railway)

---

## 📞 Support

Si vous rencontrez des problèmes :
1. Vérifiez les logs dans Railway
2. Vérifiez la documentation Railway : [docs.railway.app](https://docs.railway.app)
3. Vérifiez que tous les fichiers de configuration sont corrects
