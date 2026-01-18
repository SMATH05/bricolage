# 🆓 Options d'hébergement GRATUITES pour Symfony

## Options recommandées (par ordre de facilité)

### 1. **Render** ⭐ EXCELLENT (Gratuit avec limites)

**Avantages:**
- ✅ Plan gratuit disponible
- ✅ Base de données PostgreSQL gratuite
- ✅ Support PHP/Symfony
- ✅ Déploiement depuis GitHub
- ✅ SSL automatique
- ✅ Très simple à configurer

**Limites du plan gratuit:**
- ⚠️ Service web se met en veille après 15 minutes d'inactivité
- ⚠️ Redémarre au premier accès (peut prendre 30-60 secondes)
- ⚠️ 750 heures gratuites/mois

**Prix:** Gratuit (avec limitations)

**Étapes:**
1. Créez un compte sur [render.com](https://render.com)
2. Connectez votre dépôt GitHub
3. Créez un nouveau "Web Service"
4. Sélectionnez votre dépôt
5. Configuration :
   - **Build Command:** `composer install --no-dev --optimize-autoloader`
   - **Start Command:** `php -S 0.0.0.0:$PORT -t public public/router.php`
   - **Environment:** PHP
6. Ajoutez une base de données PostgreSQL (gratuite)
7. Configurez les variables d'environnement
8. Déployez !

**Fichier `render.yaml` déjà créé dans votre projet !**

---

### 2. **Fly.io** ⭐ TRÈS BON (Gratuit généreux)

**Avantages:**
- ✅ Plan gratuit généreux (3 VMs gratuites)
- ✅ Support PHP/Symfony
- ✅ Base de données PostgreSQL incluse
- ✅ Pas de mise en veille
- ✅ SSL automatique
- ✅ Déploiement depuis GitHub

**Limites:**
- ⚠️ 3 VMs gratuites max
- ⚠️ 160GB de transfert/mois
- ⚠️ Configuration un peu plus complexe

**Prix:** Gratuit jusqu'à 3 VMs

**Étapes:**
1. Créez un compte sur [fly.io](https://fly.io)
2. Installez `flyctl` (CLI)
3. Connectez votre dépôt GitHub
4. Créez une app : `fly launch`
5. Ajoutez PostgreSQL : `fly postgres create`
6. Déployez : `fly deploy`

---

### 3. **AlwaysData** ⭐ BON (Hébergement PHP gratuit)

**Avantages:**
- ✅ 100% gratuit
- ✅ Support PHP 8.2
- ✅ Base de données MySQL/PostgreSQL
- ✅ Pas de publicité
- ✅ SSL gratuit
- ✅ FTP/SSH inclus

**Limites:**
- ⚠️ 100MB d'espace disque
- ⚠️ 1 base de données
- ⚠️ Configuration manuelle requise
- ⚠️ Pas de déploiement automatique GitHub

**Prix:** Gratuit

**Étapes:**
1. Créez un compte sur [alwaysdata.com](https://www.alwaysdata.com)
2. Créez un site web
3. Uploadez vos fichiers via FTP
4. Configurez la base de données
5. Configurez les variables d'environnement

---

### 4. **InfinityFree** (Hébergement PHP gratuit)

**Avantages:**
- ✅ 100% gratuit
- ✅ Support PHP
- ✅ Base de données MySQL
- ✅ Pas de limite de bande passante
- ✅ SSL gratuit

**Limites:**
- ⚠️ 5GB d'espace
- ⚠️ Publicité sur le site (peut être désactivée)
- ⚠️ Pas de déploiement automatique
- ⚠️ Support limité

**Prix:** Gratuit

**Étapes:**
1. Créez un compte sur [infinityfree.net](https://www.infinityfree.net)
2. Créez un site web
3. Uploadez vos fichiers via FTP
4. Configurez la base de données MySQL

---

### 5. **000webhost** (Hébergement PHP gratuit)

**Avantages:**
- ✅ 100% gratuit
- ✅ Support PHP
- ✅ Base de données MySQL
- ✅ SSL gratuit
- ✅ cPanel inclus

**Limites:**
- ⚠️ 300MB d'espace
- ⚠️ Publicité
- ⚠️ Pas de déploiement automatique
- ⚠️ Support limité

**Prix:** Gratuit

---

### 6. **Oracle Cloud Free Tier** (VPS gratuit)

**Avantages:**
- ✅ VPS gratuit permanent (2 VMs)
- ✅ 10TB de transfert/mois
- ✅ Contrôle total
- ✅ Pas de limites de temps
- ✅ Très puissant

**Limites:**
- ⚠️ Configuration manuelle requise
- ⚠️ Nécessite des connaissances Linux
- ⚠️ Crédit card requise (mais gratuit)

**Prix:** Gratuit (permanent)

**Étapes:**
1. Créez un compte sur [oracle.com/cloud](https://www.oracle.com/cloud/free/)
2. Créez une instance VM (Ubuntu)
3. Installez PHP, Nginx, PostgreSQL
4. Configurez votre application
5. Configurez SSL avec Let's Encrypt

---

### 7. **Google Cloud Run** (Gratuit avec limites)

**Avantages:**
- ✅ Plan gratuit généreux
- ✅ Support conteneurs Docker
- ✅ Scaling automatique
- ✅ SSL automatique

**Limites:**
- ⚠️ 2 millions de requêtes/mois gratuites
- ⚠️ Nécessite Docker
- ⚠️ Configuration plus complexe

**Prix:** Gratuit jusqu'à 2M requêtes/mois

---

## 🎯 Ma recommandation TOP 3

### 1. **Render** (Le plus simple)
- ✅ Gratuit
- ✅ Très facile à configurer
- ✅ Base de données incluse
- ⚠️ Se met en veille après 15 min (mais redémarre automatiquement)

### 2. **Fly.io** (Le plus puissant)
- ✅ Gratuit généreux
- ✅ Pas de mise en veille
- ✅ Très performant
- ⚠️ Configuration un peu plus complexe

### 3. **AlwaysData** (Le plus traditionnel)
- ✅ 100% gratuit
- ✅ Hébergement PHP classique
- ✅ Pas de mise en veille
- ⚠️ Configuration manuelle

---

## 📋 Comparaison rapide

| Plateforme | Gratuit | Facilité | Base de données | Mise en veille | Recommandé |
|------------|---------|----------|-----------------|----------------|------------|
| **Render** | ✅ | ⭐⭐⭐⭐⭐ | ✅ PostgreSQL | ⚠️ Oui (15min) | ⭐⭐⭐⭐⭐ |
| **Fly.io** | ✅ | ⭐⭐⭐⭐ | ✅ PostgreSQL | ✅ Non | ⭐⭐⭐⭐⭐ |
| **AlwaysData** | ✅ | ⭐⭐⭐ | ✅ MySQL/PostgreSQL | ✅ Non | ⭐⭐⭐⭐ |
| **InfinityFree** | ✅ | ⭐⭐⭐ | ✅ MySQL | ✅ Non | ⭐⭐⭐ |
| **000webhost** | ✅ | ⭐⭐⭐ | ✅ MySQL | ✅ Non | ⭐⭐ |
| **Oracle Cloud** | ✅ | ⭐⭐ | ❌ À installer | ✅ Non | ⭐⭐⭐⭐ |

---

## 🚀 Configuration pour Render (Recommandé)

Le fichier `render.yaml` est déjà créé dans votre projet !

1. **Créez un compte Render**
2. **Connectez GitHub**
3. **Créez un nouveau "Web Service"**
4. **Sélectionnez votre dépôt**
5. **Render détectera automatiquement `render.yaml`**
6. **Ajoutez les variables d'environnement**
7. **Déployez !**

---

## 💡 Astuce

Pour éviter la mise en veille sur Render (plan gratuit), vous pouvez utiliser un service comme [UptimeRobot](https://uptimerobot.com) (gratuit) pour ping votre site toutes les 5 minutes.
