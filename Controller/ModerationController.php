<?php
// Contrôleur de modération simple
require_once __DIR__ . '/../Model/config.php';

class ModerationController {
    private PDO $db;

    public function __construct() {
        $this->db = Config::getConnexion();
        $this->ensureTable();
    }

    /**
     * Crée la table de flags si elle n'existe pas.
     */
    private function ensureTable(): void {
        $sql = "
            CREATE TABLE IF NOT EXISTS ModerationFlag (
                id INT AUTO_INCREMENT PRIMARY KEY,
                content_type VARCHAR(32) NOT NULL,
                reason VARCHAR(64) NOT NULL,
                content TEXT NOT NULL,
                metadata TEXT NULL,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
        ";
        $this->db->exec($sql);
    }

    /**
     * Logique de modération : retourne flagged=true si contenu interdit.
     * Peut être adaptée facilement en ajoutant/retirant des mots.
     */
    public static function moderateText(string $text): array {
        // Liste blanche : si un terme est trouvé, on publie sans modération.
        $whitelist = [
            'nom_du_produit',
            'marque_officielle',
            'bonjour à tous',
        ];
        foreach ($whitelist as $ok) {
            if (stripos($text, $ok) !== false) {
                return ['flagged' => false, 'reason' => null];
            }
        }

        // Regex par catégorie
        $patterns = [
            'insultes'   => '/\b(con|idiot|abruti|merde|fdp|enculé)\b/i',
            'violence'   => '/\b(tuer|frapper|massacrer|égorger|bombarder)\b/i',
            'sexuel'     => '/\b(porn|xxx|sexe|fell[atio]+|sodomie)\b/i',
            'haine'      => '/\b(nazi|raciste|haineux|suprémaciste|antisémite)\b/i',
            'spam'       => '/(http[s]?:\/\/|www\.|viagra|bitcoin|gagner de l\'argent|casino)/i',
            'hors_sujet' => '/\b(lorem ipsum|blabla|asdfgh)\b/i',
        ];

        foreach ($patterns as $reason => $regex) {
            if (preg_match($regex, $text)) {
                return ['flagged' => true, 'reason' => $reason];
            }
        }

        return ['flagged' => false, 'reason' => null];
    }

    /**
     * Sauvegarde un contenu signalé.
     */
    public function flagContent(string $type, string $content, string $reason, array $metadata = []): bool {
        $sql = "INSERT INTO ModerationFlag (content_type, reason, content, metadata) VALUES (:type, :reason, :content, :meta)";
        $query = $this->db->prepare($sql);
        $query->bindValue('type', $type, PDO::PARAM_STR);
        $query->bindValue('reason', $reason, PDO::PARAM_STR);
        $query->bindValue('content', $content, PDO::PARAM_STR);
        $query->bindValue('meta', json_encode($metadata, JSON_UNESCAPED_UNICODE), PDO::PARAM_STR);
        $ok = $query->execute();

        // Notification email
        if ($ok) {
            $this->notifyAdmin($type, $content, $reason, $metadata);
        }

        return $ok;
    }

    /**
     * Notifie l'admin par email lorsqu'un contenu est bloqué.
     */
    private function notifyAdmin(string $type, string $content, string $reason, array $metadata = []): void {
        $to = 'mohammedmonta@gmail.com';
        $subject = "[Modération] Contenu bloqué ({$type})";
        $metaText = '';
        foreach ($metadata as $k => $v) {
            $metaText .= "{$k}: {$v}\n";
        }

        $body = "Un contenu a été bloqué automatiquement.\n\n"
              . "Type: {$type}\n"
              . "Raison: {$reason}\n"
              . "Métadonnées:\n{$metaText}\n"
              . "Contenu:\n{$content}\n";

        // Silence les warnings si mail n'est pas configuré, mais loggable si besoin
        @mail($to, $subject, $body);
    }

    public function countFlags(): int {
        return (int)$this->db->query("SELECT COUNT(*) FROM ModerationFlag")->fetchColumn();
    }

    public function findFlags(int $page = 1, int $limit = 10): array {
        $sql = "SELECT * FROM ModerationFlag ORDER BY created_at DESC LIMIT :limit OFFSET :offset";
        $query = $this->db->prepare($sql);
        $query->bindValue(':limit', $limit, PDO::PARAM_INT);
        $query->bindValue(':offset', ($page - 1) * $limit, PDO::PARAM_INT);
        $query->execute();
        return $query->fetchAll(PDO::FETCH_ASSOC);
    }
}

