<?php

/*
 * Copyright (C) 2016 Gallaun Mario mario.gallaun@gmx.at
 */

/**
 * Description of wpstats
 *
 * @author mario
 */
class wpstats {
    private $conn;
    
    function __construct() {
        try {
            $this->conn = new PDO('mysql:host=localhost;dbname=waermepumpe', 'wp_user', 'Ikikulopi485');
        } catch (PDOException $e) {
            print "Error!: " . $e->getMessage() . "<br/>";
            die();
        }
    }
    
    
}
