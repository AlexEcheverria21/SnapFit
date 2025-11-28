<?php
require_once "include.php";
class favoriDao{
    private ?PDO $pdo;  

    public function __construct(?PDO $pdo = null){
    $this->pdo = $pdo;
    }

    /**
    * Get the value of pdo
    */ 
    public function getPdo(): ?PDO
    {
        return $this->pdo;
    }

    /**
     * Set the value of pdo
     *
     */ 
    public function setPdo($pdo): void
    {
        $this->pdo = $pdo;

    }

    /**
    * @brief Récupère un favori par son ID
    * @details Sélectionne une ligne dans la table FAVORI via son identifiant unique
    * @param int $id ID du favori à rechercher
    * @return Favori|null Instance de Favori ou null si non trouvé
    * @throws PDOException En cas d'erreur lors de la requête SQL
    */
    public function find(int $id): ?Favori {
        $sql = "SELECT * FROM FAVORI WHERE id_favori = :id";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':id' => $id]);

        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        // Si une ligne correspondante est trouvée, on instancie l'objet
        if ($row) {
            return $this->hydrate($row);
        }
        return null;
    }
    /**
    * @brief Récupère tous les favoris
    * @details Retourne tous les favoris présents en base de données, triés par date décroissante
    * @return array Tableau d'objets Favori
    * @throws PDOException En cas d'erreur lors de la requête SQL
    */
     public function findAll(): array {
        $sql = "SELECT * FROM FAVORI ORDER BY date_fav DESC";
        $stmt = $this->pdo->query($sql);

        $favoris = [];

        // Boucle sur chaque ligne de résultat pour remplir le tableau d'objets
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $favoris[] = $this->hydrate($row);
        }
        return $favoris;
    }

    /**
     * @brief Liste les favoris d'un utilisateur spécifique
     * @details Utilise une jointure avec la table de liaison 'favoriser'
     */
    public function findAllByUtilisateur(int $idUtilisateur): array {
        $sql = "SELECT f.* FROM FAVORI f
                JOIN FAVORISER l ON f.id_favori = l.id_favori
                WHERE l.id_utilisateur = :id_u
                ORDER BY f.date_fav DESC"; 
        
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':id_u' => $idUtilisateur]);

        $favoris = [];
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $favoris[] = $this->hydrate($row);
        }
        return $favoris;
    }

    /**
     * @brief Ajoute un favori et le lie à un utilisateur
     * @param Favori $favori L'objet favori à créer
     * @param int $idUtilisateur L'ID de l'utilisateur qui ajoute ce favori
     * @return bool True si l'ajout complet a réussi
     */
    public function create(Favori $favori, int $idUtilisateur): bool {
        try {
            //démarre une transaction 
            $this->pdo->beginTransaction();

            //Insertion du Favori
            $sql = "INSERT INTO FAVORI (url, image, categorie, marque, date_fav) 
                    VALUES (:url, :image, :categorie, :marque, :date_fav)";
            
            $stmt = $this->pdo->prepare($sql);
            
            $result = $stmt->execute([
                ':url'       => $favori->getUrl(),
                ':image'     => $favori->getImage(),
                ':categorie' => $favori->getCategorie(),
                ':marque'    => $favori->getMarque(),
                ':date_fav'  => $favori->getDate_fav()
            ]);

            // Si ça rate, on annule tout
            if (!$result) {
                $this->pdo->rollBack();
                return false;
            }

            //récupère l'ID du favori qu'on vient de créer
            $idFavori = (int)$this->pdo->lastInsertId();
            $favori->setId_favori($idFavori); 

            //Création du lien dans la table de jointure
            $sqlLien = "INSERT INTO Ajouter (id_utilisateur, id_favori) VALUES (:id_u, :id_f)";
            $stmtLien = $this->pdo->prepare($sqlLien);
            
            $resultLien = $stmtLien->execute([
                ':id_u' => $idUtilisateur,
                ':id_f' => $idFavori
            ]);

            if (!$resultLien) {
                $this->pdo->rollBack();
                return false;
            }

            //On valide la transaction.
            $this->pdo->commit();
            return true;

        } catch (Exception $e) {
            //on annule tout
            if ($this->pdo->inTransaction()) {
                $this->pdo->rollBack();
            }
            return false;
        }
    }

    /**
    * @brief Met à jour un favori
    * @details Modifie les informations d'un favori existant
    * @param Favori $favori Objet favori avec les nouvelles valeurs
    * @return bool True si la mise à jour a réussi, false sinon
    * @throws PDOException En cas d'erreur lors de l'exécution de la requête
    */
    public function update(Favori $favori): bool {
        $sql = "UPDATE FAVORI 
                SET url = :url,
                    image = :image,
                    categorie = :categorie,
                    marque = :marque,
                    date_fav = :date_fav
                WHERE id_favori = :id";

        $stmt = $this->pdo->prepare($sql);

        return $stmt->execute([
            ':url'       => $favori->getUrl(),
            ':image'     => $favori->getImage(),
            ':categorie' => $favori->getCategorie(),
            ':marque'    => $favori->getMarque(),
            ':date_fav'  => $favori->getDate_fav(),
            ':id'        => $favori->getId_favori()
        ]);
    }
    /**
    * @brief Retire un favori pour un utilisateur spécifique
    * @details Supprime le lien dans la table FAVORISER, mais garde l'article en base
    */
    public function deleteFavori(int $idFavori, int $idUtilisateur): bool {
        //On supprime le lien dans la table de jointure
        $sql = "DELETE FROM FAVORISER 
                WHERE id_favori = :id_f AND id_utilisateur = :id_u";
        
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([
            ':id_f' => $idFavori,
            ':id_u' => $idUtilisateur
        ]);
    }

    /**
    * @brief Compte le nombre total de favoris
    * @details Utile pour afficher des statistiques
    * @return int Nombre total de favoris
    * @throws PDOException En cas d'erreur lors de la requête SQL
    */
    public function countFavoris(): int {
        $sql = "SELECT COUNT(*) FROM FAVORI";
        $stmt = $this->pdo->query($sql);
        return (int)$stmt->fetchColumn();
    }

    /**
    * @brief Vérifie si un produit est en favori pour un utilisateur
    * @details Permet de savoir si un produit est déjà dans les favoris d'un utilisateur
    * @param int $idUtilisateur ID de l'utilisateur
    * @param string $url URL du produit à vérifier
    * @return bool True si le produit est en favori, false sinon
    * @throws PDOException En cas d'erreur lors de la requête SQL
    */
    public function isInFavorites(int $idUtilisateur, string $url): bool {
        $sql = "SELECT COUNT(*) FROM FAVORI WHERE id_utilisateur = :id_utilisateur AND url = :url";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            ':id_utilisateur' => $idUtilisateur,
            ':url' => $url
            ]);
        return $stmt->fetchColumn() > 0;
    }

    /**
    * @brief Transforme un tableau associatif (BDD) en objet Favori
    */
    private function hydrate(array $row): Favori {
        return new Favori(
            isset($row['id_favori']) ? (int)$row['id_favori'] : null,
            $row['url'] ?? null,
            $row['image'] ?? null,
            $row['categorie'] ?? null,
            $row['marque'] ?? null,
            $row['date_fav'] ?? null
        );
    }
}