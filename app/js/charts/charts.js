$(document).ready(function () { 
    createAgeHistogram();
    createMembersStatisticsChart();    
});
    
    
    
function addTableCells(Id, tableWidth, chartMeta){
    var table;
    var td;
    var tr;
    var canvas;
    var placeHolder = document.getElementById(Id);
    table = document.createElement('table');
    placeHolder.appendChild(table);
    for(var i = 0; i<chartMeta.charts.length / tableWidth; i++){
        tr = document.createElement('tr');
        table.appendChild(tr);
        for(var j=0;j< tableWidth; j++){
            td = document.createElement('td');
            tr.appendChild(td);
            canvas =  document.createElement('canvas'); 
            td.appendChild(canvas);
            canvas.setAttribute("id",chartMeta.charts[i*tableWidth + j].id );
            canvas.setAttribute("width",1200/tableWidth);
            canvas.setAttribute("height","200");
        }
        placeHolder.appendChild(table);
    }
}