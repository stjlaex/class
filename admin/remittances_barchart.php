<!DOCTYPE html>
<meta charset="utf-8">
<head>
	<style>
		.axis {
			font: 10px sans-serif;
			}
		.axis path, .axis line {
			fill: none;
			stroke: #000;
			shape-rendering: crispEdges;
			}
		.line {
			stroke: #000000;
			stroke-width: 0.5;
			}
		rect, .x .tick{
			cursor:pointer;
			}
		.total:hover {
			fill: #204a87;
			} 
		.total {
			fill: #3465a4;
			}
		.paid:hover {
			fill: #4e9a06;
			} 
		.paid {
			fill: #73d216;
			} 
		.notpaid:hover {
			fill: #a40000;
			} 
		.notpaid {
			fill: #cc0000;
			}
		.legend span{
			margin-right:4px;
			padding:4px;
			color:#000;
			}
		.legend span.total{
			background-color:#3465A4;
			}
		.legend span.paid{
			background-color:#73D216;
			}
		.legend span.notpaid{
			background-color:#CC0000;
			}
		svg{
			background-color:#fff !important;
			}
		.label{
			background-color:#f57900 !important;
			}
		#loading{
			display:block;
			margin:0 auto;
			}
		#loadingchart{
			padding: 20px;
			background-color: white;
			margin: 10px;
			width: 90%;
			display:block;
			}
	</style>
</head>

