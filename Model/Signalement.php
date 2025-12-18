<?php 
class Signalement {
    private ?int $id_signalement; private string $titre; private string $description;
    public function __construct(?int $id_signalement, string $titre, string $description) {
        $this->id_signalement = $id_signalement; $this->titre = $titre; $this->description = $description;
    }
    public function getId(): ?int { return $this->id_signalement; }
    public function getTitre(): string { return $this->titre; }
    public function getDescription(): string { return $this->description; }
    public function setId(int $id_signalement): void { $this->id_signalement = $id_signalement; }
}
