<?php
// Fichier : Model/Evaluation.php

class Evaluation {
    
    // Propriétés
    private ?int $id;
    private int $solutionId; // ID de la solution évaluée (clé étrangère)
    private string $stars;    // Représentation des étoiles (ex: "⭐⭐⭐")
    private string $comment;
    private string $author;   // Nom de l'utilisateur qui a évalué

    /**
     * Constructeur
     * * @param int|null $id L'identifiant de l'évaluation (null si nouvelle)
     * @param int $solutionId L'identifiant de la solution évaluée
     * @param string $stars La note (ex: étoiles)
     * @param string $comment Le commentaire associé à la note
     * @param string $author Le nom de l'auteur de l'évaluation
     */
    public function __construct(?int $id, int $solutionId, string $stars, string $comment, string $author) {
        $this->id = $id;
        $this->solutionId = $solutionId;
        $this->stars = $stars;
        $this->comment = $comment;
        $this->author = $author;
    }

    // Getters

    public function getId(): ?int {
        return $this->id;
    }

    public function getSolutionId(): int {
        return $this->solutionId;
    }

    public function getStars(): string {
        return $this->stars;
    }

    public function getComment(): string {
        return $this->comment;
    }
    
    public function getAuthor(): string {
        return $this->author;
    }
    
    // Setters (optionnels, omis pour la simplicité)
}