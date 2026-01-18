# 🚂 Guide de déploiement Railway - Accès à votre site

## Comment trouver l'URL de votre site sur Railway

### Méthode 1 : Via le Dashboard Railway

1. **Connectez-vous à Railway**
   - Allez sur [railway.app](https://railway.app)
   - Connectez-vous avec votre compte

2. **Sélectionnez votre projet**
   - Cliquez sur le projet que vous avez créé

3. **Trouvez l'URL**
   - Dans votre service PHP/Web, vous verrez une section **"Settings"** ou **"Networking"**
   - Cherchez **"Generate Domain"** ou **"Custom Domain"**
   - Railway génère automatiquement une URL comme : `votre-projet.up.railway.app`

4. **Copiez l'URL**
   - Cliquez sur l'URL générée
   - Elle devrait ressembler à : `https://votre-projet-production.up.railway.app`

### Méthode 2 : Via les logs de déploiement

1. **Ouvrez votre service**
   - Cliquez sur le service PHP/Web dans votre projet

2. **Allez dans l'onglet "Deployments"**
   - Vous verrez les logs de déploiement

3. **Cherchez dans les logs**
   - Railway affiche souvent l'URL dans les logs
   - Cherchez une ligne avec "Listening on" ou "Server running on"

### Méthode 3 : Via les variables d'environnement

1. **Ouvrez les Settings de votre service**
2. **Allez dans "Variables"**
3. **Cherchez `RAILWAY_PUBLIC_DOMAIN`**
   - Cette variable contient votre URL publique

---

## 🔧 Configuration de l'URL personnalisée (optionnel)

### Étape 1 : Générer un domaine Railway

1. Dans votre service, allez dans **"Settings"**
2. Cliquez sur **"Generate Domain"**
3. Railway créera une URL comme : `votre-projet-production.up.railway.app`

### Étape 2 : Configurer un domaine personnalisé (optionnel)

Si vous avez votre propre domaine :

1. Dans **"Settings"** → **"Networking"**
2. Cliquez sur **"Custom Domain"**
3. Ajoutez votre domaine (ex: `monsite.com`)
4. Suivez les instructions pour configurer les DNS

---

## ⚠️ Problèmes courants

### Le site ne s'affiche pas

1. **Vérifiez que le déploiement est réussi**
   - Allez dans "Deployments"
   - Vérifiez qu'il n'y a pas d'erreurs (icône verte ✅)

2. **Vérifiez les variables d'environnement**
   - `APP_ENV=prod`
   - `DATABASE_URL` est correctement configuré
   - `APP_SECRET` est défini

3. **Vérifiez les logs**
   - Cliquez sur votre service
   - Allez dans l'onglet "Logs"
   - Cherchez les erreurs

### Erreur 502 Bad Gateway

- Votre application PHP ne démarre pas correctement
- Vérifiez la commande de démarrage dans `railway.json`
- Vérifiez que le port `$PORT` est utilisé

### Erreur de base de données

- Vérifiez que le service PostgreSQL est démarré
- Vérifiez que `DATABASE_URL` pointe vers la bonne base
- Exécutez les migrations : `php bin/console doctrine:migrations:migrate`

---

## 📝 Checklist de déploiement

- [ ] Service PHP/Web créé et déployé
- [ ] Service PostgreSQL créé et démarré
- [ ] Variables d'environnement configurées
- [ ] Migrations exécutées
- [ ] URL générée et accessible
- [ ] Site fonctionne correctement

---

## 🔗 Structure typique de l'URL Railway

```
https://[nom-du-projet]-[environnement].up.railway.app
```

Exemple :
```
https://bricolage-production.up.railway.app
```

---

## 💡 Astuce

Railway génère automatiquement une URL HTTPS sécurisée. Vous n'avez pas besoin de configurer SSL manuellement !
