<?php
require_once __DIR__ . '/../components/sidebar.php';
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Contenus modérés • Administration</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 0; padding: 0; background-color: #f4f4f9; }
        .content { margin-left: 70px; padding: 40px; }
        h1 { margin-bottom: 20px; color: #333; }
        table { width: 100%; border-collapse: collapse; background: white; border-radius: 10px; overflow: hidden; box-shadow: 0 4px 15px rgba(0,0,0,0.1); }
        th, td { padding: 14px; text-align: left; border-bottom: 1px solid #eee; vertical-align: top; }
        thead { background-color: #e9ecef; }
        tbody tr:hover { background-color: #f8f9fa; }
        .reason { font-weight: bold; color: #dc3545; }
        .meta { color: #666; font-size: 0.9em; }
        .pagination { text-align: center; margin-top: 20px; }
        .pagination a { padding: 8px 12px; margin: 0 4px; background: #ddd; color: #333; text-decoration: none; border-radius: 4px; }
        .pagination a.active { background: #007bff; color: white; }
        .pagination a:hover { background: #ccc; }
        pre { white-space: pre-wrap; word-break: break-word; margin: 0; }
    </style>
</head>
<body>
<div class="content">
    <h1>Contenus supprimés automatiquement</h1>

    <?php if (empty($flags)): ?>
        <p style="color:#666;">Aucun contenu signalé.</p>
    <?php else: ?>
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Type</th>
                    <th>Raison</th>
                    <th>Contenu</th>
                    <th>Métadonnées</th>
                    <th>Date</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($flags as $flag): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($flag['id']); ?></td>
                        <td><?php echo htmlspecialchars($flag['content_type']); ?></td>
                        <td class="reason"><?php echo htmlspecialchars($flag['reason']); ?></td>
                        <td><pre><?php echo htmlspecialchars($flag['content']); ?></pre></td>
                        <td class="meta">
                            <?php
                                $meta = json_decode($flag['metadata'] ?? '', true);
                                if (!empty($meta)) {
                                    foreach ($meta as $k => $v) {
                                        echo '<div><strong>' . htmlspecialchars($k) . ':</strong> ' . htmlspecialchars((string)$v) . '</div>';
                                    }
                                } else {
                                    echo '-';
                                }
                            ?>
                        </td>
                        <td><?php echo htmlspecialchars($flag['created_at']); ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <div class="pagination">
            <?php if ($page > 1): ?>
                <a href="?action=list_flags&page=<?php echo $page - 1; ?>">&laquo; Précédent</a>
            <?php endif; ?>

            <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                <a href="?action=list_flags&page=<?php echo $i; ?>" <?php if ($i == $page) echo 'class="active"'; ?>><?php echo $i; ?></a>
            <?php endfor; ?>

            <?php if ($page < $totalPages): ?>
                <a href="?action=list_flags&page=<?php echo $page + 1; ?>">Suivant &raquo;</a>
            <?php endif; ?>
        </div>
    <?php endif; ?>
</div>
</body>
</html>

