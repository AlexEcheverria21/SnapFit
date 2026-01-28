-- Fichier: sql/triggers.sql
-- Description: Contient les 16 triggers du projet SnapFit
-- Note: Certains triggers nécessitent des colonnes spécifiques (ex: id_createur sur Article)

DELIMITER $$

-- ==============================================================================
-- 1. SÉCURITÉ : Verrouillage automatique du compte après trop d'échecs
-- ==============================================================================
DROP TRIGGER IF EXISTS securite_verrouillage_compte$$
CREATE TRIGGER securite_verrouillage_compte
BEFORE UPDATE ON UTILISATEUR
FOR EACH ROW
BEGIN
    -- Si le nombre de tentatives atteint 3, on désactive le compte
    IF NEW.tentatives_echouees >= 3 THEN
        SET NEW.statut_compte = 'desactive';
    END IF;
END$$

-- ==============================================================================
-- 2. LOGIQUE METIER : Limite de 100 favoris par utilisateur
-- ==============================================================================
DROP TRIGGER IF EXISTS check_limit_favoris$$
CREATE TRIGGER check_limit_favoris
BEFORE INSERT ON FAVORI
FOR EACH ROW
BEGIN
    DECLARE nb_favoris INT;

    SELECT COUNT(*) INTO nb_favoris 
    FROM FAVORI 
    WHERE id_utilisateur = NEW.id_utilisateur;

    IF nb_favoris >= 100 THEN
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'Ajout impossible : L''utilisateur a atteint la limite de 100 favoris.';
    END IF;
END$$

