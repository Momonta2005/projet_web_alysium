<?php

// Fichier : frontoffice_index.php (Point d'Entrée)

// Définit le chemin racine pour des includes robustes
define('ROOT_PATH', __DIR__ . '/');

// ===================================
// 1. INCLUSION DES MODELS (ENTITÉS & CONFIG)
// ===================================
require_once ROOT_PATH . 'Model/config.php';
require_once ROOT_PATH . 'Model/Signalement.php';
require_once ROOT_PATH . 'Model/Solution.php';
require_once ROOT_PATH . 'Model/Evaluation.php';


// ===================================
// 2. INCLUSION DES CONTROLLERS
// ===================================
require_once ROOT_PATH . 'Controller/SignalementController.php'; 
require_once ROOT_PATH . 'Controller/SolutionController.php';
require_once ROOT_PATH . 'Controller/EvaluationController.php';


// Définit l'ID du signalement actuel, récupéré via GET ou par défaut
$signalementId = (int)($_GET['id'] ?? 123); 

try {
    // Instanciation des contrôleurs
    $signalementC = new SignalementController();
    $solutionC = new SolutionController();
    $evaluationC = new EvaluationController();

    // ===================================
    // 1. Traitement des requêtes POST (AJOUT DE UPDATE ET DELETE)
    // ===================================
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $action = $_POST['action'] ?? '';
        $success = false;
        
        // L'ID du signalement actuel
        $currentId = (int)($_POST['report_id'] ?? $signalementId);

        if ($action === 'add_solution' && isset($_POST['solutionText'])) {
            $success = $solutionC->addSolution($currentId, $_POST['solutionText']);
            
        } elseif ($action === 'update_solution' && isset($_POST['solution_id'], $_POST['newSolutionText'])) {
            // NOUVELLE LOGIQUE DE MODIFICATION
            $success = $solutionC->updateSolution((int)$_POST['solution_id'], $_POST['newSolutionText']);

        } elseif ($action === 'delete_solution' && isset($_POST['solution_id'])) {
            // NOUVELLE LOGIQUE DE SUPPRESSION
            $success = $solutionC->deleteSolution((int)$_POST['solution_id']);

        } elseif ($action === 'add_evaluation' && isset($_POST['solution_id'], $_POST['evaluation_text'])) {
            $success = $evaluationC->addEvaluation((int)$_POST['solution_id'], $_POST['evaluation_text']);
        }

        // Pattern PRG : Redirection après POST pour éviter la resoumission
        if ($success) {
             header("Location: frontoffice_index.php?id=" . $currentId);
             exit; 
        } else {
             // Logique à implémenter pour les échecs (ex: message d'erreur en session)
        }
    }

    // ===================================
    // 2. Affichage (Requête GET)
    // ===================================
    
    // Le SignalementController prépare le modèle de vue
    $viewModel = $signalementC->getViewModel($signalementId);

    // Charger la Vue et lui passer les données
    require 'View/frontoffice/solution_evaluation.php';

} catch (\Exception $e) {
    // Gestion des erreurs critiques (ex: base de données non connectée)
    echo "<h1>Erreur critique</h1>";
    echo "<p>La page n'a pu être chargée : " . htmlspecialchars($e->getMessage()) . "</p>";
}