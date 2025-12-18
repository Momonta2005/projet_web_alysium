# Alysium - Module Solution & Évaluation

## Description du Projet
**ALYSIUM : De la Plainte à la Résolution**

Qu'est-ce qu'un signalement sans solution viable ? Une simple plainte qui résonne dans le vide. Ce constat a guidé le développement du module **Solution & Évaluation** au sein de la plateforme Alysium. Ce système est conçu pour faire pivoter l'approche de la gestion urbaine : au lieu de se contenter d'identifier les dégradations, nous ingénions leur réparation.

Ce module crée une place publique numérique où les signalements citoyens évoluent vers des résolutions structurées et portées par la communauté. En intégrant une **IA avancée (Gemini)** pour affiner les propositions et traduire les échanges, ainsi qu'une matrice d'évaluation robuste pour un consensus démocratique, nous transformons les résidents passifs en architectes actifs de leur environnement.

**Fonctionnalités Principales :**
*   **Gestion des Solutions** : Soumission, modification et suppression de solutions pour les signalements.
*   **Système d'Évaluation** : Notation (étoiles) et commentaires pour faire émerger les meilleures idées.
*   **Intégration IA (Gemini)** : Assistance à la rédaction, recherche contextuelle et traduction automatique.
*   **Modération Automatisée** : Filtrage de contenu inapproprié.
*   **Export PDF** : Génération de rapports pour les démarches administratives.

## Table des Matières
1. [Installation](#installation)
2. [Utilisation](#utilisation)
3. [Contribution](#contribution)
4. [Licence](#licence)

## Installation

Pour installer et configurer le projet localement, suivez ces étapes :

1.  Clonez le repository :
    ```bash
    git clone https://github.com/Momonta2005/projet_web_alysium.git
    ```
2.  Configuration de l'environnement :
    *   Assurez-vous d'avoir **XAMPP** (ou un équivalent WAMP/MAMP) installé pour exécuter PHP et MySQL.
    *   Placez le dossier du projet dans votre répertoire web (ex: `C:\xampp\htdocs\partie_solution_evaluation`).
3.  Base de données :
    *   Importez le script SQL (si fourni) dans votre outil de gestion de base de données (phpMyAdmin).
    *   Configurez les accès dans `Model/config.php` si nécessaire.

## Utilisation

Ce projet est une application web PHP.

### Prérequis
*   **PHP 7.4+**
*   **Serveur MySQL**

### Accès
1.  Lancez votre serveur Apache et MySQL via XAMPP.
2.  Accédez à l'interface utilisateur via votre navigateur :
    *   **Front Office** (Citoyens) : `http://localhost/partie_solution_evaluation/frontoffice_index.php`
    *   **Back Office** (Administration) : `http://localhost/partie_solution_evaluation/backoffice_index.php`

### Fonctionnalités Clés
*   Utilisez le formulaire pour proposer une **Solution** à un problème donné.
*   Utilisez les étoiles pour **Évaluer** la pertinence d'une solution.
*   Interrogez **Gemini** via la barre de recherche pour obtenir des suggestions d'amélioration urbaine.

## Contribution

Les contributions sont les bienvenues ! Pour contribuer :

1.  Forkez le projet.
2.  Créez votre branche de fonctionnalité (`git checkout -b feature/AmazingFeature`).
3.  Commitez vos changements (`git commit -m 'Add some AmazingFeature'`).
4.  Pushez sur la branche (`git push origin feature/AmazingFeature`).
5.  Ouvrez une Pull Request.

## Licence

Ce projet est sous licence **MIT**. Cela signifie que vous êtes libre d'utiliser, de modifier et de distribuer ce code, à condition de conserver la notice de copyright et de licence.
