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
            $this->conn = new PDO('mysql:host='.dbconf::host.';dbname='.dbconf::db.'', dbconf::user, dbconf::password);
        } catch (PDOException $e) {
            print "Error!: " . $e->getMessage() . "<br/>";
            die();
        }
    }
    
    public function get_cost($timestamp) {
        $t = $this->get_hour($timestamp);
        $stmt = $this->conn->prepare('SELECT cost/10 as cost FROM power_cost WHERE time = FROM_UNIXTIME(:time)');
        $stmt->bindValue(':time',$t,PDO::PARAM_INT);
        $stmt->execute();
        
        if($stmt->rowCount() != 0) {
            $res = $stmt->fetch(PDO::FETCH_OBJ);
            return $this->get_real_price($res->cost,(1/24/4));
        } else {
            return 0;
        }  
    }
    
    public function get_avg_cost($start, $end){   
        $stmt = $this->conn->prepare('SELECT avg(cost)/10 AS av FROM power_cost WHERE time >= FROM_UNIXTIME(:start) AND time <= FROM_UNIXTIME(:end)');
        $stmt->bindValue(':start',$start,PDO::PARAM_INT);
        $stmt->bindValue(':end',$end,PDO::PARAM_INT);
        $stmt->execute();
        return $this->get_real_price($stmt->fetch(PDO::FETCH_OBJ)->av, 0);
    }
    
    public function get_cost_all($start, $end, $interval = 'm'){
        switch ($interval) {
            case 'm':
                $stmt = $this->conn->prepare('SELECT unix_timestamp(time) as time, (cost/10) as cost FROM power_cost WHERE time >= FROM_UNIXTIME(:start) AND time <= FROM_UNIXTIME(:end)');
                $duration = 1/24/4;
                break;
            case 'h':
                $stmt = $this->conn->prepare("SELECT str_to_date(concat(year(time),'.',month(time),'.',dayofmonth(time),' ',hour(time),':00',':00'),'%Y.%m.%d %H:%i:%s') AS timestamp_inteval, SUM(cost/10) AS cost, unix_timestamp(time) AS time
                                      FROM power_cost
                                      WHERE time >= FROM_UNIXTIME(:start) AND time <= FROM_UNIXTIME(:end)
                                      GROUP BY timestamp_inteval
                                      ORDER BY timestamp_inteval ASC");
                $duration = 1/24;
                break;
            case 'd':
                $stmt = $this->conn->prepare("SELECT str_to_date(concat(year(time),'.',month(time),'.',dayofmonth(time),' ','00',':00',':00'),'%Y.%m.%d %H:%i:%s') AS timestamp_inteval, SUM(cost/10) AS cost, unix_timestamp(time) AS time
                                      FROM power_cost
                                      WHERE time >= FROM_UNIXTIME(:start) AND time <= FROM_UNIXTIME(:end)
                                      GROUP BY timestamp_inteval
                                      ORDER BY timestamp_inteval ASC");
                $duration = 1;
                break;
            case 'w':
                $stmt = $this->conn->prepare("SELECT CONCAT(YEAR(time), '/', WEEK(time)) AS timestamp_inteval, SUM(cost/10) as cost, unix_timestamp(time)
                                        FROM power_cost
                                        WHERE time >= FROM_UNIXTIME(:start) AND time <= FROM_UNIXTIME(:end)
                                        GROUP BY timestamp_inteval
                                        ORDER BY YEAR(time) ASC, WEEK(time) ASC");
                $duration = 7;
                break;
            case 'mo':
                $stmt = $this->conn->prepare("SELECT CONCAT(YEAR(time), '/', MONTH(time)) AS timestamp_inteval, SUM(cost/10) as cost, unix_timestamp(time)
                                        FROM power_cost
                                        WHERE time >= FROM_UNIXTIME(:start) AND time <= FROM_UNIXTIME(:end)
                                        GROUP BY timestamp_inteval
                                        ORDER BY YEAR(time) ASC, MONTH(time) ASC");
                $duration = 30;
                break;
        }
        $stmt->bindValue(':start',$start,PDO::PARAM_INT);
        $stmt->bindValue(':end',$end,PDO::PARAM_INT);
        $stmt->execute();
        
        $data = [];
        while ($row = $stmt->fetch(PDO::FETCH_OBJ)) {
            $tmp = [];
            $tmp['time'] = (int) $row->time;
            $tmp['cost'] = (float) $this->get_real_price($row->cost,$duration);
            
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
    
    private function get_real_price($price,$duration) {
        return $price + ($this->config->add_pow_price) + ($this->config->add_pow_price_d*$duration);
    }
            
}
