<?php
/**
 * @file    annuaire.dao.php
 * @author  Antigravity (Team SnapFit)
 * @brief   Définit une classe pour gérer la persistance des Domaines (Annuaire).
 *          Effectue les opérations sur la table DOMAINE.
 * @version 1.0
 * @date    28/01/2026
 */

require_once 'annuaire.class.php';

class AnnuaireDao {
    private ?PDO $pdo;

    public function __construct(?PDO $pdo = null) {
        $this->pdo = $pdo;
    }

    /**
     * @brief   Récupère tous les domaines de l'annuaire.
     */
    public function findAll(): array {
        $sql = "SELECT id_domaine as id, url_racine, nom, statut, description 
                FROM DOMAINE ORDER BY nom ASC";
        $stmt = $this->pdo->query($sql);
        return $this->hydrateMany($stmt->fetchAll(PDO::FETCH_ASSOC));
    }

    /**
     * @brief   Récupère les domaines par statut (eco ou scam).
     */
    public function findByStatut(string $statut): array {
        $sql = "SELECT id_domaine as id, url_racine, nom, statut
                FROM DOMAINE WHERE statut = :statut ORDER BY nom ASC";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':statut' => $statut]);
        return $this->hydrateMany($stmt->fetchAll(PDO::FETCH_ASSOC));
    }

    /**
     * @brief   Hydrate un objet Annuaire à partir d'un tableau associatif.
     */
    private function hydrate(array $ligne): Annuaire {
        return new Annuaire(
            $ligne['id'] ?? null,
            $ligne['url_racine'] ?? null,
            $ligne['nom'] ?? null,
            $ligne['statut'] ?? null,
        );
    }

    /**
     * @brief   Hydrate une liste d'objets Annuaire.
     */
    private function hydrateMany(array $lignes): array {
        $domaines = [];
        foreach ($lignes as $ligne) {
            $domaines[] = $this->hydrate($ligne);
        }
        return $domaines;
    }
}
