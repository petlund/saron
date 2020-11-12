    var getJSON = function(url, callback) {
    var xhr = new XMLHttpRequest();
    xhr.open('GET', url, true);
    xhr.responseType = 'json';
    xhr.onload = function() {
            var status = xhr.status;
            if (status === 200) {
                callback(null, xhr.response);
            } 
            else {
                callback(status, xhr.response);
            }
        };
    xhr.send();
    };
             
    var url = "http://127.0.0.1:8888/wordpress/saron/app/web-api/listOrganizationStructureGraphic.php";

    getJSON(url + '?selection=nodes', function(err, saron_nodes){
        getJSON(url + '?selection=edges', function(err, saron_edges){
            createTree(saron_nodes, saron_edges);
        });
    });
    
    function createTree(saron_nodes, saron_edges){
      
        var nodes = new vis.DataSet(saron_nodes);
        var edges = new vis.DataSet(saron_edges);

        // create a network
        var container = document.getElementById("#ORG_GRAPH");

        var data = {
          nodes: nodes,
          edges: edges
        };
        var options = {
            autoResize: true,
            height: '600px',
            width: '100%',
            layout: {
                hierarchical: {
                    direction: "LR",
                    //sortMethod: "directed"
                },
                physics: {
                    hierarchicalRepulsion: {
                        avoidOverlap: 1.0
                    }
                }
            }
        };
        var network = new vis.Network(container, data, options);
      
    }