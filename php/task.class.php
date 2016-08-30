<?php

/*
 * Copyright (C) 2016 Gallaun Mario mario.gallaun@gmx.at
 */

/**
 * Description of task
 * Represents a Timed Task
 * @author mario
 */
class task {
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
     * Lädt eien Task aus der Datenbank
     * @param string $name Name
     * @return type
     */
    public function load($name) {
        $stmt = $this->conn->prepare('SELECT * FROM task WHERE name = :name');
        $stmt->bindParam(':name', $name, PDO::PARAM_STR);
        $stmt->execute();
        return $stmt->fetchObject();
    }
    
    /**
     * Speichert einen neuen Task
     * @param string $name Name des Task
     * @param array $week In welchen Wochen (Monat) soll der Task ausgeführt werden
     * @param array $day An welchen Wochentagen soll der Task ausgeführt werden
     * @param array $hour Zu welchen Stunden soll der Task ausgeführt werden
     * @param int $max_run_time Maximale Laufzeit des Tasks
     */
    public function save($name, $week, $day, $hour, $max_run_time=0) {
        if(!$name) {
            return false;
        }
        
        echo $name;
        
        $s_week = implode(',', $week);
        $s_day = implode(',', $day);
        $s_hour = implode(',', $hour);
        
        $stmt = $this->conn->prepare('INSERT INTO task (name, run_week_of_month, run_day_of_week, run_hour, max_run_time) '
                                    . 'VALUES (:name, :week, :day, :hour, :max_run_time) '
                                    . 'ON DUPLICATE KEY UPDATE run_week_of_month = :week, run_day_of_week = :day, run_hour = :hour, max_run_time = :max_run_time');
        $stmt->bindParam(':name', $name, PDO::PARAM_STR);
        $stmt->bindParam(':week', $s_week, PDO::PARAM_STR);
        $stmt->bindParam(':day', $s_day, PDO::PARAM_STR);
        $stmt->bindParam(':hour', $s_hour, PDO::PARAM_STR);
        $stmt->bindParam(':max_run_time', $max_run_time, PDO::PARAM_INT);
        return $stmt->execute();
    }
}
