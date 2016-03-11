function tracking_summary_comparison(){
	
	}

function clickToCompare(){
	compareColumn=document.getElementById('compare');
	updateColumn(compareColumn);
	compareColumn.removeAttribute('style');
	}

function updateColumn(object){
	var childnodes=object.childNodes;

	if(childnodes[0].id=='assessment'){
		var assessid=childnodes[0].value;
		var classid=childnodes[2].value;
		var th=object;
		var colno=0;
		while((th=th.previousSibling)!=null){colno++;}
		}
	else{
		th=object.parentNode;
		if(object.id=='class'){
			var classid=object.value;
			var assessid=th.id;
			if(assessid=='compare'){var assessid=th.childNodes[0].value;}
			}
		else{
			var assessid=object.value;
			var classid=th.childNodes[2].value;
			}
		var colno=0;
		while((th=th.previousSibling)!=null){colno++;}
		}

	var tables=document.getElementById('graph').getElementsByTagName("tbody");
	for(var r=0; r<tables.length; r++){
		if(tables[r].id==assessid){
			var rows=tables[r].getElementsByTagName("tr");
			for(var c=0; c<rows.length; c++){
				if(rows[c].id=='res'){
					var res=rows[c];
					}
				if(rows[c].id==classid){
					var row=rows[c];
					break;
					}
				}
			break;
			}
		}

	var rows=document.getElementById('content').getElementsByTagName("tr");
	for(var j=0; j<rows.length; j++){
		for(var i=0;i<res.childNodes.length;i++){
			if(rows[j].childNodes[0].innerHTML!='' && rows[j].childNodes[0].innerHTML==res.childNodes[i].innerHTML){
				var t=document.createElement('table');
				t.className='subcell';
				var r=t.insertRow(0); 
				var c=r.insertCell(0);
				c.innerHTML=row.childNodes[i].innerHTML;
				if(parseFloat(c.innerHTML)>parseFloat(0.45)){c.className='hilife';}
				else if(parseFloat(c.innerHTML)>parseFloat(0.30)){c.className='golife';}
				else if(parseFloat(c.innerHTML)>parseFloat(0.15)){c.className='pauselife';}
				if(parseFloat(c.innerHTML)>0){c.innerHTML=(Math.round(parseFloat(c.innerHTML)*100))+" %";}
				if(childnodes[0].id=='assessment' && object.style.display=='none'){
					var newcell=rows[j].insertCell(colno);
					newcell.appendChild(t);
					}
				else{
					rows[j].childNodes[colno].innerHTML='';
					rows[j].childNodes[colno].appendChild(t);
					}
				}
			}
		}
	}
