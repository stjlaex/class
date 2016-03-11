function tracking_chart_cats() {
	window.resizeTo(1010, 680);

	var students = [],
	data1 = [],
	averages = [],
	names = [],
	data = [];

	var rows=document.getElementById("graph_data1").getElementsByTagName("tr");
	totalsum=0;
	assessno=0;
	for(var r=0; r<rows.length; r++){
		temp = [];
		var assess_sum=0;
		var studentsno=0;
		var tdcells=rows[r].getElementsByTagName("td");
		var thcells=rows[r].getElementsByTagName("th");
		for(var c=0; c<tdcells.length; c++){
			if(tdcells[c].hasChildNodes()){
				temp.push(parseFloat(tdcells[c].childNodes[0].data));
				if(parseFloat(tdcells[c].childNodes[0].data)>0){
					assess_sum+=parseFloat(tdcells[c].childNodes[0].data);
					studentsno++;
					}
				}
			else{
				temp.push(parseFloat('0'));
				}
			}

		assess_average=assess_sum/studentsno;

		for(var c=0; c<thcells.length; c++){
			if(thcells[c].hasChildNodes()){
				names[assessno]=thcells[c].childNodes[0].data;
				if(thcells[c].childNodes[0].data=="VSA"){data[0]=[1,assess_average];}
				else if(thcells[c].childNodes[0].data=="QSA"){data[1]=[2,assess_average];}
				else if(thcells[c].childNodes[0].data=="NVA"){data[2]=[3,assess_average];}
				}
			}

		data1[r]=temp;
		totalsum+=assess_average;
		assessno++;
		}
	totalaverage=(totalsum/rows.length).toFixed(2);
	data[3]=[4,totalaverage];

	var tdcells=document.getElementById("graph_axis").getElementsByTagName("td");
	var sum2=0;
	for(var c=0; c<tdcells.length; c++){
		if(tdcells[c].hasChildNodes()){
			students.push(tdcells[c].childNodes[0].data);
			}
		}

	var margin = {top: 20, right: 15, bottom: 60, left: 60}
			, width = 960 - margin.left - margin.right
			, height = 500 - margin.top - margin.bottom;

	var x = d3.scale.linear()
			.domain([0, 5])
			.range([ 0, width ]);

	var y = d3.scale.linear()
			.domain([50, 150])
			.range([ height, 0 ]);

	var chart = d3.select('body')
			.append('svg:svg')
			.attr('width', width + margin.right + margin.left)
			.attr('height', height + margin.top + margin.bottom)
			.attr('class', 'chart');
	
	chart.append('line')
		.attr('class', 'median')
		.attr('x1', margin.left)
		.attr('x2', width+margin.right+margin.left)
		.attr('y1', margin.top+(height/2))
		.attr('y2', margin.top+(height/2));

	chart.append('line')
		.attr('class', 'zone')
		.attr('x1', margin.left)
		.attr('x2', width+margin.right+margin.left)
		.attr('y1', margin.top+(height/2))
		.attr('y2', margin.top+(height/2));

	var main = chart.append('g')
				.attr('transform', 'translate(' + margin.left + ',' + margin.top + ')')
				.attr('width', width)
				.attr('height', height)
				.attr('class', 'main');


	var xAxis = d3.svg.axis().scale(x).orient("bottom")
				.ticks(6)
				.tickFormat(function (d, i) {
					return ['','Verbal', 'Quantitative', 'Non-verbal', 'Total',''][d];
					});

	main.append('g')
		.attr('transform', 'translate(0,' + height + ')')
		.attr('class', 'main axis date')
		.call(xAxis);


	var yAxis = d3.svg.axis()
			.scale(y)
			.orient('left');

	main.append('g')
		.attr('transform', 'translate(0,0)')
		.attr('class', 'main axis date')
		.call(yAxis)
		.append("text")
		.attr("class", "label")
		.attr("transform", "rotate(-90)")
		.attr("y", 6)
		.attr("dy", ".71em")
		.style("text-anchor", "end")
		.text("Score");

	var g = main.append("svg:g"); 

	var tooltip = d3.select("body").append("div")
				.attr("class", "tooltip")
				.style("opacity", 0);

	d3.select("body").append("div")
				.attr("class", "legend")
				.attr("width", 18)
				.attr("height", 18)
				.html("CATs 2014 - Class")
				.style("left", (margin.left + width - 80) + "px")
				.style("top", (60 + margin.top) + "px")
				.style("position", "absolute");
	d3.select("svg").append("svg:circle")
				.attr("cx", function (d,i) { return (margin.left + width - 100); } )
				.attr("cy", function (d) { return ( margin.top - 10); } )
				.attr("r", 8);
	d3.select("body").append("div")
				.attr("class", "legend")
				.attr("width", 18)
				.attr("height", 18)
				.html("<select id='singlestudentselect' style='font-size: 1em;'></select>")
				.style("left", (margin.left + width - 80) + "px")
				.style("top", (80 + margin.top) + "px")
				.style("position", "absolute");
	d3.select("svg").append("svg:circle")
				.attr("cx", function (d,i) { return (margin.left + width - 100); } )
				.attr("cy", function (d) { return ( margin.top +10 ); } )
				.attr("r", 4)
				.style("fill", "#ef2929");
	$('#singlestudentselect1').find('option').clone().appendTo('#singlestudentselect');

	g.selectAll("scatter-dots")
		.data(data)
		.enter()
		.append("svg:circle")
			.attr("cx", function (d,i) { return x(d[0]); } )
			.attr("cy", function (d) { return y(d[1]); } )
			.attr("r", 5)
			.on("mouseover", function(d) {
				types=['','Verbal', 'Quantitative', 'Non-verbal', 'Total',''];
				type=types[d[0]];score=d[1];
				tooltip.transition().duration(200).style("opacity", '0.9');
				tooltip.html("Class average <br>" + type + " - " + score).style("left", (x(d[0]) + 5 + margin.left) + "px").style("top", (y(d[1]) + 70 + margin.top) + "px");
				})
			.on("mouseout", function(d) {
				tooltip.transition()
					.duration(500)
					.style("opacity", 0);
				});

	color = d3.scale.category10();
	var legend = g.selectAll(".legend")
				.data(color.domain())
				.enter().append("g")
				.attr("class", "legend")
				.attr("transform", function(d, i) { return "translate(0," + i * 20 + ")"; });

	legend.append("rect")
		.attr("x", margin.left + width - 18)
		.attr("width", 18)
		.attr("height", 18)
		.style("fill", color);

	legend.append("text")
		.attr("x", margin.left + width - 24)
		.attr("y", 9)
		.attr("dy", ".35em")
		.style("text-anchor", "end")
		.text(function(d) { return d;});

	$('#singlestudentselect').change( function() {

		var studentindex=$('#singlestudentselect').val();
		var student=$('#singlestudentselect option:selected').text();

		if(studentindex==-1){
			d3.select("svg").selectAll("circle#singlestudent")
				.data([])
				.exit()
				.remove();
			}
		if(studentindex>=0){
			for(var c=0; c<names.length; c++){
				if(names[c]=='VSA'){var firstrow=data1[c][studentindex];}
				else if(names[c]=='QSA'){var secondrow=data1[c][studentindex];}
				else if(names[c]=='NVA'){var thirdrow=data1[c][studentindex];}
				}

			var data2=[];
			data2[0]=[1,firstrow];
			data2[1]=[2,secondrow];
			data2[2]=[3,thirdrow];
			data2[3]=[4,((firstrow+secondrow+thirdrow)/3).toFixed(2)];

			d3.select("svg").selectAll("circle#singlestudent")
				.data([])
				.exit()
				.remove();

			g.selectAll("scatter-dots")
			.data(data2)
			.enter()
			.append("svg:circle")
				.attr("cx", function (d,i) { return x(d[0]); } )
				.attr("cy", function (d) { return y(d[1]); } )
				.attr("r", 4)
				.attr("id", "singlestudent")
				.style("fill", "#ef2929")
				.on("mouseover", function(d) {
					types=['','Verbal', 'Quantitative', 'Non-verbal', 'Total',''];
					type=types[d[0]];score=d[1];
					tooltip.transition().duration(200).style("opacity", '0.9');
					tooltip.html(student+" <br>" + type + " - " + score).style("left", (x(d[0]) + 5 + margin.left) + "px").style("top", (y(d[1]) + 70 + margin.top) + "px");
					})
				.on("mouseout", function(d) {
					tooltip.transition()
						.duration(500)
						.style("opacity", 0);
					});
			}
		});
	}
