<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Choix de l'Interface</title>
    <style>
        body { font-family: Arial, sans-serif; display: flex; justify-content: center; align-items: center; height: 100vh; margin: 0; background-color: #f4f4f9; }
        .container { text-align: center; background-color: white; padding: 40px; border-radius: 10px; box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1); }
        h1 { color: #333; margin-bottom: 30px; }
        .options { display: flex; gap: 30px; }
        .option-box { 
            text-decoration: none; 
            padding: 20px 30px; 
            border-radius: 8px; 
            transition: background-color 0.3s, transform 0.2s;
            flex-grow: 1;
        }
        .front-office { background-color: #007bff; color: white; }
        .back-office { background-color: #28a745; color: white; }
        .option-box:hover { transform: translateY(-5px); box-shadow: 0 6px 15px rgba(0, 0, 0, 0.15); }
        .icon { font-size: 40px; margin-bottom: 10px; display: block; }
    </style>
</head>
<body>
    <div class="container">
        <h1>Bienvenue sur l'Application de Signalement</h1>
        <div class="options">
            <a href="frontoffice_index.php" class="option-box front-office">
                <span class="icon">🏠</span>
                Interface Citoyenne (Front-Office)
            </a>
            <a href="backoffice_index.php" class="option-box back-office">
                <span class="icon">🔒</span>
                Administration (Back-Office)
            </a>
        </div>
    </div>
</body>
</html>