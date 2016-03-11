function tracking_chart_pimpie() {
	//window.resizeTo(1010, 680);

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
		average=assess_sum/studentsno;
		data1[r]=temp;
		}
	data=data1;

	var tdcells=document.getElementById("graph_axis").getElementsByTagName("td");
	var sum2=0;
	for(var c=0; c<tdcells.length; c++){
		if(tdcells[c].hasChildNodes()){
			students.push(tdcells[c].childNodes[0].data);
			}
		}

	var width = 450,
	height = 20;

	var x = d3.scale.linear()
		.domain([50, 150])
		.range([ 0, width ]);

	var xAxis = d3.svg.axis()
		.scale(x)
		.orient("bottom");

	averagediv=d3.select("#average").append("svg")
			.attr("width", 70)
			.attr("height", 15)
			.append("g")
			.attr("transform", "translate(0,0)");
	
	averagediv.append("svg:rect")
		.attr("x", 0 )
		.attr("y", 0 )
		.attr("width", 50)
		.attr("height", 15)
		.style("fill", "#ef2929");
	
	averagediv.append("text")
		.attr("x", 4 )
		.attr("y", 7 )
		.style("fill","white")
		.attr("dy", ".35em")
		.text(" "+average.toFixed(2));

	for(r=0;r<students.length;r++){
		var svg = d3.select("td#graphicattach-"+r).append("svg")
			.attr("width", width+40)
			.attr("height", height)
			.append("g")
			.attr("transform", "translate(10,0)");

		svg.append("g")
			.attr("class", "x axis")
			.style("display","none")
			.call(xAxis);

		svg.append("svg:rect")
			.attr('class', 'zone')
			.attr('x', function (d,i) { return x(90); })
			.attr('y', function (d,i) { return 0; })
			.attr('width', x(110)- x(90))
			.attr('height', height);

		svg.append("svg:rect")
					.attr("x", function (d,i) { return x(average); } )
					.attr("y", function (d,i) { return 0; } )
					.attr("width", 2)
					.attr("height", height)
					.style("fill", "#ef2929");

		if(data[0][r]>0){
			svg.selectAll("scatter-dots")
				.data(data)
				.enter()
				.append("svg:circle")
					.attr("cx", function (d,i) { return x(data[0][r]); } )
					.attr("cy", function (d,i) { return height/2; } )
					.attr("r", 4);
			}
		}


	var height = 50;

	var svg = d3.select("th#axis").append("svg")
		.attr("width", width+40)
		.attr("height", height)
		.append("g")
		.attr("transform", "translate(10,20)")
		.style("display","block");

	svg.append("g")
		.attr("class", "x axis")
		.call(xAxis);

	}
