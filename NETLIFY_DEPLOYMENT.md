# Configuration Netlify pour Symfony

## ⚠️ IMPORTANT : Netlify ne supporte pas PHP/Symfony

Netlify est conçu pour les sites statiques et ne peut pas exécuter des applications PHP/Symfony complètes.

## Si vous voulez quand même essayer (non recommandé)

### Configuration dans l'interface Netlify :

1. **Base directory:** 
   - Laissez **vide** ou mettez `.` (point)
   - C'est le répertoire racine de votre projet

2. **Build command:**
   ```
   composer install --no-dev --optimize-autoloader && php bin/console asset-map:compile
   ```
   Ou laissez vide si vous ne voulez pas de build

3. **Publish directory:**
   ```
   public
   ```
   C'est le dossier `public/` de Symfony qui contient `index.php`

4. **Functions directory:**
   ```
   netlify/functions
   ```
   (Laissez par défaut, vous n'en aurez probablement pas besoin)

## Alternatives recommandées pour Symfony

### 1. **Replit** (que vous utilisez déjà)
- Support PHP natif
- Base de données PostgreSQL incluse
- Déploiement simple

### 2. **Railway**
- Support PHP/Symfony
- Base de données PostgreSQL/MySQL
- Déploiement depuis GitHub

### 3. **Heroku**
- Support PHP/Symfony
- Add-ons pour bases de données
- Configuration via `Procfile`

### 4. **DigitalOcean App Platform**
- Support PHP/Symfony
- Base de données managée
- Scaling automatique

### 5. **VPS traditionnel**
- Contrôle total
- Apache/Nginx + PHP-FPM
- Base de données MySQL/PostgreSQL

## Conclusion

Pour votre projet Symfony avec base de données, **continuez avec Replit** ou migrez vers Railway/Heroku. Netlify ne fonctionnera pas pour une application Symfony complète.
