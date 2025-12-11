<?php
/**
 * Vue : Liste des évaluations (Backoffice) - Avec solution liée affichée
 * @var array $evaluations
 * @var string $message (optionnel)
 */

require_once __DIR__ . '/../components/sidebar.php';
$current_sort = $_GET['sort'] ?? 'date_desc';
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Liste des Évaluations • Administration</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 0; padding: 0; background-color: #f4f4f9; }
        .content { margin-left: 70px; padding: 40px; }
        h1 { margin-bottom: 20px; color: #333; }
.pagination { text-align: center; margin-top: 20px; }
.pagination a { padding: 8px 12px; margin: 0 4px; background: #ddd; color: #333; text-decoration: none; border-radius: 4px; }
.pagination a.active { background: #007bff; color: white; }
.pagination a:hover { background: #ccc; }
        /* Bouton de tri */
        .sort-container { margin: 20px 0; text-align: right; position: relative; display: inline-block; }
        #sortEvalsBtn {
            padding: 12px 20px; background: #28a745; color: white; border: none;
            border-radius: 8px; cursor: pointer; font-size: 15px; box-shadow: 0 3px 10px rgba(0,0,0,0.2);
        }
        #sortEvalsBtn:hover { background: #218838; }

        #evalSortDropdown {
            display: none; position: absolute; right: 0; top: 100%; background: white;
            min-width: 240px; border-radius: 8px; box-shadow: 0 10px 25px rgba(0,0,0,0.2);
            overflow: hidden; z-index: 1000; margin-top: 8px;
        }
        .sort-item { display: block; padding: 14px 20px; text-decoration: none; color: #333; }
        .sort-item:hover { background-color: #f0f0f0; }
        .sort-item.active { background-color: #28a745; color: white; font-weight: bold; }

        /* Tableau */
        table { width: 100%; border-collapse: collapse; margin-top: 20px; background: white;
                border-radius: 10px; overflow: hidden; box-shadow: 0 4px 15px rgba(0,0,0,0.1); }
        th, td { padding: 15px; text-align: left; border-bottom: 1px solid #eee; }
        thead { background-color: #e9ecef; }
        tbody tr:hover { background-color: #f8f9fa; }

        /* Solution liée (nouvelle colonne) */
        .solution-text {
            font-size: 0.92em;
            color: #444;
            line-height: 1.4;
        }
        .solution-link {
            color: #007bff;
            text-decoration: none;
            font-weight: 500;
        }
        .solution-link:hover { text-decoration: underline; }

        /* Boutons */
        .btn-read { background: #007bff; color: white; padding: 8px 14px; border: none; border-radius: 6px; cursor: pointer; margin-right: 5px; }
        .btn-read:hover { background: #0069d9; }
        .btn-delete { background: #dc3545; color: white; padding: 8px 14px; border: none; border-radius: 6px; cursor: pointer; }
        .btn-delete:hover { background: #c82333; }

        /* Modal */
        .modal { display: none; position: fixed; z-index: 1000; left: 0; top: 0; width: 100%; height: 100%;
                 background-color: rgba(0,0,0,0.5); animation: fadeIn 0.3s; }
        .modal-content { background: white; margin: 8% auto; padding: 30px; width: 90%; max-width: 700px;
                         border-radius: 12px; box-shadow: 0 10px 30px rgba(0,0,0,0.3); position: relative; }
        .close { position: absolute; top: 10px; right: 20px; font-size: 32px; cursor: pointer; color: #aaa; }
        .close:hover { color: #dc3545; }
        @keyframes fadeIn { from {opacity: 0;} to {opacity: 1;} }
        
        /* Styles pour l'icône de traduction */
        .translate-icon {
            cursor: pointer;
            font-size: 1.2em;
            margin-right: 8px;
            display: inline-block;
            vertical-align: middle;
            transition: transform 0.2s;
        }
        .translate-icon:hover {
            transform: scale(1.2);
        }
        .translate-menu {
            display: none;
            position: absolute;
            background: white;
            border: 1px solid #ddd;
            border-radius: 6px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.15);
            z-index: 1000;
            min-width: 150px;
            padding: 8px 0;
            margin-top: 5px;
        }
        .translate-menu-item {
            padding: 10px 16px;
            cursor: pointer;
            display: block;
            text-decoration: none;
            color: #333;
            transition: background-color 0.2s;
        }
        .translate-menu-item:hover {
            background-color: #f0f0f0;
        }
        .translate-container {
            position: relative;
            display: inline-block;
        }
        
        /* Modale de traduction */
        .translation-modal { display: none; position: fixed; z-index: 2000; left: 0; top: 0; width: 100%; height: 100%; overflow: auto; background-color: rgba(0,0,0,0.5); }
        .translation-modal-content { background-color: #ffffff; margin: 10% auto; padding: 30px; border-radius: 12px; width: 90%; max-width: 600px; box-shadow: 0 10px 30px rgba(0,0,0,0.3); }
        .translation-modal-content h3 { margin-top: 0; color: #007bff; }
        .translation-result { background-color: #f8f9fa; padding: 15px; border-radius: 8px; margin-top: 15px; border-left: 4px solid #007bff; line-height: 1.6; }
        .translation-loading { text-align: center; padding: 20px; }
        .translation-loading::after { content: '...'; animation: dots 1.5s steps(4, end) infinite; }
        @keyframes dots { 0%, 20% { content: '.'; } 40% { content: '..'; } 60%, 100% { content: '...'; } }
        .close-translation { float: right; font-size: 28px; font-weight: bold; color: #aaa; cursor: pointer; }
        .close-translation:hover { color: #000; }
    </style>
</head>
<body>

<div class="content">

    <h1>Liste des Évaluations</h1>

    <!-- Bouton de tri -->
    <div class="sort-container">
        <button id="sortEvalsBtn">
            Trier par date <span id="currentEvalSort">Plus récent d'abord</span>
        </button>
        <a href="export_pdf.php?type=evaluations&sort=<?php echo urlencode($current_sort); ?>"
   style="float:right; padding:10px 18px; background:#dc3545; color:white; text-decoration:none; border-radius:6px; font-weight:bold; box-shadow:0 2px 8px rgba(0,0,0,0.2);"
   onclick="return confirm('Exporter toutes les évaluations en PDF ? (Tri actuel conservé)')">
   📥 Exporter en PDF
</a>
        <div id="evalSortDropdown">
            <a href="backoffice_index.php?action=list_evaluations&sort=date_desc" class="sort-item <?php echo $current_sort === 'date_desc' ? 'active' : ''; ?>">
                Plus récent d'abord
            </a>
            <a href="backoffice_index.php?action=list_evaluations&sort=date_asc" class="sort-item <?php echo $current_sort === 'date_asc' ? 'active' : ''; ?>">
                Plus ancien d'abord
            </a>
        </div>
    </div>

    <?php if (!empty($message)): ?>
        <div style="padding: 15px; background: #d4edda; color: #155724; border-radius: 8px; margin: 20px 0;">
            <?php echo htmlspecialchars($message); ?>
        </div>
    <?php endif; ?>

    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Solution liée</th>
                <th>Note</th>
                <th>Commentaire</th>
                <th>Auteur</th>
                <th>Date</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($evaluations)): ?>
                <tr><td colspan="7" style="text-align:center; color:#888; padding: 40px;">Aucune évaluation trouvée.</td></tr>
            <?php else: ?>
                <?php foreach ($evaluations as $eval): ?>
                    <?php
                        // Récupérer l'ID du signalement lié à la solution (besoin d'une jointure supplémentaire)
                        // On va faire une petite requête rapide pour avoir le signalementId
                        $stmt = Config::getConnexion()->prepare("SELECT signalementId FROM Solution WHERE id = ?");
                        $stmt->execute([$eval['solutionId']]);
                        $signalementId = $stmt->fetchColumn();
                    ?>
                    <tr>
                        <td><?php echo htmlspecialchars($eval['id']); ?></td>
                        <td style="max-width: 400px;">
                            <div class="solution-text">
                                <strong>Solution #<?php echo htmlspecialchars($eval['solutionId']); ?></strong><br>
                                <?php
                                    $desc = htmlspecialchars($eval['solution_description'] ?? 'Solution supprimée');
                                    $short = strlen($desc) > 100 ? substr($desc, 0, 100) . '...' : $desc;
                                    echo $short;
                                ?>
                                <br><br>
                                <a href="frontoffice_index.php?id=<?php echo $signalementId; ?>#solution-<?php echo $eval['solutionId']; ?>"
                                   target="_blank" class="solution-link">
                                    Voir la solution complète →
                                </a>
                            </div>
                        </td>
                        <td><?php echo htmlspecialchars($eval['stars']); ?></td>
                        <td style="max-width: 300px;">
                            <span class="translate-container">
                                <span class="translate-icon" onclick="openTranslateMenu(event, '<?php echo htmlspecialchars(addslashes($eval['comment'])); ?>')">🌐</span>
                                <div class="translate-menu" id="translate-menu-eval-<?php echo $eval['id']; ?>">
                                    <a href="#" class="translate-menu-item" onclick="translateText(event, '<?php echo htmlspecialchars(addslashes($eval['comment'])); ?>', 'fr')">Français</a>
                                    <a href="#" class="translate-menu-item" onclick="translateText(event, '<?php echo htmlspecialchars(addslashes($eval['comment'])); ?>', 'en')">English</a>
                                </div>
                            </span>
                            <?php echo htmlspecialchars(substr($eval['comment'], 0, 80)) . (strlen($eval['comment']) > 80 ? '...' : ''); ?>
                        </td>
                        <td><?php echo htmlspecialchars($eval['author']); ?></td>
                        <td><?php echo date('d/m/Y H:i', strtotime($eval['date_evaluation'])); ?></td>
                        <td>
                            <button class="btn-read" onclick="openModal(<?php echo $eval['id']; ?>)">Lire</button>
                            <form method="POST" action="backoffice_index.php" style="display:inline;">
                                <input type="hidden" name="action" value="delete_evaluation">
                                <input type="hidden" name="id" value="<?php echo $eval['id']; ?>">
                                <button type="submit" class="btn-delete" onclick="return confirm('Supprimer cette évaluation ?');">
                                    Supprimer
                                </button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>
<div class="pagination">
    <?php if ($page > 1): ?>
        <a href="?action=list_evaluations&sort=<?php echo urlencode($sort); ?>&page=<?php echo $page - 1; ?>">&laquo; Précédent</a>
    <?php endif; ?>
    
    <?php for ($i = 1; $i <= $totalPages; $i++): ?>
        <a href="?action=list_evaluations&sort=<?php echo urlencode($sort); ?>&page=<?php echo $i; ?>" <?php if ($i == $page) echo 'class="active"'; ?>><?php echo $i; ?></a>
    <?php endfor; ?>
    
    <?php if ($page < $totalPages): ?>
        <a href="?action=list_evaluations&sort=<?php echo urlencode($sort); ?>&page=<?php echo $page + 1; ?>">Suivant &raquo;</a>
    <?php endif; ?>
</div>
    <!-- Modal de lecture -->
    <div id="readModal" class="modal">
        <div class="modal-content">
            <span class="close" onclick="closeModal()">×</span>
            <h2>Détails de l'Évaluation</h2>
            <p><strong>Note :</strong> <span id="modalStars"></span></p>
            <p><strong>Commentaire :</strong><br><span id="modalComment" style="background:#f8f9fa; padding:12px; border-radius:6px; display:block; margin-top:8px;"></span></p>
            <p><strong>Auteur :</strong> <span id="modalAuthor"></span></p>
            <p><strong>Date :</strong> <span id="modalDate"></span></p>
            <p><strong>Solution associée :</strong><br>
                <span id="modalSolution" style="background:#e9ecef; padding:12px; border-radius:6px; display:block; margin-top:8px; font-size:0.95em;"></span>
            </p>
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

</div>

<script>
// Données pour le modal
const evaluationsData = {
    <?php foreach ($evaluations as $eval): ?>
    <?php echo $eval['id']; ?>: {
        stars: <?php echo json_encode($eval['stars']); ?>,
        comment: <?php echo json_encode($eval['comment']); ?>,
        author: <?php echo json_encode($eval['author']); ?>,
        date: <?php echo json_encode(date('d/m/Y à H:i', strtotime($eval['date_evaluation']))); ?>,
        solution: <?php echo json_encode($eval['solution_description'] ?? 'Solution non disponible'); ?>
    },
    <?php endforeach; ?>
};

function openModal(id) {
    if (evaluationsData[id]) {
        document.getElementById('modalStars').textContent = evaluationsData[id].stars + ' / 5';
        document.getElementById('modalComment').textContent = evaluationsData[id].comment;
        document.getElementById('modalAuthor').textContent = evaluationsData[id].author;
        document.getElementById('modalDate').textContent = evaluationsData[id].date;
        document.getElementById('modalSolution').textContent = evaluationsData[id].solution;
        document.getElementById('readModal').style.display = 'block';
    }
}

function closeModal() {
    document.getElementById('readModal').style.display = 'none';
}

window.onclick = function(e) {
    if (e.target === document.getElementById('readModal')) closeModal();
}

// Menu déroulant tri
document.getElementById('sortEvalsBtn').addEventListener('click', function(e) {
    e.stopPropagation();
    const dropdown = document.getElementById('evalSortDropdown');
    dropdown.style.display = dropdown.style.display === 'block' ? 'none' : 'block';
});

document.addEventListener('click', () => {
    document.getElementById('evalSortDropdown').style.display = 'none';
});

// Texte du bouton
const sortTexts = { 'date_desc': 'Plus récent d\'abord', 'date_asc': 'Plus ancien d\'abord' };
document.getElementById('currentEvalSort').textContent = sortTexts['<?php echo $current_sort; ?>'] || 'Plus récent d\'abord';

// =========================================================
// Fonctionnalité de traduction
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
    
    fetch('backoffice_index.php', {
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

// Fermer la modale si on clique en dehors
window.onclick = function(event) {
    const modal = document.getElementById('translationModal');
    const readModal = document.getElementById('readModal');
    if (event.target === modal) {
        closeTranslationModal();
    }
    if (event.target === readModal) {
        closeModal();
    }
}

// Fermer les menus si on clique ailleurs
document.addEventListener('click', function(event) {
    if (!event.target.closest('.translate-container')) {
        document.querySelectorAll('.translate-menu').forEach(menu => {
            menu.style.display = 'none';
        });
    }
});
</script>

</body>
</html>