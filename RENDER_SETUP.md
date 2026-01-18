# 🚀 Guide de déploiement Render - Configuration complète

## ✅ Votre projet est maintenant prêt pour Render !

Tous les fichiers nécessaires ont été créés et configurés.

## 📋 Fichiers créés/modifiés

- ✅ `render.yaml` - Configuration Render optimisée
- ✅ `public/router.php` - Router amélioré pour PHP built-in server
- ✅ `.renderignore` - Fichiers à ignorer lors du déploiement
- ✅ `scripts/render-build.sh` - Script de build (optionnel)

## 🚀 Étapes de déploiement sur Render

### Étape 1 : Préparer votre dépôt GitHub

1. **Assurez-vous que tous les fichiers sont commités :**
   ```bash
   git add .
   git commit -m "Prepare for Render deployment"
   git push origin main
   ```

### Étape 2 : Créer un compte Render

1. Allez sur [render.com](https://render.com)
2. Cliquez sur **"Get Started for Free"**
3. Connectez-vous avec votre compte **GitHub**

### Étape 3 : Créer la base de données PostgreSQL

1. Dans le dashboard Render, cliquez sur **"New +"**
2. Sélectionnez **"PostgreSQL"**
3. Configurez :
   - **Name:** `bricolage-db`
   - **Database:** `bricolage`
   - **User:** `bricolage_user`
   - **Plan:** `Free`
4. Cliquez sur **"Create Database"**
5. **Notez l'Internal Database URL** (vous en aurez besoin)

### Étape 4 : Créer le Web Service

1. Dans le dashboard, cliquez sur **"New +"**
2. Sélectionnez **"Web Service"**
3. Connectez votre dépôt GitHub si ce n'est pas déjà fait
4. Sélectionnez votre dépôt `bricolage`
5. Render détectera automatiquement le fichier `render.yaml` !

**Si la détection automatique ne fonctionne pas, configurez manuellement :**

- **Name:** `bricolage-app`
- **Environment:** `PHP`
- **Region:** Choisissez la région la plus proche
- **Branch:** `main` (ou votre branche principale)
- **Root Directory:** (laissez vide)
- **Build Command:**
  ```
  composer install --no-dev --optimize-autoloader && php bin/console cache:clear --env=prod && php bin/console cache:warmup --env=prod
  ```
- **Start Command:**
  ```
  php -S 0.0.0.0:$PORT -t public public/router.php
  ```
- **Plan:** `Free`

### Étape 5 : Configurer les variables d'environnement

Dans votre Web Service, allez dans **"Environment"** et ajoutez :

```
APP_ENV=prod
APP_DEBUG=0
APP_SECRET=<généré automatiquement par Render>
DATABASE_URL=<copiez depuis votre base de données PostgreSQL>
```

**Pour obtenir DATABASE_URL :**
1. Allez dans votre base de données PostgreSQL
2. Dans l'onglet **"Connections"**
3. Copiez **"Internal Database URL"**
4. Collez-le dans la variable `DATABASE_URL` de votre Web Service

**Variables optionnelles (si vous utilisez Google OAuth) :**
```
GOOGLE_CLIENT_ID=votre_client_id
GOOGLE_CLIENT_SECRET=votre_client_secret
```

### Étape 6 : Déployer

1. Cliquez sur **"Create Web Service"**
2. Render va automatiquement :
   - Cloner votre dépôt
   - Installer les dépendances Composer
   - Construire votre application
   - Démarrer le serveur
3. Attendez que le déploiement soit terminé (icône verte ✅)

### Étape 7 : Exécuter les migrations

1. Dans votre Web Service, allez dans l'onglet **"Shell"**
2. Exécutez :
   ```bash
   php bin/console doctrine:migrations:migrate --no-interaction
   ```
3. Attendez que les migrations soient terminées

### Étape 8 : Créer les dossiers d'upload

Dans le Shell Render, exécutez :

```bash
mkdir -p public/uploads/annonces
mkdir -p public/uploads/profiles
chmod -R 755 public/uploads
```

### Étape 9 : Accéder à votre site

1. Une fois déployé, Render génère automatiquement une URL
2. Elle ressemble à : `https://bricolage-app.onrender.com`
3. Cliquez sur l'URL pour accéder à votre site !

## 🔧 Configuration avancée

### Éviter la mise en veille (plan gratuit)

Le plan gratuit met le service en veille après 15 minutes d'inactivité.

**Solution : Utiliser UptimeRobot (gratuit)**

1. Créez un compte sur [uptimerobot.com](https://uptimerobot.com)
2. Ajoutez un nouveau monitor :
   - **Monitor Type:** HTTP(s)
   - **URL:** Votre URL Render
   - **Monitoring Interval:** 5 minutes
3. Votre site restera actif !

### Vérifier les logs

1. Dans votre Web Service, allez dans l'onglet **"Logs"**
2. Vous verrez tous les logs en temps réel
3. Utile pour déboguer les problèmes

### Redéployer après un changement

Render redéploie automatiquement quand vous poussez du code sur GitHub.

Ou manuellement :
1. Allez dans votre Web Service
2. Cliquez sur **"Manual Deploy"** → **"Deploy latest commit"**

## 🐛 Dépannage

### Erreur 404
- Vérifiez que `startCommand` utilise `public/router.php`
- Vérifiez les logs dans Render

### Erreur de base de données
- Vérifiez que `DATABASE_URL` est correct
- Vérifiez que les migrations sont exécutées
- Vérifiez que la base de données est démarrée

### Erreur de permissions (uploads)
- Exécutez dans le Shell : `chmod -R 755 public/uploads`

### Site ne démarre pas
- Vérifiez les logs
- Vérifiez que toutes les variables d'environnement sont définies
- Vérifiez que `composer install` s'est bien exécuté

## ✅ Checklist finale

- [ ] Dépôt GitHub à jour
- [ ] Compte Render créé
- [ ] Base de données PostgreSQL créée
- [ ] Web Service créé et configuré
- [ ] Variables d'environnement configurées
- [ ] Migrations exécutées
- [ ] Dossiers d'upload créés
- [ ] Site accessible via l'URL Render
- [ ] (Optionnel) UptimeRobot configuré

## 🎉 C'est tout !

Votre site Symfony est maintenant déployé sur Render !

**URL de votre site :** `https://bricolage-app.onrender.com` (ou l'URL générée par Render)

---

## 📞 Support

Si vous rencontrez des problèmes :
1. Vérifiez les logs dans Render
2. Vérifiez la documentation Render : [render.com/docs](https://render.com/docs)
3. Vérifiez que tous les fichiers de configuration sont corrects
