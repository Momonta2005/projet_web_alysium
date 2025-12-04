<?php
// Fichier : backoffice_index.php
// Routeur principal du Backoffice (Administration)

require_once 'Controller/SolutionController.php';
require_once 'Controller/EvaluationController.php';

$solutionController = new SolutionController();
$evaluationController = new EvaluationController();

// Récupération des paramètres
$action = $_REQUEST['action'] ?? 'list_solutions';
$id     = (int)($_REQUEST['id'] ?? 0);
$sort   = $_GET['sort'] ?? '';  // Paramètre de tri (ex: date_asc, eval_count_desc, etc.)
$page   = (int)($_GET['page'] ?? 1);  // Paramètre de page (défaut: 1)
$message = '';

// ===================================================================
// 1. TRAITEMENT DES ACTIONS POST (Suppression)
// ===================================================================
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // Suppression d'une SOLUTION
    if ($action === 'delete_solution' && $id > 0) {
        $success = $solutionController->deleteSolution($id);
        $message = $success
            ? "Solution #$id supprimée avec succès."
            : "Erreur lors de la suppression de la solution #$id.";
        
        // Redirection pour éviter re-soumission, en conservant sort et page
        header('Location: backoffice_index.php?action=list_solutions&sort=' . urlencode($sort) . '&page=' . $page . '&message=' . urlencode($message));
        exit;
    }

    // Suppression d'une ÉVALUATION
    if ($action === 'delete_evaluation' && $id > 0) {
        $success = $evaluationController->deleteEvaluation($id);
        $message = $success
            ? "Évaluation #$id supprimée avec succès."
            : "Erreur lors de la suppression de l'évaluation #$id.";

        header('Location: backoffice_index.php?action=list_evaluations&sort=' . urlencode($sort) . '&page=' . $page . '&message=' . urlencode($message));
        exit;
    }
}

// ===================================================================
// 2. GESTION DES MESSAGES (affichage après redirection)
// ===================================================================
if (isset($_GET['message'])) {
    $message = htmlspecialchars($_GET['message']);
}

// ===================================================================
// 3. ROUTING & AFFICHAGE DES VUES
// ===================================================================
switch ($action) {

    case 'list_solutions':
    default:
        $solutions = $solutionController->findAllSolutionsForAdmin($sort, $page, 4);
        $totalSolutions = $solutionController->countSolutions();
        $totalPages = ceil($totalSolutions / 4);
        require 'View/backoffice/list_solutions.php';
        break;

    case 'list_evaluations':
        $evaluations = $evaluationController->findAllEvaluations($sort, $page, 4);
        $totalEvaluations = $evaluationController->countEvaluations();
        $totalPages = ceil($totalEvaluations / 4);
        require 'View/backoffice/list_evaluations.php';
        break;

    // Tu pourras ajouter d'autres actions ici plus tard :
    // case 'edit_solution': ...
    // case 'stats': ...
}