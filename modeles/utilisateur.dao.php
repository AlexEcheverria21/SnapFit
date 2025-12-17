<?php
class UtilisateurDao {
    //Attributs privés
    private ?PDO $pdo;


    //Constructeur
    public function __construct(?PDO $pdo=null){
        $this->pdo = $pdo;
    }

    //Getters
    public function getPdo(): ?PDO {
        return $this->pdo;
    }

    //Setters
    public function setPdo(?PDO $pdo): void {
        $this->pdo = $pdo;
    }

    // Méthodes

    /**
     * @brief Crée un nouvel utilisateur
     * @details Insère un nouvel utilisateur dans la base de données.
     * @param Utilisateur $utilisateur Objet utilisateur à ajouter
     * @return bool True si l'ajout a réussi, false sinon
     * @throws PDOException Si la requête échoue.
     */
    public function add(Utilisateur $utilisateur): bool {

        // On insère le rôle 'user' par défaut et la date actuelle avec NOW()
        $sql = "INSERT INTO UTILISATEUR (nom, prenom, mot_de_passe_hash, role, date_inscription, email, nom_connexion, sexe, pays)
                VALUES (:nom, :prenom, :mdp, :role, NOW(), :email, :login, :sexe, :pays)";
        $pdoStatement = $this->pdo->prepare($sql);

        return $pdoStatement->execute(array(
            ':nom' => $utilisateur->getNom(),
            ':prenom' => $utilisateur->getPrenom(),
            ':mdp' => $utilisateur->getMotDePasseHash(),
            ':role' => $utilisateur->getRole() ?? 'user', // Rôle par défaut
            ':email' => $utilisateur->getEmail(),
            ':login' => $utilisateur->getNomConnexion(),
            ':sexe' => $utilisateur->getSexe(),
            ':pays' => $utilisateur->getPays()
        ));
    }

    /**
     * @brief Récupère un utilisateur par son ID
     * @details Sélectionne un utilisateur spécifique via son ID et hydrate l'objet.
     * @param int $id ID de l'utilisateur à rechercher
     * @return Utilisateur|null Instance d'Utilisateur ou null si non trouvé
     * @throws PDOException Si la requête échoue.
     */
    public function find(int $id): ?Utilisateur {
        $sql = "SELECT * FROM UTILISATEUR WHERE id_utilisateur = :id";
        $pdoStatement = $this->pdo->prepare($sql);
        $pdoStatement->execute([':id' => $id]);
        $row = $pdoStatement->fetch(PDO::FETCH_ASSOC);

        if ($row) {
            return $this->hydrate([$row]);
        }
        
        return null;
    }

    /**
     * @brief Récupère tous les utilisateurs
     * @details Retourne tous les utilisateurs présents en base de données, triés par date d'inscription décroissante
     * @return array Tableau d'objets Utilisateur
     * @throws PDOException En cas d'erreur lors de la requête SQL
     */
    public function findAll(): array {
        $sql = "SELECT * FROM UTILISATEUR ORDER BY date_inscription DESC"; 
        $pdoStatement = $this->pdo->prepare($sql);
        $pdoStatement->execute();
        $pdoStatement->setFetchMode(PDO::FETCH_ASSOC);
        $tableau = $pdoStatement->fetchAll();
        
        // On délègue la création des objets à hydrateMany
        return $this->hydrateMany($tableau);
    }

    /**
     * @brief Récupère un utilisateur par son email
     * @details Sélectionne un utilisateur spécifique via son email et hydrate l'objet.
     * @param string $email Email de l'utilisateur à rechercher
     * @return Utilisateur|null Instance d'Utilisateur ou null si non trouvé
     * @throws PDOException Si la requête échoue.
     */
    public function findByEmail(string $email): ?Utilisateur {

        $sql = "SELECT * FROM UTILISATEUR WHERE email = :email";
        $pdoStatement = $this->pdo->prepare($sql);
        $pdoStatement->execute(array(':email' => $email));
        $pdoStatement->setFetchMode(PDO::FETCH_ASSOC);
        $tableau = $pdoStatement->fetchAll();

        if (count($tableau) > 0) {
            return $this->hydrate($tableau);
        }

        return null;
    }

     /**
     * @brief Met à jour un utilisateur
     * @details Modifie les informations d'un utilisateur existant
     * @param Utilisateur $utilisateur Objet utilisateur avec les nouvelles valeurs
     * @return bool True si la mise à jour a réussi, false sinon
     * @throws PDOException En cas d'erreur lors de l'exécution de la requête
     */
    public function update(Utilisateur $utilisateur): bool {
        $sql = "UPDATE UTILISATEUR SET 
                nom = :nom,
                prenom = :prenom,
                mot_de_passe_hash = :mot_de_passe_hash,
                role = :role,
                email = :email,
                nom_connexion = :nom_connexion,
                sexe = :sexe,
                pays = :pays
                WHERE id_utilisateur = :id";

        $pdoStatement = $this->pdo->prepare($sql);

        return $pdoStatement->execute(array(
            ':nom' => $utilisateur->getNom(),
            ':prenom' => $utilisateur->getPrenom(),
            ':mot_de_passe_hash' => $utilisateur->getMotDePasseHash(),
            ':role' => $utilisateur->getRole(),
            ':email' => $utilisateur->getEmail(),
            ':nom_connexion' => $utilisateur->getNomConnexion(),
            ':sexe' => $utilisateur->getSexe(),
            ':pays' => $utilisateur->getPays(),
            ':id' => $utilisateur->getIdUtilisateur()
        ));

    }

    /**
     * @brief Supprime un utilisateur
     * @details Efface l'entrée correspondante à l'ID fourni dans la table UTILISATEUR
     * @param int $idUtilisateur ID de l'utilisateur à supprimer
     * @return bool True si la suppression a réussi, false sinon
     * @throws PDOException En cas d'erreur lors de l'exécution de la suppression
     */
    public function delete(int $idUtilisateur): bool {
        $sql = "DELETE FROM UTILISATEUR WHERE id_utilisateur = :id";
        $pdoStatement = $this->pdo->prepare($sql);
        return $pdoStatement->execute([':id' => $idUtilisateur]);
    }

    private function hydrate(array $row): Utilisateur {
        $u = new Utilisateur();
        $u->setIdUtilisateur($row['id_utilisateur']);
        $u->setNom($row['nom']);
        $u->setPrenom($row['prenom']);
        $u->setEmail($row['email']);
        $u->setMotDePasseHash($row['mot_de_passe_hash']);
        $u->setRole($row['role']);
        $u->setNomConnexion($row['nom_connexion']);
        return $u;
    }
}