<?php

require_once 'Recherche.class.php';

class RechercheDAO {
    private ?PDO $pdo;

    /**
     * @brief Constructeur de la classe
     * @details Initialise le DAO avec une instance de connexion à la base de données.
     * @throws Aucun
     */
    public function __construct(PDO $db) {
        $this->db = $db;
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
     * @brief Ajoute une recherche en base de données
     * @details Prépare et exécute une requête INSERT. Si l'insertion réussit, l'ID de l'objet passé en paramètre est mis à jour.
     * @throws PDOException En cas d'erreur lors de l'exécution de la requête SQL.
     */
    public function add(Recherche $recherche): bool {
        $sql = "INSERT INTO RECHERCHE (id_utilisateur, image, date_recherche, api_id) 
                VALUES (:id_utilisateur, :image, :date_recherche, :api_id)";

        $stmt = $this->db->prepare($sql);

        $result = $stmt->execute([
            ':id_utilisateur' => $recherche->getIdUtilisateur(),
            ':image'          => $recherche->getImage(),
            ':date_recherche' => $recherche->getDateRecherche(),
            ':api_id'         => $recherche->getApiId()
        ]);

        // Vérification du succès de l'insertion pour hydrater l'ID de l'objet
        if ($result) {
            $recherche->setIdRecherche((int)$this->db->lastInsertId());
        }

        return $result;
    }

    /**
     * @brief Récupère une recherche par son ID
     * @details Sélectionne une ligne dans la table RECHERCHE via son identifiant unique et retourne une instance de l'objet ou null.
     * @throws PDOException En cas d'erreur lors de la requête SQL.
     */
    public function getById(int $id): ?Recherche {
        $sql = "SELECT * FROM RECHERCHE WHERE id_recherche = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':id' => $id]);

        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        // Si une ligne correspondante est trouvée, on instancie l'objet
        if ($row) {
            return new Recherche(
                $row['id_utilisateur'],
                $row['image'],
                $row['api_id'],
                $row['date_recherche'],
                $row['id_recherche']
            );
        }
        return null;
    }

    /**
     * @brief Liste les recherches d'un utilisateur
     * @details Récupère toutes les entrées de la table RECHERCHE associées à un ID utilisateur, triées par date décroissante.
     * @throws PDOException En cas d'erreur lors de la requête SQL.
     */
    public function findAllByUtilisateur(int $idUtilisateur): array {
        $sql = "SELECT * FROM RECHERCHE WHERE id_utilisateur = :id_u ORDER BY date_recherche DESC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':id_u' => $idUtilisateur]);

        $recherches = [];

        // Boucle sur chaque ligne de résultat pour remplir le tableau d'objets
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $recherches[] = new Recherche(
                $row['id_utilisateur'],
                $row['image'],
                $row['api_id'],
                $row['date_recherche'],
                $row['id_recherche']
            );
        }
        return $recherches;
    }

    /**
     * @brief Supprime une recherche
     * @details Efface l'entrée correspondante à l'ID fourni dans la table RECHERCHE.
     * @throws PDOException En cas d'erreur lors de l'exécution de la suppression.
     */
    public function delete(int $idRecherche): bool {
        $sql = "DELETE FROM RECHERCHE WHERE id_recherche = :id";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([':id' => $idRecherche]);
    }
}
?>