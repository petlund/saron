function createAgeHistogram(){
    var chartsMeta = '{' +
                        '"charts":[' +
                            '{"id":"memberAge1","label":"Åldersfördelning [Antal/åldersgrupp]"},' +
                            '{"id":"memberEnterAge2","label":"Ålder för start av medlemskap [Antal/åldersgrupp]"},' +
                            '{"id":"memberEnterAge2a","label":"Ålder för start av medlemskap  senaste 5 åren [Antal/åldersgrupp]"},' +
                            '{"id":"memberEndAge3","label":"Ålder för avslut av medlemskap ej avliden [Antal/åldersgrupp]"},' +
                            '{"id":"memberEndAge3a","label":"Ålder för avslut av medlemskap ej avliden  senaste 5 åren [Antal/åldersgrupp]"},' +
                            '{"id":"baptistAge4","label":"Dopålder [Antal/åldersgrupp]"},' +
                            //'{"id":"baptistAge4a","label":"Dopålder senaste 5 åren [Antal/åldersgrupp]"},' +
                            '{"id":"baptistAge5","label":"Dopålder för döpta i Korskyrkan [Antal/åldersgrupp]"},' +
                            '{"id":"baptistAge5a","label":"Dopålder för döpta i Korskyrkan senaste 5 åren [Antal/åldersgrupp]"}' +
                        ']'+
                    '}';

    var jsonChartsMeta = JSON.parse(chartsMeta);      

    addTableCells("HistogramChart", 1, jsonChartsMeta);
    $.ajax({
        url: '/' + saron.uri.saron + 'app/web-api/listStatistics.php?TablePath=demographicHistogram'
    }).then(function(data) {
        chartData = JSON.parse(data);      
        for(var i=0; i< jsonChartsMeta.charts.length; i++)              
            createMembershipBarChart(chartData.Results[i], jsonChartsMeta.charts[i]);
    });    
};    



function createMembershipBarChart(chartData, chart){    
    var context = document.getElementById(chart.id).getContext('2d');
    var mValue = new Array();
    var fValue = new Array();
    var _Value = new Array();
    var histogramLabels = new Array();
    
    
    
    var age = chartData.Records[0].age;
    var minAgeGroup = 0;//chartData.Records[0].ageGroup;
    var maxAgeGroup = 22;//chartData.Records[chartData.Records.length - 1].ageGroup;
    
    var currentAgeGroup = -1;
    for(var i = minAgeGroup; i <= maxAgeGroup; i++){
        histogramLabels.push('' + (i * 5) + ' - ' + ((i+1)*5 - 1));
        mValue.push(0);
        fValue.push(0);
        _Value.push(0);
    }
    
    
    var all=0;
    for(var i = 0; i < chartData.Records.length; i++){
        all += Number(chartData.Records[i].amount);
        if(chartData.Records[i].Gender === '1')
            mValue[chartData.Records[i].ageGroup] = chartData.Records[i].amount;
        else if(chartData.Records[i].Gender === '2')
            fValue[chartData.Records[i].ageGroup] = chartData.Records[i].amount; 
        else    
            _Value[chartData.Records[i].ageGroup] = chartData.Records[i].amount; 
    };
        
    
    var stackedBar = new Chart(context, {
        type: 'bar',
        data: {
            labels: histogramLabels,
            datasets:[
                    {
                        label: 'M',
                        backgroundColor: 'rgba(200, 30, 64, 0.5)',
                        data: mValue
                    },
                    {
                        label: 'F',
                        backgroundColor: 'rgba(64, 200, 30, 0.5)',
                        data: fValue
                    },
                    {
                        label: '-',
                        backgroundColor: 'lightgray',
                        data: _Value
                    }                
            ]
        },
        options: {
            title: {
                display: true,
                text: chart.label
            },
            scales: {
                xAxes: [{
                    stacked: true
                }],
                yAxes: [{
                    stacked: true
                }]
            }
        }
    });
}

