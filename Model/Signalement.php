<?php 
class Signalement {
    private ?int $id; private string $titre; private string $description;
    public function __construct(?int $id, string $titre, string $description) {
        $this->id = $id; $this->titre = $titre; $this->description = $description;
    }
    public function getId(): ?int { return $this->id; }
    public function getTitre(): string { return $this->titre; }
    public function getDescription(): string { return $this->description; }
    public function setId(int $id): void { $this->id = $id; }
}
