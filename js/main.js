/* 
 * Copyright (C) 2016 Gallaun Mario mario.gallaun@gmx.at
 */

var chart_data_points = [];

window.onload = function () {
    var page = getUrlParameter('page');
    switch (page) {
        case 'dashboard':
            render_charts_dashboard();
            break;
        case 'history_day':
            render_charts_history_day();
            break;
        case 'history_week':
            render_charts_history_week();
            break;
        case 'history_month':
            render_charts_history_month();
            break;
    }
};

function render_charts_history_day() {
    var now = new Date();
    var today_00 = Math.round(new Date(now.getFullYear(), now.getMonth(), now.getDate(), 0, 0, 0, 0) / 1000);
    var today_24 = Math.round(new Date(now.getFullYear(), now.getMonth(), now.getDate(), 23, 59, 0, 0) / 1000);
    var yesterday_00 = Math.round(new Date(now.getFullYear(), now.getMonth(), now.getDate()-1, 0, 0, 0, 0) / 1000);
    var yesterday_24 = Math.round(new Date(now.getFullYear(), now.getMonth(), now.getDate()-1, 23, 59, 0, 0) / 1000);
    
    var req0 = $.getJSON('data.php', {action: "get_cost", start: today_00, stop: today_24, interval: 'h'}, function (data) {});
    var req1 = $.getJSON('data.php', {action: "get_cost", start: yesterday_00, stop: yesterday_24, interval: 'h'}, function (data) {});
    
    $.when(req0, req1).done(function(d0, d1){
        var data_points = [[],[]];
        for (var key in d0[0].data) {
            var curr = d0[0].data[key];
            var time = new Date(curr.time*1000);
            var tmp = {
                    x: parseInt(key),
                    y: curr.cost,
                    label: time.getHours()+':00'
            };
            data_points[0].push(tmp);
        }
        
        for (var key in d1[0].data) {
            var curr = d1[0].data[key];
            var time = new Date(curr.time*1000);
            var tmp = {
                    x: parseInt(key),
                    y: curr.cost,
                    label: time.getHours()+':00'
            };
            data_points[1].push(tmp);
        }
        
        var chart_data = [
            {
                type: "column",
                showInLegend: true,
                legendText: "Heute",
                yValueFormatString:"0.####€",
                dataPoints: data_points[0]
            },
            {
                type: "column",
                showInLegend: true,
                legendText: "Gestern",
                yValueFormatString:"0.####€",
                dataPoints: data_points[1]
            } 
        ];
        
        render_column_chart(chart_data, 'chart_pow_cost', "Stromkosten [€]", true);
    });
    
    var req2 = $.getJSON('data.php', {action: "get_power_usage", start: today_00, stop: today_24, interval: 'h'}, function (data) {});
    var req3 = $.getJSON('data.php', {action: "get_power_usage", start: yesterday_00, stop: yesterday_24, interval: 'h'}, function (data) {});
    
    $.when(req2, req3).done(function(d0, d1){
        var data_points = [[],[]];
        for (var key in d0[0].data) {
            var curr = d0[0].data[key];
            var time = new Date(curr.time*1000);
            var tmp = {
                    x: parseInt(key),
                    y: curr.pow / 1000,
                    label: time.getHours()+':00'
            };
            data_points[0].push(tmp);
        }
        
        for (var key in d1[0].data) {
            var curr = d1[0].data[key];
            var time = new Date(curr.time*1000);
            var tmp = {
                    x: parseInt(key),
                    y: curr.pow / 1000,
                    label: time.getHours()+':00'
            };
            data_points[1].push(tmp);
        }
        
        var chart_data = [
            {
                type: "column",
                showInLegend: true,
                legendText: "Heute",
                yValueFormatString:"0.###kWh",
                dataPoints: data_points[0]
            },
            {
                type: "column",
                showInLegend: true,
                legendText: "Gestern",
                yValueFormatString:"0.###kWh",
                dataPoints: data_points[1]
            } 
        ];
        
        render_column_chart(chart_data, 'chart_pow_usage', "Stromverbrauch [kWh]", true);
    });
}