<body>

	<script src="js/d3/d3.v3.min.js"></script>
	<script src="js/jquery-1.8.2.min.js"></script>
	<script>
		$(document).ready(function(){
			var loading=document.createElement('img');
			loading.src='images/roller.gif';
			loading.id='loading';
			$("#loadingchart").append(loading);
			$('loading').css('margin','0 auto');
			var data='';
			var request = $.ajax({
				url: "admin/httpscripts/remittances_barchart_results.php",
				type: "GET",
				dataType: "json"
				});
			request.done(function( response ) {
				$("#loadingchart").remove();
				data=response;
				var maxyear=0;
				$('#year').css('display','block');
				$.each(data,function(year,results){
					if(year>maxyear){maxyear=year;}
					$('#year')
						.append($('<option>', { value : year })
						.text((year-1)+"-"+year));
					});
				$('#year').val(maxyear);
				remittancesChart(data[maxyear]);
				});
			$(window).resize(function() {remittancesChart(data[$('#year').val()]);});
			$('#year').change(function(){remittancesChart(data[$('#year').val()]);});

		});
	</script>

	<div id='barchartcontent'>
		<div>
			<select id='year' style="display:none;"></select>
		</div>
		<div id='viewbarchart' class="chart"></div>
		<div id='loadingchart'></div>
	</div>

	<script>
		function remittancesChart(data){
			d3.selectAll("svg").remove();

			var maxvalue=0;
			$.each(data,function(index,value){
				if(value[1][0]>maxvalue){maxvalue=value[1][0];}
			});

			var margin={top: 20, right: 20, bottom: 70, left: 60},
					width=$('#viewbarchart').width()-margin.left-margin.right-20,
					height=300-margin.top-margin.bottom;

			var x=d3.scale.ordinal()
					.rangeRoundBands([0, width], .1)
					.domain([8,9,10,11,12,1,2,3,4,5,6,7]);

			var y=d3.scale.linear()
					.range([height, 0])
					.domain([0,maxvalue]);

			var xAxis=d3.svg.axis()
						.scale(x)
						.orient("bottom")
						.ticks(12)
						.tickFormat(function (d, i) {
							return ['','January','February','March','April','May','June','July','August','September','October','November','December'][d];
							});

			var yAxis=d3.svg.axis()
						.scale(y)
						.orient("left")
						.ticks(10);

			var svg=d3.select("#viewbarchart").append("svg")
					.attr("width", width + margin.left + margin.right)
					.attr("height", height + margin.top + margin.bottom)
					.append("g")
					.attr("transform", "translate(" + margin.left + "," + margin.top + ")");

			svg.append("g")
				.attr("class", "x axis")
				.attr("id", "xaxis")
				.attr("transform", "translate(0," + height + ")")
				.call(xAxis)
				.selectAll("text")
				.style("text-anchor", "end")
				.attr("dx", "-.8em")
				.attr("dy", "-.55em")
				.attr("transform", "rotate(-60)" )
				.on('click',openRemittance);

			svg.append("g")
				.attr("class", "y axis")
				.call(yAxis)
				.append("text")
				.attr("transform", "rotate(-90)")
				.attr("y", 6)
				.attr("dy", ".71em")
				.style("text-anchor", "end")
				.text("Total (€)");

			svg.append("g")
				.attr("transform","translate("+(width-150)+",10)")
				.style("font-size","12px")
				.append("foreignObject")
				.attr("width", 190)
				.attr("height", 20)
				.html("<div class='legend' style='background-color:#fff !important;'><span class='total'>Not invoiced</span><span class='paid'>Paid</span><span class='notpaid'>Not paid</span></div>");

			svg.selectAll("bar")
				.data(data)
				.enter()
				.append("rect")
				.attr("class","total")
				.attr("x", function(d) { return x(d[0]); })
				.attr("width", x.rangeBand())
				.attr("y", function(d) { return y(d[1][0]); })
				.attr("height", function(d) { return height - y(d[1][0]); })
				.on('click',openRemittance)
				.on("mouseover", function(d) {
					svg.append("line")
						.attr("class", "line")
						.attr({ x1: x(0), y1: y(d[1][0]), x2: x(d[0]), y2: y(d[1][0]) });
					svg.append("text")
						.attr("class", "label")
						.attr({ x: x(d[0])/2, y: y(d[1][0])-5})
						.text("Total: "+d[1][0] + " €");
					})
				.on("mouseout", function(d) {
					svg.selectAll("line.line,text.label")
						.data([])
						.exit()
						.remove();
					});

			svg.selectAll("bar")
				.data(data)
				.enter()
				.append("rect")
				.attr("class","paid")
				.attr("x", function(d) { return x(d[0]); })
				.attr("width", x.rangeBand())
				.attr("y", function(d) { return y(d[1][1]); })
				.attr("height", function(d) { return height - y(d[1][1]); })
				.on('click',openRemittance)
				.on("mouseover", function(d) {
					svg.append("line")
						.attr("class", "line")
						.attr({ x1: x(0), y1: y(d[1][1]), x2: x(d[0]), y2: y(d[1][1]) });
					svg.append("text")
						.attr("class", "label")
						.attr({ x: x(d[0])/2, y: y(d[1][1])-5})
						.text("Paid: "+d[1][1]  + " €");
					})
				.on("mouseout", function(d) {
					svg.selectAll("line.line,text.label")
						.data([])
						.exit()
						.remove();
					});

			svg.selectAll("bar")
				.data(data)
				.enter()
				.append("rect")
				.attr("class","notpaid")
				.attr("x", function(d) { return x(d[0]); })
				.attr("width", x.rangeBand())
				.attr("y", function(d,i) { return y(d[1][1]) -(height - y(d[1][2])); })
				.attr("height", function(d) { return height - y(d[1][2]); })
				.on('click',openRemittance)
				.on("mouseover", function(d) {
					svg.append("line")
						.attr("class", "line")
						.attr({ x1: x(0), y1: y(d[1][1]), x2: x(d[0]), y2: y(d[1][1]) });
					svg.append("line")
						.attr("class", "line")
						.attr({ x1: x(0), y1: y(d[1][1]) -(height - y(d[1][2])), 
							x2: x(d[0]), y2: y(d[1][1]) -(height - y(d[1][2])) 
							});
					svg.append("text")
						.attr("class", "label")
						.attr({ x: x(d[0])/2, y: y(d[1][1]) -(height - y(d[1][2])) -5})
						.text("Not paid: "+d[1][2]  + " €");
					})
				.on("mouseout", function(d) {
					svg.selectAll("line.line,text.label")
						.data([])
						.exit()
						.remove();
					});
			}

		function openRemittance(d){
				if($.isArray(d)){
					var month = d[0];
					}
				else{var month = d;}
				year=document.getElementById('year').value;
				document.location.href = "admin.php?current=fees_remittance_list.php&month="+month+"&year="+year;
				}
	</script>
</body>
