<?php
/**
 * @file    favori.dao.php
 * @author  Thomas (Team SnapFit)
 * @brief   Gère les favoris (Liaison Utilisateur <-> Article).
 *          Adapté pour le schéma V3 : Table de liaison FAVORI et table de contenu ARTICLE.
 * @version 3.0
 * @date    16/12/2025
 */

require_once 'article.dao.php';

class FavoriDao {
    private ?PDO $pdo;

    public function __construct(?PDO $pdo = null) {
        $this->pdo = $pdo;
    }

    /**
     * @brief   Récupère tous les articles favoris d'un utilisateur.
     * @return  Article[]
     */
    public function findAllByUser(int $idUtilisateur): array {
        $sql = "SELECT A.id_article as id, A.url, A.image, A.date_creation 
                FROM ARTICLE A
                JOIN FAVORI F ON A.id_article = F.id_article
                WHERE F.id_utilisateur = :id_u
                ORDER BY F.date_ajout DESC";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':id_u' => $idUtilisateur]);

        // On réutilise l'hydratation de ArticleDao pour la cohérence,
        // ou on le fait à la main ici car ArticleDao n'est pas statique.
        // Simple : Hydratation manuelle ici pour éviter dépendance cyclique complexe.
        $articles = [];
        while ($ligne = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $articles[] = new Article(
                $ligne['id'],
                $ligne['url'],
                $ligne['image'],
                $ligne['date_creation']
            );
        }
        return $articles;
    }

    /**
     * @brief   Ajoute un article aux favoris de l'utilisateur.
     *          Si l'article n'existe pas encore en base, il est créé.
     * @param   int $idUtilisateur
     * @param   Article $article (Objet contenant les infos issues de l'API)
     * @return  bool Succès
     */
    public function add(int $idUtilisateur, Article $article): bool {
        try {
            $this->pdo->beginTransaction();

            // 1. Vérifier si l'article existe déjà (par URL ou API ID)
            $articleDao = new ArticleDao($this->pdo);
            // On suppose une méthode findByUrl ou on fait une requête directe ici
            // Pour faire simple et robuste V3 : On cherche par URL
            $sqlCheck = "SELECT id_article FROM ARTICLE WHERE url = :url LIMIT 1";
            $stmtCheck = $this->pdo->prepare($sqlCheck);
            $stmtCheck->execute([':url' => $article->getUrl()]);
            $existingId = $stmtCheck->fetchColumn();

            if ($existingId) {
                $idArticle = $existingId;
            } else {
                // Création de l'article
                if ($articleDao->create($article)) {
                    $idArticle = $this->pdo->lastInsertId();
                } else {
                    throw new Exception("Impossible de créer l'article");
                }
            }

            // 2. Créer le lien Favori (IGNORE pour éviter erreur si doublon)
            $sqlLink = "INSERT IGNORE INTO FAVORI (id_utilisateur, id_article, date_ajout) 
                        VALUES (:id_u, :id_a, NOW())";
            $stmtLink = $this->pdo->prepare($sqlLink);
            $stmtLink->execute([
                ':id_u' => $idUtilisateur,
                ':id_a' => $idArticle
            ]);

            $this->pdo->commit();
            return true;

        } catch (Exception $e) {
            if ($this->pdo->inTransaction()) {
                $this->pdo->rollBack();
            }
            return false;
        }
    }

    /**
     * @brief   Supprime un favori (Le lien, pas l'article).
     */
    public function delete(int $idUtilisateur, int $idArticle): bool {
        $sql = "DELETE FROM FAVORI WHERE id_utilisateur = :id_u AND id_article = :id_a";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([
            ':id_u' => $idUtilisateur,
            ':id_a' => $idArticle
        ]);
    }
}