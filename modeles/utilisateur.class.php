<?php
/**
 * @file    utilisateur.class.php
 * @author  Alex Echeverria
 * @brief   Définit la classe Utilisateur.
 * @version 1.0
 * @date    17/12/2025
 */

require_once 'bd.class.php';

// Constantes de sécurité
const MAX_CONNEXIONS_ECHOUEES = 3;
const DELAI_ATTENTE_CONNEXION = 60 * 5; // 5 minutes en secondes

class Utilisateur {

    //Attributs privés
    private ?int $id_utilisateur = null;
    private ?string $nom = null;
    private ?string $prenom = null;
    private ?string $email; // Obligatoire
    private ?string $mot_de_passe_hash; // Obligatoire
    private string $role = 'user';
    private ?string $nom_connexion = null;
    private ?string $date_inscription = null;

    // Propriétés de Sécurité
    private int $tentativesEchouees = 0;
    private ?string $dateDernierEchecConnexion = null;
    private string $statutCompte = 'actif';

    //Contructeur
    public function __construct(
        string $email, 
        string $passwordHashOrClear,
        ?string $nom = null,
        ?string $prenom = null,
        ?string $nom_connexion = null
    ) {
        $this->email = $email;
        $this->mot_de_passe_hash = $passwordHashOrClear; // haché si inscription
        $this->nom = $nom;
        $this->prenom = $prenom;
        $this->nom_connexion = $nom_connexion;
    }

    // Méthode pour vérifier si l'email existe déjà dans la base de données
    public function emailExiste(): bool {
        $pdo = Bd::getInstance()->getConnexion();
        $req = $pdo->prepare('SELECT COUNT(*) FROM UTILISATEUR WHERE email = :email');
        $req->execute(['email' => $this->email]);
        return $req->fetchColumn() > 0;
    }

    // MÉTHODES PUBLIQUES
    
    // Méthode pour vérifier la robustesse du mot de passe
    public function estRobuste(string $password): bool {
        $regex = '/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[\W_]).{8,}$/';
        return preg_match($regex, $password) === 1;
    }

    /**
     * @brief Inscription sécurisée
     */
    public function inscription(string $passwordClair): void {
        if (!$this->estRobuste($passwordClair)) {
            throw new Exception("mdp_faible");
        }
        if ($this->emailExiste()) {
            throw new Exception("compte_existant");
        }

        $pdo = Bd::getInstance()->getConnexion();
        $this->mot_de_passe_hash = password_hash($passwordClair, PASSWORD_BCRYPT);
        
        // On génère un nom de connexion unique si pas fourni
        if (!$this->nom_connexion) {
            $this->nom_connexion = explode('@', $this->email)[0] . uniqid();
        }

        // Suppression de sexe et pays de l'INSERT
        $sql = "INSERT INTO UTILISATEUR (email, mot_de_passe_hash, role, nom_connexion, nom, prenom, date_inscription) 
                VALUES (:email, :mdp, :role, :login, :nom, :prenom, NOW())";
        
        $req = $pdo->prepare($sql);
        $req->execute([
            'email' => $this->email,
            'mdp' => $this->mot_de_passe_hash,
            'role' => $this->role,
            'login' => $this->nom_connexion,
            'nom'  => $this->nom ?? 'Anonyme',
            'prenom' => $this->prenom ?? 'User'
        ]);
        
        $this->id_utilisateur = $pdo->lastInsertId();
    }

    /**
     * @brief Authentification sécurisée
     */
    public function authentification(string $passwordClair): bool {
        $pdo = Bd::getInstance()->getConnexion();
        
        // Récupération des infos sécurité
        $req = $pdo->prepare(
            'SELECT id_utilisateur, mot_de_passe_hash, tentatives_echouees, date_dernier_echec_connexion, statut_compte, role, nom, prenom, nom_connexion 
             FROM UTILISATEUR WHERE email = :email'
        );
        $req->execute(['email' => $this->email]);
        $user = $req->fetch(PDO::FETCH_ASSOC);

        if (!$user) return false;

        // Hydratation interne
        $this->id_utilisateur = $user['id_utilisateur'];
        $this->mot_de_passe_hash = $user['mot_de_passe_hash']; // Hash stocké
        $this->tentativesEchouees = $user['tentatives_echouees'];
        $this->dateDernierEchecConnexion = $user['date_dernier_echec_connexion'];
        $this->statutCompte = $user['statut_compte'];
        $this->role = $user['role'];
        $this->nom = $user['nom'];
        $this->prenom = $user['prenom'];
        $this->nom_connexion = $user['nom_connexion'];

        // Vérif Compte Désactivé
        if ($this->statutCompte === 'desactive') {
            if (!$this->delaiAttenteEstEcoule()) {
                throw new Exception("compte_desactive");
            }
            $this->reactiverCompte();
        }

        // Vérif Password
        if (password_verify($passwordClair, $this->mot_de_passe_hash)) {
            if ($this->tentativesEchouees > 0) {
                $this->reinitialiserTentativesConnexions();
            }
            return true;
        } else {
            $this->gererEchecConnexion();
            return false;
        }
    }

