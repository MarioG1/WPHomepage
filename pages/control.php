<?php
    include_once 'php/task.class.php';
    $task = new task();
    $legi = $task->load('legionellen');
?>

<script type="text/javascript">
    $(document).ready(function() {
        $('#week').multiselect();
        $('#day').multiselect();
        $('#time').multiselect();
    });
</script>

<div id="page-wrapper">
    <div class="row">
        <div class="col-lg-12">
            <h1 class="page-header">Steuerung</h1>
        </div>
        <!-- /.col-lg-12 -->
    </div>
    <div class="row">
        <div class="col-lg-6">
            <div class="panel panel-default">
                <div class="panel-heading">
                    <span class="glyphicon glyphicon-cog" aria-hidden="true"></span> Legionellenschutz Einstellungen
                </div>
                <div class="panel-body">
                    <form class="form-horizontal" action="index.php?page=contr_leg" method="post">
                        <div class="form-group">
                            <label class="col-sm-4 control-label">Intervall (Woche)</label>
                            <div class="col-sm-8">
                                <select id="week" multiple="multiple" name="week[]" style="width: 100%">
                                    <?php
                                        $week = $legi->run_week_of_month;
                                        for($i=1;$i<=5;$i++) {
                                            if(strpos($week, (string)$i) !== false) {
                                                echo '<option value="'.$i.'" selected="selected">Woche '.$i.'</option>';
                                            } else {
                                                echo '<option value="'.$i.'">Woche '.$i.'</option>';
                                            }
                                        }
                                    ?>
                                </select>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-sm-4 control-label">Wochentag</label>
                            <div class="col-sm-8">
                                <select id="day" multiple="multiple" name="day[]" style="width: 100%">
                                    <?php
                                        $days = ['Sonntag','Montag','Dienstag','Mittwoch','Donnerstag','Freitag','Samstag'];
                                        $day = $legi->run_day_of_week;
                                        for($i=0;$i<=6;$i++) {
                                            if(strpos($day, (string)$i) !== false) {
                                                echo '<option value="'.$i.'" selected="selected">'.$days[$i].'</option>';
                                            } else {
                                                echo '<option value="'.$i.'">'.$days[$i].'</option>';
                                            }
                                        }
                                    ?>
                                </select>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-sm-4 control-label">Startzeit</label>
                            <div class="col-sm-8">
                                <select id="time" name="hour[]"style="width: 100%">
                                    <?php
                                        $hour = $legi->run_hour;
                                        for($i=-1;$i<=24;$i++) {
                                            $hour_arr = explode(',', $hour);
                                            if(in_array($i, $hour_arr)) {
                                                if($i == -1) {
                                                    echo '<option value="'.$i.'" selected="selected">Auto</option>';
                                                } else {
                                                    echo '<option value="'.$i.'" selected="selected">'.sprintf('%02d', $i).':00</option>';
                                                }
                                            } else {
                                                if($i == -1) {
                                                    echo '<option value="'.$i.'">Auto</option>';
                                                } else {
                                                    echo '<option value="'.$i.'">'.sprintf('%02d', $i).':00</option>';
                                                }
                                            }
                                        }
                                    ?>
                                </select>
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="col-sm-offset-10 col-sm-2">
                                <button type="submit" class="btn btn-info" name="save_leg">Speichern</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
