<?php
/**
 * @file    recherche.dao.php
 * @author  Team SnapFit
 * @brief   DAO pour l'historique des recherches.
 * @details Gère le CRUD de la table RECHERCHE et le nettoyage automatique.
 * @version 1.0
 * @date    2025-12-23
 */

require_once 'include.php';

class RechercheDAO {
    private ?PDO $pdo;

    /**
     * @brief Constructeur de la classe
     * @details Initialise le DAO avec une instance de connexion à la base de données.
     * @throws Aucun
     */
    public function __construct(PDO $pdo) {
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
     * @brief Ajoute une recherche en base de données
     * @details Prépare et exécute une requête INSERT. Si l'insertion réussit, l'ID de l'objet passé en paramètre est mis à jour.
     * @throws PDOException En cas d'erreur lors de l'exécution de la requête SQL.
     */
    public function add(Recherche $recherche): bool {
        $sql = "INSERT INTO RECHERCHE (id_utilisateur, image_scan, date_recherche) 
                VALUES (:id_utilisateur, :image, :date_recherche)";

        $stmt = $this->pdo->prepare($sql);

        $result = $stmt->execute([
            ':id_utilisateur' => $recherche->getIdUtilisateur(),
            ':image'          => $recherche->getImage(),
            ':date_recherche' => $recherche->getDateRecherche()
        ]);

        // Vérification du succès de l'insertion pour hydrater l'ID de l'objet
        if ($result) {
            $recherche->setIdRecherche((int)$this->pdo->lastInsertId());
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
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':id' => $id]);

        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        // Si une ligne correspondante est trouvée, on instancie l'objet
        // Si une ligne correspondante est trouvée, on instancie l'objet
        if ($row) {
            return new Recherche(
                $row['id_utilisateur'],
                $row['image_scan'],
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
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':id_u' => $idUtilisateur]);

        $recherches = [];

        // Boucle sur chaque ligne de résultat pour remplir le tableau d'objets
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $recherches[] = $this->hydrate($row);
        }
        return $recherches;
    }

    /**
     * @brief Récupère les recherches récentes d'un utilisateur
     * @details Utile pour afficher un historique rapide ("Vos dernières recherches")
     * @param int $userId ID de l'utilisateur
     * @param int $limit Nombre de résultats à retourner (par défaut 10)
     * @return array Liste d'objets Recherche
     */
    public function findRecent(int $userId, int $limit = 10): array {
        $sql = "SELECT * FROM RECHERCHE 
                WHERE id_utilisateur = :id_u 
                ORDER BY date_recherche DESC 
                LIMIT :limit";
        
        $stmt = $this->pdo->prepare($sql);
        // BindValue est nécessaire pour LIMIT car execute() traite parfois les nombres comme des chaînes
        $stmt->bindValue(':id_u', $userId, PDO::PARAM_INT);
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();

        $recherches = [];
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $recherches[] = $this->hydrate($row);
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
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([':id' => $idRecherche]);
    }

    /**
     * @brief Supprime les anciennes recherches
     * @details Supprime toutes les recherches plus vieilles qu'un certain nombre de jours.
     * @param int $days Nombre de jours de rétention (par défaut 30)
     * @return bool True si la requête a réussi
     */
    public function deleteOldSearches(int $days = 30): bool {
        $sql = "DELETE FROM RECHERCHE WHERE date_recherche < DATE_SUB(NOW(), INTERVAL :days DAY)";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([':days' => $days]);
    }

    /**
     * @brief   Hydrate un objet Recherche à partir d'un tableau associatif.
     * @param   array $row Ligne brute de la BDD.
     * @return  Recherche Objet hydraté.
     */
    private function hydrate(array $row): Recherche {
        return new Recherche(
            isset($row['id_utilisateur']) ? (int)$row['id_utilisateur'] : null,
            $row['image_scan'] ?? null,
            $row['date_recherche'] ?? null,
            isset($row['id_recherche']) ? (int)$row['id_recherche'] : null,
        );
    }
}
?>