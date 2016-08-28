<?php

include_once 'dbconf.class.php';

/*
 * Copyright (C) 2016 Gallaun Mario mario.gallaun@gmx.at
 */

/**
 * Description of config
 *
 * @author mario
 */

class config {

    private $conn;
    public $v;

    function __construct() {
        try {
            $this->conn = new PDO('mysql:host='.dbconf::host.';dbname='.dbconf::db.'', dbconf::user, dbconf::password);
        } catch (PDOException $e) {
            print "Error!: " . $e->getMessage() . "<br/>";
            die();
        }
    }
    
    public function load_config() {
        $tmp = [];
        
        $stmt = $this->conn->prepare('SELECT * FROM settings');
        $stmt->execute();
        $res = $stmt->fetchAll();
        
        foreach($res as $col){
            $tmp[$col['name']] = $col['value'];
        }
        
        $this->v = (object)$tmp;
    }
    
    public function save_config($config) {
        foreach($config as $c) {
            $stmt = $this->conn->prepare('UPDATE settings SET value = :value WHERE name = :name');
            $stmt->bindValue(':value',$c['value']);
            $stmt->bindValue(':name',$c['name']);
            $stmt->execute();
        }
    }

}
