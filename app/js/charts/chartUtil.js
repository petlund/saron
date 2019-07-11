$(document).ready(function () { 
    createAgeHistogram();
    createMembersStatisticsChart();    
});
    
function addTableCells(tableId, tableWidth, chartMeta){
    var table = document.getElementById(tableId);
    var td;
    var tr;
    var canvas;
    for(i = 0; i<chartMeta.charts.length / tableWidth; i++){
        tr = document.createElement('tr');
        for(j=0;j< tableWidth; j++){
            td = tr.appendChild( document.createElement('td') );
            canvas = td.appendChild( document.createElement('canvas') );
            canvas.setAttribute("id",chartMeta.charts[i*tableWidth + j].id );
            canvas.setAttribute("width",1200/tableWidth);
            canvas.setAttribute("height","200");
        }
        table.appendChild(tr);
    }
}