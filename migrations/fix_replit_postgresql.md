# Instructions pour corriger la base de données sur Replit

## Problème
La migration a été créée avec la syntaxe MySQL, mais Replit utilise PostgreSQL. Il faut recréer les tables avec la syntaxe PostgreSQL.

## Solution

### Option 1 : Via la console Replit

1. Connectez-vous à votre base de données PostgreSQL sur Replit
2. Exécutez ces commandes pour supprimer toutes les tables :

```sql
DROP TABLE IF EXISTS candidature CASCADE;
DROP TABLE IF EXISTS annonce CASCADE;
DROP TABLE IF EXISTS chercheur CASCADE;
DROP TABLE IF EXISTS recruteur CASCADE;
DROP TABLE IF EXISTS admin CASCADE;
DROP TABLE IF EXISTS "user" CASCADE;
DROP TABLE IF EXISTS messenger_messages CASCADE;
DROP TABLE IF EXISTS doctrine_migration_versions CASCADE;
```

3. Ensuite, réexécutez la migration :

```bash
php bin/console doctrine:migrations:migrate --no-interaction
```

### Option 2 : Via Symfony Console

1. Supprimez toutes les migrations exécutées :
```bash
php bin/console doctrine:migrations:version --delete --all --no-interaction
```

2. Réexécutez la migration :
```bash
php bin/console doctrine:migrations:migrate --no-interaction
```

### Option 3 : Recréer complètement la base de données

```bash
php bin/console doctrine:database:drop --force
php bin/console doctrine:database:create
php bin/console doctrine:migrations:migrate --no-interaction
```

## Vérification

Après avoir exécuté la migration, vérifiez que la table `user` existe avec la colonne `id` :

```sql
SELECT column_name, data_type 
FROM information_schema.columns 
WHERE table_name = 'user';
```

Vous devriez voir une colonne `id` de type `integer` ou `serial`.

