<!DOCTYPE html>
<meta charset="utf-8">
	  <title>Everything</title>
	  <style>
.node circle {
  fill: #fff;
  stroke: steelblue;
  stroke-width: 1.5px;
}

.node {
  font: 10px sans-serif;
}
  #draggable { width: 150px; height: 150px; padding: 0.5em; }

</style>
  <link rel="stylesheet" href="//code.jquery.com/ui/1.11.4/themes/smoothness/jquery-ui.css">
  <script src="//code.jquery.com/jquery-1.10.2.js"></script>
  <script src="//code.jquery.com/ui/1.11.4/jquery-ui.js"></script>
<script src="//d3js.org/d3.v3.min.js"></script>
<body>

	  <div id="draggable" class="ui-widget-content">
  <p>Draggable node</p>
	  <p id = "posX">x</p>
	  <p id = "posY">y</p>
</div>

<script>
  $(function() {
    $( "#draggable" ).draggable({
        drag: function(){
            var offset = $(this).offset();
            var xPos = offset.left;
            var yPos = offset.top;
            $('#posX').text('x: ' + xPos);
            $('#posY').text('y: ' + yPos);
        }
    });

	$("#draggable").css({'top': 200, 'left' : 200});

  });
var jsonData = [
	{"name": "ArrayInterpolator", "x": 50, "y": 50},
      {"name": "ColorInterpolator", "x": 50, "y": 70},
      {"name": "DateInterpolator", "x": 50, "y": 90},
      {"name": "Interpolator", "x": 50, "y": 110},
      {"name": "MatrixInterpolator", "x": 50, "y": 130},
      {"name": "NumberInterpolator", "x": 50, "y": 150},
      {"name": "ObjectInterpolator", "x": 50, "y": 170},
      {"name": "PointInterpolator", "x": 50, "y": 190},
      {"name": "RectangleInterpolator", "x": 50, "y": 210}
];

var width = 960,
    height = 500;

var dragNode = d3.behavior.drag()
	.on("drag", function(d,i) {
					d.x += d3.event.dx;
					d.y += d3.event.dy;
					d3.select(this).attr("transform", function(d,i) {
														  return "translate(" + [ d.x,d.y ] + ")"
															  });
				});

var svg = d3.select("body").append("svg")
    .attr("width", width)
    .attr("height", height)
	.style("background-color", '#eeeeee')
  .append("g")
    .attr("transform", "translate(40,0)");

  var node = svg.selectAll(".node")
      .data(jsonData)
    .enter().append("g")
      .attr("class", "node")
      .attr("transform", function(d) { return "translate(" + d.x + "," + d.y + ")"; })
	  .call(dragNode);

  node.append("circle")
      .attr("r", 4.5);

  node.append("text")
      .attr("dx", function(d) { return d.children ? -8 : 8; })
      .attr("dy", 3)
      .style("text-anchor", "start")
      .text(function(d) { return d.name; });

d3.select(self.frameElement).style("height", height + "px");


var boxWidth = 600;
var boxHeight = 400;
/*
var box = d3.select('body')
            .append('svg')
            .attr('class', 'box')
            .attr('width', boxWidth)
            .attr('height', boxHeight);
*/

var drag = d3.behavior.drag()
             .on('dragstart', function() { circle.style('fill', 'red'); })
             .on('drag', function() { circle.attr('cx', d3.event.x)
                                            .attr('cy', d3.event.y); })
             .on('dragend', function() { circle.style('fill', 'black'); });

var circle = svg.selectAll('.draggableCircle')
                .data([{ x: (boxWidth / 2), y: (boxHeight / 2), r: 25 }])
                .enter()
                .append('svg:circle')
                .attr('class', 'draggableCircle')
                .attr('cx', function(d) { return d.x; })
                .attr('cy', function(d) { return d.y; })
                .attr('r', function(d) { return d.r; })
                .call(drag)
                .style('fill', 'black');

</script>
