function eyfs_statements_results() {
	var students = [], gender = [], age = [],
		language = [], headers = [],
		defdata = [], data = [];
	var genderindex='', dobindex='', langindex='';
	var m=new Array(), f=new Array();
	var youngest=new Array(), yothers=new Array();
	var lspa=new Array(), lothers=new Array();
	var averages=new Array(),averagerow="";

	var rows=document.getElementById("headers").getElementsByTagName("tr");
	for(var r=0; r<rows.length; r++){
		var tdcells=rows[r].getElementsByTagName("th");
		for(var c=0; c<tdcells.length; c++){
			if(tdcells[c].hasChildNodes()){
				if(tdcells[c].childNodes[0].data=='Gender'){genderindex=c;}
				if(tdcells[c].childNodes[0].data=='Date of Birth'){dobindex=c;}
				if(tdcells[c].childNodes[0].data=='Language'){langindex=c;}
				if(tdcells[c].childNodes[0].data=='Entry'){totalsindex=c;}
				headers.push(tdcells[c].childNodes[0].data);
				if(c>=6){averages[c]=0;}
				else{averages[c]=headers[c];}
				}
			}
		}

	var rows=document.getElementById("students").getElementsByTagName("tr");
	for(var r=0; r<rows.length; r++){
		temp = [];
		var studentsno=0;
		var tdcells=rows[r].getElementsByTagName("td");
		for(var c=0; c<tdcells.length; c++){
			if(tdcells[c].hasChildNodes()){
				temp.push(tdcells[c].childNodes[0].data);
				if(tdcells[c].childNodes[0].data){
					studentsno++;
					}
				}
			}
		if(tdcells[genderindex].innerHTML=='M'){m.push(rows[r]);}else{f.push(rows[r]);}
		if(tdcells[dobindex].innerHTML>=(tdcells[dobindex].innerHTML.substring(0,4)+'-09-01')){youngest.push(rows[r]);}else{yothers.push(rows[r]);}
		if(tdcells[langindex].innerHTML=='SPA'){lspa.push(rows[r]);}else{lothers.push(rows[r]);}
		defdata.push(rows[r]);
		}
	gender=[['Boys',m],['Girls',f]];
	age=[['Sep-Dec',youngest],['Jan-Aug',yothers]];
	language=[['Spanish',lspa],['Other',lothers]];

	for(var c=0;c<headers.length;c++){
		if(c>=6){
			var sum=0;
			for(var i=0;i<defdata.length;i++){
				sum+=parseInt(defdata[i].childNodes[c].innerHTML);
				}
			averages[c]=(sum/defdata.length).toFixed(0);
			data[0]=[[1,averages[totalsindex]],[3,averages[totalsindex+1]],[5,averages[totalsindex+2]],[7,averages[totalsindex+3]]];
			}
		}
	for(var c=0;c<headers.length;c++){
		if(c>=totalsindex){
			cl='totals';
			}
		else{cl='';}
		if(c>=6){
			averagerow+="<th class='averages "+cl+"'>"+averages[c]+"</th>";
			}
		}
	$("#averages tr").append(averagerow);
	termsChart(data);
	$(".name").click(function(){
		var i=0;var row=new Array();
		d3.selectAll("svg").remove();
		for(var c=0;c<headers.length;c++){
		if(c>=6){
			var sum=0;
			for(var i=0;i<defdata.length;i++){
				sum+=parseInt(defdata[i].childNodes[c].innerHTML);
				}
			averages[c]=(sum/defdata.length).toFixed(0);
			data[0]=[[1,averages[totalsindex]],[3,averages[totalsindex+1]],[5,averages[totalsindex+2]],[7,averages[totalsindex+3]]];
			}
		}
		data[1]=undefined;
		$("td.selected").attr('class',"details name");
		$(this).attr('class',"selected");
		$.each($(this).parent().get(0).childNodes,function(){
			if(i>=totalsindex){
				row[i]=this.innerHTML;
				}
			i++;
			});
		data[2]=[[1,row[totalsindex]],[3,row[totalsindex+1]],[5,row[totalsindex+2]],[7,row[totalsindex+3]]];
		termsChart(data);
		});

	$("#sort").change(function(){
		$("td.selected").attr('class',"details name");
		d3.selectAll("svg").remove();
		var def=false;
		if(this.value=='gender'){array=gender;}
		if(this.value=='age'){array=age;}
		if(this.value=='language'){array=language;}
		if(this.value=='default'){array=defdata;def=true}
		document.getElementById('results').parentNode.removeChild(document.getElementById('results'));
		var newtable=groupRows(array,headers,def);
		$('#t').append(newtable['table']);
		var d=newtable['averages'];
		termsChart(d);
		$(".name").click(function(){
			var i=0;var row=new Array();
			d3.selectAll("svg").remove();
			$("td.selected").attr('class',"details name");
			$(this).attr('class',"selected");
			$.each($(this).parent().get(0).childNodes,function(){
				if(i>=totalsindex){
					row[i]=this.innerHTML;
					}
				i++;
				});
			d[2]=[[1,row[totalsindex]],[3,row[totalsindex+1]],[5,row[totalsindex+2]],[7,row[totalsindex+3]]];
			termsChart(d);
			});
		});

	function termsChart(d){
		var margin = {top: 20, right: 15, bottom: 60, left: 60}
					, width = 450 - margin.left - margin.right
					, height = 350 - margin.top - margin.bottom;
		var dt=d[0],dt2=d[1],dt3=d[2];

		for(c=0;c<1;c++){
			var noentry=false, ts=false, k=-2;

			data=[[1,dt[0][1]],[3,dt[1][1]],[5,dt[2][1]],[7,dt[3][1]]];
			if(typeof dt2==='undefined'){
				data2=0;
				var second=0;
				}
			else{
				data2=[[1,dt2[0][1]],[3,dt2[1][1]],[5,dt2[2][1]],[7,dt2[3][1]]];
				var second=0.35;
				}
			if(typeof dt3==='undefined'){
				data3=0;
				var third=0;
				}
			else{
				data3=[[1,dt3[0][1]],[3,dt3[1][1]],[5,dt3[2][1]],[7,dt3[3][1]]];
				var third=0.70;
				}

			var maxvalue=0;
			$.each(data,function(index,value){
				if(parseFloat(value[1])>parseFloat(maxvalue)){maxvalue=parseFloat(value[1])+10;}
				});
			if(second!=0){
				$.each(data2,function(index,value){
					if(parseFloat(value[1])>parseFloat(maxvalue)){maxvalue=parseFloat(value[1])+10;}
					});
				}
			if(third!=0){
				$.each(data3,function(index,value){
					if(parseFloat(value[1])>parseFloat(maxvalue)){maxvalue=parseFloat(value[1])+10;}
					});
				}

			var x = d3.scale.linear()
					.domain([0, 7-k])
					.range([ 0, width ]);

			var y = d3.scale.linear()
					.domain([0, maxvalue])
					.range([ height, 0 ]);

			var chart = d3.select('body')
					.append('svg:svg')
					.attr('width', width + margin.right + margin.left)
					.attr('height', height + margin.top + margin.bottom)
					.attr('class', 'chart');

			var main = chart.append('g')
						.attr('transform', 'translate(' + margin.left + ',' + margin.top + ')')
						.attr('width', width)
						.attr('height', height)
						.attr('class', 'main');

			var ticks=new Array();
			if(c==0){
				ticks[1]='Entry';
				ticks[3]='End Term 1';
				ticks[5]='End Term 2';
				ticks[7]='End Term 3';
				}

			var xAxis = d3.svg.axis().scale(x).orient("bottom")
						.ticks(7)
						.tickValues([1,1, 3,3, 5,5, 7,7])
						.tickFormat(function (d, i) {
							return ticks[d];
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
				.text("Average");

			var g = main.append("svg:g");
			var main=g.selectAll("scatter-dots")
				.data(data)
				.enter();
			var line = d3.svg.line()
						.x(function(d,i) { return x(i); })
						.y(function(d) { return y(d); });
			main.append("line")
				.attr("x1", function(d,i){ if(i==0){return 0;}else{return x(data[i-1][0]);} })
				.attr("y1", function(d,i){ if(i==0){return height;}else{return y(data[i-1][1]);} })
				.attr("x2", function(d){ return x(d[0]);})
				.attr("y2", function(d){ return y(d[1]);})
				.style("stroke", "steelblue");
			main.append("circle")
					.attr("x", function (d) { return x(d[0]); } )
					.attr("y", function (d) { return y(d[1]); } )
					.attr("width", function (d,i) { return 50; } )
					.attr("height", function (d) { return height-y(d[1]); } )
					.style("fill", "steelblue");
			main.append("text")
					.attr("class", "value")
					.attr("x", function(d){ return x(d[0]);})
					.attr("y", function (d) { return y(d[1])-5; } )
					.attr("text-anchor", "right")
					.style("font-weight",600)
					.text(function(d){return "" + d[1] + "";});

			if(second!=0){
				var main2=g.selectAll("scatter-dots")
					.data(data2)
					.enter();
				main2.append("line")
						.attr("x1", function(d,i){ if(i==0){return 0;}else{return x(data2[i-1][0]);} })
						.attr("y1", function(d,i){ if(i==0){return height;}else{return y(data2[i-1][1]);} })
						.attr("x2", function(d){ return x(d[0]);})
						.attr("y2", function(d){ return y(d[1]);})
						.style("stroke", "firebrick");
				main2.append("circle")
						.attr("x", function (d) { return x(d[0]); } )
						.attr("y", function (d) { return y(d[1]); } )
						.attr("width", function (d,i) { return 50; } )
						.attr("height", function (d) { return height-y(d[1]); } )
						.style("fill", "firebrick");
				main2.append("text")
						.attr("class", "value")
						.attr("x", function(d){ return x(d[0]);})
						.attr("y", function (d) { return y(d[1])-5; } )
						.attr("text-anchor", "right")
						.style("font-weight",600)
						.text(function(d){return "" + d[1] + "";});
				}
			if(third!=0){
				var main3=g.selectAll("scatter-dots")
					.data(data3)
					.enter();
				main3.append("line")
						.attr("x1", function(d,i){ if(i==0){return 0;}else{return x(data3[i-1][0]);} })
						.attr("y1", function(d,i){ if(i==0){return height;}else{return y(data3[i-1][1]);} })
						.attr("x2", function(d){ return x(d[0]);})
						.attr("y2", function(d){ return y(d[1]);})
						.style("stroke", "green");
				main3.append("circle")
						.attr("x", function (d) { return x(d[0]); } )
						.attr("y", function (d) { return y(d[1]); } )
						.attr("width", function (d,i) { return 50; } )
						.attr("height", function (d) { return height-y(d[1]); } )
						.style("fill", "firebrick");
				main3.append("text")
						.attr("class", "value")
						.attr("x", function(d){ return x(d[0]);})
						.attr("y", function (d) { return y(d[1])-5; } )
						.attr("text-anchor", "right")
						.style("font-weight",600)
						.text(function(d){return "" + d[1] + "";});
				}
			}
		}

	function groupRows(array,headers,def){
		var averages=new Array(), result=new Array(), data=new Array();
		var table="<table id='results'>";
		table+="<thead id='headers'><tr>";
		for(var i=0;i<headers.length;i++){
			if(i>=6){averages[i]=0;}
			else{averages[i]=headers[i];}
			if(i==0){cl='details name';}
			else if(i<6 && i>0){cl='details';}
			else if(i>=totalsindex){cl='totals'}
			else{cl='';}
			table+="<th class="+cl+">"+headers[i]+"</th>";
			}
		table+="</tr></thead>";
		if(!def){
			mrows=array[0][1];
			frows=array[1][1];
			table+="<tbody id='students'><tr><th class='group' style='background-color:steelblue;'>"+array[0][0]+"</th></tr>";
			for(var i=0;i<mrows.length;i++){
				for(var c=0;c<headers.length;c++){
					if(c>=6){
						averages[c]+=parseInt(mrows[i].childNodes[c].innerHTML);
						}
					}
				table+="<tr>"+mrows[i].innerHTML+"</tr>";
				}
			for(var c=0;c<headers.length;c++){
				averages[c]=(averages[c]/mrows.length).toFixed(0);
				data[0]=[[1,averages[totalsindex]],[3,averages[totalsindex+1]],[5,averages[totalsindex+2]],[7,averages[totalsindex+3]]];
				}
			table+="<tr><th colspan='6'  class='averages'>Average</th>";
			for(var i=0;i<averages.length;i++){
				if(i>=totalsindex){cl='totals'}
				else{cl='';}
				if(i>=6){
					table+="<th class='averages "+cl+"'>"+averages[i]+"</th>";
					}
				}
			table+="</tr>";
			table+="</tbody>";
			for(var i=0;i<headers.length;i++){
				if(i>=6){averages[i]=0;}
				else{averages[i]=headers[i];}
				}
			table+="<tbody id='students'><tr><th class='group' style='background-color:firebrick;'>"+array[1][0]+"</th></tr>";
			for(var i=0;i<frows.length;i++){
				for(var c=0;c<headers.length;c++){
					if(c>=6){
						averages[c]+=parseInt(frows[i].childNodes[c].innerHTML);
						}
					}
				table+="<tr>"+frows[i].innerHTML+"</tr>";
				}
			for(var c=0;c<headers.length;c++){
				averages[c]=(averages[c]/frows.length).toFixed(0);
				data[1]=[[2,averages[totalsindex]],[4,averages[totalsindex+1]],[6,averages[totalsindex+2]],[8,averages[totalsindex+3]]];
				}
			table+="<tr><th colspan='6'  class='averages'>Average</th>";
			for(var i=0;i<averages.length;i++){
				if(i>=totalsindex){cl='totals'}
				else{cl='';}
				if(i>=6){
					table+="<th class='averages "+cl+"'>"+averages[i]+"</th>";
					}
				}
			table+="</tr>";
			table+="</tbody>";
			}
		else{
			table+="<tbody id='students'>";
			for(var i=0;i<array.length;i++){
				for(var c=0;c<headers.length;c++){
					if(c>=6){
						averages[c]+=parseInt(array[i].childNodes[c].innerHTML);
						}
					}
				table+="<tr>"+array[i].innerHTML+"</tr>";
				}
			for(var c=0;c<headers.length;c++){
				averages[c]=(averages[c]/array.length).toFixed(0);
				data[0]=[[1,averages[totalsindex]],[3,averages[totalsindex+1]],[5,averages[totalsindex+2]],[7,averages[totalsindex+3]]];
				}
			table+="<tr><th colspan='6' class='averages'>Average</th>";
			for(var i=0;i<averages.length;i++){
				if(i>=totalsindex){cl='totals'}
				else{cl='';}
				if(i>=6){
					table+="<th class='averages "+cl+"'>"+averages[i]+"</th>";
					}
				}
			table+="</tr>";
			table+="</tbody>";
			}
		result['table']=table;
		result['averages']=data;
		return result;
		}
	}
