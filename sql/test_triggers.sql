-- Fichier: sql/test_triggers.sql
-- Description: Scénarios de test pour les 16 triggers.
-- Instructions: Exécutez les blocs un par un.

-- ==============================================================================
-- 1. check_limit_favoris (Limite 100 favoris)
-- ==============================================================================
-- Note : Difficile à tester sans spammer la base. On vérifie juste qu'on peut en insérer un.
INSERT INTO ARTICLE (url) VALUES ('http://test-limit.com');
SET @art_id = LAST_INSERT_ID();
INSERT INTO FAVORI (id_utilisateur, id_article) VALUES (1, @art_id);
-- Si ça marche, c'est bon (car < 100). Pour tester le blocage, il faudrait une boucle de 100 insertions.

-- ==============================================================================
-- 2. auto_favori_createur (Auto favori créateur)
-- ==============================================================================
-- Note : Ce trigger est inactif car votre table ARTICLE n'a pas de colonne 'id_createur'.
-- Test ignoré car fonctionnel uniquement si vous modifiez la structure de la BDD.

-- ==============================================================================
-- 3. suppr_article_orphelin (Suppression auto article)
-- ==============================================================================
-- On supprime le favori créé en (1)
DELETE FROM FAVORI WHERE id_article = @art_id;
-- VÉRIFICATION : L'article doit être supprimé
SELECT * FROM ARTICLE WHERE id_article = @art_id;

-- ==============================================================================
-- 4. validate_article_url (Ajout HTTPS)
-- ==============================================================================
INSERT INTO ARTICLE (url) VALUES ('www.sans-http.com', 'img.png');
SELECT url FROM ARTICLE WHERE url LIKE '%sans-http%'; -- Doit avoir https://

-- ==============================================================================
-- 5. check_doublon_domaine_extension (Anti doublon domaine)
-- ==============================================================================
INSERT INTO DOMAINE (url_racine, nom, statut) VALUES ('test-doublon.com', 'Test', 'eco');
-- Doit échouer :
INSERT INTO DOMAINE (url_racine, nom, statut) VALUES ('test-doublon.fr', 'Test FR', 'eco');

-- ==============================================================================
-- 6. securite_verrouillage_compte (Lock après 3 échecs)
-- ==============================================================================
INSERT INTO UTILISATEUR (nom_connexion, email, mot_de_passe_hash) VALUES ('hacker', 'hacker@test.com', 'hash');
UPDATE UTILISATEUR SET tentatives_echouees = 3 WHERE nom_connexion = 'hacker';
SELECT statut_compte FROM UTILISATEUR WHERE nom_connexion = 'hacker'; -- Doit être 'desactive'

-- ==============================================================================
-- 7. reset_attempts_on_unlock (Reset tentatives à l'unlock)
-- ==============================================================================
UPDATE UTILISATEUR SET statut_compte = 'actif' WHERE nom_connexion = 'hacker';
SELECT tentatives_echouees FROM UTILISATEUR WHERE nom_connexion = 'hacker'; -- Doit être 0

-- ==============================================================================
-- 8. verif_email_avant_inscription (Doublon Email)
-- ==============================================================================
-- Doit échouer car 'admin@snapfit.com' existe déjà (si importé depuis snapfit.sql)
INSERT INTO UTILISATEUR (nom, email, mot_de_passe_hash, nom_connexion) VALUES ('Test', 'admin@snapfit.com', 'pw', 'test2');

-- ==============================================================================
-- 9. prevent_future_inscription (Pas de date future)
-- ==============================================================================
INSERT INTO UTILISATEUR (nom_connexion, email, mot_de_passe_hash, date_inscription) 
VALUES ('futureman', 'future@man.com', 'hash', '2099-01-01');
SELECT date_inscription FROM UTILISATEUR WHERE nom_connexion = 'futureman'; -- Doit être date du jour

-- ==============================================================================
-- 10. prevent_date_inscription_change (Immuabilité date inscription)
-- ==============================================================================
UPDATE UTILISATEUR SET date_inscription = '2000-01-01' WHERE nom_connexion = 'futureman';
SELECT date_inscription FROM UTILISATEUR WHERE nom_connexion = 'futureman'; -- Ne doit PAS avoir changé

-- ==============================================================================
-- 11. normalize_email_insert (Email minuscule insert)
-- ==============================================================================
INSERT INTO UTILISATEUR (nom_connexion, email, mot_de_passe_hash) VALUES ('shouty', 'SHOUTY@MAIL.COM', 'hash');
SELECT email FROM UTILISATEUR WHERE nom_connexion = 'shouty'; -- shouty@mail.com

-- ==============================================================================
-- 12. normalize_email_update (Email minuscule update)
-- ==============================================================================
UPDATE UTILISATEUR SET email = 'NEW.UPPER@MAIL.COM' WHERE nom_connexion = 'shouty';
SELECT email FROM UTILISATEUR WHERE nom_connexion = 'shouty'; -- new.upper@mail.com

-- ==============================================================================
-- 13. protect_super_admin (Protection ID 1)
-- ==============================================================================
DELETE FROM UTILISATEUR WHERE id_utilisateur = 1; -- Doit échouer

-- ==============================================================================
-- 14. cleanup_password_reset (Nettoyage token)
-- ==============================================================================
-- On met un token fictif
UPDATE UTILISATEUR SET token_reinitialisation = 'token123' WHERE nom_connexion = 'hacker';
-- On change le mot de passe
UPDATE UTILISATEUR SET mot_de_passe_hash = 'newhash' WHERE nom_connexion = 'hacker';
SELECT token_reinitialisation FROM UTILISATEUR WHERE nom_connexion = 'hacker'; -- Doit être NULL

-- ==============================================================================
-- 15. validate_search_integrity (Image obligatoire pour recherche)
-- ==============================================================================
-- Doit échouer (image vide)
INSERT INTO RECHERCHE (id_utilisateur, image_scan) VALUES (1, '');

-- ==============================================================================
-- 16. audit_user_delete (Audit suppression)
-- ==============================================================================
-- Note : Ce trigger nécessite une table UTILISATEUR_ARCHIVE.
-- On la crée temporairement pour le test :
CREATE TABLE IF NOT EXISTS UTILISATEUR_ARCHIVE LIKE UTILISATEUR;
ALTER TABLE UTILISATEUR_ARCHIVE ADD COLUMN date_suppression DATETIME;

-- Suppression d'un user test
DELETE FROM UTILISATEUR WHERE nom_connexion = 'shouty';

-- Vérification archive (Si le trigger a été décommenté pour activer la partie INSERT)
-- SELECT * FROM UTILISATEUR_ARCHIVE;
