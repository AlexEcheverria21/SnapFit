-- Script SQL à importer dans votre bdd phpMyAdmin
-- Oubliez pas de mettre vos bons identifiants dans config.exemple.yaml et de renommer le fichier en config.yaml

-- NETTOYAGE COMPLET, vous pouvez exécuter ce script dans votre base actuelle dans 
-- importer sur phpmyadmin
SET FOREIGN_KEY_CHECKS = 0;
DROP TABLE IF EXISTS FAVORI;
DROP TABLE IF EXISTS AJOUTER; -- Ancienne table au cas ou
DROP TABLE IF EXISTS RECHERCHE;
DROP TABLE IF EXISTS ARTICLE;
DROP TABLE IF EXISTS DOMAINE;
DROP TABLE IF EXISTS SITE_SCAM; -- pareil
DROP TABLE IF EXISTS SITE_ECO; -- pareil
DROP TABLE IF EXISTS UTILISATEUR;


-- CRÉATION DES TABLES

-- 1. Table UTILISATEUR
CREATE TABLE UTILISATEUR (

    -- Champs Identité
    id_utilisateur INT AUTO_INCREMENT PRIMARY KEY,
    nom VARCHAR(50),
    prenom VARCHAR(50),
    mot_de_passe_hash VARCHAR(255) NOT NULL,
    role VARCHAR(20) DEFAULT 'user', -- user/admin
    date_inscription DATETIME DEFAULT CURRENT_TIMESTAMP, 
    email VARCHAR(100) UNIQUE NOT NULL,
    nom_connexion VARCHAR(50) UNIQUE NOT NULL,

    -- Champs Sécurité 
    tentatives_echouees INT DEFAULT 0 NOT NULL,
    date_dernier_echec_connexion DATETIME DEFAULT NULL,
    statut_compte ENUM('actif', 'desactive') DEFAULT 'actif',
    token_reinitialisation VARCHAR(255) DEFAULT NULL,
    expiration_token DATETIME DEFAULT NULL

); 

-- 2. Table DOMAINE (Sites Eco + Scam)
CREATE TABLE DOMAINE (
    id_domaine INT AUTO_INCREMENT PRIMARY KEY,
    url_racine VARCHAR(255) UNIQUE NOT NULL, 
    nom VARCHAR(100),
    statut ENUM('eco', 'scam')
);

-- 3. Table ARTICLE 
CREATE TABLE ARTICLE (
    id_article INT AUTO_INCREMENT PRIMARY KEY,
    url VARCHAR(500) NOT NULL,
    image VARCHAR(500),
    api_ref_id VARCHAR(100) UNIQUE, -- l'API Serpapi nous renvoie un id unique par article
    date_creation DATETIME DEFAULT CURRENT_TIMESTAMP
);

-- 4. Table FAVORI (Table de liaison entre Utilisateur et Article)
CREATE TABLE FAVORI (
    id_utilisateur INT,
    id_article INT,
    date_ajout DATETIME DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id_utilisateur, id_article),
    CONSTRAINT fk_fav_user FOREIGN KEY (id_utilisateur) REFERENCES UTILISATEUR(id_utilisateur) ON DELETE CASCADE,
    CONSTRAINT fk_fav_article FOREIGN KEY (id_article) REFERENCES ARTICLE(id_article) ON DELETE CASCADE
);

-- 5. Table RECHERCHE (Historique)
CREATE TABLE RECHERCHE (
    id_recherche INT AUTO_INCREMENT PRIMARY KEY,
    id_utilisateur INT,
    image_scan VARCHAR(255) NOT NULL,
    date_recherche DATETIME DEFAULT CURRENT_TIMESTAMP,
    api_id VARCHAR(100), -- Pour reprendre l'image de l'article recherché
    CONSTRAINT fk_rech_user FOREIGN KEY (id_utilisateur) REFERENCES UTILISATEUR(id_utilisateur) ON DELETE CASCADE
) ENGINE=InnoDB;

-- JEU DE DONNÉES TEST 

INSERT INTO UTILISATEUR (nom, prenom, mot_de_passe_hash, role, email, nom_connexion) VALUES
('Admin', 'Super', '$2y$10$8K1p/a0d3.0.0.0.0.0.0.0.0.0.0.0', 'admin', 'admin@snapfit.com', 'admin'),
('User', 'Test', '$2y$10$8K1p/a0d3.0.0.0.0.0.0.0.0.0.0.0', 'user', 'user@test.com', 'user');

INSERT INTO DOMAINE (url_racine, nom, statut) VALUES
('shein.com', 'Shein', 'scam'),
('temu.com', 'Temu', 'scam'),
('aliexpress.com', 'AliExpress', 'scam'),
('wish.com', 'Wish', 'scam'),
('patagonia.com', 'Patagonia', 'eco'),
('veja-store.com', 'Veja', 'eco'),
('vinted.fr', 'Vinted', 'eco');

-- Réactivation des vérifications
SET FOREIGN_KEY_CHECKS = 1;

