function createMembersStatisticsChart() { 
    $.ajax({
        url: '/' + SARON_URI + 'app/web-api/listStatistics.php'
    }).then(function(data) {
        var chartData = JSON.parse(data);      
        var chartsMeta = '{' +
                            '"charts":[' +
                                '{"id":"number_of_members","label":"Medlemmar [Antal]"},' +
                                '{"id":"diff", "label":"Förändring medlemmar [Antal]"},' +
                                '{"id":"number_of_new_members", "label":"Nya medlemmar [Antal]"},' +
                                '{"id":"number_of_finnished_members","label":"Avslutade medlemskap [Antal]"},' +
                                '{"id":"number_of_dead", "label":"Avlidna medlemmar [Antal]"},' +
                                '{"id":"number_of_baptist_people", "label":"Döpta [Antal]"},' +
                                '{"id":"avg_age", "label":"Medelålder för medlemmar [År]"},' +
                                '{"id":"avg_membership_time", "label":"Tid för medlemskap [ÅR]"}' +
                            ']'+
                        '}';
                        
        var meta = JSON.parse(chartsMeta);      
        
        addTableCells("StatisticsChart", 2, meta);

        for(var i=0; i< meta.charts.length; i++)              
            createMembershipLineChart(chartData, meta.charts[i]);

    });
};    



function createMembershipLineChart(chartData, meta){    
    var ctx = document.getElementById(meta.id).getContext('2d');
        
    var labels = new Array();
    var values = new Array();
    for(var i = 0; i < chartData.Records.length; i++){
        labels.push(chartData.Records[i].year.substr(0,4));
        values.push(chartData.Records[i][meta.id]);
        console.log(meta.id + ": " + chartData.Records[i].year.substr(0,4) + " = " + Number(chartData.Records[i][meta.id]) + " Text: " + chartData.Records[i][meta.id]);
    }
    
    var chart = new Chart(ctx, {
        type: 'line',
        data: {
            labels: labels,
            datasets: [{
                label: meta.label,
                data: values,
                backgroundColor: 'rgba(255, 99, 132, 0)',
                borderColor: 'rgba(50,50,150,1)',
                borderWidth: 1
            }]
        },
        options: {
            drawPoints:{
                enabled: false
            },
            scales: {
                yAxes: [{
                    ticks: {
                        beginAtZero:false
                    }
                }]
            }
        }
        
    });
}