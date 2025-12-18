<?php
// Fichier : Controller/EvaluationController.php

require_once __DIR__ . '/../Model/config.php';
require_once __DIR__ . '/ModerationController.php';

class EvaluationController {
    private PDO $db;

    public function __construct() {
        $this->db = Config::getConnexion();
    }
    
    /**
     * Ajoute une évaluation (note et commentaire) à une solution.
     * @param int $solutionId L'ID de la solution évaluée
     * @param int $rating La note (1 à 5)
     * @param string $comment Le commentaire (optionnel)
     * @return bool True si l'évaluation a été ajoutée avec succès
     */
    public function addEvaluation(int $solutionId, int $rating, string $comment = ''): bool {
        // Validation de la note
        if ($rating < 1 || $rating > 5) {
            error_log("Note invalide: $rating (doit être entre 1 et 5)");
            return false;
        }
        
        // Conversion de la note numérique en étoiles pour le stockage
        $stars = str_repeat('⭐', $rating);
        
        // Nettoyage et validation du commentaire (obligatoire)
        $comment = trim($comment);
        if (empty($comment) || strlen($comment) < 10) {
            error_log("Commentaire invalide: doit contenir au moins 10 caractères");
            return false;
        }
        
        $author = 'Utilisateur Logué'; 

        // Modération : si commentaire interdit, on flag et on ne publie pas
        $moderation = new ModerationController();
        $check = ModerationController::moderateText($comment);
        if ($check['flagged']) {
            $moderation->flagContent(
                'evaluation',
                $comment,
                $check['reason'],
                ['solutionId' => $solutionId]
            );
            return true; // Traité mais non publié
        }

        // Requête SQL d'insertion
        $sql = "INSERT INTO Evaluation (solutionId, stars, comment, author) VALUES (:sol_id, :stars, :comment, :author)";
        try {
            $query = $this->db->prepare($sql);
            $query->bindValue('sol_id', $solutionId, PDO::PARAM_INT);
            $query->bindValue('stars', $stars, PDO::PARAM_STR);
            $query->bindValue('comment', $comment, PDO::PARAM_STR);
            $query->bindValue('author', $author, PDO::PARAM_STR);
            return $query->execute();
        } catch (\Exception $e) {
            error_log("Erreur ajout évaluation: " . $e->getMessage()); 
            return false;
        }
    }

    /**
     * Compte le nombre total d'évaluations.
     */
    public function countEvaluations(): int {
        $sql = "SELECT COUNT(*) FROM Evaluation";
        try {
            return (int)$this->db->query($sql)->fetchColumn();
        } catch (\Exception $e) {
            error_log("Erreur countEvaluations: " . $e->getMessage());
            return 0;
        }
    }

    public function findAllEvaluations(string $sort = 'date_desc', int $page = 1, int $limit = 4): array {
        $baseSql = "
            SELECT 
                e.id, 
                e.solutionId, 
                e.stars, 
                e.comment, 
                e.author,
                e.date_evaluation,
                s.description AS solution_description
            FROM Evaluation e
            INNER JOIN Solution s ON e.solutionId = s.id
            ORDER BY 
        ";

        $orderBy = ($sort === 'date_asc') ? "e.date_evaluation ASC" : "e.date_evaluation DESC";

        $sql = $baseSql . $orderBy . " LIMIT :limit OFFSET :offset";

        try {
            $query = $this->db->prepare($sql);
            $query->bindValue(':limit', $limit, PDO::PARAM_INT);
            $query->bindValue(':offset', ($page - 1) * $limit, PDO::PARAM_INT);
            $query->execute();
            return $query->fetchAll(PDO::FETCH_ASSOC);
        } catch (\Exception $e) {
            error_log("Erreur findAllEvaluations : " . $e->getMessage());
            return [];
        }
    }

    public function deleteEvaluation(int $evaluationId): bool {
        $sql = "DELETE FROM Evaluation WHERE id = :id";
        try {
            $query = $this->db->prepare($sql);
            $query->bindValue('id', $evaluationId, PDO::PARAM_INT);
            return $query->execute();
        } catch (\Exception $e) {
            error_log("Erreur suppression évaluation: " . $e->getMessage());
            return false;
        }
    }
}