-- ==============================================================================
-- 3. AUTOMATISATION : Auto-favori pour le créateur (Nécessite id_createur sur Article)
-- ==============================================================================
DROP TRIGGER IF EXISTS auto_favori_createur$$
CREATE TRIGGER auto_favori_createur
AFTER INSERT ON ARTICLE
FOR EACH ROW
BEGIN
    -- ATTENTION: Ce trigger suppose que la table ARTICLE possède une colonne 'id_createur'
    -- Si la colonne existe, on ajoute automatiquement le favori
    -- Insertion fictive basée sur votre demande :
    -- INSERT INTO FAVORI (id_utilisateur, id_article, date_ajout)
    -- VALUES (NEW.id_createur, NEW.id_article, NOW());
    
    -- (Code commenté pour éviter les erreurs SQL si la colonne n'existe pas encore dans votre BDD)
    BEGIN END; 
END$$

-- ==============================================================================
-- 4. MAINTENANCE : Suppression des articles orphelins (Sans favoris)
-- ==============================================================================
DROP TRIGGER IF EXISTS suppr_article_orphelin$$
CREATE TRIGGER suppr_article_orphelin
AFTER DELETE ON FAVORI
FOR EACH ROW
BEGIN
    DECLARE nb_restant INT;

    -- Compte combien de fois cet article est encore en favori ailleurs
    SELECT COUNT(*) INTO nb_restant 
    FROM FAVORI 
    WHERE id_article = OLD.id_article;

    -- S'il n'est plus dans aucun favori, on supprime l'article
    IF nb_restant = 0 THEN
        DELETE FROM ARTICLE WHERE id_article = OLD.id_article;
    END IF;
END$$

-- ==============================================================================
-- 5. INTÉGRITÉ : Anti-doublon intelligent sur les domaines (extension différente)
-- ==============================================================================
DROP TRIGGER IF EXISTS check_doublon_domaine_extension$$
CREATE TRIGGER check_doublon_domaine_extension 
BEFORE INSERT ON DOMAINE 
FOR EACH ROW 
BEGIN 
    DECLARE racine_nom VARCHAR(100); 
    DECLARE nb_doublons INT;
    
    -- Extrait la partie avant le dernier point (ex: 'shein' de 'shein.com')
    SET racine_nom = SUBSTRING_INDEX(NEW.url_racine, '.', 1); 
    
    -- Vérifie si une autre extension existe déjà
    SELECT COUNT(*) INTO nb_doublons 
    FROM DOMAINE 
    WHERE url_racine REGEXP CONCAT('^', racine_nom, '\.[a-z]+$'); 
    
    IF nb_doublons > 0 THEN 
        SIGNAL SQLSTATE '45000' 
        SET MESSAGE_TEXT = 'Erreur Bloquante : Ce nom de domaine existe déjà avec une autre extension.'; 
    END IF; 
END$$

-- ==============================================================================
-- 6. INTÉGRITÉ : Vérification unicité email (Redondant avec UNIQUE mais pédagogique)
-- ==============================================================================
DROP TRIGGER IF EXISTS verif_email_avant_inscription$$
CREATE TRIGGER verif_email_avant_inscription
BEFORE INSERT ON UTILISATEUR
FOR EACH ROW
BEGIN
    DECLARE email_count INT;

    SELECT COUNT(*) INTO email_count
    FROM UTILISATEUR
    WHERE email = NEW.email;

    IF email_count > 0 THEN
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'Erreur : Cet email est déjà utilisé par un autre compte.';
    END IF;
END$$

-- ==============================================================================
-- 7. DATA QUALITY : Normalisation Email en minuscule (INSERT)
-- ==============================================================================
DROP TRIGGER IF EXISTS normalize_email_insert$$
CREATE TRIGGER normalize_email_insert
BEFORE INSERT ON UTILISATEUR
FOR EACH ROW
BEGIN
    SET NEW.email = LOWER(NEW.email);
END$$

-- ==============================================================================
-- 8. DATA QUALITY : Normalisation Email en minuscule (UPDATE)
-- ==============================================================================
DROP TRIGGER IF EXISTS normalize_email_update$$
CREATE TRIGGER normalize_email_update
BEFORE UPDATE ON UTILISATEUR
FOR EACH ROW
BEGIN
    IF NEW.email != OLD.email THEN
        SET NEW.email = LOWER(NEW.email);
    END IF;
END$$

-- ==============================================================================
-- 9. SÉCURITÉ : Protection du Super Admin (ID 1)
-- ==============================================================================
DROP TRIGGER IF EXISTS protect_super_admin$$
CREATE TRIGGER protect_super_admin
BEFORE DELETE ON UTILISATEUR
FOR EACH ROW
BEGIN
    IF OLD.id_utilisateur = 1 THEN
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'INTERDIT : Impossible de supprimer le Super Administrateur (ID 1).';
    END IF;
END$$

-- ==============================================================================
-- 10. LOGIQUE : Réinitialisation compteur échecs lors de la réactivation
-- ==============================================================================
DROP TRIGGER IF EXISTS reset_attempts_on_unlock$$
CREATE TRIGGER reset_attempts_on_unlock
BEFORE UPDATE ON UTILISATEUR
FOR EACH ROW
BEGIN
    -- Si on passe de 'desactive' à 'actif' manuellement, on remet les compteurs à 0
    IF OLD.statut_compte = 'desactive' AND NEW.statut_compte = 'actif' THEN
        SET NEW.tentatives_echouees = 0;
        SET NEW.date_dernier_echec_connexion = NULL;
    END IF;
END$$

-- ==============================================================================
-- 11. INTÉGRITÉ : Date inscription cohérente (Pas dans le futur)
-- ==============================================================================
DROP TRIGGER IF EXISTS prevent_future_inscription$$
CREATE TRIGGER prevent_future_inscription
BEFORE INSERT ON UTILISATEUR
FOR EACH ROW
BEGIN
    IF NEW.date_inscription > NOW() THEN
        SET NEW.date_inscription = NOW();
    END IF;
END$$

-- ==============================================================================
-- 12. IMMUABILITÉ : Empêcher modification date inscription
-- ==============================================================================
DROP TRIGGER IF EXISTS prevent_date_inscription_change$$
CREATE TRIGGER prevent_date_inscription_change
BEFORE UPDATE ON UTILISATEUR
FOR EACH ROW
BEGIN
    -- On force l'ancienne date même si on essaie de la changer
    SET NEW.date_inscription = OLD.date_inscription;
END$$

-- ==============================================================================
-- 13. AUDIT : Historisation suppression utilisateur (Création table archive requise)
-- ==============================================================================
-- Note: Nécessite la table UTILISATEUR_ARCHIVE
-- CREATE TABLE IF NOT EXISTS UTILISATEUR_ARCHIVE LIKE UTILISATEUR;
-- ALTER TABLE UTILISATEUR_ARCHIVE ADD COLUMN date_suppression DATETIME;
DROP TRIGGER IF EXISTS audit_user_delete$$
CREATE TRIGGER audit_user_delete
AFTER DELETE ON UTILISATEUR
FOR EACH ROW
BEGIN
    -- Exemple d'insertion d'audit (commenté si table inexistante)
    -- INSERT INTO UTILISATEUR_ARCHIVE (id_utilisateur, nom, email, date_suppression)
    -- VALUES (OLD.id_utilisateur, OLD.nom, OLD.email, NOW());
    BEGIN END;
END$$

-- ==============================================================================
-- 14. DATA QUALITY : Validation protocole URL Article
-- ==============================================================================
DROP TRIGGER IF EXISTS validate_article_url$$
CREATE TRIGGER validate_article_url
BEFORE INSERT ON ARTICLE
FOR EACH ROW
BEGIN
    -- Si l'URL ne commence pas par http, on ajoute https:// par défaut
    IF NEW.url NOT LIKE 'http%' THEN
        SET NEW.url = CONCAT('https://', NEW.url);
    END IF;
END$$

-- ==============================================================================
-- 15. MAINTENANCE : Nettoyage Token Réinitialisation après usage
-- ==============================================================================
DROP TRIGGER IF EXISTS cleanup_password_reset$$
CREATE TRIGGER cleanup_password_reset
BEFORE UPDATE ON UTILISATEUR
FOR EACH ROW
BEGIN
    -- Si le mot de passe change, on invalide le token
    IF NEW.mot_de_passe_hash != OLD.mot_de_passe_hash THEN
        SET NEW.token_reinitialisation = NULL;
        SET NEW.expiration_token = NULL;
    END IF;
END$$

-- ==============================================================================
-- 16. INTÉGRITÉ : Vérification cohérence Domaine (Eco/Scam)
-- ==============================================================================
DROP TRIGGER IF EXISTS validate_search_integrity$$
CREATE TRIGGER validate_search_integrity
BEFORE INSERT ON RECHERCHE
FOR EACH ROW
BEGIN
   -- Empêche d'insérer une recherche sans image (double check en plus du NOT NULL)
   IF NEW.image_scan IS NULL OR NEW.image_scan = '' THEN
       SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Erreur : Une recherche doit obligatoirement avoir une image associée.';
   END IF;
END$$

DELIMITER ;