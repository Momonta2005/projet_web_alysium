<?php
// View/backoffice/list_solutions.php

require_once __DIR__ . '/../components/sidebar.php';
$current_sort = $_GET['sort'] ?? 'score_desc';
?>

<h1>Modération et Classement des Solutions</h1>

<?php if (!empty($message)): ?>
    <div class="message"><?php echo htmlspecialchars($message); ?></div>
<?php endif; ?>

<!-- BOUTON DE TRI AVEC MENU DÉROULANT -->
<div style="margin: 20px 0; text-align: right;">
    <button id="sortSolutionsBtn" style="
        padding: 10px 16px;
        background: #007bff;
        color: white;
        border: none;
        border-radius: 6px;
        cursor: pointer;
        font-size: 14px;
        box-shadow: 0 2px 5px rgba(0,0,0,0.2);
    ">
        Trier par <span id="currentSortText">Meilleur score</span> ▼
    </button>
<a href="export_pdf.php?type=solutions&sort=<?php echo urlencode($current_sort); ?>"
   style="float:right; padding:10px 18px; background:#dc3545; color:white; text-decoration:none; border-radius:6px; font-weight:bold; box-shadow:0 2px 8px rgba(0,0,0,0.2);"
   onclick="return confirm('Exporter toutes les solutions en PDF ? (Tri actuel conservé)')">
   📥 Exporter en PDF
