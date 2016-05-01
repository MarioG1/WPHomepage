<?php
include_once 'php/functions.php';
include_once 'php/pwcosts.class.php';
include_once 'php/wpstats.class.php';
$pwcost = new pwcosts();
$wphist = new wpstats();

$y = date('Y',time());
$m = date('m',time());
$d = date('d',time());

$h = date('H',time());
$min = date('i',time());

$tm_first = new DateTime("1-$m-$y 00:00");
#echo "  tm_first: " . $tm_first->format('Y-m-d H:i:s');

$tm_last = new DateTime("1-$m-$y 23:59");
$tm_last->modify("last day of this month");
#echo "  tm_last: " . $tm_last->format('Y-m-d H:i:s');
        
$lm_first = clone $tm_first;
$lm_first->modify("first day of last month");
#echo "  lm_first: " . $lm_first->format('Y-m-d H:i:s');

$lm_last = clone $tm_last;
$lm_last->modify("last day of last month");
#echo "  lm_last: " . $lm_last->format('Y-m-d H:i:s');

$today_lm = new DateTime();
$today_lm->modify('-1 Month');
if($today_lm->format('m') != date('m',time())-1) {
   $today_lm = clone $lm_last; 
}
#echo "  today_lm: " . $today_lm->format('Y-m-d H:i:s');


?>

<div id="page-wrapper">
    <div class="row">
        <div class="col-lg-12">
            <h1 class="page-header">Stromverbrauch dieser Monat</h1>
        </div>
        <!-- /.col-lg-12 -->
    </div>
    <!-- /.row -->
    <div class="row">
        <div class="col-lg-3 col-md-6">
            <div class="panel panel-primary">
                <div class="panel-heading">
                    <div class="row">
                        <div class="col-xs-3">
                            <span class="glyphicon glyphicon-euro fa-5x" aria-hidden="true"></span>
                        </div>
                        <div class="col-xs-9 text-right">
                            <div class="huge"><?php echo  number_format($wphist->get_cost($tm_first->getTimestamp(), $tm_last->getTimestamp()),4,',','.')?></div>
                            <div><?php
                                $cost_tw = $wphist->get_cost($tm_first->getTimestamp(), $tm_last->getTimestamp());
                                $cost_lw = $wphist->get_cost($lm_first->getTimestamp(), $today_lm->getTimestamp());
                                
                                if(($cost_tw - $cost_lw) > 0){
                                    echo '<b style="color: #FF0000;">';
                                    echo '+';   
                                } else {
                                    echo '<b style="color: #00FF00;">';
                                }
                                echo  number_format($cost_tw - $cost_lw,4,',','.');
                                ?></b></div>
                            <div>Kosten dieser Monat</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6">
            <div class="panel panel-green">
                <div class="panel-heading">
                    <div class="row">
                        <div class="col-xs-3">
                            <i class="fa fa-tasks fa-5x"></i>
                        </div>
                        <div class="col-xs-9 text-right">
                            <div class="huge"><?php echo  number_format($wphist->get_pow($tm_first->getTimestamp(), $tm_last->getTimestamp())/1000,2,',','.')?>kWh</div>
                            <div><?php
                                $pow_tw = $wphist->get_pow($tm_first->getTimestamp(), $tm_last->getTimestamp())/1000;
                                $pow_lw = $wphist->get_pow($lm_first->getTimestamp(), $today_lm->getTimestamp())/1000;  
                                
                                if(($pow_tw - $pow_lw) > 0){
                                    echo '<b style="color: #FF0000;">';
                                    echo '+';   
                                } else {
                                    echo '<b style="color: #00FF00;">';
                                }
                                echo number_format($pow_tw - $pow_lw,2,',','.');
                                echo "kWh";
                                ?></b></div>
                            <div>Stromverbrauch dieser Monat</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6">
            <div class="panel panel-yellow">
                <div class="panel-heading">
                    <div class="row">
                        <div class="col-xs-3">
                            <i class="glyphicon glyphicon-time fa-5x"></i>
                        </div>
                        <div class="col-xs-9 text-right">
                            <div class="huge"><?php echo get_hm($wphist->get_runtime($tm_first->getTimestamp(), $tm_last->getTimestamp()),3)?></div>
                            <div><?php
                                $run_tw = $wphist->get_runtime($tm_first->getTimestamp(), $tm_last->getTimestamp());
                                $run_lw = $wphist->get_runtime($lm_first->getTimestamp(), $today_lm->getTimestamp());
                                
                                if(($run_tw - $run_lw) > 0){
                                    echo '<b style="color: #FF0000;">';
                                    echo '+';   
                                } else {
                                    echo '<b style="color: #00FF00;">';
                                    echo '-'; 
                                }
                                echo get_hm(abs($run_tw - $run_lw));
                                ?></b></div>
                            <div>Laufzeit dieser Monat</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6">
            <div class="panel panel-red">
                <div class="panel-heading">
                    <div class="row">
                        <div class="col-xs-3">
                            <i class="glyphicon glyphicon-flash fa-5x"></i>
                        </div>
                        <div class="col-xs-9 text-right">
                            <div class="huge"><?php echo number_format($pwcost->get_avg_cost($tm_first->getTimestamp(), $tm_last->getTimestamp()),4,',','.')?></div>
                            <div><?php
                                $price_tw = $pwcost->get_avg_cost($tm_first->getTimestamp(), $tm_last->getTimestamp());
                                $price_lw = $pwcost->get_avg_cost($lm_first->getTimestamp(), $today_lm->getTimestamp());
                                
                                if(($price_tw - $price_lw) > 0){
                                    echo '<b style="color: #FF0000;">';
                                    echo '+';   
                                } else {
                                    echo '<b style="color: #00FF00;">';
                                }
                                echo number_format($price_tw - $price_lw,4,',','.');
                                ?></b></div>
                            <div>&#216; Strompreis dieser Monat</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- /.row -->
    <div class="row">
        <div class="col-lg-6">
            <div class="panel panel-default">
                <div class="panel-heading">
                    <i class="fa fa-bar-chart-o fa-fw"></i> Stromverbrauch dieser Monat/letzte Monat
                </div>
                <!-- /.panel-heading -->
                <div class="panel-body">
                    <div id="chart_pow_usage" style="width: 100%; height: 300px;"></div>
                </div>
                <!-- /.panel-body -->
            </div>
            <!-- /.panel -->
        </div>
        <div class="col-lg-6">
            <div class="panel panel-default">
                <div class="panel-heading">
                    <i class="fa fa-bar-chart-o fa-fw"></i> Stromkosten dieserr Monat/letzte Monat
                </div>
                <!-- /.panel-heading -->
                <div class="panel-body">
                    <div id="chart_pow_cost" style="width: 100%; height: 300px;"></div>
                </div>
                <!-- /.panel-body -->
            </div>
            <!-- /.panel -->
        </div>
    </div>
</div>