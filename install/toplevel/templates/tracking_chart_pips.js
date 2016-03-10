function tracking_chart_pips() {
	window.resizeTo(1010, 680);

	var students = [],
	data1 = [],
	averages = [],
	names = [],
	data = [];
	//accesses first element with the specified ID then gets table row
	var rows=document.getElementById("graph_data1").getElementsByTagName("tr");
	
	totalsum=0;
	assessno=0;
	
	
	// function to round data for columns SSM, SSR, SSP and SST
	function roundData() {
    
	noOfElements = document.getElementById("data").children.length;
	for(var s=2; s < noOfElements; s++) {
		var sstCol = String(Math.round(Number(document.getElementById("data").children[s].children[1].innerHTML)));
		document.getElementById("data").children[s].children[1].innerHTML = sstCol;	
	}
	
	//noOfRows = document.getElementById("data").children[2].children.length;	

	for(var a=2; a < noOfElements; a++) {
		var noOfRows = document.getElementById("data").children[a].children.length;			

		for(var b=3; b < noOfRows; b++) {
			var otherCols = String(Math.round(Number(document.getElementById("data").children[a].children[b].innerHTML)));
			document.getElementById("data").children[a].children[b].innerHTML = otherCols;
			}
		}
	}
	
	
	
	function createMenu() {
	    var menuSec = document.createElement("div");
	    menuSec.width = "18";
	    menuSec.height = "18";
	    menuSec.innerHTML = "<select id='subjectselect'> <option selected='selected' value='-1'>Select Subject</option> \
	                         <option value='0'>SSM</option>  \
                             <option value='1'>SSR</option>  \
                             <option value='2'>SSP</option></select>"; 
        menuSec.style.position = "absolute";
        menuSec.style.left = "88%";
        menuSec.style.top = "40px"                      
	    
	    var elementName = document.getElementsByClassName("head")[0];
	    var childName = elementName.children[1];
	    elementName.insertBefore(menuSec, childName);
	    
	    var legend = document.createElement("div");
	    legend.width = "18";
	    legend.height = "18";
	    
	    legend.innerHTML = "PIPS - SST";
	    
	    legend.style.position = "absolute";
        legend.style.left = "88%";
        legend.style.top = "20px";
        
            
	    
	   
	    
	    elementName.insertBefore(legend, childName);
	    
	    var circleMenu = d3.select(".head").append("svg")
	        .attr("width",50)
	        .attr("height",50)
	        .style("position", "absolute")
	        .style("top", "15px")
	        .style("left", "86%")	        
	        .append("g");
	    
	    circleMenu
	        .append("circle")	        
	        .attr("cx", 10)
	        .attr("cy", 38)
	        .attr("r", 4)
	        .style("fill", "#33CC33"); 
	    
        circleMenu
        .append("circle")	        
	        .attr("cx", 10)
	        .attr("cy", 10)
	        .attr("r", 4)
	        .style("fill", "steelblue"); 

	    
	         
	        
    }	
    
    function getData(colString) {
        //rowsLength = document.getElementById("data").getElementsByTagName("tr").length;
        
        rowArr = [];
        var numColLength = document.getElementById("data").children[0].children.length;
        for(var numCol = 2; numCol < numColLength; numCol++) {
            var headerColumn = document.getElementById("data").children[0].children[numCol].innerHTML;
            console.log(headerColumn + colString);
            if(headerColumn == colString) {
                var colNum = numCol + 1;
                }
            }
        //for(var rowsData = 2; rowsData < rowsLength; rowsData++) {
        var dataArr = [];
        var colLength = document.getElementById("data").children.length;
        
        for(var rowNum = 2; rowNum < colLength; rowNum++) {
                var checkElements = document.getElementById("data").children[rowNum].children.length;
                if(checkElements > 3) {
                    var scoreData = document.getElementById("data").children[rowNum].children[colNum].innerHTML;
                    dataArr.push(parseFloat(scoreData));
                    }
                else {
                    data.push(parseFloat('0'));
                    }    
            }  
            
        rowArr[0] = dataArr; 
        //}
        return rowArr;
    }
	
	

	//Call function to round data
	//roundData();	
	
	//removeData();
	//createHeaders();
	
    
	for(var r=0; r<rows.length; r++){
		temp = [];
		var assess_sum=0;
		var studentsno=0;
		
		var tdcells=rows[r].getElementsByTagName("td");
		//"th" table heading
		var thcells=rows[r].getElementsByTagName("th");
		//iterates through columns
		for(var c=0; c<tdcells.length; c++){
			// check to see if tdcells[c] has child node
			if(tdcells[c].hasChildNodes()){
				//.push:Adds an element to an array, parsefloat: string to float
				temp.push(parseFloat(tdcells[c].childNodes[0].data));
				if(parseFloat(tdcells[c].childNodes[0].data)>0){
					assess_sum+=parseFloat(tdcells[c].childNodes[0].data);
					//increases student number
					studentsno++;
					}
				}
			    else{
				// else give a floating value of zero
				temp.push(parseFloat('0'));
				}
			}
		average= assess_sum/studentsno;
		//For each row add the array of column values
		data1[r]=temp;
		}
	data=data1;
		
	
	//graph_axis refers to student names info
	var tdcells=document.getElementById("graph_axis").getElementsByTagName("td");
	var sum2=0;
	for(var c=0; c<tdcells.length; c++){
		if(tdcells[c].hasChildNodes()){
			//.data returns of selected node
			students.push(tdcells[c].childNodes[0].data);
			}
		}

	
	
	//width of graph
	var width = 450,
	height = 20;
	// x axis
	var x = d3.scale.linear()
		// range from 0 to 100
		.domain([0, 100])
		.range([ 0, width ]);
	//creates axis
	var xAxis = d3.svg.axis()
		.scale(x)
		//responsible for positioning the numbers below 
		.orient("bottom");
	
	averagediv=d3.select("#average").append("svg")
			//sets width to 70 
			.attr("width", 70)
			//sets height to 15
			.attr("height", 15)
			.append("g")
			.attr("transform", "translate(0,0)");
	//Average value graphic
	averagediv.append("svg:rect")
		//sets x to 0
		.attr("x", 0 )
		//set y to 0
		.attr("y", 0 )
		.attr("width", 50)
		.attr("height", 15)		
		.style("fill", "steelblue");
	
	averagediv.append("text")
		.attr("x", 4 )
		.attr("y", 7 )
		.style("fill","white")
		.attr("dy", ".35em")
		.text(" "+average.toFixed(2));
	//creates graphics for all students
	
	//removeData();
	//createHeaders();
	createMenu();
	roundData();
	data_ssm = getData('SSM');
	data_ssr = getData('SSR');
	data_ssp = getData('SSP');
	
	
	// turn SST header to blue
	//document.getElementById(data).
	
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
		//Standard deviation graphic
		svg.append("svg:rect")
			.attr('class', 'zone')
			.attr('x', function (d,i) { return x(40); })
			.attr('y', function (d,i) { return 0; })
			.attr('width', x(110)- x(90))
			.attr('height', height);
		//Creates average markings
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

	
	//SST header
	sstHeader = document.getElementById("data").children[1].children[0];
	sstHeader.style.backgroundColor = "steelblue";

	$('#subjectselect').change( function() {
	    var subjectindex = $('#subjectselect').val();
	    var subject = $('#subjectselect option:selected').text();  
	    //console.log(subject);
        function menuSelect() {
            if(subject == 'SSM') {
                return data_ssm;
                }
            else if(subject == 'SSR') {
                return data_ssr;
                }
            else if(subject == 'SSP') {
                return data_ssp;
                }
        }    
                
	    
	        for(r=0;r<students.length;r++){
	           
	            d3.select("#graphicattach-"+r).select('svg').select('g').select('circle.temp')
				.data([])
				.exit()
				.remove();
	            }
	            
	        var colCountLength = 5;    
	        for(var colCount = 2; colCount < colCountLength; colCount++) {
	            colHeader = document.getElementById("data").children[0].children[colCount];
	            colHeader.removeAttribute("style");
	        }    
	        
	        var selectArr = menuSelect();
	        //var headerNum = Number(subjectindex) + 2;
	        
	        function matchHeader() {
	            var columnsLength = document.getElementById("data").children[0].children.length;
	            for(var columnNum = 2; columnNum < columnsLength; columnNum++) {
	                var columnName = document.getElementById("data").children[0].children[columnNum].innerHTML; 
	                
	                if(subject == columnName) {
	                    console.log(columnNum);
	                    return columnNum;
	                    }       
	                }
	        }
	        
	        var headerNum = matchHeader();
	        
	        //console.log(headerNum);
	        var subjectHeader = document.getElementById("data").children[0].children[headerNum];
	        if(headerNum > 1) {
	            subjectHeader.style.backgroundColor = "#33CC33";
	            }
	        //subjectHeader.removeAttribute("style");
	        for(r=0;r<students.length;r++){
	            if(selectArr[0][r]>0){
 	                var cellchart=d3.select("#graphicattach-"+r).select('svg').select('g');
	                //console.log(cellchart);
			        cellchart.append("g:circle")
			            .attr("cx", function (d,i) { return x(selectArr[0][r]); } )
			            .attr("cy", function (d,i) { return 10; } )
			            .attr("class", "temp")//Give class name to separate from SST circles
			            .attr("r", 4)
			            .style("fill", "#33CC33");   
		            }
		        }
		    
		    
		
	});			
	
	var height = 50;
	//Positioning of the range scale
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

	
	
