# 🔧 Fix 404 Error on Railway

## Problème
Vous obtenez une erreur 404 sur votre site Railway déployé.

## Solutions

### Solution 1 : Utiliser Apache (Recommandé) ✅

Railway devrait utiliser le `Procfile` qui contient Apache. Vérifiez dans Railway :

1. **Dans Railway Dashboard :**
   - Allez dans votre service PHP
   - Ouvrez **"Settings"**
   - Vérifiez **"Start Command"**
   - Il devrait être : `vendor/bin/heroku-php-apache2 public/`

2. **Si ce n'est pas le cas, modifiez-le manuellement :**
   - Dans Railway, allez dans **Settings** → **Deploy**
   - Changez **Start Command** en :
     ```
     vendor/bin/heroku-php-apache2 public/
     ```

3. **Redeployez :**
   - Railway redéploiera automatiquement avec la nouvelle commande

### Solution 2 : Utiliser le serveur PHP intégré avec router

Si Apache ne fonctionne pas, utilisez cette commande :

```
php -S 0.0.0.0:$PORT -t public public/router.php
```

Le fichier `public/router.php` a été créé pour router correctement les requêtes.

### Solution 3 : Vérifier les variables d'environnement

Assurez-vous que ces variables sont définies dans Railway :

1. **Allez dans votre service** → **Variables**
2. **Vérifiez :**
   ```
   APP_ENV=prod
   APP_SECRET=votre_secret_ici
   DATABASE_URL=postgresql://...
   ```

### Solution 4 : Vérifier les logs

1. **Dans Railway Dashboard :**
   - Cliquez sur votre service
   - Ouvrez l'onglet **"Logs"**
   - Cherchez les erreurs

2. **Erreurs communes :**
   - `Class not found` → Vérifiez que `composer install` s'est bien exécuté
   - `Database connection failed` → Vérifiez `DATABASE_URL`
   - `Cache directory not writable` → Vérifiez les permissions

### Solution 5 : Vérifier que les migrations sont exécutées

Dans Railway, vous pouvez exécuter des commandes :

1. **Allez dans votre service**
2. **Ouvrez "Deployments"**
3. **Cliquez sur le dernier déploiement**
4. **Utilisez "Run Command"** pour exécuter :
   ```
   php bin/console doctrine:migrations:migrate --no-interaction
   ```

### Solution 6 : Vérifier le routing Symfony

Testez si les routes fonctionnent :

1. **Dans Railway, exécutez :**
   ```
   php bin/console debug:router
   ```

2. **Vérifiez que vos routes sont listées**

## Checklist de vérification

- [ ] Start Command est correct (`vendor/bin/heroku-php-apache2 public/`)
- [ ] Variables d'environnement configurées (APP_ENV, APP_SECRET, DATABASE_URL)
- [ ] Base de données PostgreSQL démarrée
- [ ] Migrations exécutées
- [ ] Pas d'erreurs dans les logs
- [ ] Cache Symfony généré (`var/cache/prod` existe)

## Commandes utiles pour Railway

Dans Railway, vous pouvez exécuter ces commandes via "Run Command" :

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

## Si rien ne fonctionne

1. **Supprimez le service et recréez-le**
2. **Vérifiez que votre code est bien poussé sur GitHub**
3. **Vérifiez que Railway est connecté au bon dépôt**
4. **Contactez le support Railway** avec les logs d'erreur
