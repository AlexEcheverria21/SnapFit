-- Structure V3 SnapFit (Optimisée et Corrigée pour Import)
-- Refactoring pour clarté sémantique et filtrage efficace
-- Tables : UTILISATEUR, ARTICLE (ex-Favori), FAVORI (ex-Ajouter), DOMAINE (ex-SiteEco/Scam), RECHERCHE

-- IMPORTANT : On désactive les vérifications pour pouvoir tout supprimer proprement
SET FOREIGN_KEY_CHECKS = 0;

-- =============================================
-- NETTOYAGE COMPLET (DROP)
-- On supprime les tables dans l'ordre inverse des dépendances (par précaution)
-- =============================================
DROP TABLE IF EXISTS FAVORI;
DROP TABLE IF EXISTS AJOUTER; -- Ancienne table
DROP TABLE IF EXISTS RECHERCHE;
DROP TABLE IF EXISTS ARTICLE;
DROP TABLE IF EXISTS DOMAINE;
DROP TABLE IF EXISTS SITE_SCAM; -- Ancienne table
DROP TABLE IF EXISTS SITE_ECO; -- Ancienne table
DROP TABLE IF EXISTS UTILISATEUR;


-- =============================================
-- CRÉATION DES TABLES
-- =============================================

-- 1. Table UTILISATEUR
CREATE TABLE UTILISATEUR (
    id_utilisateur INT AUTO_INCREMENT PRIMARY KEY,
    nom VARCHAR(50),
    prenom VARCHAR(50),
    mot_de_passe_hash VARCHAR(255) NOT NULL,
    role VARCHAR(20) DEFAULT 'user',
    date_inscription DATETIME DEFAULT CURRENT_TIMESTAMP,
    email VARCHAR(100) UNIQUE NOT NULL,
    nom_connexion VARCHAR(50) UNIQUE NOT NULL,
    sexe VARCHAR(20),
    pays VARCHAR(50),
    -- Champs Sécurité (Atelier Auth)
    tentatives_echouees INT DEFAULT 0 NOT NULL,
    date_dernier_echec_connexion DATETIME DEFAULT NULL,
    statut_compte ENUM('actif', 'desactive') DEFAULT 'actif',
    token_reinitialisation VARCHAR(255) DEFAULT NULL,
    expiration_token DATETIME DEFAULT NULL
) ENGINE=InnoDB;

-- 2. Table DOMAINE (Centralisation Eco + Scam + Autres)
CREATE TABLE DOMAINE (
    id_domaine INT AUTO_INCREMENT PRIMARY KEY,
    url_racine VARCHAR(255) UNIQUE NOT NULL, -- ex: 'shein.com'
    nom VARCHAR(100),
    statut ENUM('eco', 'scam', 'neutre') DEFAULT 'neutre',
    description TEXT
) ENGINE=InnoDB;

-- 3. Table ARTICLE (Source de vérité des produits)
CREATE TABLE ARTICLE (
    id_article INT AUTO_INCREMENT PRIMARY KEY,
    url VARCHAR(500) NOT NULL,
    image VARCHAR(500),
    categorie VARCHAR(100),
    marque VARCHAR(100),
    api_ref_id VARCHAR(100) UNIQUE, -- ID unique API
    date_creation DATETIME DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- 4. Table FAVORI (Table de liaison User <-> Article)
CREATE TABLE FAVORI (
    id_utilisateur INT,
    id_article INT,
    date_ajout DATETIME DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id_utilisateur, id_article),
    CONSTRAINT fk_fav_user FOREIGN KEY (id_utilisateur) REFERENCES UTILISATEUR(id_utilisateur) ON DELETE CASCADE,
    CONSTRAINT fk_fav_article FOREIGN KEY (id_article) REFERENCES ARTICLE(id_article) ON DELETE CASCADE
) ENGINE=InnoDB;

-- 5. Table RECHERCHE (Historique)
CREATE TABLE RECHERCHE (
    id_recherche INT AUTO_INCREMENT PRIMARY KEY,
    id_utilisateur INT,
    image_scan VARCHAR(255) NOT NULL,
    date_recherche DATETIME DEFAULT CURRENT_TIMESTAMP,
    api_id VARCHAR(100),
    CONSTRAINT fk_rech_user FOREIGN KEY (id_utilisateur) REFERENCES UTILISATEUR(id_utilisateur) ON DELETE CASCADE
) ENGINE=InnoDB;


-- =============================================
-- JEU DE DONNÉES TEST (SEEDING)
-- =============================================

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
