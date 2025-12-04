<?php
// Fichier : View/components/navbar_header.php (En-tête du Front-Office)
// Définit le début du document HTML, le CSS et la barre de navigation.

// On récupère l'ID du signalement actuel pour les liens (si nécessaire)
$current_report_id = (int)($_REQUEST['id'] ?? 0); 
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Interface Citoyen | Consultation Signalement</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, Cantarell, sans-serif; background-color: #f7f7f7; }

        /* Navbar Styles */
        .navbar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 20px 60px;
            background: white;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
        }

        .logo {
            display: flex;
            align-items: center;
            gap: 10px;
            font-size: 20px;
            font-weight: 600;
            color: #1a1a1a;
            text-decoration: none; /* Rendre le logo cliquable vers le portail */
        }

        .logo-icon {
            width: 32px;
            height: 32px;
            background: linear-gradient(135deg, #0f766e 0%, #14b8a6 100%);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: bold;
        }

        .nav-links {
            display: flex;
            gap: 40px;
            list-style: none;
        }

        .nav-links a {
            text-decoration: none;
            color: #4a5568;
            font-size: 15px;
            transition: color 0.3s;
        }

        .nav-links a:hover { color: #0f766e; }

        .action-btn {
            background: #0f3d3a;
            color: white;
            padding: 12px 28px;
            border-radius: 8px;
            text-decoration: none;
            font-size: 15px;
            transition: background 0.3s;
        }

        .action-btn:hover { background: #0a2f2c; }

        .main-content-wrapper {
             /* Ajoute une marge pour que le contenu ne se cache pas sous la navbar (si elle était fixe) */
             padding: 20px 60px; 
        }
        
        @media (max-width: 768px) {
            .navbar { padding: 20px 30px; }
            .nav-links { display: none; }
            .main-content-wrapper { padding: 20px 30px; }
        }
    </style>
</head>
<body>
    <nav class="navbar">
        <a href="index.php" class="logo">
            <div class="logo-icon">P</div>
            <span>Portail Citoyen</span>
        </a>
        <ul class="nav-links">
            <li><a href="index.php">Accueil</a></li>
            <li><a href="frontoffice_index.php?action=create_report">Créer un Signalement</a></li>
            <?php if ($current_report_id > 0): ?>
                <li><a href="frontoffice_index.php?id=<?php echo $current_report_id; ?>">Ce Signalement</a></li>
            <?php endif; ?>
            <li><a href="frontoffice_index.php?action=about">À Propos</a></li>
        </ul>
        <a href="backoffice_index.php" class="action-btn">Accès Admin</a> 
    </nav>
    
    <div class="main-content-wrapper"></div>