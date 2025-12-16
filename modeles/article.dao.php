<?php
class ArticleDao {
    private ?PDO $pdo;

    public function __construct(?PDO $pdo) {
        $this->pdo = $pdo;
    }

    public function create(Article $article) {
        $sql = "INSERT INTO ARTICLE (url, image, api_ref_id, date_creation) VALUES (:url, :image, :ref, NOW())";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            ':url' => $article->getUrl(),
            ':image' => $article->getImage(),
            ':ref' => $article->getApiRefId()
        ]);
        return $this->pdo->lastInsertId();
    }
}