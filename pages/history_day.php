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

$today_00 = new DateTime("$d-$m-$y 00:00");
$today_24 = new DateTime("$d-$m-$y 23:59");

$yesterday_00 = new DateTime("$d-$m-$y 00:00");
$yesterday_00->modify('-1 Day');

$yesterday_24 = new DateTime("$d-$m-$y 23:59");
$yesterday_24->modify('-1 Day');

$yesterday_act = new DateTime();
$yesterday_act->modify('-1 Day');
        
?>

<div id="page-wrapper">
    <div class="row">
        <div class="col-lg-12">
            <h1 class="page-header">Stromverbrauch Heute</h1>
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
                            <div class="huge"><?php echo round($wphist->get_cost($today_00->getTimestamp(), $today_24->getTimestamp()),4)?></div>
                            <div><?php
                                $cost_today = round($wphist->get_cost($today_00->getTimestamp(), $today_24->getTimestamp()),4);
                                $cost_yesterday = round($wphist->get_cost($yesterday_00->getTimestamp(), $yesterday_act->getTimestamp()),4);
                                
                                if(($cost_today - $cost_yesterday) > 0){
                                    echo '<b style="color: #FF0000;">';
                                    echo '+';   
                                } else {
                                    echo '<b style="color: #00FF00;">';
                                }
                                echo $cost_today - $cost_yesterday;
                                ?></b></div>
                            <div>Kosten Heute </div>
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
                            <div class="huge"><?php echo round($wphist->get_pow($today_00->getTimestamp(), $today_24->getTimestamp())/1000,3)?>kWh</div>
                            <div><?php
                                $pow_today = round($wphist->get_pow($today_00->getTimestamp(), $today_24->getTimestamp())/1000,3);
                                $pow_yesterday = round($wphist->get_pow($yesterday_00->getTimestamp(), $yesterday_act->getTimestamp())/1000,3);
                                
                                if(($pow_today - $pow_yesterday) > 0){
                                    echo '<b style="color: #FF0000;">';
                                    echo '+';   
                                } else {
                                    echo '<b style="color: #00FF00;">';
                                }
                                echo $pow_today - $pow_yesterday;
                                echo "kWh";
                                ?></b></div>
                            <div>Stromverbrauch Heute</div>
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
                            <div class="huge"><?php echo get_hm($wphist->get_runtime($today_00->getTimestamp(), $today_24->getTimestamp()),3)?></div>
                            <div><?php
                                $run_today = round($wphist->get_runtime($today_00->getTimestamp(), $today_24->getTimestamp()),3);
                                $run_yesterday = round($wphist->get_runtime($yesterday_00->getTimestamp(), $yesterday_act->getTimestamp()),3);
                                
                                if(($run_today - $run_yesterday) > 0){
                                    echo '<b style="color: #FF0000;">';
                                    echo '+';   
                                } else {
                                    echo '<b style="color: #00FF00;">';
                                    echo '-'; 
                                }
                                echo get_hm(abs($run_today - $run_yesterday));
                                ?></b></div>
                            <div>Laufzeit Heute</div>
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
                            <div class="huge"><?php echo round($pwcost->get_avg_cost($today_00->getTimestamp(), $today_24->getTimestamp()),4)?></div>
                            <div><?php
                                $price_today = round($pwcost->get_avg_cost($today_00->getTimestamp(), $today_24->getTimestamp()),4);
                                $price_yesterday = round($pwcost->get_avg_cost($yesterday_00->getTimestamp(), $yesterday_24->getTimestamp()),4);
                                
                                if(($price_today - $price_yesterday) > 0){
                                    echo '<b style="color: #FF0000;">';
                                    echo '+';   
                                } else {
                                    echo '<b style="color: #00FF00;">';
                                }
                                echo $price_today - $price_yesterday;
                                ?></b></div>
                            <div>&#216; Strompreis Heute</div>
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
                    <i class="fa fa-bar-chart-o fa-fw"></i> Stromverbrauch Heute/Gestern
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
                    <i class="fa fa-bar-chart-o fa-fw"></i> Stromkosten Heute/Gestern
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

