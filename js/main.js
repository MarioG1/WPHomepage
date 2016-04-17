/* 
 * Copyright (C) 2016 Gallaun Mario mario.gallaun@gmx.at
 */

window.onload = function () {
    var now = new Date();
    var start = Math.round(new Date(now.getFullYear(), now.getMonth(), now.getDate(), 0, 0, 0, 0)/1000);
    var stop = Math.round(new Date(now.getFullYear(), now.getMonth(), now.getDate(), 23, 59, 0, 0)/1000);
    $.getJSON('data.php', {action: "get_power_cost", start: start, stop: stop}, function (data) {
        var chart_data = [];
        for(var key in data.data) {
            var curr = data.data[key];
            var tmp = {
                x: new Date(curr.time*1000),
                y: curr.cost*100
            };
            chart_data.push(tmp);
        }
        render_setpLine_chart(chart_data, 'chart_pow_cost', "Stromkosten [c/kWh]");
    });
};

function render_setpLine_chart(data, id, title) {
    var chart = new CanvasJS.Chart(id, {
        axisX: {
            lineThickness: 2,
            valueFormatString: "H:mm"
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

