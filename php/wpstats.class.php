<?php

include_once 'config.class.php';
include_once 'pwcosts.class.php';

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

    public function get_pow($start, $end) {
        $stmt = $this->conn->prepare('SELECT SUM(power) AS av FROM power_history WHERE time > FROM_UNIXTIME(:start) AND time < FROM_UNIXTIME(:end)');
        $stmt->bindValue(':start', $start, PDO::PARAM_INT);
        $stmt->bindValue(':end', $end, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_OBJ)->av;
    }
    
    public function get_cost($start, $end) {
        $pwcost = new pwcosts(); 
        
        $stmt = $this->conn->prepare("SELECT str_to_date(concat(year(time),'.',month(time),'.',dayofmonth(time),' ',hour(time),':00',':00'),'%Y.%m.%d %H:%i:%s') AS timestamp_inteval, SUM(power) AS power, unix_timestamp(time) AS time
                                      FROM power_history
                                      WHERE time > FROM_UNIXTIME(:start) AND time < FROM_UNIXTIME(:end)
                                      GROUP BY timestamp_inteval
                                      ORDER BY timestamp_inteval DESC");
        $stmt->bindValue(':start', $start, PDO::PARAM_INT);
        $stmt->bindValue(':end', $end, PDO::PARAM_INT);
        $stmt->execute();
        $rows =  $stmt->fetchAll();
        
        $sum_cost = 0;
        foreach ($rows AS $row){
            $time = $row['time'];
            $cost = $pwcost->get_cost($time);
            $sum_cost += $cost * $row['power']/1000;
        }
        return $sum_cost;
    }
    
    public function get_pow_all($start, $end){   
        $stmt = $this->conn->prepare('SELECT unix_timestamp(time) as time, power FROM power_history WHERE time > FROM_UNIXTIME(:start) AND time < FROM_UNIXTIME(:end)');
        $stmt->bindValue(':start',$start,PDO::PARAM_INT);
        $stmt->bindValue(':end',$end,PDO::PARAM_INT);
        $stmt->execute();
        
        $data = [];
        while ($row = $stmt->fetch(PDO::FETCH_OBJ)) {
            $tmp = [];
            $tmp['time'] = (int) $row->time;
            $tmp['pow'] = (float) $row->power;
            
            $data[] = (object) $tmp;
        }
        return $data;
    }
    
    public function get_runtime($start, $end) {
        $stmt = $this->conn->prepare('SELECT COUNT(*) AS num FROM power_history WHERE power > :power AND time > FROM_UNIXTIME(:start) AND time < FROM_UNIXTIME(:end)');
        $power = $this->config->pow_running;
        $stmt->bindValue(':power', $power, PDO::PARAM_INT);
        $stmt->bindValue(':start', $start, PDO::PARAM_INT);
        $stmt->bindValue(':end', $end, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_OBJ)->num * 15;
    }

}
