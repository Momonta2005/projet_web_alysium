<?php
// Fichier : export_pdf.php
// Export PDF des solutions ou évaluations avec tri actuel

require_once 'TCPDF/tcpdf.php';
require_once 'Controller/SolutionController.php';
require_once 'Controller/EvaluationController.php';

$type = $_GET['type'] ?? ''; // 'solutions' ou 'evaluations'
$sort = $_GET['sort'] ?? '';

if ($type !== 'solutions' && $type !== 'evaluations') {
    die('Type invalide.');
}

// Création du PDF
$pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
$pdf->SetCreator('Administration Signalement');
$pdf->SetAuthor('Admin');
$pdf->SetTitle($type === 'solutions' ? 'Liste des Solutions' : 'Liste des Évaluations');
$pdf->SetMargins(10, 15, 10);
$pdf->SetAutoPageBreak(TRUE, 15);
$pdf->setHeaderFont(Array('helvetica', '', 12));
$pdf->setFooterFont(Array('helvetica', '', 10));
$pdf->SetHeaderData('', 0, 'Administration - Export ' . ucfirst($type), 'Date: ' . date('d/m/Y H:i'));

$pdf->AddPage();
$pdf->SetFont('helvetica', 'B', 14);
$pdf->Cell(0, 10, $type === 'solutions' ? 'TOUTES LES SOLUTIONS PROPOSÉES' : 'TOUTES LES ÉVALUATIONS', 0, 1, 'C');
$pdf->Ln(5);
$pdf->SetFont('helvetica', '', 10);

if ($type === 'solutions') {
    $controller = new SolutionController();
    // On récupère TOUTES les solutions sans limite (ignore pagination)
    $solutions = $controller->findAllSolutionsForAdmin($sort, 1, 9999); // 9999 = tout

    if (empty($solutions)) {
        $pdf->Cell(0, 10, 'Aucune solution trouvée.', 0, 1, 'C');
    } else {
        // En-tête tableau
        $pdf->SetFont('helvetica', 'B', 10);
        $pdf->SetFillColor(230, 230, 230);
        $pdf->Cell(15, 8, 'ID', 1, 0, 'C', true);
        $pdf->Cell(50, 8, 'Signalement', 1, 0, 'C', true);
        $pdf->Cell(70, 8, 'Description', 1, 0, 'C', true);
        $pdf->Cell(20, 8, 'Score', 1, 0, 'C', true);
        $pdf->Cell(20, 8, 'Évals', 1, 0, 'C', true);
        $pdf->Cell(25, 8, 'Date', 1, 1, 'C', true);

        $pdf->SetFont('helvetica', '', 9);
        foreach ($solutions as $s) {
            $pdf->Cell(15, 8, $s['solution_id'], 1);
            $pdf->Cell(50, 8, '#' . $s['signalementId'] . ' ' . substr($s['signalement_titre'], 0, 25), 1);
            $description = substr(strip_tags($s['solution_description']), 0, 80) . '...';
            $pdf->Cell(70, 8, $description, 1);
            $pdf->Cell(20, 8, number_format($s['average_stars_score'], 1), 1, 0, 'C');
            $pdf->Cell(20, 8, $s['evaluation_count'], 1, 0, 'C');
            $pdf->Cell(25, 8, date('d/m/Y', strtotime($s['date_proposition'])), 1, 1, 'C');
        }
    }
}
else { // evaluations
    $controller = new EvaluationController();
    $evaluations = $controller->findAllEvaluations($sort, 1, 9999);

    if (empty($evaluations)) {
        $pdf->Cell(0, 10, 'Aucune évaluation trouvée.', 0, 1, 'C');
    } else {
        $pdf->SetFont('helvetica', 'B', 10);
        $pdf->SetFillColor(230, 230, 230);
        $pdf->Cell(15, 8, 'ID', 1, 0, 'C', true);
        $pdf->Cell(25, 8, 'Note', 1, 0, 'C', true);
        $pdf->Cell(80, 8, 'Commentaire', 1, 0, 'C', true);
        $pdf->Cell(40, 8, 'Solution / Signalement', 1, 0, 'C', true);
        $pdf->Cell(25, 8, 'Date', 1, 1, 'C', true);

        $pdf->SetFont('helvetica', '', 9);
        foreach ($evaluations as $e) {
            $pdf->Cell(15, 8, $e['id'], 1);
            $pdf->Cell(25, 8, $e['stars'], 1, 0, 'C');
            $comment = substr($e['comment'], 0, 50) . (strlen($e['comment']) > 50 ? '...' : '');
            $pdf->Cell(80, 8, $comment, 1);
            $pdf->Cell(40, 8, substr($e['solution_description'], 0, 30) . '...', 1);
            $pdf->Cell(25, 8, date('d/m/Y', strtotime($e['date_evaluation'])), 1, 1, 'C');
        }
    }
}

// Sortie du PDF
$pdf->Output('export_' . $type . '_' . date('Y-m-d') . '.pdf', 'D'); // D = téléchargement forcé
exit;