function render_charts_history_week() {
    var days = ['Sonntag','Montag','Dienstag','Mittwoch','Donnerstag','Freitag','Samstag'];
    var now = new Date();
    
    if(now.getDay() === 0) {
        var day_of_week = 6;
    } else {
        var day_of_week = now.getDay() - 1;
    }
    
    var monday_tw_00 = new Date(now.getFullYear(), now.getMonth(), now.getDate()-day_of_week, 0, 0, 0, 0);
    var sunday_tw_24 = new Date(now.getFullYear(), now.getMonth(), now.getDate()+(6-day_of_week), 23, 59, 0, 0) ;
    var monday_lw_00 = new Date(monday_tw_00.getFullYear(), monday_tw_00.getMonth(), monday_tw_00.getDate()-7, 0, 0, 0, 0);
    var sunday_lw_24 = new Date(sunday_tw_24.getFullYear(), sunday_tw_24.getMonth(), sunday_tw_24.getDate()-7, 23, 59, 0, 0);
    
    var req0 = $.getJSON('data.php', {action: "get_cost", start: Math.round(monday_tw_00 / 1000), stop: Math.round(sunday_tw_24 / 1000), interval: 'd'}, function (data) {});
    var req1 = $.getJSON('data.php', {action: "get_cost", start: Math.round(monday_lw_00 / 1000), stop: Math.round(sunday_lw_24 / 1000), interval: 'd'}, function (data) {});
    
    $.when(req0, req1).done(function(d0, d1){
        var data_points = [[],[]];
        for (var key in d0[0].data) {
            var curr = d0[0].data[key];
            var time = new Date(curr.time*1000);
            var tmp = {
                    x: parseInt(key),
                    y: curr.cost,
                    label: days[time.getDay()]
            };
            data_points[0].push(tmp);
        }
        
        for (var key in d1[0].data) {
            var curr = d1[0].data[key];
            var time = new Date(curr.time*1000);
            var tmp = {
                    x: parseInt(key),
                    y: curr.cost,
                    label: days[time.getDay()]
            };
            data_points[1].push(tmp);
        }
        
        var chart_data = [
            {
                type: "column",
                showInLegend: true,
                legendText: "Diese Woche",
                yValueFormatString:"0.####€",
                dataPoints: data_points[0]
            },
            {
                type: "column",
                showInLegend: true,
                legendText: "Letzte Woche",
                yValueFormatString:"0.####€",
                dataPoints: data_points[1]
            } 
        ];
        
        render_column_chart(chart_data, 'chart_pow_cost', "Stromkosten [€]", true);
    });
    
    var req2 = $.getJSON('data.php', {action: "get_power_usage", start: Math.round(monday_tw_00 / 1000), stop: Math.round(sunday_tw_24 / 1000), interval: 'd'}, function (data) {});
    var req3 = $.getJSON('data.php', {action: "get_power_usage", start: Math.round(monday_lw_00 / 1000), stop: Math.round(sunday_lw_24 / 1000), interval: 'd'}, function (data) {});
    
    $.when(req2, req3).done(function(d0, d1){
        var data_points = [[],[]];
        for (var key in d0[0].data) {
            var curr = d0[0].data[key];
            var time = new Date(curr.time*1000);
            var tmp = {
                    x: parseInt(key),
                    y: curr.pow / 1000,
                    label: days[time.getDay()]
            };
            data_points[0].push(tmp);
        }
        
        for (var key in d1[0].data) {
            var curr = d1[0].data[key];
            var time = new Date(curr.time*1000);
            var tmp = {
                    x: parseInt(key),
                    y: curr.pow / 1000,
                    label: days[time.getDay()]
            };
            data_points[1].push(tmp);
        }
        
        var chart_data = [
            {
                type: "column",
                showInLegend: true,
                legendText: "Diese Woche",
                yValueFormatString:"0.###kWh",
                dataPoints: data_points[0]
            },
            {
                type: "column",
                showInLegend: true,
                legendText: "Letze Woche",
                yValueFormatString:"0.###kWh",
                dataPoints: data_points[1]
            } 
        ];
        
        render_column_chart(chart_data, 'chart_pow_usage', "Stromverbrauch [kWh]", true);
    });
}

