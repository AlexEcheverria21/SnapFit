-- Structure V2 - Ajout Article et Domaine
CREATE TABLE UTILISATEUR (
    id_utilisateur INT AUTO_INCREMENT PRIMARY KEY,
    nom VARCHAR(50),
    prenom VARCHAR(50),
    email VARCHAR(100) UNIQUE NOT NULL,
    mot_de_passe_hash VARCHAR(255) NOT NULL,
    role VARCHAR(20) DEFAULT 'user',
    nom_connexion VARCHAR(50) UNIQUE,
    date_inscription DATETIME DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

CREATE TABLE DOMAINE (
    id_domaine INT AUTO_INCREMENT PRIMARY KEY,
    url_racine VARCHAR(255) UNIQUE NOT NULL,
    nom VARCHAR(100),
    statut ENUM('eco', 'scam', 'neutre') DEFAULT 'neutre',
    description TEXT
) ENGINE=InnoDB;

CREATE TABLE ARTICLE (
    id_article INT AUTO_INCREMENT PRIMARY KEY,
    url VARCHAR(500) NOT NULL,
    image VARCHAR(500),
    categorie VARCHAR(100),
    marque VARCHAR(100),
    api_ref_id VARCHAR(100) UNIQUE,
    date_creation DATETIME DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;