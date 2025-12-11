<?php
// Fichier : Model/config.php

class Config {
    private static $pdo = null;
    private function __construct() {}

    public static function getConnexion(): PDO {
        if (!isset(self::$pdo)) {
            $servername = "localhost";
            $dbname = "signalementt"; // Nom de votre base de données
            $username = "root";
            $password = "";
            $dsn = "mysql:host=$servername;dbname=$dbname;charset=utf8mb4";
            
            try {
                self::$pdo = new PDO($dsn, $username, $password, [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    PDO::ATTR_EMULATE_PREPARES => false, 
                ]);
            } catch (\PDOException $e) {
                // En production, loguer l'erreur au lieu de la die()
                throw new \Exception("Erreur de connexion à la base de données : " . $e->getMessage());
            }
        }
        return self::$pdo;
    }

    public static function getGeminiKey(): string {
        return "AIzaSyAswkXpITaaxn2wjNFtPgdn49ltBrbA1mU";
    }
}