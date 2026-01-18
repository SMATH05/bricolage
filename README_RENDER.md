# 🚀 Déploiement sur Render.com

Ce projet est configuré et prêt pour être déployé sur Render.com.

## ✅ Configuration complète

Tous les fichiers nécessaires sont en place :
- `render.yaml` - Configuration Render
- `public/router.php` - Router pour PHP built-in server
- `.renderignore` - Fichiers à ignorer
- Dossiers d'upload créés

## 🚀 Déploiement rapide

1. **Poussez votre code sur GitHub**
2. **Créez un compte sur [render.com](https://render.com)**
3. **Connectez votre dépôt GitHub**
4. **Créez une base de données PostgreSQL** (gratuite)
5. **Créez un Web Service** - Render détectera automatiquement `render.yaml`
6. **Configurez les variables d'environnement**
7. **Exécutez les migrations** dans le Shell Render
8. **C'est tout !**

## 📖 Guide complet

Consultez `RENDER_SETUP.md` pour le guide détaillé étape par étape.

## 🔧 Variables d'environnement requises

```
APP_ENV=prod
APP_DEBUG=0
APP_SECRET=<généré par Render>
DATABASE_URL=<depuis votre base PostgreSQL>
```

## 📝 Notes importantes

- Le plan gratuit met le service en veille après 15 minutes d'inactivité
- Utilisez UptimeRobot pour éviter la mise en veille
- Les fichiers uploadés sont stockés dans `public/uploads/`
- Les migrations doivent être exécutées manuellement après le premier déploiement

## 🆘 Support

En cas de problème, vérifiez :
1. Les logs dans Render
2. La configuration dans `render.yaml`
3. Les variables d'environnement
