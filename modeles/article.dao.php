<?php

require_once 'Article.class.php';

class ArticleDAO {
    private ?PDO $pdo;

    /**
     * @brief Constructeur du DAO
     * @details Initialise la connexion PDO.
     * @throws Aucun
     */
    public function __construct(?PDO $pdo = null){
        $this->pdo = $pdo;
    }

    /**
     * @brief Récupère l'objet PDO
     * @details Retourne l'instance de connexion actuelle.
     * @throws Aucun
     */
    public function getPdo(): ?PDO {
        return $this->pdo;
    }

    /**
     * @brief Définit l'objet PDO
     * @details Met à jour l'instance de connexion à la base de données.
     * @throws Aucun
     */
    public function setPdo($pdo): void {
        $this->pdo = $pdo;
    }

    /**
     * @brief Récupère un article par son ID
     * @details Sélectionne un article spécifique via sa clé primaire et hydrate l'objet.
     * @throws PDOException Si la requête échoue.
     */
    public function find(int $id): ?Article {
        $sql = "SELECT * FROM ARTICLE WHERE id = :id";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':id' => $id]);

        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        // Si une ligne est trouvée, on instancie l'objet avec les attributs spécifiques
        if ($row) {
            $article = new Article();
            $article->setId($row['id']);
            $article->setIndicePertinence($row['indice_pertinence']);
            $article->setTitreSite($row['titre_site']);
            $article->setUrl($row['url']);
            $article->setTitreRecherche($row['titre_recherche']);
            $article->setImage($row['image']);
            $article->setInStock((bool)$row['in_stock']);
            $article->setPrice($row['price']);

            return $article;
        }

        return null;
    }

    /**
     * @brief Récupère tous les articles
     * @details Retourne la liste complète des articles de la base.
     * @throws PDOException Si la requête échoue.
     */
    public function findAll(): array {
        $sql = "SELECT * FROM ARTICLE";
        $stmt = $this->pdo->query($sql);

        $articles = [];

        // Parcours des résultats pour construire le tableau d'objets
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $article = new Article();
            $article->setId($row['id']);
            $article->setIndicePertinence($row['indice_pertinence']);
            $article->setTitreSite($row['titre_site']);
            $article->setUrl($row['url']);
            $article->setTitreRecherche($row['titre_recherche']);
            $article->setImage($row['image']);
            $article->setInStock((bool)$row['in_stock']);
            $article->setPrice($row['price']);

            $articles[] = $article;
        }

        return $articles;
    }

    /**
     * @brief Recherche des articles
     * @details Recherche partielle sur le titre du site ou le titre de recherche.
     * @throws PDOException Si la requête échoue.
     */
    public function findBySearch(string $query): array {
        $sql = "SELECT * FROM ARTICLE WHERE titre_site LIKE :q OR titre_recherche LIKE :q";
        $stmt = $this->pdo->prepare($sql);

        $searchTerm = "%" . $query . "%";
        $stmt->execute([':q' => $searchTerm]);

        $articles = [];

        // Parcours des résultats trouvés
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $article = new Article();
            $article->setId($row['id']);
            $article->setIndicePertinence($row['indice_pertinence']);
            $article->setTitreSite($row['titre_site']);
            $article->setUrl($row['url']);
            $article->setTitreRecherche($row['titre_recherche']);
            $article->setImage($row['image']);
            $article->setInStock((bool)$row['in_stock']);
            $article->setPrice($row['price']);

            $articles[] = $article;
        }

        return $articles;
    }

    /**
     * @brief Crée un nouvel article
     * @details Insère les données de l'objet Article en base et met à jour son ID.
     * @throws PDOException Si l'insertion échoue.
     */
    public function create(Article $article): bool {
        $sql = "INSERT INTO ARTICLE (indice_pertinence, titre_site, url, titre_recherche, image, in_stock, price) 
                VALUES (:indice, :titre_site, :url, :titre_recherche, :image, :in_stock, :price)";

        $stmt = $this->pdo->prepare($sql);

        $result = $stmt->execute([
            ':indice'          => $article->getIndicePertinence(),
            ':titre_site'      => $article->getTitreSite(),
            ':url'             => $article->getUrl(),
            ':titre_recherche' => $article->getTitreRecherche(),
            ':image'           => $article->getImage(),
            ':in_stock'        => $article->getInStock() ? 1 : 0, // Conversion booléen pour SQL si nécessaire
            ':price'           => $article->getPrice()
        ]);

        // Si l'insertion a réussi, on récupère l'ID auto-incrémenté
        if ($result) {
            $article->setId((int)$this->pdo->lastInsertId());
        }

        return $result;
    }

    /**
     * @brief Met à jour un article existant
     * @details Modifie tous les champs d'un article identifié par son ID.
     * @throws PDOException Si la mise à jour échoue.
     */
    public function update(Article $article): bool {
        $sql = "UPDATE ARTICLE SET 
                    indice_pertinence = :indice, 
                    titre_site = :titre_site, 
                    url = :url, 
                    titre_recherche = :titre_recherche,
                    image = :image,
                    in_stock = :in_stock,
                    price = :price
                WHERE id = :id";

        $stmt = $this->pdo->prepare($sql);

        return $stmt->execute([
            ':indice'          => $article->getIndicePertinence(),
            ':titre_site'      => $article->getTitreSite(),
            ':url'             => $article->getUrl(),
            ':titre_recherche' => $article->getTitreRecherche(),
            ':image'           => $article->getImage(),
            ':in_stock'        => $article->getInStock() ? 1 : 0,
            ':price'           => $article->getPrice(),
            ':id'              => $article->getId()
        ]);
    }

    /**
     * @brief Supprime un article
     * @details Efface l'article correspondant à l'ID donné.
     * @throws PDOException Si la suppression échoue.
     */
    public function delete(int $id): bool {
        $sql = "DELETE FROM ARTICLE WHERE id = :id";
        $stmt = $this->pdo->prepare($sql);

        return $stmt->execute([':id' => $id]);
    }
}
?>