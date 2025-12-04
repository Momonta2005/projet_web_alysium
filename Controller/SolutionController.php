<?php
// Fichier : Controller/SolutionController.php

require_once __DIR__ . '/../Model/config.php';

class SolutionController {
    private PDO $db;

    public function __construct() {
        $this->db = Config::getConnexion();
    }

    /**
     * Ajoute une nouvelle solution à la base de données.
     */
    public function addSolution(int $signalementId, string $description): bool {
        if (empty(trim($description))) {
            return false;
        }
        $sql = "INSERT INTO Solution (signalementId, description) VALUES (:sid, :desc)";
        try {
            $query = $this->db->prepare($sql);
            $query->bindValue('sid', $signalementId, PDO::PARAM_INT);
            $query->bindValue('desc', $description, PDO::PARAM_STR);
            return $query->execute();
        } catch (\Exception $e) {
            error_log("Erreur ajout solution: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Modifie la description d'une solution existante.
     */
    public function updateSolution(int $solutionId, string $newDescription): bool {
        if (empty(trim($newDescription))) {
            return false;
        }
        $sql = "UPDATE Solution SET description = :desc WHERE id = :id";
        try {
            $query = $this->db->prepare($sql);
            $query->bindValue('id', $solutionId, PDO::PARAM_INT);
            $query->bindValue('desc', $newDescription, PDO::PARAM_STR);
            return $query->execute();
        } catch (\Exception $e) {
            error_log("Erreur modification solution: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Supprime une solution par son ID.
     */
    public function deleteSolution(int $solutionId): bool {
        $sql = "DELETE FROM Solution WHERE id = :id";
        try {
            $query = $this->db->prepare($sql);
            $query->bindValue('id', $solutionId, PDO::PARAM_INT);
            return $query->execute();
        } catch (\Exception $e) {
            error_log("Erreur suppression solution: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Compte le nombre total de solutions.
     */
    public function countSolutions(): int {
        $sql = "SELECT COUNT(*) FROM Solution";
        try {
            return (int)$this->db->query($sql)->fetchColumn();
        } catch (\Exception $e) {
            error_log("Erreur countSolutions: " . $e->getMessage());
            return 0;
        }
    }

    /**
     * Récupère TOUTES les solutions du système, avec le titre du signalement
     * associé et classées par la moyenne des évaluations. Avec pagination.
     */
    public function findAllSolutionsForAdmin(string $sort = '', int $page = 1, int $limit = 4): array {
        $baseSql = "
            SELECT 
                s.id AS solution_id, 
                s.description AS solution_description,
                s.date_proposition,
                s.signalementId,
                t.titre AS signalement_titre,
                IFNULL(AVG(LENGTH(e.stars) / LENGTH('⭐')), 0) AS average_stars_score,
                COUNT(e.id) AS evaluation_count
            FROM Solution s
            INNER JOIN Signalement t ON s.signalementId = t.id
            LEFT JOIN Evaluation e ON s.id = e.solutionId
            GROUP BY s.id, s.description, s.date_proposition, s.signalementId, t.titre
            ORDER BY 
        ";

        switch ($sort) {
            case 'date_asc':
                $orderBy = "s.date_proposition ASC";
                break;
            case 'date_desc':
                $orderBy = "s.date_proposition DESC";
                break;
            case 'eval_count_asc':
                $orderBy = "evaluation_count ASC, average_stars_score DESC";
                break;
            case 'eval_count_desc':
                $orderBy = "evaluation_count DESC, average_stars_score DESC";
                break;
            default: // score_desc ou rien
                $orderBy = "average_stars_score DESC, s.date_proposition DESC";
                break;
        }

        $sql = $baseSql . $orderBy . " LIMIT :limit OFFSET :offset";

        try {
            $query = $this->db->prepare($sql);
            $query->bindValue(':limit', $limit, PDO::PARAM_INT);
            $query->bindValue(':offset', ($page - 1) * $limit, PDO::PARAM_INT);
            $query->execute();
            return $query->fetchAll(PDO::FETCH_ASSOC);
        } catch (\Exception $e) {
            error_log("Erreur findAllSolutionsForAdmin : " . $e->getMessage());
            return [];
        }
    }
}