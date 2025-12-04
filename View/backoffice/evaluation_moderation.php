<?php
// Fichier : View/backoffice/evaluation_moderation.php
// Vue de modération globale des Évaluations. Intégré avec le sidebar et le thème.

$evaluations = $viewModel['evaluations'] ?? [
    ['id' => 10, 'solution_id' => 5, 'signalement_id' => 123, 'note' => '3 / 5', 'comment' => 'La solution est partiellement efficace, mais coûteuse et longue...', 'member_name' => 'Jean Dupont', 'date' => '2025-11-21', 'full_comment' => 'La solution est partiellement efficace, mais coûteuse et longue à mettre en œuvre. Une alternative devrait être envisagée.'],
    ['id' => 11, 'solution_id' => 8, 'signalement_id' => 125, 'note' => '5 / 5', 'comment' => 'Excellente et rapide, je recommande sans hésiter.', 'member_name' => 'Alice Martin', 'date' => '2025-11-21', 'full_comment' => 'Excellente et rapide, je recommande sans hésiter. Rien à redire sur la mise en place.']
];

function safe(string $text): string {
    return htmlspecialchars($text, ENT_QUOTES, 'UTF-8');
}
?>

<style>
/* Styles spécifiques pour les boutons d'action d'évaluation dans cette vue */
.btn-lire { 
    background-color: #007bff; color: white; border: none; 
    padding: 8px 12px; border-radius: 6px; cursor: pointer; 
    margin-right: 5px; transition: background-color 0.3s ease;
}
.btn-lire:hover { background-color: #0056b3; }

/* Styles Modale (doit être définie dans l'en-tête de la page principale ou ici) */
.modal { display: none; position: fixed; z-index: 1000; left: 0; top: 0; width: 100%; height: 100%; overflow: auto; background-color: rgba(0, 0, 0, 0.7); }
.modal-content { 
    background-color: #1f1f1f; color: #f0f0f0;
    margin: 10% auto; padding: 30px; border: 1px solid #444; 
    width: 80%; max-width: 500px; border-radius: 8px;
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
}
.modal-content h2 { margin-top: 0; border-bottom: 1px solid #444; padding-bottom: 10px;}
.close-btn { color: #aaa; float: right; font-size: 28px; font-weight: bold; cursor: pointer; }
.close-btn:hover { color: white; }
</style>

<header style="border-bottom: 1px solid rgba(120, 120, 120, 0.2); padding-bottom: 20px;">
    <h1>Modération Globale des Évaluations</h1>
    <p style="color:#aaa;">Liste de toutes les évaluations pour vérification et suppression.</p>
</header>

<section>
    <table class="evaluations-table">
        <thead>
            <tr>
                <th>ID Éval.</th>
                <th>ID Sol.</th>
                <th>Membre</th>
                <th>Note</th>
                <th>Commentaire (Début)</th>
                <th>Date</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($evaluations as $eval): ?>
                <tr>
                    <td><?= safe((string)$eval['id']) ?></td>
                    <td>
                        <a href="backoffice_index.php?action=view_solution_evaluations&solution_id=<?= safe((string)$eval['solution_id']) ?>" class="link">#<?= safe((string)$eval['solution_id']) ?></a>
                    </td>
                    <td><?= safe($eval['member_name']) ?></td>
                    <td><?= safe($eval['note']) ?></td>
                    <td><?= safe(substr($eval['comment'], 0, 30)) ?>...</td>
                    <td><?= safe($eval['date']) ?></td>
                    <td>
                        <button type="button" class="btn-lire" 
                            onclick="openModal(
                                '<?= safe(addslashes($eval['id'])) ?>', 
                                '<?= safe(addslashes($eval['member_name'])) ?>', 
                                '<?= safe(addslashes($eval['note'])) ?>', 
                                '<?= safe(addslashes($eval['date'])) ?>', 
                                '<?= safe(addslashes($eval['full_comment'] ?? $eval['comment'])) ?>'
                            )">
                            Lire
                        </button>
                        
                        <form method="POST" action="backoffice_index.php" style="display:inline;" onsubmit="return confirm('Supprimer l\'évaluation #<?= $eval['id'] ?> ?');">
                            <input type="hidden" name="action" value="delete_evaluation">
                            <input type="hidden" name="evaluation_id" value="<?= safe((string)$eval['id']) ?>">
                            <button type="submit" class="btn-delete">Supprimer</button>
                        </form>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</section>

<div id="readModal" class="modal">
    <div class="modal-content">
        <span class="close-btn" onclick="closeModal()">&times;</span>
        <h2>Détails de l'Évaluation #<span id="modal-id"></span></h2>
        
        <p><strong>Membre :</strong> <span id="modal-member"></span></p>
        <p><strong>Note Attribuée :</strong> <span id="modal-note"></span></p>
        <p><strong>Date d'évaluation :</strong> <span id="modal-date"></span></p>
        <hr style="border-color:#444;">
        <p><strong>Commentaire Complet :</strong></p>
        <p id="modal-comment" style="white-space: pre-wrap;"></p>
    </div>
</div>

<script>
    const modal = document.getElementById('readModal');

    function openModal(id, member, note, date, comment) {
        document.getElementById('modal-id').textContent = id;
        document.getElementById('modal-member').textContent = member;
        document.getElementById('modal-note').textContent = note;
        document.getElementById('modal-date').textContent = date;
        
        // Nettoyer le commentaire des échappements
        const cleanComment = comment.replace(/\\'/g, "'").replace(/\\"/g, '"');
        document.getElementById('modal-comment').textContent = cleanComment;
        
        modal.style.display = 'block';
    }

    function closeModal() {
        modal.style.display = 'none';
    }

    // Fermer la modale si l'utilisateur clique en dehors
    window.onclick = function(event) {
        if (event.target === modal) {
            closeModal();
        }
    }
</script>