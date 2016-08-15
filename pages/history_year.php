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

$act = new DateTime();

$ty_first = new DateTime("1-1-$y 00:00");
#echo "  tm_first: " . $tm_first->format('Y-m-d H:i:s');

$ty_last = new DateTime("31-12-$y 23:59");
        
$ly_first = new DateTime("1-1-".($y-1)." 00:00");

$ly_last = new DateTime("31-12-".($y-1)." 23:59");

$today_ly = new DateTime();
$today_ly->modify('-1 year');


?>

<div id="page-wrapper">
    <div class="row">
        <div class="col-lg-12">
            <h1 class="page-header">Stromverbrauch dieses Jahr</h1>
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
                            <div class="huge"><?php echo  number_format($wphist->get_cost($ty_first->getTimestamp(), $act->getTimestamp())/100,4,',','.')?></div>
                            <div><?php
                                $cost_ty = $wphist->get_cost($ty_first->getTimestamp(), $act->getTimestamp())/100;
                                $cost_ly = $wphist->get_cost($ly_first->getTimestamp(), $today_ly->getTimestamp())/100;
                                
                                if(($cost_ty - $cost_ly) > 0){
                                    echo '<b style="color: #FF0000;">';
                                    echo '+';   
                                } else {
                                    echo '<b style="color: #00FF00;">';
                                }
                                echo  number_format($cost_ty - $cost_ly,4,',','.');
                                ?></b></div>
                            <div>Kosten dieses Jahr</div>
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
                            <div class="huge"><?php echo  number_format($wphist->get_pow($ty_first->getTimestamp(), $ty_last->getTimestamp()),2,',','.')?>kWh</div>
                            <div><?php
                                $pow_ty = $wphist->get_pow($ty_first->getTimestamp(), $ty_last->getTimestamp());
                                $pow_ly = $wphist->get_pow($ly_first->getTimestamp(), $today_ly->getTimestamp());  
                                
                                if(($pow_ty - $pow_ly) > 0){
                                    echo '<b style="color: #FF0000;">';
                                    echo '+';   
                                } else {
                                    echo '<b style="color: #00FF00;">';
                                }
                                echo number_format($pow_ty - $pow_ly,2,',','.');
                                echo "kWh";
                                ?></b></div>
                            <div>Stromverbrauch dieses Jahr</div>
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
                            <div class="huge"><?php echo get_hm($wphist->get_runtime($ty_first->getTimestamp(), $ty_last->getTimestamp()),3)?></div>
                            <div><?php
                                $run_ty = $wphist->get_runtime($ty_first->getTimestamp(), $ty_last->getTimestamp());
                                $run_ly = $wphist->get_runtime($ly_first->getTimestamp(), $today_ly->getTimestamp());
                                
                                if(($run_ty - $run_ly) > 0){
                                    echo '<b style="color: #FF0000;">';
                                    echo '+';   
                                } else {
                                    echo '<b style="color: #00FF00;">';
                                    echo '-'; 
                                }
                                echo get_hm(abs($run_ty - $run_ly));
                                ?></b></div>
                            <div>Laufzeit dieses Jahr</div>
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
                            <div class="huge"><?php echo number_format($pwcost->get_avg_cost($ty_first->getTimestamp(), $ty_last->getTimestamp())/100,4,',','.')?></div>
                            <div><?php
                                $price_ty = $pwcost->get_avg_cost($ty_first->getTimestamp(), $ty_last->getTimestamp())/100;
                                $price_ly = $pwcost->get_avg_cost($ly_first->getTimestamp(), $today_ly->getTimestamp())/100;
                                
                                if(($price_ty - $price_ly) > 0){
                                    echo '<b style="color: #FF0000;">';
                                    echo '+';   
                                } else {
                                    echo '<b style="color: #00FF00;">';
                                }
                                echo number_format($price_ty - $price_ly,4,',','.');
                                ?></b></div>
                            <div>&#216; Strompreis dieses Jahr</div>
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