# 🔐 Variables d'environnement Railway

## Variables requises

Copiez-collez ces variables dans Railway → Votre Service → Variables

### Variables essentielles

```env
APP_ENV=prod
APP_DEBUG=0
APP_SECRET=<générez un secret aléatoire>
```

### Générer APP_SECRET

Dans Railway, vous pouvez :
1. Laisser Railway le générer automatiquement
2. Ou générer un secret avec cette commande :
   ```bash
   php -r "echo bin2hex(random_bytes(32));"
   ```

### DATABASE_URL (automatique)

Si vous avez connecté PostgreSQL à votre service Web, Railway fournit automatiquement `DATABASE_URL`.

Sinon, copiez depuis votre service PostgreSQL → Variables → `DATABASE_URL`

Format typique :
```
postgresql://postgres:password@host:5432/railway
```

## Variables optionnelles

### Google OAuth (si utilisé)

```env
GOOGLE_CLIENT_ID=votre_client_id_google
GOOGLE_CLIENT_SECRET=votre_secret_google
```

### Autres variables Symfony

```env
TRUSTED_PROXIES=*
TRUSTED_HOSTS=*
```

## Comment ajouter dans Railway

1. Allez dans votre service Web
2. Cliquez sur l'onglet **"Variables"**
3. Cliquez sur **"+ New Variable"**
4. Ajoutez chaque variable :
   - **Key:** `APP_ENV`
   - **Value:** `prod`
5. Répétez pour chaque variable

## Variables automatiques Railway

Railway fournit automatiquement (ne pas ajouter manuellement) :
- `PORT` - Port d'écoute de votre application
- `RAILWAY_ENVIRONMENT` - Environnement Railway
- `RAILWAY_PROJECT_ID` - ID du projet
- `RAILWAY_SERVICE_ID` - ID du service

## Vérification

Après avoir ajouté les variables, redéployez votre service pour qu'elles prennent effet.
