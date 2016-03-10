function tracking_barchart_app() {


	var students=document.getElementsByClassName("name");

    var width = 900, height = 600;
	var barwidth = width - 140, barheight = 40;
    var r = Raphael("chart", width, height);
	r.rect(0, 0, width, height, 5).attr({fill: "#fff", stroke: "none"});

    var labeltxt = {"font": '14px "Arial"', stroke: "none", fill: "#000"};


 for(var sidno=0; sidno<students.length; sidno++){
	 var studentname=students[sidno].childNodes[0].data

     var p1 = [],
     p2 = [],
     p3 = [],
     p4 = [],
     p5 = [];


     var tdcells=document.getElementById(studentname).getElementsByClassName("value");
	 var v1=tdcells[0].childNodes[0].data;
	 var v2=tdcells[1].childNodes[0].data;
	 var v3=tdcells[2].childNodes[0].data;
	 var v4=tdcells[3].childNodes[0].data;
	 var v5=tdcells[4].childNodes[0].data;

	 var TOT=200;

	 var sidwidth=v1/TOT*barwidth;

	 p1.push(TOT*((v1-v2)/sidwidth));
	 p2.push(TOT*((v2-v3)/sidwidth));
	 p3.push(TOT*((v3-v4)/sidwidth));
	 p4.push(TOT*((v4-v5)/sidwidth));


	 r.rect(20, sidno*barheight+25, sidwidth, barheight-15, 5).attr({fill: "#ddd", stroke: "none"});
     r.text(100, sidno*barheight+20, studentname).attr(labeltxt);


	 var fin = function () {
		this.flag = r.popup(this.bar.x, this.bar.y, this.bar.value || "0").insertBefore(this);
		},
	 fout = function () {
		this.flag.animate({opacity: 0}, 150, function () {this.remove();});
		};

 	 r.hbarchart(20,sidno*barheight+20,sidwidth/2,40,[p4,p3,p2,p1]).hover(fin,fout);

 }
	/* x-axis labels for each group 
    var X = (barwidth) / (groups.length + 1);
    for (var i = 0, ii = groups.length; i < ii; i++) {
        r.text(X * (i+1) + 60, height - 20, groups[i]).attr(labeltxt);
		}
	*/
}
