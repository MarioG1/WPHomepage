<?php

/*
 * Copyright (C) 2016 Gallaun Mario mario.gallaun@gmx.at
 */

include_once 'config.class.php';

/**
 * Description of pwcosts
 *
 * @author mario
 */
class pwcosts {
    private $conn;
    private $config;
    
    function __construct() {
        $config = new config();
        $config->load_config();
        
        $this->config = $config->v;
        
        try {
            $this->conn = new PDO('mysql:host=localhost;dbname=waermepumpe', 'wp_user', 'Ikikulopi485');
        } catch (PDOException $e) {
            print "Error!: " . $e->getMessage() . "<br/>";
            die();
        }
    }
    
    public function get_cost($timestamp) {
        $t = $this->get_hour($timestamp);
        $stmt = $this->conn->prepare('SELECT cost FROM power_cost WHERE time = FROM_UNIXTIME(:time)');
        $stmt->bindValue(':time',$t,PDO::PARAM_INT);
        $stmt->execute();
        
        if($stmt->rowCount() != 0) {
            $res = $stmt->fetch(PDO::FETCH_OBJ);
            return $this->get_real_price($res->cost);
        } else {
            return 0;
        }  
    }
    
    public function get_avg_cost($start, $end){   
        $stmt = $this->conn->prepare('SELECT avg(cost) AS av FROM power_cost WHERE time > FROM_UNIXTIME(:start) AND time < FROM_UNIXTIME(:end)');
        $stmt->bindValue(':start',$start,PDO::PARAM_INT);
        $stmt->bindValue(':end',$end,PDO::PARAM_INT);
        $stmt->execute();
        return $this->get_real_price($stmt->fetch(PDO::FETCH_OBJ)->av);
    }
    
    public function get_cost_all($start, $end){   
        $stmt = $this->conn->prepare('SELECT unix_timestamp(time) as time, cost FROM power_cost WHERE time > FROM_UNIXTIME(:start) AND time < FROM_UNIXTIME(:end)');
        $stmt->bindValue(':start',$start,PDO::PARAM_INT);
        $stmt->bindValue(':end',$end,PDO::PARAM_INT);
        $stmt->execute();
        
        $data = [];
        while ($row = $stmt->fetch(PDO::FETCH_OBJ)) {
            $tmp = [];
            $tmp['time'] = (int) $row->time;
            $tmp['cost'] = (float) $this->get_real_price($row->cost);
            
            $data[] = (object) $tmp;
        }
        return $data;
    }
    
    private function get_hour($timestamp) {
        $y = date('Y',$timestamp);
        $m = date('m',$timestamp);
        $d = date('d',$timestamp);
        $h = date('H',$timestamp);
        return strtotime("$d-$m-$y $h:00"); 
    }
    
    private function get_real_price($price) {
        return $price/1000 + ($this->config->add_pow_price/100);
    }
            
}
