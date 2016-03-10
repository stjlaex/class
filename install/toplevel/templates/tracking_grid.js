function tracking_grid() {
	// Creates canvas 320 × 200 at 10, 50
	//var paper = Raphael(10, 50, 320, 200);
	// Creates circle at x = 50, y = 40, with radius 10
	//var circle = paper.circle(50, 40, 10);
	// Sets the fill attribute of the circle to red (#f00)
	//circle.attr("fill", "#f00");
	// Sets the stroke attribute of the circle to white (#fff)
	//circle.attr("stroke", "#fff");
    var data = [],
		cdata = [],
		siddata = [],
        axisx = [],
        axisy = [],
        axisyextra = [];

    var dcells=document.getElementById("graph_data").getElementsByTagName("td");
	var dy=document.getElementById("graph_data").getElementsByTagName("th");
	var dx=document.getElementById("graph_axis").getElementsByTagName("th");

	for(var c=0; c<dcells.length; c++){
		if(dcells[c].hasChildNodes()){
			data.push(parseFloat(dcells[c].childNodes[0].data,10));
			siddata.push(dcells[c].getElementsByTagName("li"));
			}
		else{
			data.push(parseFloat('0', 10));
			siddata.push();
			}
		var inheritedstyle=document.defaultView.getComputedStyle(dcells[c],"");
		cdata.push(inheritedstyle.backgroundColor);
		}
	for(var c=0; c<dx.length; c++){
		axisx.push(dx[c].childNodes[0].data);
		}
	for(var c=0; c<dy.length; c++){
		axisy.push(dy[c].childNodes[0].data);
		axisyextra.push(dy[c].childNodes[1].firstChild.nodeValue);
		}

    var width = 800,
        height = 300,
        leftgutter = 40,
        bottomgutter = 20,
        r = Raphael("chart", width, height),
        txt = {"font": '12px "Arial"', stroke: "none", fill: "#000"},
        txtextra = {"font": '11px "Arial"', stroke: "none", fill: "#666"},
        X = (width - leftgutter) / (axisx.length + 1),
        Y = (height - bottomgutter) / axisy.length,
        color = "#000";
        max = Math.round(X / 2) - 1;
		r.rect(0, 0, width, height, 5).attr({fill: "#eee", stroke: "none"});
    for (var i = 0, ii = axisx.length; i < ii; i++) {
        r.text(leftgutter + X * (i + 1.5), 294, axisx[i]).attr(txt);
		}
    for (var i = 0, ii = axisy.length; i < ii; i++) {
        var label=r.text(leftgutter, Y * (i + .4), axisy[i]).attr(txt);
        var label=r.text(leftgutter, Y * (i + .8), axisyextra[i]).attr(txtextra);
		}
    var o = 0;
    for (var i = 0, ii = axisy.length; i < ii; i++) {
        for (var j = 0, jj = axisx.length; j < jj; j++) {
            var R = data[o] && Math.min(Math.round(Math.sqrt(data[o] / Math.PI) * 4), max);
            if (cdata[o]) {
                (function (dx, dy, R, value, sidlist) {
                    var color = "#066";
                    var dc = r.rect(leftgutter + X*(j+1), Y*i, X, Y ,0).attr({stroke: "#eee", fill: cdata[o]});
					if (R) {
						var dxr = dx;
						var dt = r.circle(dxr, dy + 10, R).attr({stroke: "none", fill: color});
						if(R < 6){
							var bg=r.circle(dxr, dy + 10, 6).attr({stroke: "none", fill: "#000", opacity: .4}).hide();
							}
						//var frame = r.rect(dx+60+20, dy+10-50, 100, 40, 5).attr({fill: "#666", stroke: "#474747", "stroke-width": 2}).hide();
						var lbl = r.text(dxr, dy + 10, data[o])
                            .attr({"font": '11px "Arial"', stroke: "none", fill: "#fff"}).hide();
						var dot = r.circle(dxr, dy + 10, max).attr({stroke: "none", fill: "#000", opacity: 0});
						dot[0].onclick=function () {
							var display=document.createElement('ul');
							for(var item=0;item<sidlist.length;item++){
								var content=document.createElement('li');
								var sid=sidlist[item].childNodes[0].data;
								var student=document.createTextNode(document.getElementById(sid).childNodes[0].data);
								content.appendChild(student);
								display.appendChild(content);
								}
							document.getElementById("panel").innerHTML='';
							document.getElementById("panel").appendChild(display);
							};
						dot[0].onmouseover = function () {
							if(bg){
								bg.show();
								} 
							else{
								var clr = Raphael.rgb2hsb(color);
								clr.b = .4;
								dt.attr("fill", Raphael.hsb2rgb(clr).hex);
								}
							lbl.show();
							};
						dot[0].onmouseout = function () {
							if(bg){
								bg.hide();
								} 
							else{
								dt.attr("fill", color);
								}
							lbl.hide();
							};
						}
					})(leftgutter + X * (j + 1.5), Y * (i + .5) - 10, R, data[o], siddata[o]);
				}
		    o++;
			}
		}
	}
