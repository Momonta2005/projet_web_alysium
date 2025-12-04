<?php
// Fichier : Model/Solution.php

class Solution {
    
    // Propriétés
    private ?int $id;
    private int $signalementId; // ID du signalement auquel cette solution est liée (clé étrangère)
    private string $description;

    /**
     * Constructeur
     * * @param int|null $id L'identifiant de la solution (null si nouvelle)
     * @param int $signalementId L'identifiant du signalement associé
     * @param string $description Le texte décrivant la solution proposée
     */
    public function __construct(?int $id, int $signalementId, string $description) {
        $this->id = $id;
        $this->signalementId = $signalementId;
        $this->description = $description;
    }

    // Getters

    public function getId(): ?int {
        return $this->id;
    }

    public function getSignalementId(): int {
        return $this->signalementId;
    }

    public function getDescription(): string {
        return $this->description;
    }
    
    // Setters (optionnels, omis pour la simplicité)
}