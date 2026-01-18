-- Script SQL pour corriger les tables PostgreSQL sur Replit
-- Exécutez ce script dans votre console PostgreSQL sur Replit

-- Supprimer toutes les tables existantes (si elles existent)
DROP TABLE IF EXISTS candidature CASCADE;
DROP TABLE IF EXISTS annonce CASCADE;
DROP TABLE IF EXISTS chercheur CASCADE;
DROP TABLE IF EXISTS recruteur CASCADE;
DROP TABLE IF EXISTS admin CASCADE;
DROP TABLE IF EXISTS "user" CASCADE;
DROP TABLE IF EXISTS messenger_messages CASCADE;

-- Créer la table user avec la syntaxe PostgreSQL
CREATE TABLE "user" (
    id SERIAL NOT NULL,
    email VARCHAR(180) NOT NULL,
    roles JSON NOT NULL,
    password VARCHAR(255) NOT NULL,
    is_verified BOOLEAN NOT NULL,
    PRIMARY KEY(id)
);

CREATE UNIQUE INDEX UNIQ_IDENTIFIER_EMAIL ON "user" (email);

-- Créer la table admin
CREATE TABLE admin (
    id SERIAL NOT NULL,
    id_admin VARCHAR(255) NOT NULL,
    nom VARCHAR(255) NOT NULL,
    email VARCHAR(255) NOT NULL,
    mot_de_passe VARCHAR(255) NOT NULL,
    regles TEXT NOT NULL,
    PRIMARY KEY(id)
);

-- Créer la table recruteur
CREATE TABLE recruteur (
    id SERIAL NOT NULL,
    user_id INT DEFAULT NULL,
    nom VARCHAR(255) NOT NULL,
    email VARCHAR(255) NOT NULL,
    password VARCHAR(255) NOT NULL,
    adresse VARCHAR(255) DEFAULT NULL,
    telephone VARCHAR(255) NOT NULL,
    photo VARCHAR(255) DEFAULT NULL,
    PRIMARY KEY(id)
);

CREATE UNIQUE INDEX UNIQ_2BD3678CA76ED395 ON recruteur (user_id);

-- Créer la table chercheur
CREATE TABLE chercheur (
    id SERIAL NOT NULL,
    user_id INT DEFAULT NULL,
    id_chercheur VARCHAR(255) NOT NULL,
    nom VARCHAR(255) NOT NULL,
    prenom VARCHAR(255) NOT NULL,
    email VARCHAR(255) NOT NULL,
    mot_de_passe VARCHAR(255) NOT NULL,
    description TEXT NOT NULL,
    disponibilite VARCHAR(255) NOT NULL,
    photo VARCHAR(255) DEFAULT NULL,
    PRIMARY KEY(id)
);

CREATE UNIQUE INDEX UNIQ_9DD29B50A76ED395 ON chercheur (user_id);

-- Créer la table annonce
CREATE TABLE annonce (
    id SERIAL NOT NULL,
    recrut_id_id INT DEFAULT NULL,
    titre VARCHAR(255) NOT NULL,
    description VARCHAR(255) NOT NULL,
    date_publication TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL,
    budget DOUBLE PRECISION NOT NULL,
    photo VARCHAR(255) DEFAULT NULL,
    PRIMARY KEY(id)
);

CREATE INDEX IDX_F65593E5CE8B0FFA ON annonce (recrut_id_id);

-- Créer la table candidature
CREATE TABLE candidature (
    id SERIAL NOT NULL,
    chercheur_id_id INT DEFAULT NULL,
    annonce_id_id INT DEFAULT NULL,
    id_candidature VARCHAR(255) NOT NULL,
    date_pro TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL,
    statut VARCHAR(255) NOT NULL,
    PRIMARY KEY(id)
);

CREATE INDEX IDX_E33BD3B826B2368C ON candidature (chercheur_id_id);
CREATE INDEX IDX_E33BD3B868C955C8 ON candidature (annonce_id_id);

-- Créer la table messenger_messages
CREATE TABLE messenger_messages (
    id BIGSERIAL NOT NULL,
    body TEXT NOT NULL,
    headers TEXT NOT NULL,
    queue_name VARCHAR(190) NOT NULL,
    created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL,
    available_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL,
    delivered_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL,
    PRIMARY KEY(id)
);

CREATE INDEX IDX_75EA56E0FB7336F0 ON messenger_messages (queue_name);
CREATE INDEX IDX_75EA56E0E3BD61CE ON messenger_messages (available_at);
CREATE INDEX IDX_75EA56E016BA31DB ON messenger_messages (delivered_at);

-- Ajouter les contraintes de clés étrangères
ALTER TABLE annonce ADD CONSTRAINT FK_F65593E5CE8B0FFA FOREIGN KEY (recrut_id_id) REFERENCES recruteur (id) NOT DEFERRABLE INITIALLY IMMEDIATE;
ALTER TABLE candidature ADD CONSTRAINT FK_E33BD3B826B2368C FOREIGN KEY (chercheur_id_id) REFERENCES chercheur (id) NOT DEFERRABLE INITIALLY IMMEDIATE;
ALTER TABLE candidature ADD CONSTRAINT FK_E33BD3B868C955C8 FOREIGN KEY (annonce_id_id) REFERENCES annonce (id) NOT DEFERRABLE INITIALLY IMMEDIATE;
ALTER TABLE chercheur ADD CONSTRAINT FK_9DD29B50A76ED395 FOREIGN KEY (user_id) REFERENCES "user" (id) NOT DEFERRABLE INITIALLY IMMEDIATE;
ALTER TABLE recruteur ADD CONSTRAINT FK_2BD3678CA76ED395 FOREIGN KEY (user_id) REFERENCES "user" (id) NOT DEFERRABLE INITIALLY IMMEDIATE;

-- Mettre à jour la table des migrations pour indiquer que la migration a été exécutée
-- (Remplacez 'DoctrineMigrations\\Version20260118095102' par la version exacte si différente)
INSERT INTO doctrine_migration_versions (version, executed_at, execution_time) 
VALUES ('DoctrineMigrations\\Version20260118095102', NOW(), 0)
ON CONFLICT (version) DO NOTHING;

