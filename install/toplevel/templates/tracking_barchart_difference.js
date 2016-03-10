function tracking_barchart_difference() {
	var students = [],
	data1 = [],
	data2 = [];


    var tdcells=document.getElementById("graph_data1").getElementsByTagName("td");
	var sum1=0;
	for(var c=0; c<tdcells.length; c++){
		if(tdcells[c].hasChildNodes()){
			data1.push(parseFloat(tdcells[c].childNodes[0].data));
			sum1+=tdcells[c].childNodes[0].data;
			}
		else{
			data1.push(parseFloat('0'));
			}
		}

    var tdcells=document.getElementById("graph_data2").getElementsByTagName("td");
	for(var c=0; c<tdcells.length; c++){
		if(tdcells[c].hasChildNodes()){
			data2.push(parseFloat(tdcells[c].childNodes[0].data));
			sum2+=tdcells[c].childNodes[0].data;
			}
		else{
			data2.push(parseFloat('0'));
			}
		}

    var tdcells=document.getElementById("graph_axis").getElementsByTagName("td");
	var sum2=0;
	for(var c=0; c<tdcells.length; c++){
		if(tdcells[c].hasChildNodes()){
			students.push(tdcells[c].childNodes[0].data);
			}
		}

var data=data1;

var margin = {top: 30, right: 10, bottom: 10, left: 10},
    width = 800 - margin.left - margin.right,
    height = 400 - margin.top - margin.bottom;

var x0 = Math.max(-d3.min(data), d3.max(data));

var x = d3.scale.linear()
    .domain([0, x0])
    .range([0, width])
    .nice();

var y = d3.scale.ordinal()
    .domain(d3.range(data.length))
    .rangeRoundBands([0, height], .2);

var xAxis = d3.svg.axis()
    .scale(x)
    .orient("top");

var chart = d3.select("#chart").append("svg")
    .attr("width", width + margin.left + margin.right)
    .attr("height", height + margin.top + margin.bottom)
    .append("g")
    .attr("transform", "translate(" + margin.left + "," + margin.top + ")");

var bars = chart.selectAll(".bar")
    .data(data)
    .enter().append("rect")
    .attr("class", function(d) { return d < 0 ? "bar negative" : "bar positive"; })
    .attr("x", function(d) { return x(Math.min(0, d)); })
    .attr("y", function(d, i) { return y(i); })
    .attr("width", function(d) { return Math.abs(x(d) - x(0)); })
    .attr("height", y.rangeBand());

var mean = getAverageFromNumArr(data,0);

chart.append("g")
    .attr("class", "x axis")
    .call(xAxis);

chart.append("g")
    .attr("class", "y axis")
	.append("line")
    .attr("x1", x(0))
    .attr("x2", x(0))
    .attr("y1", 0)
    .attr("y2", height);
chart.append("g")
    .attr("class", "y axis mean")
	.append("line")
	.attr("x1", x(mean))
	.attr("x2", x(mean))
    .attr("y1", 0)
    .attr("y2", height);

chart.selectAll(".label")
	.data(students)
	.enter().append("text")
    .attr("x", 10)
	.attr("y", function(d,i) { return y(i)+ y.rangeBand() / 2; })
    .attr("dx", 0)
    .attr("dy", ".35em")
    .attr("text-anchor", "start")
	.text(function(d) { return d; });
	}



// Programmer: Larry Battle 
// Date: Mar 06, 2011
// Purpose: Calculate standard deviation, variance, and average among an array of numbers.
var isArray = function (obj) {
	return Object.prototype.toString.call(obj) === "[object Array]";
},
getNumWithSetDec = function( num, numOfDec ){
	var pow10s = Math.pow( 10, numOfDec || 0 );
	return ( numOfDec ) ? Math.round( pow10s * num ) / pow10s : num;
},
getAverageFromNumArr = function( numArr, numOfDec ){
	if( !isArray( numArr ) ){ return false;	}
	var i = numArr.length, 
		sum = 0;
		sumno = 0;
	while( i-- ){
		if(numArr[ i ]!=0){
		sum += numArr[ i ];
		sumno++;
		}
	}
	return getNumWithSetDec( (sum / sumno ), numOfDec );
},
getVariance = function( numArr, numOfDec ){
	if( !isArray(numArr) ){ return false; }
	var avg = getAverageFromNumArr( numArr, numOfDec ), 
		i = numArr.length,
		v = 0;
 
	while( i-- ){
		v += Math.pow( (numArr[ i ] - avg), 2 );
	}
	v /= numArr.length;
	return getNumWithSetDec( v, numOfDec );
},
getStandardDeviation = function( numArr, numOfDec ){
	if( !isArray(numArr) ){ return false; }
	var stdDev = Math.sqrt( getVariance( numArr, numOfDec ) );
	return getNumWithSetDec( stdDev, numOfDec );
};