function admission_chart_current_new() {
	admission_chart('current',2017,'','');
	}


function admission_chart(charttype,enrolyear,centername,centerurl) {

	/* empty the chart space */
	var div = document.getElementById("chart");
	while(div.firstChild){
		div.removeChild(div.firstChild);
		}

	if(charttype=='current'){
		chart_current(enrolyear);
		document.getElementById("admissionsprint").style.display='none';
		}
	else if(charttype=='reenrol'){
		chart_reenrol(enrolyear,centername,centerurl);
		document.getElementById("admissionsprint").style.display='block';
		window.Calendar.setup({
					inputField  : document.getElementById("admissionsprintdate"),
					ifFormat    : "%Y-%m-%d",
					button      : "calendar-admissionsprintdate"
					});
		}

	/* done */


	/**
	 *
	 */
	function chart_current(enrolyear) {

		var tds = document.getElementsByClassName("centername");
		var allcenters = [];
		for(var i=0; i<tds.length; i++){
			if(tds[i].childNodes.length>0){
				allcenters.push(tds[i].childNodes[0].data);
				}
			}
		var centernames = DistinctArray(allcenters,true);

		var tds=document.getElementsByClassName("centerurl");
		console.log(tds[0]);
		var allcentersurl = [];
		for(var i=0; i<tds.length; i++){
			if(tds[i].childNodes.length>0){
				allcentersurl.push(tds[i].childNodes[0].data);
				}
			}
		var centerurls = DistinctArray(allcentersurl,true);

		var fullheight = 90;
		var margin = {top: 10, right: 90, bottom: 20, left: 10};
		var width = 820 - margin.left - margin.right;
		var height = fullheight - margin.top - margin.bottom;

		var header = d3.select("#chart").append("svg")
			.attr("width", width + margin.left + margin.right)
			.attr("height", 30)
			.append("g")
			.attr("transform", "translate(" + margin.left + "," + margin.top + ")");
		header.append("svg:text")
			.attr("class","headtext")
			.attr("x", function(d){ return width / 2 + margin.left - 8; } )
			.attr("y", 0)
			.text("Current Roll " + enrolyear);
		header.append("svg:text")
			.attr("class","sidetext")
			.attr("x", function(d){ return width - margin.left + 8  + margin.right/2; } )
			.attr("y", 0)
			.text("Totals");
		header.append("svg:text")
			.attr("class","sidetext")
			.attr("x", function(d){ return width - margin.left - 18  + margin.right/2; } )
			.attr("y", 15)
			.text("Roll  / Spaces");

		
		for(var centerno=0; centerno<centernames.length; centerno++){
			var centername=centernames[centerno];
			var centerurl=centerurls[centerno];
			var tableid=centername + enrolyear;
			var totalroll=document.getElementById(tableid).getElementsByClassName("totalcurrentroll")[0].childNodes[0].data;
			var totalspaces=document.getElementById(tableid).getElementsByClassName("totalspaces")[0].childNodes[0].data;


			/* START filling arrays */
			var groups = [],
			currentroll = [],
			spaces = [],
			full = [],
			capacity = [];
			
			var tdcells=document.getElementById(tableid).getElementsByClassName("currentroll");
			for(var c=0; c<tdcells.length; c++){
				if(tdcells[c].hasChildNodes()){
					currentroll.push(parseFloat(tdcells[c].childNodes[0].data));
				}
				else{
					currentroll.push(parseFloat('0'));
				}
			}
		
			var tdcells=document.getElementById(tableid).getElementsByClassName("spaces");
			for(var c=0; c<tdcells.length; c++){
				if(tdcells[c].hasChildNodes()){
					spaces.push(parseFloat(tdcells[c].childNodes[0].data));
				}
				else{
					spaces.push(parseFloat('0'));
				}
			}
		
			var tdcells=document.getElementById(tableid).getElementsByClassName("capacity");
			var maxroll=0;
			for(var c=0; c<tdcells.length; c++){
				if(tdcells[c].hasChildNodes()){
					capacity.push(parseFloat(tdcells[c].childNodes[0].data));
				}
				else{
					capacity.push(parseFloat('0'));
				}
				if(capacity[c]>maxroll){maxroll=capacity[c];}
			}


			var tdcells=document.getElementById(tableid).getElementsByClassName("xlabel");
			console.log(tdcells);
			for(var c=0; c<tdcells.length; c++){
				if(tdcells[c].hasChildNodes()){
					groups.push(tdcells[c].childNodes[0].data);
				}
			}
		
			for(var c=0; c<tdcells.length; c++){
				percent=Math.round(100 * (capacity[c] - spaces[c]) / capacity[c]);
				full.push(percent);
			}

			if(!groups[0].contains("Pre")){
				/* Not all schools start with Pre-Nursery and we want them to. */
				groups.unshift("");
				currentroll.unshift(0);
				spaces.unshift(0);
				full.unshift(0);
				capacity.unshift(0);
				}
			/* END filling arrays */


			var dataset=currentroll;
				
			var chart = d3.select("#chart").append("svg")
				.attr("width", width + margin.left + margin.right)
				.attr("height", fullheight)
				.append("g")
				.attr("transform", "translate(" + margin.left + "," + margin.top + ")");
		
			var bars = chart.selectAll(".bar")
				.data(dataset)
				.enter()
				.append("rect")
				.attr("class", function(d) { return d < 0 ? "bar negative" : "bar positive"; })
				.attr("x", function(d, i){ return i * (width / 16 ) + margin.left;} )
				.attr("y", function(d) { return height - height * d / maxroll; })
				.attr("fill", function(d,i) { return "rgb(0, 0,"+( height + (100 - full[i]) * 20 )+")"; })
				.attr("height", function(d) { return height * d / maxroll; })
				.attr("width", width / 16 - 2)
				.append("svg:title")
				.text(function(d,i) { return spaces[i]+ " Spaces : ";})
				.append("svg:title")
				.text(function(d,i) { return currentroll[i] + " Filled : ";})
				.append("svg:title")
				.text(function(d,i) { return full[i] + "% Full";});

			var labels = chart.selectAll(".grouplabel")
				.data(groups)
				.enter()
				.append("g")
				.classed("grouplabel",true)
				.append("svg:text")
				.attr("x", function(d, i){ return i * (width / 16) + margin.left;} )
				.attr("y", function(d){ return fullheight - margin.bottom; } )
				.text(function(d){ return d; } );

			var centerlabel = chart
				.append("g")
				.append("svg:text")
				.attr("fill","rgb(20,20,20)")
				.attr("font-size","10")
				.attr("font-weight","600")
				.attr("x", function(d){ return 0; } )
				.attr("y", function(d) { return (margin.top)/2; } )
				.text(centername)
				.on('mouseover', function(){ d3.select(this).style("fill","#999");} )
				.on('mouseout', function(){ d3.select(this).style("fill","#000");} )
				.on('click', function(){ admission_chart('reenrol',2016,escape(this.textContent),geturl(this.textContent));});

			function geturl(centername){
				for(var i=0; i<centernames.length; i++){
					if(centernames[i]==centername){return centerurls[i];}
					}
				}

			var totaltext2 = chart
				.append("svg:text")
				.attr("class","sidetext")
				.attr("x", function(d){ return width - margin.left + margin.right/2; } )
				.attr("y", function(d) { return fullheight / 2; })
				.text(totalroll+ " / "+totalspaces);

			/* END this center*/
			}
		}



	/**
	 *
	 *
	 */
	function chart_reenrol(enrolyear,centername,centerurl) {
		document.getElementById('admissionsprintbutton').onclick=function(){
			admission_print(centerurl);
			}

		var tableid=unescape(centername) + enrolyear;
		//alert(tableid);

		var fullheight = 450;
		var margin = {top: 0, right: 0, bottom: 0, left: 50};
		var width = 500 - margin.left - margin.right;
		var height = fullheight - margin.top - margin.bottom;

		var header = d3.select("#chart").append("svg")
			.attr("width", width + margin.left + margin.right)
			.attr("height", 30)
			.append("g")
			.attr("transform", "translate(" + margin.left + "," + margin.top + ")");

		header.append("svg:text")
			.attr("class","headtext")
			.attr("x", function(d){ return  margin.left; } )
			.attr("y", 10)
			.text(unescape(centername) + ": Re-enrolment " + enrolyear);
			

			/* START filling arrays for this center*/
			var groups = [],
			ylabels = ['Group','Reenroling','Pending','Leavers'],
			reenroling = [],
			pending = [],
			leavers = [];

			var groupscells=document.getElementById(tableid).getElementsByClassName("xlabel");
			var reenrolingcells=document.getElementById(tableid).getElementsByClassName("reenroling");
			var pendingcells=document.getElementById(tableid).getElementsByClassName("pending");
			var leaverscells=document.getElementById(tableid).getElementsByClassName("leavers");
			for(var c=0; c < groupscells.length; c++){
				if(groupscells[c].hasChildNodes()){
					groups.push(groupscells[c].childNodes[0].data);

					var valuepair= new Object;
					if(reenrolingcells[c].hasChildNodes()){
						value=parseFloat(reenrolingcells[c].childNodes[0].data);
						}
					else{
						value=parseFloat('0');
						}
					valuepair.x=c;
					valuepair.y=value;
					reenroling.push(valuepair)

					var valuepair= new Object;
					if(pendingcells[c].hasChildNodes()){
						value=parseFloat(pendingcells[c].childNodes[0].data);
						}
					else{
						value=parseFloat('0');
						}
					valuepair.x=c;
					valuepair.y=value;
					pending.push(valuepair)

					var valuepair= new Object;
					if(leaverscells[c].hasChildNodes()){
						value=parseFloat(leaverscells[c].childNodes[0].data);
						}
					else{
						value=parseFloat('0');
						}
					valuepair.x=c;
					valuepair.y=value;
					leavers.push(valuepair)

					}
				}



			var groupnames = DistinctArray(groups,true);

			var data = new Array();
			data[2]=reenroling;
			data[1]=pending;
			data[0]=leavers;

			var color = d3.scale.ordinal()
				.range(["orangered", "goldenrod", "yellowgreen"]);

			var x = d3.scale
				.ordinal()
				.domain(d3.range(data[0].length))
				.rangeRoundBands([margin.left, width - margin.left], .1);

			var y = d3.scale
				.linear()
				.range([height, 0 + margin.left]);

			var yAxis = d3.svg.axis()
				.scale(y)
				.orient("left")
				.ticks(10);

			barStack(data);
			y.domain(data.extent);


			var chart = d3.select("#chart")
				.append("svg")
				.attr("height",height)
				.attr("width",width);

			chart.selectAll(".series")
				.data(data)
				.enter()
				.append("g")
				.classed("series",true)
				.style("fill", function(d,i) { return color(i); })
				.selectAll("rect")
				.data(Object)
				.enter()
				.append("rect");

			chart.append("g")
				.attr("class","axis")
				.call(yAxis);

			drawbars();


	function barStack(d) {
		var l = d[0].length;
		while (l--) {
			var posBase = 0, negBase = 0;
			d.forEach(function(d) {
				d=d[l];
				d.size = Math.abs(d.y);
				if (d.y<0)  {
					d.y0 = negBase;
					negBase-=d.size;
					} 
				else { 
					d.y0 = posBase = posBase + d.size;
					} 
				});
			}
		d.extent= d3.extent(d3.merge(d3.merge(d.map(function(e) { return e.map(function(f) { return [f.y0,f.y0-f.size]; }); }))));
		return d;
		}

	function drawbars() {
		var dur=0;
		/* Readjust the range to width and height */
		x.rangeRoundBands([height, 0 + margin.left], .1);
		y.range([margin.left, width - margin.left]);
			
		/* Reposition and redraw axis */
		chart.select(".axis")
			.attr("transform","translate (0 "+x(0)+")")
			.call(yAxis.orient("bottom"));
			
		/* Reposition the elements */
		chart.selectAll(".series rect")
			.attr("y",function(d,i) { return x(d.x)})
			.attr("x",function(d) { return y(d.y0-d.size)})
			.attr("width",function(d) { return y(d.size)-y(0)})
			.attr("height",x.rangeBand());	

		var labels = chart.selectAll(".grouplabel")
				.data(groupnames)
				.enter()
				.append("g")
				.classed("grouplabel",true)
				.append("svg:text")
				.attr("x", 0)
				.attr("y", function(d, i){ return x( i * x.rangeBand()) - margin.top - x.rangeBand() /2;} )
				.text(function(d,i) { if(i>0){ return d; } else { return '';} });
		}
	}


	/**
	 * Return an array of distinct values from an array with duplicates.
	 */
	function DistinctArray(array,ignorecase) {
		if(typeof ignorecase =="undefined"||array==null||array.length==0) return null;
		if(typeof ignorecase =="undefined") ignorecase=false;
		var sMatchedItems="";
		var foundCounter=0;
		var newArray=[];
		for(var i=0;i<array.length;i++) {
			var sFind=ignorecase?array[i].toLowerCase():array[i];
			if(sMatchedItems.indexOf("|"+sFind+"|")<0) {
				sMatchedItems+="|"+sFind+"|";
				newArray[foundCounter++]=array[i];
				}
			}
		return newArray;
		}

	document.getElementById('template-content').style.height='680px';

}

function admission_print(centerurl) {
	var username='classis';
	var password=document.getElementById('centerpassword').value;
	var date=document.getElementById('admissionsprintdate').value;
	var url='https://'+centerurl+'/../classnew/admin/httpscripts/admissions_print_new.php?enddate='+date+'&username='+username+'&password='+password;
	xmlHttp=new XMLHttpRequest();
	xmlHttp.open("GET", url, true);
	xmlHttp.onreadystatechange=function(){
		if(xmlHttp.readyState==4 && xmlHttp.status==200){
			var response=JSON.parse(xmlHttp.responseText);
			var xmlReport=response.html;
			parent.openModalWindow('',xmlReport,true);
			}
		};
	xmlHttp.send();
	}

