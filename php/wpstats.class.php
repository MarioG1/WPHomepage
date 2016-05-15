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
        $stmt = $this->conn->prepare('SELECT SUM(power/1000) AS av FROM power_history WHERE time >= FROM_UNIXTIME(:start) AND time <= FROM_UNIXTIME(:end)');
        $stmt->bindValue(':start', $start, PDO::PARAM_INT);
        $stmt->bindValue(':end', $end, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_OBJ)->av;
    }
    
    public function get_cost($start, $end) {
        $pwcost = new pwcosts();
        
        $stmt = $this->conn->prepare("SELECT SUM(ph.power/1000) as power, SUM((pc.cost/10)*(ph.power/1000)) as cost FROM power_history ph
                                        LEFT JOIN power_cost pc ON str_to_date(concat(year(ph.time),'.',month(ph.time),'.',dayofmonth(ph.time),' ',hour(ph.time),':00',':00'),'%Y.%m.%d %H:%i:%s') = pc.time
                                        WHERE ph.time >= FROM_UNIXTIME(:start) AND ph.time <= FROM_UNIXTIME(:end)");
        $stmt->bindValue(':start', $start, PDO::PARAM_INT);
        $stmt->bindValue(':end', $end, PDO::PARAM_INT);
        $stmt->execute();
        $row = $stmt->fetchObject();
        
        $duration = ($end-$start)/86400;
        return $row->cost + $this->get_fees($duration, $row->power);
      
    }

 public function get_cost_all($start, $end, $interval='m') {
        $pwcost = new pwcosts();
        switch ($interval) {
            case 'm':
                $duration = 1/24/4;
                $stmt = $this->conn->prepare("SELECT ph.time as normal_time ,unix_timestamp(ph.time) as time, ph.power/1000 as power, (pc.cost/10)*(ph.power/1000) as cost FROM power_history ph
                                                LEFT JOIN power_cost pc ON str_to_date(concat(year(ph.time),'.',month(ph.time),'.',dayofmonth(ph.time),' ',hour(ph.time),':00',':00'),'%Y.%m.%d %H:%i:%s') = pc.time
                                                WHERE ph.time >= FROM_UNIXTIME(:start) AND ph.time <= FROM_UNIXTIME(:end)");
                break;
            case 'h':
                $duration = 1/24;
                $stmt = $this->conn->prepare("SELECT str_to_date(concat(year(ph.time),'.',month(ph.time),'.',dayofmonth(ph.time),' ',hour(ph.time),':00',':00'),'%Y.%m.%d %H:%i:%s') AS timestamp_inteval, unix_timestamp(ph.time) AS time, SUM(ph.power/1000) AS power, SUM((pc.cost/10)*(ph.power/1000)) as cost
                                                FROM power_history ph
                                                LEFT JOIN power_cost pc ON str_to_date(concat(year(ph.time),'.',month(ph.time),'.',dayofmonth(ph.time),' ',hour(ph.time),':00',':00'),'%Y.%m.%d %H:%i:%s') = pc.time
                                                WHERE ph.time >= FROM_UNIXTIME(:start) AND ph.time <= FROM_UNIXTIME(:end)
                                                GROUP BY timestamp_inteval
                                                ORDER BY timestamp_inteval ASC");
                break;
            case 'd':
                $duration = 1;
                $stmt = $this->conn->prepare("SELECT str_to_date(concat(year(ph.time),'.',month(ph.time),'.',dayofmonth(ph.time),' ','00',':00',':00'),'%Y.%m.%d %H:%i:%s') AS timestamp_inteval, unix_timestamp(ph.time) AS time, SUM(ph.power/1000) as power, SUM((pc.cost/10)*(ph.power/1000)) as cost
                                                FROM power_history ph
                                                LEFT JOIN power_cost pc ON str_to_date(concat(year(ph.time),'.',month(ph.time),'.',dayofmonth(ph.time),' ',hour(ph.time),':00',':00'),'%Y.%m.%d %H:%i:%s') = pc.time
                                                WHERE ph.time >= FROM_UNIXTIME(:start) AND ph.time <= FROM_UNIXTIME(:end)
                                                GROUP BY timestamp_inteval
                                                ORDER BY timestamp_inteval ASC");
                break;
            case 'w':
                $duration = 7;
                $stmt = $this->conn->prepare("SELECT CONCAT(YEAR(ph.time), '/', WEEK(ph.time)) AS timestamp_inteval, unix_timestamp(ph.time) AS time, SUM(ph.power/1000) as power, SUM((pc.cost/10)*(ph.power/1000)) as cost
                                                FROM power_history ph
                                                LEFT JOIN power_cost pc ON str_to_date(concat(year(ph.time),'.',month(ph.time),'.',dayofmonth(ph.time),' ',hour(ph.time),':00',':00'),'%Y.%m.%d %H:%i:%s') = pc.time
                                                WHERE ph.time >= FROM_UNIXTIME(:start) AND ph.time <= FROM_UNIXTIME(:end)
                                                GROUP BY timestamp_inteval
                                                ORDER BY timestamp_inteval ASC");
                break;
        }
     
        $stmt->bindValue(':start', $start, PDO::PARAM_INT);
        $stmt->bindValue(':end', $end, PDO::PARAM_INT);
        $stmt->execute();
        
        $data = [];
        while ($row = $stmt->fetch(PDO::FETCH_OBJ)) {
            $tmp = [];
            $tmp['time'] = (int) $row->time;
            $tmp['cost'] = (float) $row->cost;
            $tmp['fee'] = $this->get_fees($duration, $row->power);
            
            $data[] = (object) $tmp;
        }
        return $data;
    }
    
    public function get_pow_all($start, $end, $interval = 'm'){   
        switch ($interval) {
            case 'm':
                $stmt = $this->conn->prepare('SELECT unix_timestamp(time) as time, (power/1000) as power FROM power_history WHERE time > FROM_UNIXTIME(:start) AND time < FROM_UNIXTIME(:end)');
                break;
            case 'h':
                $stmt = $this->conn->prepare("SELECT str_to_date(concat(year(time),'.',month(time),'.',dayofmonth(time),' ',hour(time),':00',':00'),'%Y.%m.%d %H:%i:%s') AS timestamp_inteval, SUM(power/1000) AS power, unix_timestamp(time) AS time
                                      FROM power_history
                                      WHERE time >= FROM_UNIXTIME(:start) AND time <= FROM_UNIXTIME(:end)
                                      GROUP BY timestamp_inteval
                                      ORDER BY timestamp_inteval ASC");
                break;
            case 'd':
                $stmt = $this->conn->prepare("SELECT str_to_date(concat(year(time),'.',month(time),'.',dayofmonth(time),' ','00',':00',':00'),'%Y.%m.%d %H:%i:%s') AS timestamp_inteval, SUM(power/1000) AS power, unix_timestamp(time) AS time
                                      FROM power_history
                                      WHERE time >= FROM_UNIXTIME(:start) AND time <= FROM_UNIXTIME(:end)
                                      GROUP BY timestamp_inteval
                                      ORDER BY timestamp_inteval ASC");
                break;
            case 'w':
                $stmt = $this->conn->prepare("SELECT CONCAT(YEAR(time), '/', WEEK(time)) AS timestamp_inteval, SUM(power/1000) AS power, unix_timestamp(time)
                                        FROM power_history
                                        WHERE time >= FROM_UNIXTIME(:start) AND time <= FROM_UNIXTIME(:end)
                                        GROUP BY timestamp_inteval
                                        ORDER BY YEAR(time) ASC, WEEK(time) ASC");
                break;
        }
        
        $stmt->bindValue(':start', $start, PDO::PARAM_INT);
        $stmt->bindValue(':end', $end, PDO::PARAM_INT);
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
        $stmt = $this->conn->prepare('SELECT COUNT(*) AS num FROM power_history WHERE power >= :power AND time <= FROM_UNIXTIME(:start) AND time < FROM_UNIXTIME(:end)');
        $power = $this->config->pow_running;
        $stmt->bindValue(':power', $power, PDO::PARAM_INT);
        $stmt->bindValue(':start', $start, PDO::PARAM_INT);
        $stmt->bindValue(':end', $end, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_OBJ)->num * 15;
    }
    
    private function get_fees($duration, $pow_used) {
        return (float) ($this->config->add_pow_price)*$pow_used + ($this->config->add_pow_price_d*$duration);
    }

}
