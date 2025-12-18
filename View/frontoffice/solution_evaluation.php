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
        /* Palette & typo */
        @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap');
        :root {
            --primary: #0077B6;
            --purple: #9D4EDD;
            --violet: #7B2CBF;
            --cyan: #00B4D8;
            --bg: #f4f6fb;
            --card: #ffffff;
            --text: #1b1b1f;
            --muted: #60606b;
            --border: #e2e6f0;
        }
        * { box-sizing: border-box; }
        body {
            font-family: 'Poppins', 'Segoe UI', sans-serif;
            margin: 0;
            padding: 24px;
            background: var(--bg);
            color: var(--text);
            line-height: 1.6;
        }
        a { color: var(--primary); text-decoration: none; }
        a:hover { text-decoration: underline; }

        header {
            background: linear-gradient(135deg, var(--primary), var(--purple));
            color: #fff;
            padding: 24px;
            border-radius: 16px;
            margin-bottom: 24px;
            display: flex;
            gap: 16px;
            align-items: center;
            box-shadow: 0 12px 30px rgba(0,0,0,0.12);
        }
        header h1 { margin: 0; font-size: 28px; font-weight: 700; }
        header p { margin: 6px 0 0; color: rgba(255,255,255,0.9); }
        header .illustration { flex-shrink: 0; }
        header .illustration img { max-width: 140px; border-radius: 12px; box-shadow: 0 6px 18px rgba(0,0,0,0.2); }

        main section {
            background: var(--card);
            padding: 20px;
            border-radius: 14px;
            margin-bottom: 20px;
            box-shadow: 0 10px 24px rgba(0,0,0,0.05);
        }
        h2 { margin: 0 0 10px; font-size: 22px; font-weight: 700; color: var(--primary); }
        h3 { margin: 6px 0 4px; font-size: 18px; font-weight: 700; color: var(--violet); }
        h4 { margin: 12px 0 6px; font-size: 16px; font-weight: 600; color: var(--text); }
        p, label, input, textarea, select { font-size: 16px; }

        .solutions-list { display: grid; gap: 14px; }
        .solution-card {
            border: 1px solid var(--border);
            padding: 16px;
            border-radius: 12px;
            background: #fff;
        }
        .solution-actions { float: right; display: flex; gap: 8px; }
        .solution-actions button {
            padding: 8px 12px;
            border-radius: 10px;
            border: none;
            cursor: pointer;
            font-weight: 600;
        }
        .btn-edit { background: var(--cyan); color: #fff; }
        .btn-delete { background: #ef476f; color: #fff; }

        .solution-text { color: var(--muted); margin: 4px 0 12px; }

        .evaluation-item {
            background: #f2f7ff;
            border: 1px solid var(--border);
            padding: 10px;
            border-radius: 10px;
            font-size: 15px;
            color: var(--text);
        }
        .evaluation-stars { font-weight: 700; margin-right: 6px; color: var(--violet); }

        .evaluation-form, .add-solution form {
            display: flex;
            flex-direction: column;
            gap: 14px;
            margin-top: 14px;
        }
        input[type="text"], textarea, select {
            width: 100%;
            padding: 12px;
            border-radius: 10px;
            border: 1px solid var(--border);
            background: #fafbff;
            transition: border 0.2s, box-shadow 0.2s;
        }
        input[type="text"]:focus, textarea:focus, select:focus {
            outline: none;
            border-color: var(--primary);
            box-shadow: 0 0 0 3px rgba(0,119,182,0.15);
        }
        
        /* Système d'étoiles interactif */
        .star-rating {
            display: flex;
            gap: 4px;
            align-items: center;
            margin-bottom: 10px;
            flex-wrap: wrap;
        }
        .star-rating label {
            font-size: 32px;
            color: #ddd;
            cursor: pointer;
            transition: transform 0.15s, color 0.15s;
            user-select: none;
            line-height: 1;
            display: inline-block;
        }
        .star-rating label:hover {
            transform: scale(1.15);
        }
        .star-rating input[type="radio"] {
            display: none;
        }
        .star-rating label.active {
            color: #ffc107;
            text-shadow: 0 0 8px rgba(255, 193, 7, 0.5);
        }
        .star-rating label.hover-active {
            color: #ffc107;
        }
        .star-rating .star-label {
            font-size: 14px;
            color: var(--muted);
            margin-left: 12px;
            font-weight: 600;
        }
        .add-solution form button, .evaluation-form button, .gemini-input-group button {
            background: var(--primary);
            color: #fff;
            border: none;
            padding: 12px 18px;
            border-radius: 12px;
            cursor: pointer;
            font-weight: 700;
            transition: transform 0.1s, box-shadow 0.2s;
        }
        .add-solution form button:hover, .evaluation-form button:hover, .gemini-input-group button:hover {
            transform: translateY(-1px);
            box-shadow: 0 10px 18px rgba(0,119,182,0.18);
        }

        .resources { display: flex; gap: 12px; flex-wrap: wrap; }
        .resource-card {
            background: #f0f8ff;
            padding: 12px 14px;
            border-radius: 10px;
            border: 1px solid var(--border);
            color: var(--text);
        }

        /* Modale générique */
        .modal { display: none; position: fixed; z-index: 1000; left: 0; top: 0; width: 100%; height: 100%; overflow: auto; background-color: rgba(0,0,0,0.4); }
        .modal-content { background-color: #fff; margin: 10% auto; padding: 20px; border: 1px solid var(--border); width: 90%; max-width: 520px; border-radius: 14px; }

        /* Modale de traduction */
        .translation-modal { display: none; position: fixed; z-index: 2000; left: 0; top: 0; width: 100%; height: 100%; overflow: auto; background-color: rgba(0,0,0,0.5); }
        .translation-modal-content { background-color: #ffffff; margin: 8% auto; padding: 28px; border-radius: 14px; width: 90%; max-width: 620px; box-shadow: 0 16px 38px rgba(0,0,0,0.18); }
        .translation-modal-content h3 { margin-top: 0; color: var(--primary); }
        .translation-result { background-color: #f7f9ff; padding: 15px; border-radius: 10px; margin-top: 12px; border-left: 4px solid var(--primary); line-height: 1.6; }
        .translation-loading { text-align: center; padding: 20px; color: var(--muted); }
        .translation-loading::after { content: '...'; animation: dots 1.5s steps(4, end) infinite; }
        @keyframes dots { 0%, 20% { content: '.'; } 40% { content: '..'; } 60%, 100% { content: '...'; } }
        .close-translation { float: right; font-size: 24px; font-weight: 700; color: #aaa; cursor: pointer; }
        .close-translation:hover { color: #000; }

        /* Gemini */
        .gemini-search-container {
            background: linear-gradient(135deg, var(--purple), var(--violet));
            padding: 18px;
            border-radius: 14px;
            margin-bottom: 20px;
            color: white;
            box-shadow: 0 12px 24px rgba(0,0,0,0.15);
        }
        .gemini-search-container h2 {
            margin-top: 0;
            font-size: 20px;
            display: flex;
            align-items: center;
            gap: 10px;
            color: white;
        }
        .gemini-input-group {
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
        }
        .gemini-input-group input {
            flex-grow: 1;
            padding: 12px;
            border: none;
            border-radius: 10px;
            font-size: 15px;
        }
        .gemini-input-group button {
            background: #fff;
            color: var(--violet);
        }
        .gemini-response {
            margin-top: 12px;
            background: rgba(255, 255, 255, 0.96);
            color: var(--text);
            padding: 12px;
            border-radius: 10px;
            display: none;
            line-height: 1.6;
        }
        .loading-spinner {
            display: inline-block;
            width: 16px;
            height: 16px;
            border: 3px solid rgba(255, 255, 255, 0.3);
            border-radius: 50%;
            border-top-color: #fff;
            animation: spin 1s ease-in-out infinite;
            display: none;
        }
        @keyframes spin { to { transform: rotate(360deg); } }

        /* Icônes de traduction */
        .translate-icon {
            cursor: pointer;
            font-size: 1.2em;
            margin-right: 8px;
            display: inline-block;
            vertical-align: middle;
            transition: transform 0.2s;
        }
        .translate-icon:hover { transform: scale(1.2); }
        .translate-menu {
            display: none;
            position: absolute;
            background: white;
            border: 1px solid var(--border);
            border-radius: 10px;
            box-shadow: 0 8px 16px rgba(0,0,0,0.12);
            z-index: 1000;
            min-width: 160px;
            padding: 8px 0;
            margin-top: 5px;
        }
        .translate-menu-item {
            padding: 10px 16px;
            cursor: pointer;
            display: block;
            text-decoration: none;
            color: var(--text);
            transition: background-color 0.2s;
            font-weight: 600;
        }
        .translate-menu-item:hover { background-color: #f5f7fb; }
        .translate-container { position: relative; display: inline-block; }
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
   📥 Exporter en PDF
</a>

        <!-- Section Gemini Integration -->
        <section class="gemini-search-container">
            <h2>✨ Assistant IA (Gemini)</h2>
            <p>Posez une question pour améliorer ce signalement ou trouver des solutions innovantes.</p>
            <div class="gemini-input-group">
                <input type="text" id="geminiPrompt" placeholder="Ex: Quelles solutions écologiques pour ce problème ?" />
                <button type="button" onclick="askGemini()">
                    <span id="geminiLoading" class="loading-spinner"></span>
                    Demander à l'IA
                </button>
            </div>
            <div id="geminiResponse" class="gemini-response"></div>
        </section>
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
                    
                    <h3>
                        <span class="translate-container">
                            <span class="translate-icon" onclick="openTranslateMenu(event, '<?php echo safe(addslashes($solution['description'])); ?>')">🌐</span>
                            <div class="translate-menu" id="translate-menu-solution-<?php echo $solution['id']; ?>">
                                <a href="#" class="translate-menu-item" onclick="translateText(event, '<?php echo safe(addslashes($solution['description'])); ?>', 'fr')">Français</a>
                                <a href="#" class="translate-menu-item" onclick="translateText(event, '<?php echo safe(addslashes($solution['description'])); ?>', 'en')">English</a>
                            </div>
                        </span>
                        Solution #<?php echo safe((string)$solution['id']); ?>
                    </h3>
                    <p class="solution-text">
                        <span class="translate-container">
                            <span class="translate-icon" onclick="openTranslateMenu(event, '<?php echo safe(addslashes($solution['description'])); ?>')">🌐</span>
                            <div class="translate-menu" id="translate-menu-solution-text-<?php echo $solution['id']; ?>">
                                <a href="#" class="translate-menu-item" onclick="translateText(event, '<?php echo safe(addslashes($solution['description'])); ?>', 'fr')">Français</a>
                                <a href="#" class="translate-menu-item" onclick="translateText(event, '<?php echo safe(addslashes($solution['description'])); ?>', 'en')">English</a>
                            </div>
                        </span>
                        <?php echo nl2br(safe($solution['description'])); ?>
                    </p>
                    
                    <section class="evaluations">
                        <h4>Évaluations:</h4>
                        <?php if (!empty($solution['evaluations'])): ?>
                            <?php foreach ($solution['evaluations'] as $index => $eval): ?>
                                <div class="evaluation-item">
                                    <span class="translate-container">
                                        <span class="translate-icon" onclick="openTranslateMenu(event, '<?php echo safe(addslashes($eval['comment'])); ?>')">🌐</span>
                                        <div class="translate-menu" id="translate-menu-eval-<?php echo $solution['id']; ?>-<?php echo $index; ?>">
                                            <a href="#" class="translate-menu-item" onclick="translateText(event, '<?php echo safe(addslashes($eval['comment'])); ?>', 'fr')">Français</a>
                                            <a href="#" class="translate-menu-item" onclick="translateText(event, '<?php echo safe(addslashes($eval['comment'])); ?>', 'en')">English</a>
                                        </div>
                                    </span>
                                    <span class="evaluation-stars"><?php echo safe($eval['stars']); ?></span>
                                    <?php echo safe($eval['comment']); ?> (par: <?php echo safe($eval['author']); ?>)
                                </div>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <div class="evaluation-item">Aucune évaluation pour cette solution.</div>
                        <?php endif; ?>
                    </section>

                    <form class="evaluation-form" method="POST" action="frontoffice_index.php?id=<?php echo safe((string)$signalement->getId()); ?>" id="evaluation_form_<?= $solution['id'] ?>">
                        <input type="hidden" name="action" value="add_evaluation">
                        <input type="hidden" name="report_id" value="<?php echo safe((string)$signalement->getId()); ?>">
                        <input type="hidden" name="solution_id" value="<?php echo safe((string)$solution['id']); ?>">
                        <input type="hidden" name="evaluation_rating" id="evaluation_rating_<?= $solution['id'] ?>" value="0">
                        
                        <div class="star-rating" id="star_rating_<?= $solution['id'] ?>">
                            <input type="radio" id="star5_<?= $solution['id'] ?>" name="star_<?= $solution['id'] ?>" value="5">
                            <label for="star5_<?= $solution['id'] ?>" onclick="setRating(<?= $solution['id'] ?>, 5)">★</label>
                            <input type="radio" id="star4_<?= $solution['id'] ?>" name="star_<?= $solution['id'] ?>" value="4">
                            <label for="star4_<?= $solution['id'] ?>" onclick="setRating(<?= $solution['id'] ?>, 4)">★</label>
                            <input type="radio" id="star3_<?= $solution['id'] ?>" name="star_<?= $solution['id'] ?>" value="3">
                            <label for="star3_<?= $solution['id'] ?>" onclick="setRating(<?= $solution['id'] ?>, 3)">★</label>
                            <input type="radio" id="star2_<?= $solution['id'] ?>" name="star_<?= $solution['id'] ?>" value="2">
                            <label for="star2_<?= $solution['id'] ?>" onclick="setRating(<?= $solution['id'] ?>, 2)">★</label>
                            <input type="radio" id="star1_<?= $solution['id'] ?>" name="star_<?= $solution['id'] ?>" value="1">
                            <label for="star1_<?= $solution['id'] ?>" onclick="setRating(<?= $solution['id'] ?>, 1)">★</label>
                            <span class="star-label" id="star_label_<?= $solution['id'] ?>">Sélectionnez une note</span>
                        </div>
                        
                        <textarea name="evaluation_comment" id="evaluation_comment_<?= $solution['id'] ?>" rows="3" placeholder="Votre commentaire (obligatoire, minimum 10 caractères)" required></textarea>
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

    <!-- Modale de traduction -->
    <div id="translationModal" class="translation-modal">
        <div class="translation-modal-content">
            <span class="close-translation" onclick="closeTranslationModal()">&times;</span>
            <h3>🌐 Traduction</h3>
            <div id="translationContent">
                <div class="translation-loading">Traduction en cours</div>
            </div>
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
        // 2. Système d'étoiles interactif
        // =========================================================
        function setRating(solutionId, rating) {
            const ratingInput = document.getElementById('evaluation_rating_' + solutionId);
            const starLabel = document.getElementById('star_label_' + solutionId);
            const starLabels = document.querySelectorAll('#star_rating_' + solutionId + ' label');
            
            // Mettre à jour la valeur cachée
            ratingInput.value = rating;
            
            // Mettre à jour le label
            const ratingTexts = ['', 'Très mauvais', 'Mauvais', 'Moyen', 'Bien', 'Excellent'];
            starLabel.textContent = rating + ' / 5' + (ratingTexts[rating] ? ' - ' + ratingTexts[rating] : '');
            
            // Mettre à jour l'apparence des étoiles
            starLabels.forEach((label, index) => {
                // index 0 = première étoile (rating 5), index 4 = dernière étoile (rating 1)
                const starValue = 5 - index;
                if (starValue <= rating) {
                    label.classList.add('active');
                    label.classList.remove('hover-active');
                } else {
                    label.classList.remove('active', 'hover-active');
                }
            });
            
            // Cocher le radio correspondant
            const radioId = 'star' + rating + '_' + solutionId;
            document.getElementById(radioId).checked = true;
        }
        
        // Gestion du survol des étoiles
        document.querySelectorAll('.star-rating').forEach(ratingDiv => {
            const labels = ratingDiv.querySelectorAll('label');
            const solutionId = ratingDiv.id.replace('star_rating_', '');
            
            labels.forEach((label, index) => {
                const starValue = 5 - index; // Inverser car les étoiles sont dans l'ordre décroissant
                
                // Survol : mettre en surbrillance toutes les étoiles jusqu'à celle survolée
                label.addEventListener('mouseenter', function() {
                    labels.forEach((l, idx) => {
                        const val = 5 - idx;
                        if (val <= starValue) {
                            l.classList.add('hover-active');
                        }
                    });
                });
                
                // Sortie du survol : retirer la surbrillance sauf celles sélectionnées
                label.addEventListener('mouseleave', function() {
                    labels.forEach((l) => {
                        l.classList.remove('hover-active');
                    });
                });
            });
        });

        // =========================================================
        // 3. Validation des formulaires d'ajout d'Évaluation
        // =========================================================
        const evaluationForms = document.querySelectorAll('.evaluation-form');

        evaluationForms.forEach(form => {
            form.addEventListener('submit', function(event) {
                const solutionId = this.querySelector('input[name="solution_id"]').value;
                const ratingInput = document.getElementById('evaluation_rating_' + solutionId);
                const commentInput = document.getElementById('evaluation_comment_' + solutionId);
                
                const rating = parseInt(ratingInput.value);
                const comment = commentInput.value.trim();

                // Validation : au moins une étoile doit être sélectionnée
                if (rating === 0 || rating < 1 || rating > 5) {
                    event.preventDefault();
                    alert('⚠️ Veuillez sélectionner une note entre 1 et 5 étoiles.');
                    return false;
                }
                
                // Validation : commentaire obligatoire (au moins 10 caractères)
                if (comment.length === 0) {
                    event.preventDefault();
                    alert('⚠️ Le commentaire est obligatoire. Veuillez saisir votre évaluation.');
                    commentInput.focus();
                    return false;
                }
                
                if (comment.length < 10) {
                    event.preventDefault();
                    alert('⚠️ Le commentaire doit contenir au moins 10 caractères.');
                    commentInput.focus();
                    return false;
                }
                
                return true;
            });
        });


        // =========================================================
        // 4. Validation du formulaire de modification (dans la modale)
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

        // =========================================================
        // 5. Fonctionnalité de traduction
        // =========================================================
        function openTranslateMenu(event, text) {
            event.stopPropagation();
            // Fermer tous les autres menus ouverts
            document.querySelectorAll('.translate-menu').forEach(menu => {
                menu.style.display = 'none';
            });
            // Ouvrir le menu correspondant
            const menu = event.target.nextElementSibling;
            if (menu && menu.classList.contains('translate-menu')) {
                menu.style.display = 'block';
                // Positionner le menu
                const rect = event.target.getBoundingClientRect();
                menu.style.left = '0px';
                menu.style.top = '100%';
            }
        }
        
        // Décodage des entités HTML avant envoi à Gemini
        function decodeEntities(str) {
            const txt = document.createElement('textarea');
            txt.innerHTML = str;
            return txt.value;
        }

        function translateText(event, text, lang) {
            event.preventDefault();
            event.stopPropagation();
            
            // Fermer le menu
            const menu = event.target.closest('.translate-menu');
            if (menu) {
                menu.style.display = 'none';
            }
            
            // Nettoyer le texte (entités HTML)
            const cleanText = decodeEntities(text);

            // Ouvrir la modale de traduction
            const modal = document.getElementById('translationModal');
            const content = document.getElementById('translationContent');
            modal.style.display = 'block';
            content.innerHTML = '<div class="translation-loading">Traduction en cours</div>';
            
            // Appeler l'API de traduction via Gemini
            const formData = new FormData();
            formData.append('action', 'translate');
            formData.append('text', cleanText);
            formData.append('lang', lang);
            
            fetch('frontoffice_index.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.text())
            .then(rawText => {
                try {
                    const data = JSON.parse(rawText);
                    if (data.error) {
                        content.innerHTML = '<div style="color: #dc3545; padding: 15px;">Erreur: ' + data.error + '</div>';
                    } else if (data.translated_text) {
                        const langName = lang === 'fr' ? 'Français' : 'English';
                        content.innerHTML = `
                            <div style="margin-bottom: 10px; color: #666;">
                                <strong>Langue cible:</strong> ${langName}
                            </div>
                            <div class="translation-result">
                                ${data.translated_text.replace(/\n/g, '<br>')}
                            </div>
                        `;
                    } else {
                        throw new Error('Réponse invalide');
                    }
                } catch (e) {
                    console.error('Erreur parsing:', e, rawText);
                    content.innerHTML = '<div style="color: #dc3545; padding: 15px;">Erreur lors de la traduction. Veuillez réessayer.</div>';
                }
            })
            .catch(error => {
                console.error('Erreur:', error);
                content.innerHTML = '<div style="color: #dc3545; padding: 15px;">Erreur de connexion. Veuillez réessayer.</div>';
            });
        }
        
        function closeTranslationModal() {
            document.getElementById('translationModal').style.display = 'none';
        }
        
        // Fermer la modale de traduction si on clique en dehors
        document.addEventListener('click', function(event) {
            const modal = document.getElementById('translationModal');
            if (event.target === modal) {
                closeTranslationModal();
            }
        });
        
        // Fermer les menus si on clique ailleurs
        document.addEventListener('click', function(event) {
            if (!event.target.closest('.translate-container')) {
                document.querySelectorAll('.translate-menu').forEach(menu => {
                    menu.style.display = 'none';
                });
            }
        });
        
        // =========================================================
        // 6. Intégration Gemini
        // =========================================================
        function askGemini() {
            const prompt = document.getElementById('geminiPrompt').value;
            const responseDiv = document.getElementById('geminiResponse');
            const loadingSpinner = document.getElementById('geminiLoading');

            if (!prompt.trim()) {
                alert("Veuillez entrer une question.");
                return;
            }

            // UI Loading State
            loadingSpinner.style.display = 'inline-block';
            responseDiv.style.display = 'none';
            responseDiv.innerHTML = '';

            const formData = new FormData();
            formData.append('action', 'gemini_search');
            formData.append('prompt', prompt);

            fetch('frontoffice_index.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.text()) // On récupère d'abord le texte brut
            .then(rawText => {
                let data;
                try {
                    data = JSON.parse(rawText);
                } catch (e) {
                    console.error("Erreur parsing JSON:", rawText);
                    throw new Error("Réponse serveur invalide (pas du JSON). Regardez la console ou le détail: " + rawText.substring(0, 500));
                }
                
                if (data.error) {
                    throw new Error(data.error);
                }

                loadingSpinner.style.display = 'none';
                responseDiv.style.display = 'block';
                
                const safeResponse = (data.response || "Pas de réponse.")
                    .replace(/&/g, "&amp;")
                    .replace(/</g, "&lt;")
                    .replace(/>/g, "&gt;")
                    .replace(/"/g, "&quot;")
                    .replace(/'/g, "&#039;")
                    .replace(/\*\*(.*?)\*\*/g, '<strong>$1</strong>')
                    .replace(/\n/g, '<br>');
                
                responseDiv.innerHTML = '<strong>🤖 Réponse de Gemini :</strong><br><br>' + safeResponse;
            })
            .catch(error => {
                console.error('Erreur:', error);
                loadingSpinner.style.display = 'none';
                // Affiche l'erreur réelle à l'utilisateur
                alert("Erreur: " + error.message);
            });
        }
    </script>
</body>
</html>