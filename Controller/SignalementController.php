<?php
// Fichier : Controller/SignalementController.php

require_once __DIR__ . '/../Model/config.php';
// !!! LES LIGNES VERS Signalement.php, Solution.php et Evaluation.php ONT ÉTÉ SUPPRIMÉES !!!

class SignalementController {
    private PDO $db;

    public function __construct() {
        $this->db = Config::getConnexion();
    }

    // =========================================================
    // CRUD pour Signalement (avec requêtes SQL ici)
    // =========================================================
    
    /**
     * Lit un Signalement par ID.
     */
    public function findSignalementById(int $id): ?Signalement {
        $sql = "SELECT * FROM Signalement WHERE id = :id";
        $query = $this->db->prepare($sql);
        $query->execute(['id' => $id]);
        $data = $query->fetch();

        if (!$data) {
            return null;
        }
        return new Signalement( // La classe Signalement est maintenant connue via index.php
            $data['id'],
            $data['titre'],
            $data['description']
        );
    }

    /**
     * Récupère une liste d'autres signalements (pour le menu déroulant de la vue).
     */
    public function findOtherSignalements(): array {
        $sql = "SELECT id, titre FROM Signalement LIMIT 5";
        $query = $this->db->query($sql);
        return $query->fetchAll();
    }
    
    /**
     * Récupère toutes les solutions pour un signalement donné avec leurs évaluations.
     */
    public function findSolutionsWithEvaluations(int $signalementId): array {
        $sql = "SELECT 
                    S.id AS solution_id, 
                    S.description AS solution_desc, 
                    E.stars, 
                    E.comment, 
                    E.author 
                FROM Solution S
                LEFT JOIN Evaluation E ON S.id = E.solutionId
                WHERE S.signalementId = :sid 
                ORDER BY S.id, E.id";
        
        $query = $this->db->prepare($sql);
        $query->execute(['sid' => $signalementId]);
        $results = $query->fetchAll();

        $solutions = [];
        foreach ($results as $row) {
            $s_id = $row['solution_id'];
            if (!isset($solutions[$s_id])) {
                $solutions[$s_id] = [
                    'id' => $s_id,
                    'description' => $row['solution_desc'],
                    'evaluations' => []
                ];
            }
            if ($row['stars'] !== null) { 
                $solutions[$s_id]['evaluations'][] = [
                    'stars' => $row['stars'],
                    'comment' => $row['comment'],
                    'author' => $row['author']
                ];
            }
        }
        return array_values($solutions);
    }

    /**
     * Prépare toutes les données nécessaires pour l'affichage de la page du signalement.
     */
    public function getViewModel(int $signalementId): array {
        $currentSignalement = $this->findSignalementById($signalementId);
        
        if (!$currentSignalement) {
             throw new \Exception("Signalement #{$signalementId} non trouvé.");
        }
        
        return [
            'signalement' => $currentSignalement,
            'solutions' => $this->findSolutionsWithEvaluations($signalementId),
            'other_reports' => $this->findOtherSignalements()
        ];
    }
}