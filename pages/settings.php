<?php
include_once 'php/config.class.php';
$config = new config();
$config->load_config();
?>

<div id="page-wrapper">
    <div class="row">
        <div class="col-lg-12">
            <h1 class="page-header">Einstellungen</h1>
        </div>
        <!-- /.col-lg-12 -->
    </div>
    <div class="row">
        <div class="col-lg-6">
            <div class="panel panel-default">
                <div class="panel-heading">
                    <span class="glyphicon glyphicon-cog" aria-hidden="true"></span> Kollektor Einstellungen
                </div>
                <div class="panel-body">
                    <form class="form-horizontal" action="index.php?page=settings" method="post">
                        <div class="form-group">
                            <label class="col-sm-4 control-label">IP</label>
                            <div class="col-sm-8">
                                <input type="text" class="form-control" name="contorller_ip" value="<?php echo $config->v->contorller_ip ?>">
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-sm-4 control-label">Passwort</label>
                            <div class="col-sm-8">
                                <input type="text" class="form-control" name="controller_password" value="<?php echo $config->v->controller_password ?>">
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-sm-4 control-label">Abfrage Intervall [s]</label>
                            <div class="col-sm-8">
                                <input type="text" class="form-control" name="check_interval" value="<?php echo $config->v->check_interval ?>">
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-sm-4 control-label">Schwellwert WP läuft [Wh]</label>
                            <div class="col-sm-8">
                                <input type="text" class="form-control" name="pow_running" value="<?php echo $config->v->pow_running ?>">
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="col-sm-offset-10 col-sm-2">
                                <button type="submit" class="btn btn-info" name="save_1">Speichern</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <div class="col-lg-6">
            <div class="panel panel-default">
                <div class="panel-heading">
                    <span class="glyphicon glyphicon-cog" aria-hidden="true"></span> Awattar Einstellungen
                </div>
                <div class="panel-body">
                    <form class="form-horizontal" action="index.php?page=settings" method="post">
                        <div class="form-group">
                            <label class="col-sm-4 control-label">Url</label>
                            <div class="col-sm-8">
                                <input type="text" class="form-control" name="awattar_api_url" value="<?php echo $config->v->awattar_api_url ?>">
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-sm-4 control-label">Token</label>
                            <div class="col-sm-8">
                                <input type="text" class="form-control" name="awattar_api_token" value="<?php echo $config->v->awattar_api_token ?>">
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="col-sm-offset-10 col-sm-2">
                                <button type="submit" class="btn btn-info" name="save_2">Speichern</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-lg-6">
            <div class="panel panel-default">
                <div class="panel-heading">
                    <span class="glyphicon glyphicon-cog" aria-hidden="true"></span> Strompreis
                </div>
                <div class="panel-body">
                    <form class="form-horizontal" action="index.php?page=settings" method="post">
                        <div class="form-group">
                            <label class="col-sm-4 control-label">Zusätzliche Kosten [c/kWh]</label>
                            <div class="col-sm-8">
                                <input type="text" class="form-control" name="add_pow_price" value="<?php echo $config->v->add_pow_price ?>">
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-sm-4 control-label">Zusätzliche Kosten [c/Tag]</label>
                            <div class="col-sm-8">
                                <input type="text" class="form-control" name="add_pow_price_d" value="<?php echo $config->v->add_pow_price_d ?>">
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="col-sm-offset-10 col-sm-2">
                                <button type="submit" class="btn btn-info" name="save_3">Speichern</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
