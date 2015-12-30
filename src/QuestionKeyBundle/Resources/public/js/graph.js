/** Based on work covered by MIT License from http://bl.ocks.org/cjrd/6863459 **/

var graph = {
    graphData: null,
    nodes: [],
    edges: [],
    consts: {
        selectedClass: "selected",
        circleGClass: "conceptG",
        graphClass: "graph",
        nodeRadius: 50,
        saveCurrentURL: null,
        saveAfter: 2000
    },
    state: {
        lastSelected: {
            // Only one of these should be set at a time.
            node: null,
            edge: null
        },
        justScaleTransGraph: false,
    },
    getNodeForId: function(id) {
        for(i in graph.nodes) {
            if (graph.nodes[i].id == id) {
                return graph.nodes[i];
            }
        }
    },
    start: function(data, showInitialData) {
        graph.graphData = data;

        var docEl = document.documentElement,
            bodyEl = document.getElementsByTagName('body')[0],
            width = window.innerWidth || docEl.clientWidth || bodyEl.clientWidth,
            height =  window.innerHeight|| docEl.clientHeight|| bodyEl.clientHeight;

        for(id in graph.graphData.nodes) {
            var position = graph.getPositionForNode(id, showInitialData);
            graph.nodes.push({title:  graph.graphData.nodes[id].title ,  id:  id,  url: graph.graphData.nodes[id].url   ,  x:  position.x  ,  y: position.y });
        }
        if (graph.graphData.start_node && graph.graphData.start_node.id) {
            graph.startAddEdge(null, graph.getNodeForId(graph.graphData.start_node.id), true);
        }
        for(id in graph.graphData.nodeOptions) {
            graph.startAddEdge(graph.getNodeForId(graph.graphData.nodeOptions[id].node.id), graph.getNodeForId(graph.graphData.nodeOptions[id].destination_node.id), false);
        }

        graph.svg = d3.select("body").append("svg")
            .attr("width", width)
            .attr("height", height);

        graph.svgG = graph.svg.append("g").classed(graph.consts.graphClass, true);

        var defs = graph.svg.append('svg:defs');
            defs.append('svg:marker')
            .attr('id', 'end-arrow')
            .attr('viewBox', '0 -5 10 10')
            .attr('refX', "32")
            .attr('markerWidth', 3.5)
            .attr('markerHeight', 3.5)
            .attr('orient', 'auto')
            .append('svg:path')
            .attr('d', 'M0,-5L10,0L0,5');

        graph.paths = graph.svgG.append("g").selectAll("g");
        graph.circles = graph.svgG.append("g").selectAll("g");

        window.onresize = function(){ graph.updateWindow(); };


        var dragSvg = d3.behavior.zoom()
            .on("zoom", function(){
                if (d3.event.sourceEvent.shiftKey){
                    // TODO  the internal d3 state is still changing
                    return false;
                } else{
                    graph.zoomed.call(graph);
                }
                return true;
            });

        graph.svg.call(dragSvg).on("dblclick.zoom", null);

        graph.svg.on("mousedown", function(d){graph.svgMouseDown.call(graph, d);});
        graph.svg.on("mouseup", function(d){graph.svgMouseUp.call(graph, d);});

        graph.updateGraph();

    },
    startAddEdge: function(source, target, isStart) {
        if (isStart) {
            graph.edges.push({source: null, target: target, isStart: true, count: 1});
            return;
        }
        for (var i = 0; i < graph.edges.length; i++) {
            if (!graph.edges[i].isStart && graph.edges[i].source.id == source.id && graph.edges[i].target.id == target.id) {
                graph.edges[i].count++;
                return;
            }
        }
        graph.edges.push({source: source, target: target, isStart: false, count: 1});

    },
    getPositionForNode: function(id, showInitialData) {
        if (showInitialData && showInitialData.nodes) {
            for(i in showInitialData.nodes) {
                if (showInitialData.nodes[i].id == id) {
                    return {
                        x: parseFloat(showInitialData.nodes[i].x),
                        y: parseFloat(showInitialData.nodes[i].y),
                    };
                }
            }
        }
        return {
            x: 200 + Math.random()*600,
            y: 200 + Math.random()*600,
        }
    },
    dragmove: function(d) {
        d.x += d3.event.dx;
        d.y +=  d3.event.dy;
        graph.updateGraph();
    },
    drag: d3.behavior.drag()
        .origin(function(d){
            return {x: d.x, y: d.y};
        })
        .on("drag", function(args){
            graph.dragmove.call(this, args);
        })
        .on("dragend", function() {
            // todo check if edge-mode is selected
        }),
    insertTitleLinebreaks: function (gEl, title) {
        var words = title.split(/\s+/g),
        nwords = words.length;
        var el = gEl.append("text")
            .attr("text-anchor","middle")
            .attr("dy", "-" + (nwords-1)*7.5);
        for (var i = 0; i < words.length; i++) {
            var tspan = el.append('tspan').text(words[i]);
            if (i > 0) {
                tspan.attr('x', 0).attr('dy', '15');
            }
        }
    },
    updateGraph: function() {

        graph.paths = graph.paths.data(graph.edges, function(d){
            if (d.isStart) {
                return 'START';
            } else {
                return String(d.source.id) + "+" + String(d.target.id);
            }
        });
        // update existing paths
        graph.paths.style('marker-end', 'url(#end-arrow)')
            .attr("d", function(d){
                if (d.isStart) {
                    return "M20,20L" + d.target.x + "," + d.target.y;
                } else {
                    return "M" + d.source.x + "," + d.source.y + "L" + d.target.x + "," + d.target.y;
                }
            });

        // add new paths
        graph.paths.enter()
            .append("path")
            .style('marker-end','url(#end-arrow)')
            .classed("link", true)
            .classed("link-multiple", function(d) { return d.count > 1; })
            .attr("d", function(d){
                if (d.isStart) {
                    return "M20,20L" + d.target.x + "," + d.target.y;
                } else {
                    return "M" + d.source.x + "," + d.source.y + "L" + d.target.x + "," + d.target.y;
                }
            })
            .on("mousedown", function(d){
                graph.pathMouseDown.call(graph, d3.select(this), d);
            })
            .on("mouseup", function(d){
                graph.pathMouseUp.call(graph, d3.select(this), d);
            });

        // remove old links
        graph.paths.exit().remove();


        // update existing nodes
        graph.circles = graph.circles.data(graph.nodes, function(d){ return d.id;});
        graph.circles.attr("transform", function(d){return "translate(" + d.x + "," + d.y + ")";});

        // add new nodes
        var newGs= graph.circles.enter()
            .append("g");

        newGs.classed(graph.consts.circleGClass, true)
            .attr("transform", function(d){return "translate(" + d.x + "," + d.y + ")";})
            .on("mouseover", function(d){

            })
            .on("mouseout", function(d){

            })
            .on("mousedown", function(d){
                graph.circleMouseDown.call(graph, d3.select(this), d);
            })
            .on("mouseup", function(d){
                graph.circleMouseUp.call(graph, d3.select(this), d);
            })
            .call(graph.drag);

        newGs.append("circle")
            .attr("r", String(graph.consts.nodeRadius));

        newGs.each(function(d){
            graph.insertTitleLinebreaks(d3.select(this), d.title);
        });
    },
    updateWindow: function(){
        var docEl = document.documentElement,
            bodyEl = document.getElementsByTagName('body')[0];
        var x = window.innerWidth || docEl.clientWidth || bodyEl.clientWidth;
        var y = window.innerHeight|| docEl.clientHeight|| bodyEl.clientHeight;
        graph.svg.attr("width", x).attr("height", y);
    },
    zoomed: function(){
        graph.state.justScaleTransGraph = true;
        d3.select("." + graph.consts.graphClass)
            .attr("transform", "translate(" + d3.event.translate + ") scale(" + d3.event.scale + ")");
    },
    svgMouseDown: function(){
    },
    svgMouseUp: function(){

    },
    circleMouseDown: function(d3node, d){
        d3.event.stopPropagation();
        graph.state.lastSelected.node = d;
        graph.state.lastSelected.edge = null;
        graph.updateSelects();
    },
    circleMouseUp: function(d3node, d){
    },
    pathMouseDown: function(d3edge, d){
        d3.event.stopPropagation();
        graph.state.lastSelected.node = null;
        graph.state.lastSelected.edge = d;
        graph.updateSelects();
    },
    pathMouseUp: function(d3edge, d){
    },
    updateSelects: function(){
        graph.paths.filter(function(cd){
            return cd !== graph.state.lastSelected.edge;
        }).classed(graph.consts.selectedClass, false);
        if (graph.state.lastSelected.edge) {
            graph.paths.filter(function(cd){
                return cd === graph.state.lastSelected.edge;
            }).classed(graph.consts.selectedClass, true);
        }
        graph.circles.filter(function(cd){
            return cd !== graph.state.lastSelected.node;
        }).classed(graph.consts.selectedClass, false);
        if (graph.state.lastSelected.node) {
            graph.circles.filter(function(cd){
                return cd === graph.state.lastSelected.node;
            }).classed(graph.consts.selectedClass, true);
        }
    },
    info: function() {
        if(graph.state.lastSelected.node) {
            window.open(graph.state.lastSelected.node.url);
        } else if (graph.state.lastSelected.edge && !graph.state.lastSelected.edge.isStart) {
            window.open(graph.state.lastSelected.edge.source.url);
        } else if (graph.state.lastSelected.edge && graph.state.lastSelected.edge.isStart) {
            window.open(graph.state.lastSelected.edge.target.url);
        }
    },
    save: function() {
        $.post(graph.consts.saveCurrentURL,  { data: graph.getInfoToSave() });
    },
    getInfoToSave: function() {
        var data = {'nodes':[   ]};
        for(i in graph.nodes) {
            data.nodes.push({ id: graph.nodes[i].id, x: graph.nodes[i].x, y: graph.nodes[i].y });
        }
        return data;
    }

};