</a>
    <div id="sortDropdown" style="
        display: none;
        position: absolute;
        background: white;
        min-width: 220px;
        box-shadow: 0 8px 16px rgba(0,0,0,0.2);
        border-radius: 6px;
        z-index: 1000;
        margin-top: 5px;
        right: 0;
    ">
        <a href="backoffice_index.php?action=list_solutions" class="sort-item <?php echo $current_sort === 'score_desc' ? 'active' : ''; ?>">
            Meilleur score (défaut)
        </a>
        <a href="backoffice_index.php?action=list_solutions&sort=date_asc" class="sort-item <?php echo $current_sort === 'date_asc' ? 'active' : ''; ?>">
            Date (plus ancien d'abord)
        </a>
        <a href="backoffice_index.php?action=list_solutions&sort=date_desc" class="sort-item <?php echo $current_sort === 'date_desc' ? 'active' : ''; ?>">
            Date (plus récent d'abord)
        </a>
        <a href="backoffice_index.php?action=list_solutions&sort=eval_count_asc" class="sort-item <?php echo $current_sort === 'eval_count_asc' ? 'active' : ''; ?>">
            Nombre d'évaluations (peu → beaucoup)
        </a>
        <a href="backoffice_index.php?action=list_solutions&sort=eval_count_desc" class="sort-item <?php echo $current_sort === 'eval_count_desc' ? 'active' : ''; ?>">
            Nombre d'évaluations (beaucoup → peu)
        </a>
    </div>
</div>

<p>Cette liste est classée dynamiquement selon votre tri.</p>

<table>
    <thead>
        <tr>
            <th>ID Sol.</th>
            <th>Signalement Associé</th>
            <th>Description (Début)</th>
            <th>Score Moyen</th>
            <th># Évaluations</th>
            <th>Date Prop.</th>
            <th>Actions</th>
        </tr>
    </thead>
    <tbody>
        <?php if (!empty($solutions)): ?>
            <?php foreach ($solutions as $solution): ?>
                <tr>
                    <td><?php echo htmlspecialchars($solution['solution_id']); ?></td>
                    <td>
                        <a href="frontoffice_index.php?id=<?php echo htmlspecialchars($solution['signalementId']); ?>" target="_blank">
                            #<?php echo htmlspecialchars($solution['signalementId']); ?>: <?php echo htmlspecialchars($solution['signalement_titre']); ?>
                        </a>
                    </td>
                    <td>
                        <span class="translate-container">
                            <span class="translate-icon" onclick="openTranslateMenu(event, '<?php echo htmlspecialchars(addslashes($solution['solution_description'])); ?>')">🌐</span>
                            <div class="translate-menu" id="translate-menu-solution-<?php echo $solution['solution_id']; ?>">
                                <a href="#" class="translate-menu-item" onclick="translateText(event, '<?php echo htmlspecialchars(addslashes($solution['solution_description'])); ?>', 'fr')">Français</a>
                                <a href="#" class="translate-menu-item" onclick="translateText(event, '<?php echo htmlspecialchars(addslashes($solution['solution_description'])); ?>', 'en')">English</a>
                            </div>
                        </span>
                        <?php echo substr(htmlspecialchars($solution['solution_description']), 0, 100) . '...'; ?>
                    </td>
                    <td class="score">
                        <?php echo number_format((float)$solution['average_stars_score'], 1); ?> / 5
                    </td>
                    <td><?php echo htmlspecialchars($solution['evaluation_count']); ?></td>
                    <td><?php echo date('d/m/Y', strtotime($solution['date_proposition'])); ?></td>
                    <td>
                        <form style="display: inline-block;" method="POST" action="backoffice_index.php">
                            <input type="hidden" name="action" value="delete_solution">
                            <input type="hidden" name="id" value="<?php echo htmlspecialchars($solution['solution_id']); ?>">
                            <button type="submit" class="btn-delete"
                                    onclick="return confirm('Supprimer cette solution ?');">
                                Supprimer
                            </button>
                        </form>
                    </td>
                </tr>
            <?php endforeach; ?>
        <?php else: ?>
            <tr><td colspan="7">Aucune solution trouvée.</td></tr>
        <?php endif; ?>
    </tbody>
</table>

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

<div class="pagination">
    <?php if ($page > 1): ?>
        <a href="?action=list_solutions&sort=<?php echo urlencode($sort); ?>&page=<?php echo $page - 1; ?>">&laquo; Précédent</a>
    <?php endif; ?>
    
    <?php for ($i = 1; $i <= $totalPages; $i++): ?>
        <a href="?action=list_solutions&sort=<?php echo urlencode($sort); ?>&page=<?php echo $i; ?>" <?php if ($i == $page) echo 'class="active"'; ?>><?php echo $i; ?></a>
    <?php endfor; ?>
    
    <?php if ($page < $totalPages): ?>
        <a href="?action=list_solutions&sort=<?php echo urlencode($sort); ?>&page=<?php echo $page + 1; ?>">Suivant &raquo;</a>
    <?php endif; ?>
</div>

<style>
    .sort-item {
        display: block;
        padding: 12px 16px;
        text-decoration: none;
        color: #333;
        font-size: 14px;
    }
    .sort-item:hover { background-color: #f0f0f0; }
    .sort-item.active {
        background-color: #007bff;
        color: white;
        font-weight: bold;
    }
    #sortDropdown a:first-child { border-top-left-radius: 6px; border-top-right-radius: 6px; }
    #sortDropdown a:last-child { border-bottom-left-radius: 6px; border-bottom-right-radius: 6px; }
    .pagination { text-align: center; margin-top: 20px; }
        .pagination a { padding: 8px 12px; margin: 0 4px; background: #ddd; color: #333; text-decoration: none; border-radius: 4px; }
.pagination a.active { background: #007bff; color: white; }
.pagination a:hover { background: #ccc; }
        
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

<script>
// Menu déroulant au clic
document.getElementById('sortSolutionsBtn').addEventListener('click', function(e) {
    e.stopPropagation();
    const dropdown = document.getElementById('sortDropdown');
    dropdown.style.display = dropdown.style.display === 'block' ? 'none' : 'block';
});

// Mettre à jour le texte du bouton selon le tri actuel
const sortTexts = {
    'score_desc': 'Meilleur score',
    'date_asc': 'Date (ancien → récent)',
    'date_desc': 'Date (récent → ancien)',
    'eval_count_asc': 'Évals (peu → beaucoup)',
    'eval_count_desc': 'Évals (beaucoup → peu)'
};
const current = '<?php echo $current_sort; ?>';
document.getElementById('currentSortText').textContent = sortTexts[current] || 'Meilleur score';

// Fermer si clic ailleurs
document.addEventListener('click', function() {
    document.getElementById('sortDropdown').style.display = 'none';
});

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
    if (event.target === modal) {
        closeTranslationModal();
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

</div>
</body>
</html>