    // MÉTHODES PRIVÉES
    // Méthode pour reinitialise les tentatives de connexion
    private function reinitialiserTentativesConnexions(): void {
        $this->tentativesEchouees = 0;
        $this->dateDernierEchecConnexion = null;
        $pdo = Bd::getInstance()->getConnexion();
        $req = $pdo->prepare('UPDATE UTILISATEUR SET tentatives_echouees = 0, date_dernier_echec_connexion = NULL WHERE id_utilisateur = :id');
        $req->execute(['id' => $this->id_utilisateur]);
    }

    // Méthode gère les échecs de connexion
    private function gererEchecConnexion(): void {
        $this->tentativesEchouees++;
        $pdo = Bd::getInstance()->getConnexion();
        
        if ($this->tentativesEchouees >= MAX_CONNEXIONS_ECHOUEES) {
            $req = $pdo->prepare(
                'UPDATE UTILISATEUR SET tentatives_echouees = :t, date_dernier_echec_connexion = NOW(), statut_compte = "desactive" WHERE id_utilisateur = :id'
            );
            $this->statutCompte = 'desactive';
        } else {
            $req = $pdo->prepare(
                'UPDATE UTILISATEUR SET tentatives_echouees = :t, date_dernier_echec_connexion = NOW() WHERE id_utilisateur = :id'
            );
        }
        $req->execute(['t' => $this->tentativesEchouees, 'id' => $this->id_utilisateur]);
    }

    // Méthode pour reactiver le compte après délai d'attente
    private function reactiverCompte(): void {
        $this->tentativesEchouees = 0;
        $this->dateDernierEchecConnexion = null;
        $this->statutCompte = 'actif';
        $pdo = Bd::getInstance()->getConnexion();
        $pdo->prepare('UPDATE UTILISATEUR SET tentatives_echouees = 0, date_dernier_echec_connexion = NULL, statut_compte = "actif" WHERE id_utilisateur = :id')->execute(['id' => $this->id_utilisateur]);
    }

    private function delaiAttenteEstEcoule(): bool {
        return $this->tempsRestantAvantReactivationCompte() === 0;
    }

    // Méthode pour obtenir le temps restant avant réactivation du compte
    public function tempsRestantAvantReactivationCompte(): int {
        if (!$this->dateDernierEchecConnexion) return 0;
        $dernierEchec = strtotime($this->dateDernierEchecConnexion);
        return max(0, DELAI_ATTENTE_CONNEXION - (time() - $dernierEchec));
    }

    // --- GETTERS & SETTERS (Cleaned) ---

    // ID
    public function getIdUtilisateur(): ?int { return $this->id_utilisateur; }
    public function setIdUtilisateur(?int $id): void { $this->id_utilisateur = $id; }

    // Nom
    public function getNom(): ?string { return $this->nom; }
    public function setNom(?string $nom): void { $this->nom = $nom; }

    // Prenom
    public function getPrenom(): ?string { return $this->prenom; }
    public function setPrenom(?string $prenom): void { $this->prenom = $prenom; }

    // Email
    public function getEmail(): ?string { return $this->email; }
    public function setEmail(string $email): void { $this->email = $email; }

    // Password
    public function getMotDePasseHash(): ?string { return $this->mot_de_passe_hash; }
    public function setMotDePasseHash(string $hash): void { $this->mot_de_passe_hash = $hash; }

    // Role
    public function getRole(): string { return $this->role; }
    public function setRole(string $role): void { $this->role = $role; }

    // Login
    public function getNomConnexion(): ?string { return $this->nom_connexion; }
    public function setNomConnexion(?string $nom_connexion): void { $this->nom_connexion = $nom_connexion; }

    // Date
    public function getDateInscription(): ?string { return $this->date_inscription; }
    public function setDateInscription(?string $date): void { $this->date_inscription = $date; }

}