<?php
class UtilisateurDao {
    private ?PDO $pdo;

    public function __construct(?PDO $pdo = null) {
        $this->pdo = $pdo;
    }

    public function getPdo(): ?PDO { return $this->pdo; }
    public function setPdo(?PDO $pdo): void { $this->pdo = $pdo; }

    /**
     * @brief Crée un nouvel utilisateur (V3 Cleaned)
     * @details Version nettoyée : plus de colonnes 'sexe'/'pays'.
     */
    public function add(Utilisateur $utilisateur): bool {
        $sql = "INSERT INTO UTILISATEUR (nom, prenom, email, mot_de_passe_hash, role, nom_connexion, date_inscription) 
                VALUES (:nom, :prenom, :email, :mdp, :role, :login, NOW())";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([
            ':nom' => $utilisateur->getNom(),
            ':prenom' => $utilisateur->getPrenom(),
            ':email' => $utilisateur->getEmail(),
            ':mdp' => $utilisateur->getMotDePasseHash(),
            ':role' => 'user', 
            ':login' => $utilisateur->getNomConnexion()
        ]);
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