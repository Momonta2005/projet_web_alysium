<?php
// export_signalement_pdf.php – Description TRÈS grande et très lisible

ob_clean();
require_once 'TCPDF/tcpdf.php';
require_once 'frontoffice_index.php';

$id = (int)($_GET['id'] ?? 0);
if ($id <= 0 || !isset($viewModel) || $viewModel['signalement']->getId() != $id) {
    die('Signalement non trouvé ou ID invalide');
}

$signalement = $viewModel['signalement'];
$solutions   = $viewModel['solutions'];

// ==================== CLASSE PDF ====================
class MYPDF extends TCPDF {
    public $id_signalement = 0;
    public $titre_signalement = '';

    public function Header() {
        $this->SetFillColor(245, 245, 245);
        $this->Rect(0, 0, $this->getPageWidth(), 30, 'F');

        $this->SetFont('helvetica', 'B', 16);
        $this->SetTextColor(0, 102, 187);
        $this->SetY(8);
        $this->Cell(0, 10, 'Signalement #' . $this->id_signalement, 0, 1, 'L');

        $this->SetFont('helvetica', '', 11);
        $this->SetTextColor(80, 80, 80);
        $this->Cell(0, 5, 'Titre : ' . $this->titre_signalement, 0, 1, 'L');
        $this->Cell(0, 5, 'Exporté le ' . date('d/m/Y à H\hi'), 0, 0, 'R');
    }

    public function Footer() {
        $this->SetY(-15);
        $this->SetFont('helvetica', 'I', 9);
        $this->SetTextColor(128, 128, 128);
        $this->Cell(0, 10, 'Page ' . $this->getAliasNumPage() . '/' . $this->getAliasNbPages(), 0, 0, 'C');
    }
}

$pdf = new MYPDF();
$pdf->id_signalement    = $id;
$pdf->titre_signalement = $signalement->getTitre();

$pdf->SetMargins(15, 35, 15);
$pdf->SetAutoPageBreak(true, 25);
$pdf->AddPage();

// Titre principal
$pdf->SetFont('helvetica', 'B', 22);
$pdf->SetTextColor(0, 102, 187);
$pdf->Cell(0, 15, 'SOLUTIONS & ÉVALUATIONS', 0, 1, 'C');
$pdf->Ln(10);

$pdf->SetFont('helvetica', '', 12);
$pdf->SetTextColor(70, 70, 70);
$pdf->MultiCell(0, 6, "Signalement : « {$signalement->getTitre()} »", 0, 'C');
$pdf->Ln(20);

// ==================== SOLUTIONS ====================
if (empty($solutions)) {
    $pdf->SetFont('helvetica', 'I', 14);
    $pdf->SetTextColor(150, 150, 150);
    $pdf->Cell(0, 10, 'Aucune solution proposée pour le moment.', 0, 1, 'C');
} else {
    foreach ($solutions as $index => $s) {
        $num = $index + 1;

        // === Pastille numéro + titre ===
        $pdf->SetFillColor(0, 102, 187);
        $pdf->SetTextColor(255, 255, 255);
        $pdf->SetFont('helvetica', 'B', 15);
        $pdf->Cell(14, 11, " $num ", 0, 0, 'C', true);

        $pdf->SetTextColor(0, 102, 187);
        $pdf->SetFont('helvetica', 'B', 15);
        $pdf->Cell(0, 11, " Solution proposée", 0, 1);
        $pdf->Ln(8);

        // === GRAND BLOC DESCRIPTION (c'est ça que tu voulais !) ===
        $pdf->SetFillColor(248, 252, 255);     // Fond bleu très clair
        $pdf->SetDrawColor(180, 210, 255);     // Bordure douce
        $pdf->SetLineWidth(0.8);

        // Hauteur minimale du cadre = 50 mm (≈ 5 cm), il s'agrandira si besoin
        $startY = $pdf->GetY();
        $pdf->Rect(15, $startY, $pdf->getPageWidth() - 30, 50, 'DF');

        $pdf->SetY($startY + 6);
        $pdf->SetX(20);

        $pdf->SetTextColor(0, 0, 0);
        $pdf->SetFont('helvetica', '', 12);           // Police un peu plus grande
        $pdf->setCellHeightRatio(1.6);                // Interligne très aéré

        $description = html_entity_decode(strip_tags($s['description']));
        $pdf->MultiCell(
            $pdf->getPageWidth() - 40,   // largeur utile
            9,                           // hauteur de ligne
            $description,
            0,
            'J'                          // Justifié = très pro
        );

        // On remet l'interligne normal pour la suite
        $pdf->setCellHeightRatio(1.2);

        // On descend après le cadre (au cas où le texte dépasse les 50 mm)
        $pdf->SetY($startY + 50 + max(0, $pdf->GetY() - ($startY + 50)) + 10);
        $pdf->Ln(12);

        // === Évaluations ===
        if (!empty($s['evaluations'])) {
            $pdf->SetFillColor(230, 240, 255);
            $pdf->SetFont('helvetica', 'B', 12);
            $pdf->Cell(0, 9, ' Évaluations (' . count($s['evaluations']) . ')', 0, 1, 'L', true);
            $pdf->Ln(3);

            foreach ($s['evaluations'] as $e) {
                $stars = str_repeat('★ ', $e['stars']) . str_repeat('☆ ', 5 - $e['stars']);

                $pdf->SetFillColor(255, 255, 255);
                $pdf->SetDrawColor(200, 200, 200);

                $pdf->SetFont('helvetica', 'B', 12);
                $pdf->SetTextColor(255, 153, 0);
                $pdf->Cell(30, 10, $stars, 'LTB', 0, 'C', true);

                $pdf->SetTextColor(60, 60, 60);
                $pdf->SetFont('helvetica', '', 10);
                $texte = html_entity_decode($e['comment']) . "\n— " . $e['author'];
                $pdf->MultiCell(0, 10, $texte, 'RTB', 'L', true);
                $pdf->Ln(5);
            }
        } else {
            $pdf->SetFont('helvetica', 'I', 11);
            $pdf->SetTextColor(150, 150, 150);
            $pdf->Cell(0, 8, 'Aucune évaluation pour cette solution', 0, 1);
        }

        $pdf->Ln(20); // Grand espace entre chaque solution
    }
}

$pdf->Output('Solutions_Signalement_' . $id . '.pdf', 'D');
exit;