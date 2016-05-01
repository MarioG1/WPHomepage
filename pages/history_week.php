<?php
include_once 'php/functions.php';
include_once 'php/pwcosts.class.php';
include_once 'php/wpstats.class.php';
$pwcost = new pwcosts();
$wphist = new wpstats();

$y = date('Y',time());
$m = date('m',time());
$d = date('d',time());

if(date('w',time()) == 0){
    $day_of_week = 6;
} else {
    $day_of_week = date('w',time())-1;
}
$h = date('H',time());
$min = date('i',time());

$monday_tw_00 = new DateTime("$d-$m-$y 00:00");
$monday_tw_00->modify("-".($day_of_week)." Day");

$sunday_tw_24 = new DateTime("$d-$m-$y 23:59");
$sunday_tw_24->modify("+".(6-$day_of_week)."Day");
        
$monday_lw_00 = clone $monday_tw_00;
$monday_lw_00->modify('-7 Day');

$sunday_lw_24 = clone $sunday_tw_24;
$sunday_lw_24->modify('-7 Day');

$today_lw = new DateTime();
$today_lw->modify('-7 Day');
?>

<div id="page-wrapper">
    <div class="row">
        <div class="col-lg-12">
            <h1 class="page-header">Stromverbrauch diese Woche</h1>
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
                            <div class="huge"><?php echo number_format($wphist->get_cost($monday_tw_00->getTimestamp(), $sunday_tw_24->getTimestamp()),4,',','.')?></div>
                            <div><?php
                                $cost_tw = $wphist->get_cost($monday_tw_00->getTimestamp(), $sunday_tw_24->getTimestamp());
                                $cost_lw = $wphist->get_cost($monday_lw_00->getTimestamp(), $today_lw->getTimestamp());
                                
                                if(($cost_tw - $cost_lw) > 0){
                                    echo '<b style="color: #FF0000;">';
                                    echo '+';   
                                } else {
                                    echo '<b style="color: #00FF00;">';
                                }
                                echo number_format($cost_tw - $cost_lw,4,',','.');
                                ?></b></div>
                            <div>Kosten diese Woche</div>
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
                            <div class="huge"><?php echo number_format($wphist->get_pow($monday_tw_00->getTimestamp(), $sunday_tw_24->getTimestamp())/1000,2,',','.')?>kWh</div>
                            <div><?php
                                $pow_tw = $wphist->get_pow($monday_tw_00->getTimestamp(), $sunday_tw_24->getTimestamp())/1000;
                                $pow_lw = $wphist->get_pow($monday_lw_00->getTimestamp(), $today_lw->getTimestamp())/1000;  
                                
                                if(($pow_tw - $pow_lw) > 0){
                                    echo '<b style="color: #FF0000;">';
                                    echo '+';   
                                } else {
                                    echo '<b style="color: #00FF00;">';
                                }
                                echo number_format($pow_tw - $pow_lw,2,',','.');
                                echo "kWh";
                                ?></b></div>
                            <div>Stromverbrauch diese Woche</div>
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
                            <div class="huge"><?php echo get_hm($wphist->get_runtime($monday_tw_00->getTimestamp(), $sunday_tw_24->getTimestamp()),3)?></div>
                            <div><?php
                                $run_tw = $wphist->get_runtime($monday_tw_00->getTimestamp(), $sunday_tw_24->getTimestamp());
                                $run_lw = $wphist->get_runtime($monday_lw_00->getTimestamp(), $today_lw->getTimestamp());
                                
                                if(($run_tw - $run_lw) > 0){
                                    echo '<b style="color: #FF0000;">';
                                    echo '+';   
                                } else {
                                    echo '<b style="color: #00FF00;">';
                                    echo '-'; 
                                }
                                echo get_hm(abs($run_tw - $run_lw));
                                ?></b></div>
                            <div>Laufzeit diese Woche</div>
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
                            <div class="huge"><?php echo number_format($pwcost->get_avg_cost($monday_tw_00->getTimestamp(), $sunday_tw_24->getTimestamp()),4,',','.')?></div>
                            <div><?php
                                $price_tw = $pwcost->get_avg_cost($monday_tw_00->getTimestamp(), $sunday_tw_24->getTimestamp());
                                $price_lw= $pwcost->get_avg_cost($monday_lw_00->getTimestamp(), $today_lw->getTimestamp());
                                
                                if(($price_tw - $price_lw) > 0){
                                    echo '<b style="color: #FF0000;">';
                                    echo '+';   
                                } else {
                                    echo '<b style="color: #00FF00;">';
                                }
                                echo number_format($price_tw - $price_lw,4,',','.');
                                ?></b></div>
                            <div>&#216; Strompreis diese Woche</div>
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
                    <i class="fa fa-bar-chart-o fa-fw"></i> Stromverbrauch diese Woche/letzte Woche
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
                    <i class="fa fa-bar-chart-o fa-fw"></i> Stromkosten diese Woche/letzte Woche
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