function render_charts_history_month() {
    var date = new Date();
    var tm_first = new Date(date.getFullYear(), date.getMonth(), 1);
    var tm_last = new Date(date.getFullYear(), date.getMonth() + 1, 0, 23, 59, 0, 0) ;
    var lm_first = new Date(date.getFullYear(), date.getMonth() - 1, 1);
    var lm_last = new Date(date.getFullYear(), date.getMonth(), 0, 23, 59, 0, 0);
    
    var req0 = $.getJSON('data.php', {action: "get_cost", start: Math.round(tm_first / 1000), stop: Math.round(tm_last / 1000), interval: 'd'}, function (data) {});
    var req1 = $.getJSON('data.php', {action: "get_cost", start: Math.round(lm_first / 1000), stop: Math.round(lm_last / 1000), interval: 'd'}, function (data) {});
    
    $.when(req0, req1).done(function(d0, d1){
        var data_points = [[],[]];
        for (var key in d0[0].data) {
            var curr = d0[0].data[key];
            var time = new Date(curr.time*1000);
            var tmp = {
                    x: parseInt(key),
                    y: curr.cost,
                    label: time.getDate()+'.'+(time.getMonth()+1)+'.'+time.getFullYear()
            };
            data_points[0].push(tmp);
        }
        
        for (var key in d1[0].data) {
            var curr = d1[0].data[key];
            var time = new Date(curr.time*1000);
            var tmp = {
                    x: parseInt(key),
                    y: curr.cost,
                    label: time.getDate()+'.'+(time.getMonth()+1)+'.'+time.getFullYear()
            };
            data_points[1].push(tmp);
        }
        
        var chart_data = [
            {
                type: "column",
                showInLegend: true,
                legendText: "Diese Woche",
                yValueFormatString:"0.####€",
                dataPoints: data_points[0]
            },
            {
                type: "column",
                showInLegend: true,
                legendText: "Letzte Woche",
                yValueFormatString:"0.####€",
                dataPoints: data_points[1]
            } 
        ];
        
        render_column_chart(chart_data, 'chart_pow_cost', "Stromkosten [€]", true);
    });
    
    var req2 = $.getJSON('data.php', {action: "get_power_usage", start: Math.round(tm_first / 1000), stop: Math.round(tm_last / 1000), interval: 'd'}, function (data) {});
    var req3 = $.getJSON('data.php', {action: "get_power_usage", start: Math.round(lm_first / 1000), stop: Math.round(lm_last / 1000), interval: 'd'}, function (data) {});
    
    $.when(req2, req3).done(function(d0, d1){
        var data_points = [[],[]];
        for (var key in d0[0].data) {
            var curr = d0[0].data[key];
            var time = new Date(curr.time*1000);
            var tmp = {
                    x: parseInt(key),
                    y: curr.pow / 1000,
                    label: time.getDate()+'.'+(time.getMonth()+1)+'.'+time.getFullYear()
            };
            data_points[0].push(tmp);
        }
        
        for (var key in d1[0].data) {
            var curr = d1[0].data[key];
            var time = new Date(curr.time*1000);
            var tmp = {
                    x: parseInt(key),
                    y: curr.pow / 1000,
                    label: time.getDate()+'.'+(time.getMonth()+1)+'.'+time.getFullYear()
            };
            data_points[1].push(tmp);
        }
        
        var chart_data = [
            {
                type: "column",
                showInLegend: true,
                legendText: "Diese Woche",
                yValueFormatString:"0.###kWh",
                dataPoints: data_points[0]
            },
            {
                type: "column",
                showInLegend: true,
                legendText: "Letze Woche",
                yValueFormatString:"0.###kWh",
                dataPoints: data_points[1]
            } 
        ];
        
        render_column_chart(chart_data, 'chart_pow_usage', "Stromverbrauch [kWh]", true);
    });
}

function render_charts_dashboard() {
    var now = new Date();
    var start = Math.round(new Date(now.getFullYear(), now.getMonth(), now.getDate(), 0, 0, 0, 0) / 1000);
    var stop = Math.round(new Date(now.getFullYear(), now.getMonth(), now.getDate(), 23, 59, 0, 0) / 1000);
    $.getJSON('data.php', {action: "get_power_cost", start: start, stop: stop, interval: 'm'}, function (data) {
        var chart_data_points = [];
        for (var key in data.data) {
            var curr = data.data[key];
            var tmp = {
                x: new Date(curr.time * 1000),
                y: curr.cost * 100
            };
            chart_data_points.push(tmp);
        }
        
        var chart_data = [
            {
                type: "stepLine",
                markerType: "none",
                dataPoints: chart_data_points
            }
        ];
        
        render_setpLine_chart(chart_data, 'chart_pow_cost', "Stromkosten [c/kWh]", true);
    });

    var start = Math.round((new Date() / 1000) - 3600 * 24);
    var stop = Math.round(new Date() / 1000);
    $.getJSON('data.php', {action: "get_power_usage", start: start, stop: stop, interval: 'm'}, function (data) {
        var chart_data_points = [];
        for (var key in data.data) {
            var curr = data.data[key];
            var tmp = {
                x: new Date(curr.time * 1000),
                y: curr.pow / 1000
            };
            chart_data_points.push(tmp);
        }
        
        var chart_data = [
            {
                type: "stepLine",
                markerType: "none",
                dataPoints: chart_data_points
            }
        ];
        
        render_setpLine_chart(chart_data, 'chart_pow_usage', "Stromverbrauch [kWh]", false);
    });
}

function render_setpLine_chart(data, id, title, show_curr_time) {
    var now = Date.now();
    if (show_curr_time) {
        var curr_time_marker = [
                                    {
                                        startValue: new Date(now),
                                        endValue: new Date(now + 60 * 1000),
                                        color: "red",
                                        opacity: 1
                                    }
                                ];
    } else {
        var curr_time_marker = [];
    }

    var chart = new CanvasJS.Chart(id, {
        axisX: {
            lineThickness: 2,
            valueFormatString: "H:mm",
            stripLines: curr_time_marker,
        },
        axisY: {
            title: title
        },
        data: data
    });
    chart.render();
}

function render_column_chart(data, id, title) {
    var chart_1 = new CanvasJS.Chart(id, {
        legend: {
            horizontalAlign: "center", // "center" , "right"
            verticalAlign: "bottom",  // "top" , "bottom"
            fontSize: 15
        },
        axisY:{
            title: title,
            minimum: 0
        },
        data: data
    });
    chart_1.render();
}

function getUrlParameter(sParam) {
    var sPageURL = decodeURIComponent(window.location.search.substring(1)),
        sURLVariables = sPageURL.split('&'),
        sParameterName,
        i;

    for (i = 0; i < sURLVariables.length; i++) {
        sParameterName = sURLVariables[i].split('=');

        if (sParameterName[0] === sParam) {
            return sParameterName[1] === undefined ? true : sParameterName[1];
        }
    }
};
