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

    public function findByEmail(string $email): ?Utilisateur {
        $sql = "SELECT * FROM UTILISATEUR WHERE email = :email";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':email' => $email]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($row) return $this->hydrate($row);
        return null;
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