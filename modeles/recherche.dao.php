<?php

// EN PARTANT DU DIAGRAMME UML SUR LE DRIVE C4
// PAS LA VERSION FINALE DU COUP

require_once 'Recherche.class.php'; 

class RechercheDAO {
    private PDO $db;

    public function __construct(PDO $db) {
        $this->db = $db;
    }

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

        if ($result) {
            $recherche->setIdRecherche((int)$this->db->lastInsertId());
        }

        return $result;
    }

    public function getById(int $id): ?Recherche {
        $sql = "SELECT * FROM RECHERCHE WHERE id_recherche = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':id' => $id]);
        
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        
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

    public function findAllByUtilisateur(int $idUtilisateur): array {
        $sql = "SELECT * FROM RECHERCHE WHERE id_utilisateur = :id_u ORDER BY date_recherche DESC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':id_u' => $idUtilisateur]);
        
        $recherches = [];
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

    public function delete(int $idRecherche): bool {
        $sql = "DELETE FROM RECHERCHE WHERE id_recherche = :id";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([':id' => $idRecherche]);
    }
}
?>