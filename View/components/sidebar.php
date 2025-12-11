<?php
// Fichier : View/components/sidebar.php
// Déterminer l'action actuelle pour gérer l'état actif des boutons
$current_action = $_REQUEST['action'] ?? 'list_solutions';
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Administration | Solutions</title>
<style>
/* General reset & fonts */
* { margin:0; padding:0; box-sizing:border-box; }
body { font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, Cantarell, sans-serif; transition: background-color 0.3s ease; }

/* Light/Dark mode background */
body.light-mode { background-color: #9D4EDD; }
body.dark-mode { background-color: #0a0a0a; }

/* Sidebar */
.sidebar {
    position: fixed; left:0; top:0; height:100vh; width:70px;
    display:flex; flex-direction:column; align-items:center; padding:20px 0;
    transition: background-color 0.3s ease;
}
body.light-mode .sidebar { background-color:#ffffff; box-shadow:2px 0 10px rgba(0,0,0,0.05); }
body.dark-mode .sidebar { background-color:#1a1a1a; box-shadow:2px 0 10px rgba(0,0,0,0.3); }

/* Logo */
.logo {
    width:40px; height:40px; border-radius:50%; display:flex; align-items:center; justify-content:center;
    font-weight:bold; font-size:20px; margin-bottom:40px; transition: background-color 0.3s ease, color 0.3s ease;
}
body.light-mode .logo { background-color:#000; color:#fff; }
body.dark-mode .logo { background-color:#fff; color:#000; }

/* Navigation items */
.nav-items { flex:1; display:flex; flex-direction:column; gap:25px; }
.nav-item {
    width:40px; height:40px; display:flex; align-items:center; justify-content:center; border-radius:10px;
    cursor:pointer; transition:all 0.3s ease; position:relative; text-decoration:none;
}
body.light-mode .nav-item { color:#666; }
body.dark-mode .nav-item { color:#888; }
.nav-item:hover { transform:scale(1.1); }
body.light-mode .nav-item:hover { background-color:#f0f0f0; color:#000; }
body.dark-mode .nav-item:hover { background-color:#2a2a2a; color:#fff; }

/* ACTIVE STATE LOGIC */
.nav-item.active { transform:scale(1); }
body.light-mode .nav-item.active { background-color:#000; color:#fff; }
body.dark-mode .nav-item.active { background-color:#fff; color:#000; }
.nav-item svg { width:20px; height:20px; }

/* Theme toggle */
.theme-toggle {
    width:40px; height:40px; border-radius:10px; display:flex; align-items:center; justify-content:center;
    cursor:pointer; transition:all 0.3s ease; margin-top:auto;
}
body.light-mode .theme-toggle { background-color:#f0f0f0; color:#000; }
body.dark-mode .theme-toggle { background-color:#2a2a2a; color:#fff; }
.theme-toggle:hover { transform:scale(1.1); }
.theme-toggle svg { width:20px; height:20px; }

/* Add button */
.add-button {
    width:40px; height:40px; border-radius:10px; display:flex; align-items:center; justify-content:center;
    cursor:pointer; transition:all 0.3s ease; margin-bottom:20px; font-size:24px; font-weight:300;
}
body.light-mode .add-button { background-color:#f0f0f0; color:#000; }
body.dark-mode .add-button { background-color:#2a2a2a; color:#fff; }
.add-button:hover { transform:scale(1.1); }

/* Content area */
.content { margin-left:70px; padding:40px; }
body.light-mode .content { color:#000; }
body.dark-mode .content { color:#fff; }

/* Styles spécifiques au tableau pour s'intégrer au thème */
.content table { width: 100%; border-collapse: collapse; margin-top: 20px; border-radius: 8px; overflow: hidden; }
.content th, .content td { padding: 15px; text-align: left; vertical-align: top; }
body.light-mode .content th { background-color: #e9ecef; color: #333; }
body.dark-mode .content th { background-color: #2a2a2a; color: #f0f0f0; }
body.light-mode .content tr:nth-child(even) { background-color: #ffffff; }
body.light-mode .content tr:nth-child(odd) { background-color: #f8f8f8; }
body.dark-mode .content tr:nth-child(even) { background-color: #1f1f1f; }
body.dark-mode .content tr:nth-child(odd) { background-color: #1a1a1a; }
.content table { border: none; }
.content th, .content td { border-bottom: 1px solid rgba(120, 120, 120, 0.2); }

.btn-delete { 
    background-color: #dc3545; color: white; border: none; 
    padding: 8px 12px; border-radius: 6px; cursor: pointer; 
    transition: background-color 0.3s ease;
}
.btn-delete:hover { background-color: #c82333; }

</style>
</head>
<body class="dark-mode">
<div class="sidebar">
    <div class="logo">S</div>

    <div class="nav-items">
        
        <a href="backoffice_index.php?action=list_solutions" 
           class="nav-item <?php echo ($current_action === 'list_solutions' || $current_action === 'default') ? 'active' : ''; ?>" 
           title="Solutions">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M20 13V7a2 2 0 00-2-2H6a2 2 0 00-2 2v6m16 0l-8 8-8-8" />
            </svg>
        </a>

        <a href="backoffice_index.php?action=list_evaluations" 
           class="nav-item <?php echo ($current_action === 'list_evaluations') ? 'active' : ''; ?>" 
           title="Évaluations">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13l-1.2 6h11.4M7 13H5.4M16 21a1 1 0 11-2 0 1 1 0 012 0zM8 21a1 1 0 11-2 0 1 1 0 012 0z" />
            </svg>
        </a>

        <a href="backoffice_index.php?action=list_flags" 
           class="nav-item <?php echo ($current_action === 'list_flags') ? 'active' : ''; ?>" 
           title="Contenus modérés">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M9.75 4.75l4.5-1.5a1 1 0 011.25.96v12.58a1 1 0 01-1.34.94l-4.5-1.5a1 1 0 00-.64 0l-4.5 1.5A1 1 0 013 16.79V4.71a1 1 0 011.25-.96l4.5 1.5a1 1 0 00.64 0z"/>
            </svg>
        </a>

        <div class="nav-item" title="Settings">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
            </svg>
        </div>
    </div>

    <div class="add-button" title="Add" onclick="window.location.href='backoffice_index.php?action=add_solution';">+</div>

    <div class="theme-toggle" onclick="toggleTheme()" title="Toggle theme">
        <svg class="sun-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="display:none;">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z"/>
        </svg>
        <svg class="moon-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"/>
        </svg>
    </div>
</div>

<div class="content">