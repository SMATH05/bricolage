# 🚀 Guide de déploiement sur Render (GRATUIT)

## Pourquoi Render ?
- ✅ **100% gratuit** (avec limitations raisonnables)
- ✅ Base de données PostgreSQL gratuite
- ✅ Déploiement automatique depuis GitHub
- ✅ SSL automatique
- ✅ Très simple à configurer

## ⚠️ Limitation du plan gratuit
- Le service se met en veille après **15 minutes d'inactivité**
- Le premier accès après veille prend **30-60 secondes** pour redémarrer
- **750 heures gratuites/mois** (plus que suffisant)

## 📋 Étapes de déploiement

### Étape 1 : Créer un compte Render
1. Allez sur [render.com](https://render.com)
2. Cliquez sur **"Get Started for Free"**
3. Connectez-vous avec votre compte **GitHub**

### Étape 2 : Créer un nouveau Web Service
1. Dans le dashboard, cliquez sur **"New +"**
2. Sélectionnez **"Web Service"**
3. Connectez votre dépôt GitHub si ce n'est pas déjà fait
4. Sélectionnez votre dépôt `bricolage`

### Étape 3 : Configurer le service
Render détectera automatiquement le fichier `render.yaml` !

**Si la détection automatique ne fonctionne pas, configurez manuellement :**

- **Name:** `bricolage-app` (ou ce que vous voulez)
- **Environment:** `PHP`
- **Build Command:** 
  ```
  composer install --no-dev --optimize-autoloader && php bin/console cache:clear --env=prod
  ```
- **Start Command:**
  ```
  php -S 0.0.0.0:$PORT -t public public/router.php
  ```
- **Plan:** `Free`

### Étape 4 : Ajouter une base de données PostgreSQL
1. Dans le dashboard, cliquez sur **"New +"**
2. Sélectionnez **"PostgreSQL"**
3. Configurez :
   - **Name:** `bricolage-db`
   - **Database:** `bricolage`
   - **User:** `bricolage_user`
   - **Plan:** `Free`
4. Cliquez sur **"Create Database"**

### Étape 5 : Configurer les variables d'environnement
Dans votre Web Service, allez dans **"Environment"** et ajoutez :

```
APP_ENV=prod
APP_SECRET=votre_secret_généré_ici
DATABASE_URL=<copiez depuis la base de données PostgreSQL>
```

**Pour obtenir DATABASE_URL :**
1. Allez dans votre base de données PostgreSQL
2. Dans **"Connections"**, copiez **"Internal Database URL"**
3. Collez-le dans la variable `DATABASE_URL`

### Étape 6 : Déployer
1. Cliquez sur **"Create Web Service"**
2. Render va automatiquement :
   - Cloner votre dépôt
   - Installer les dépendances
   - Démarrer votre application
3. Attendez que le déploiement soit terminé (icône verte ✅)

### Étape 7 : Exécuter les migrations
1. Dans votre Web Service, allez dans **"Shell"**
2. Exécutez :
   ```bash
   php bin/console doctrine:migrations:migrate --no-interaction
   ```

### Étape 8 : Accéder à votre site
1. Une fois déployé, Render génère automatiquement une URL
2. Elle ressemble à : `https://bricolage-app.onrender.com`
3. Cliquez sur l'URL pour accéder à votre site !

## 🔧 Configuration avancée

### Éviter la mise en veille (optionnel)
Pour éviter que votre site se mette en veille après 15 minutes :

1. Utilisez [UptimeRobot](https://uptimerobot.com) (gratuit)
2. Créez un compte
3. Ajoutez un "HTTP(s) Monitor"
4. Entrez l'URL de votre site Render
5. Configurez pour ping toutes les **5 minutes**
6. Votre site restera actif !

## 🐛 Dépannage

### Erreur 404
- Vérifiez que `startCommand` utilise `public/router.php`
- Vérifiez les logs dans Render

### Erreur de base de données
- Vérifiez que `DATABASE_URL` est correctement configuré
- Vérifiez que les migrations sont exécutées

### Site ne démarre pas
- Vérifiez les logs dans Render
- Vérifiez que toutes les variables d'environnement sont définies

## ✅ Checklist

- [ ] Compte Render créé
- [ ] Web Service créé et configuré
- [ ] Base de données PostgreSQL créée
- [ ] Variables d'environnement configurées
- [ ] Migrations exécutées
- [ ] Site accessible via l'URL Render
- [ ] (Optionnel) UptimeRobot configuré pour éviter la veille

## 🎉 C'est tout !

Votre site est maintenant déployé gratuitement sur Render !
