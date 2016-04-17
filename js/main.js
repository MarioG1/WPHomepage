/* 
 * Copyright (C) 2016 Gallaun Mario mario.gallaun@gmx.at
 */

window.onload = function () {
    var page = getUrlParameter('page');
    switch (page) {
        case 'dashboard':
            render_charts_dashboard();
            break;
        case 'history':
            break;
    }
};

function render_charts_dashboard() {
    var now = new Date();
    var start = Math.round(new Date(now.getFullYear(), now.getMonth(), now.getDate(), 0, 0, 0, 0) / 1000);
    var stop = Math.round(new Date(now.getFullYear(), now.getMonth(), now.getDate(), 23, 59, 0, 0) / 1000);
    $.getJSON('data.php', {action: "get_power_cost", start: start, stop: stop}, function (data) {
        var chart_data = [];
        for (var key in data.data) {
            var curr = data.data[key];
            var tmp = {
                x: new Date(curr.time * 1000),
                y: curr.cost * 100
            };
            chart_data.push(tmp);
        }
        render_setpLine_chart(chart_data, 'chart_pow_cost', "Stromkosten [c/kWh]", true);
    });

    var start = Math.round((new Date() / 1000) - 3600 * 24);
    var stop = Math.round(new Date() / 1000);
    $.getJSON('data.php', {action: "get_power_usage", start: start, stop: stop}, function (data) {
        var chart_data = [];
        for (var key in data.data) {
            var curr = data.data[key];
            var tmp = {
                x: new Date(curr.time * 1000),
                y: curr.pow / 1000
            };
            chart_data.push(tmp);
        }
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
        data: [
            {
                type: "stepLine",
                markerType: "none",
                dataPoints: data
            }
        ]
    });
    chart.render();
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
