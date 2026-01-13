<?php

/**
 * @file    article.dao.php
 * @author  Thomas (Team SnapFit)
 * @brief   Définit une classe pour gérer la persistance des Articles.
 *          Effectue les opérations CRUD sur la table ARTICLE (V3).
 * @version 0.3
 * @date    14/12/2025
 */

require_once 'article.class.php';

class ArticleDao {
    private ?PDO $pdo;

    public function __construct(?PDO $pdo = null) {
        $this->pdo = $pdo;
    }

    /**
     * @brief   Lit un Article par son ID.
     */
    public function find(int $id): ?Article {
        $sql = "SELECT id_article as id, url, image, date_creation 
                FROM ARTICLE WHERE id_article = :id";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':id' => $id]);
        $ligne = $stmt->fetch(PDO::FETCH_ASSOC);

        return $ligne ? $this->hydrate($ligne) : null;
    }

    /**
     * @brief   Liste tous les articles.
     */
    public function findAll(): array {
        $sql = "SELECT id_article as id, url, image, date_creation 
                FROM ARTICLE ORDER BY date_creation DESC";
        $stmt = $this->pdo->query($sql);
        return $this->hydrateMany($stmt->fetchAll(PDO::FETCH_ASSOC));
    }

    /**
     * @brief   Crée un nouvel article.
     */
    public function create(Article $article): bool {
        $sql = "INSERT INTO ARTICLE (url, image, date_creation) 
                VALUES (:url, :image, NOW())";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([
            ':url' => $article->getUrl(),
            ':image' => $article->getImage()
        ]);
    }

    /**
     * @brief   Hydrate un objet Article à partir d'un tableau associatif.
     * @param   array $ligne Ligne de la BDD.
     * @return  Article Objet hydraté.
     */
    private function hydrate(array $ligne): Article {
        return new Article(
            $ligne['id'] ?? null,
            $ligne['url'] ?? null,
            $ligne['image'] ?? null,
            $ligne['date_creation'] ?? null
        );
    }

    /**
     * @brief   Hydrate une liste d'Articles.
     * @param   array $lignes Tableau de lignes BDD.
     * @return  Article[] Tableau d'objets.
     */
    private function hydrateMany(array $lignes): array {
        $articles = [];
        foreach ($lignes as $ligne) {
            $articles[] = $this->hydrate($ligne);
        }
        return $articles;
    }
}