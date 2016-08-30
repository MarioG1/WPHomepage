<?php

/*
 * Copyright (C) 2016 Gallaun Mario mario.gallaun@gmx.at
 */

/**
 * Description of status
 *
 * @author mario
 */
class status {
    private $conn;

    function __construct() {       
        try {
            $this->conn = new PDO('mysql:host='.dbconf::host.';dbname='.dbconf::db.'', dbconf::user, dbconf::password);
        } catch (PDOException $e) {
            print "Error!: " . $e->getMessage() . "<br/>";
            die();
        }
    }
    
    /**
     * Gibt den aktuellen Status zurück
     * @param string $name Name
     * @return type
     */
    public function get($name) {
        $stmt = $this->conn->prepare('SELECT value FROM status WHERE name = :name');
        $stmt->bindParam(':name', $name, PDO::PARAM_STR);
        $stmt->execute();
        return $stmt->fetchColumn();
    }
}
