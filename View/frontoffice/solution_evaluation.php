<?php
// Fichier : View/solution_evaluation.php

/**
 * Ce fichier est la Vue. Il ne doit contenir AUCUNE logique d'accès aux données (SQL)
 * ni de logique métier. Il utilise le $viewModel préparé par SignalementController.
 * * @var array $viewModel
 * @var Signalement $signalement
 * @var array $solutions
 */

// Extraction des variables du ViewModel pour plus de clarté
$signalement = $viewModel['signalement'];
$solutions = $viewModel['solutions'];
$otherReports = $viewModel['other_reports'];

// Fonction utilitaire pour la sécurité (prévention XSS)
function safe(string $text): string {
    // Utilisation de ENT_QUOTES pour les apostrophes et guillemets
    return htmlspecialchars($text, ENT_QUOTES, 'UTF-8');
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Signalement #<?php echo safe((string)$signalement->getId()); ?> | Module Solution & Évaluation</title>
    <style>
        /* Styles CSS inchangés pour le design de la page */
        body { font-family: Arial, sans-serif; margin: 0; padding: 20px; background-color: #f4f4f9; }
        header { background-color: #ffffff; padding: 20px; border-radius: 8px; margin-bottom: 20px; display: flex; box-shadow: 0 2px 4px rgba(0,0,0,0.1); }
        header h1 { color: #333; margin-top: 0; }
        header .illustration { margin-left: 20px; flex-shrink: 0; }
        header .illustration img { max-width: 150px; height: auto; border-radius: 4px; }
        main section { background-color: #ffffff; padding: 20px; border-radius: 8px; margin-bottom: 20px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); }
        .solution-card { border: 1px solid #ddd; padding: 15px; margin-top: 10px; border-radius: 6px; }
        .solution-card h3 { color: #007bff; margin-top: 0; display: inline-block; }
        .evaluation-item { background-color: #e9ecef; padding: 8px; margin-top: 5px; border-radius: 4px; font-size: 0.9em; }
        .evaluation-stars { font-weight: bold; margin-right: 5px; }
        .evaluation-form, .add-solution form { display: flex; gap: 10px; margin-top: 15px; flex-wrap: wrap; }
        .evaluation-form input[type="text"] { flex-grow: 1; padding: 8px; border: 1px solid #ccc; border-radius: 4px; }
        .add-solution form textarea, .add-solution form select { width: 100%; padding: 8px; margin-top: 5px; border: 1px solid #ccc; border-radius: 4px; }
        .add-solution form button, .evaluation-form button { background-color: #28a745; color: white; border: none; padding: 10px 15px; border-radius: 4px; cursor: pointer; }
        .resources { display: flex; gap: 10px; margin-top: 10px; }
        .resource-card { background-color: #e0f7fa; padding: 10px; border-radius: 4px; border: 1px solid #00bcd4; }
        
        /* Styles pour les actions CRUD */
        .solution-actions { float: right; }
        .solution-actions button { margin-left: 5px; padding: 5px 10px; border-radius: 4px; cursor: pointer; }
        .btn-edit { background-color: #ffc107; color: #333; border: none; }
        .btn-delete { background-color: #dc3545; color: white; border: none; }
        
        /* Styles Modale */
        .modal { display: none; position: fixed; z-index: 1000; left: 0; top: 0; width: 100%; height: 100%; overflow: auto; background-color: rgba(0,0,0,0.4); }
        .modal-content { background-color: #fefefe; margin: 15% auto; padding: 20px; border: 1px solid #888; width: 80%; max-width: 500px; border-radius: 8px; }
        .modal-content h2 { margin-top: 0; }
        
        /* Ajustement pour le feedback JS */
        input[type="text"], textarea { margin-bottom: 15px; }
    </style>
</head>
<body>
    
    <header>
        <div class="text">
            <p style="opacity:0.8; font-weight: 600; margin-bottom: 8px;">Accueil / Signalements / Détails</p>
            <h1>Signalement #<?php echo safe((string)$signalement->getId()); ?>: <?php echo safe($signalement->getTitre()); ?></h1>
            <p><?php echo safe($signalement->getDescription()); ?></p>
            <input type="hidden" name="report_id" value="<?php echo safe((string)$signalement->getId()); ?>">
        </div>
        <div class="illustration">
            <img src="https://via.placeholder.com/150" alt="" />
        </div>
    </header>

    <main>
        <a href="export_signalement_pdf.php?id=<?php echo htmlspecialchars((string)$signalement->getId()); ?>"
   style="float:right; padding:10px 18px; background:#dc3545; color:white; text-decoration:none; border-radius:6px; font-weight:bold; box-shadow:0 2px 8px rgba(0,0,0,0.2);"
   onclick="return confirm('Exporter ce signalement en PDF ?')">
   📥 Exporter en PDF
</a>
        <section class="signalement">
            <h2>Ressources associées</h2>
            <div class="resources">
                <div class="resource-card">🖼️ photo_parc1.jpg</div>
                <div class="resource-card">🎥 video_parc.mp4</div>
                <div class="resource-card">📄 Rapport localisation (PDF)</div>
            </div>
        </section>

        <section>
            <h2>Solutions existantes (<?php echo count($solutions); ?>)</h2>

            <div class="solutions-list" id="solutionsList">
                
                <?php if (empty($solutions)): ?>
                    <p>Aucune solution proposée pour le moment.</p>
                <?php endif; ?>

                <?php foreach ($solutions as $solution): ?>
                <article class="solution-card">
                    
                    <div class="solution-actions">
                        <button class="btn-edit" onclick="openEditModal(<?= $solution['id'] ?>, '<?= safe(addslashes($solution['description'])) ?>')">Modifier</button>
                        
                        <form method="POST" style="display:inline;" onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer la solution #<?= $solution['id'] ?> ? Cette action est irréversible.');">
                            <input type="hidden" name="action" value="delete_solution">
                            <input type="hidden" name="report_id" value="<?= safe((string)$signalement->getId()) ?>">
                            <input type="hidden" name="solution_id" value="<?= safe((string)$solution['id']) ?>">
                            <button type="submit" class="btn-delete">Supprimer</button>
                        </form>
                    </div>
                    
                    <h3>Solution #<?php echo safe((string)$solution['id']); ?></h3>
                    <p class="solution-text"><?php echo nl2br(safe($solution['description'])); ?></p>
                    
                    <section class="evaluations">
                        <h4>Évaluations:</h4>
                        <?php if (!empty($solution['evaluations'])): ?>
                            <?php foreach ($solution['evaluations'] as $eval): ?>
                                <div class="evaluation-item">
                                    <span class="evaluation-stars"><?php echo safe($eval['stars']); ?></span>
                                    <?php echo safe($eval['comment']); ?> (par: <?php echo safe($eval['author']); ?>)
                                </div>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <div class="evaluation-item">Aucune évaluation pour cette solution.</div>
                        <?php endif; ?>
                    </section>

                    <form class="evaluation-form" method="POST" action="frontoffice_index.php?id=<?php echo safe((string)$signalement->getId()); ?>">
                        <input type="hidden" name="action" value="add_evaluation">
                        <input type="hidden" name="report_id" value="<?php echo safe((string)$signalement->getId()); ?>">
                        <input type="hidden" name="solution_id" value="<?php echo safe((string)$solution['id']); ?>">
                        <input type="text" name="evaluation_text" id="evaluation_text_<?= $solution['id'] ?>" placeholder="Votre évaluation (ex : ⭐⭐⭐ - Commentaire)" />
                        <button type="submit">Évaluer</button>
                    </form>
                </article>
                <?php endforeach; ?>

            </div>
        </section>

        <section class="add-solution">
            <h2>Proposer une nouvelle solution</h2>
            <form id="solutionForm" method="POST" action="frontoffice_index.php?id=<?php echo safe((string)$signalement->getId()); ?>">
                <input type="hidden" name="action" value="add_solution">
                <input type="hidden" name="report_id" value="<?php echo safe((string)$signalement->getId()); ?>">
                
                <label for="report">Signalement associé</label>
                <select id="report" name="report" disabled>
                    <option value="<?php echo safe((string)$signalement->getId()); ?>" selected>
                        Signalement #<?php echo safe((string)$signalement->getId()); ?> - <?php echo safe($signalement->getTitre()); ?>
                    </option>
                    <?php 
                    if (isset($otherReports)) {
                        foreach ($otherReports as $report) {
                            echo '<option value="' . safe((string)$report['id']) . '">Signalement #' . safe((string)$report['id']) . ' - ' . safe($report['titre']) . '</option>';
                        }
                    }
                    ?>
                </select>

                <label for="solutionText" style="margin-top:15px;">Description de la solution</label>
                <textarea id="solutionText" name="solutionText" rows="5" placeholder="Décrivez votre proposition..."></textarea>

                <button type="submit">Proposer la solution</button>
            </form>
        </section>
    </main>

    <div id="editSolutionModal" class="modal">
        <div class="modal-content">
            <h2>Modifier la Solution</h2>
            <form id="editForm" method="POST" action="frontoffice_index.php?id=<?php echo safe((string)$signalement->getId()); ?>">
                <input type="hidden" name="action" value="update_solution">
                <input type="hidden" name="report_id" value="<?= safe((string)$signalement->getId()) ?>">
                <input type="hidden" name="solution_id" id="edit_solution_id" value="">
                
                <label for="edit_solution_text">Nouvelle description :</label>
                <textarea id="edit_solution_text" name="newSolutionText" rows="5" style="width: 100%; margin-bottom: 5px;"></textarea>
                
                <button type="submit" class="btn-edit">Sauvegarder les modifications</button>
                <button type="button" onclick="document.getElementById('editSolutionModal').style.display='none';">Annuler</button>
            </form>
        </div>
    </div>

    <script>
        
        // =========================================================
        // 1. Validation du formulaire d'ajout de Solution
        // =========================================================
        const solutionForm = document.getElementById('solutionForm');

        if (solutionForm) {
            solutionForm.addEventListener('submit', function(event) {
                const solutionText = document.getElementById('solutionText');
                
                // Contrôle: longueur minimale de 20 caractères
                if (solutionText.value.trim().length < 20) {
                    event.preventDefault(); 
                    // Utilisation de la fonction alert() simple
                    alert('⚠️ La description de la solution doit contenir au moins 20 caractères.');
                    solutionText.focus(); 
                    return false;
                }
                return true;
            });
        }

        // =========================================================
        // 2. Validation des formulaires d'ajout d'Évaluation
        // =========================================================
        const evaluationForms = document.querySelectorAll('.evaluation-form');

        evaluationForms.forEach(form => {
            form.addEventListener('submit', function(event) {
                const evaluationInput = this.querySelector('input[name="evaluation_text"]');
                const text = evaluationInput.value.trim();

                // Regex pour valider le format : étoiles (1 à 5) suivi d'un commentaire d'au moins 10 caractères
                const regex = /^(⭐{1,5})\s*(-?\s*).{10,}$/u;
                
                if (!regex.test(text)) {
                    event.preventDefault();
                    // Utilisation de la fonction alert() simple
                    alert('⚠️ Format invalide. Utilisez 1 à 5 étoiles (ex: ⭐⭐⭐) suivi d\'au moins 10 caractères de commentaire (ex: ⭐⭐⭐ - Très utile.).');
                    evaluationInput.focus();
                    return false;
                }
                return true;
            });
        });


        // =========================================================
        // 3. Validation du formulaire de modification (dans la modale)
        // =========================================================
        const editForm = document.getElementById('editSolutionModal').querySelector('form');

        if (editForm) {
            editForm.addEventListener('submit', function(event) {
                const newSolutionText = document.getElementById('edit_solution_text');

                // Contrôle: longueur minimale de 20 caractères
                if (newSolutionText.value.trim().length < 20) {
                    event.preventDefault();
                    // Utilisation de la fonction alert() simple
                    alert(' La nouvelle description doit contenir au moins 20 caractères.');
                    newSolutionText.focus();
                    return false;
                }
                return true;
            });
        }

        // =========================================================
        // Fonction de gestion de la Modale (existante)
        // =========================================================
        
        window.openEditModal = function(solutionId, currentDescription) {
            document.getElementById('edit_solution_id').value = solutionId;
            document.getElementById('edit_solution_text').value = currentDescription.replace(/\\'/g, "'").replace(/\\"/g, '"');
            
            document.getElementById('editSolutionModal').style.display = 'block';
        }
    </script>
</body>
</html>