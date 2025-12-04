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
                    <td><?php echo substr(htmlspecialchars($solution['solution_description']), 0, 100) . '...'; ?></td>
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
</script>

</div>
</body>
</html>