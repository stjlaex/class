var thismap;

var markersArray = [];

var groupsArray = [];

var iconsArray = [];

var homeno;


function initialize() {

    var mapOptions = {
		zoom: 10,
		mapTypeId: google.maps.MapTypeId.ROADMAP
		};

    thismap = new google.maps.Map(document.getElementById("map_canvas"), mapOptions);

	loadCenters();

	}

function loadCenters() {
	var centerTables=document.getElementsByTagName("table");
	var centerno=0;

	for(var n=0; n<centerTables.length; n++){
		if(centerTables[n].id!=''){

			var newcenter=centerTables[n].rows[0].cells[0].firstChild.nodeValue.split(',');
			var center_LatLng = new google.maps.LatLng(newcenter[0],newcenter[1]);
			if(n==0){
				thismap.setCenter(center_LatLng);
				}

			var center_marker = new google.maps.Marker({
				position: center_LatLng,
				map: thismap,
				title: centerTables[n].id
				});

			centerno=n;
			loadHomes(centerno,centerTables[n].id);

			}
		}
	}

function loadHomes(centerno,tableId) {
	var tableRows=document.getElementById(tableId).rows;
	var newhome;
	var newhomeLatLng;

	for(var rown=0; rown<tableRows.length; rown++){
		if(tableRows[rown].id!=''){
			var rowCells=tableRows[rown].cells;
			groupid=tableRows[rown].id;
			groupsArray[groupid]=[];
			for(var celln=0; celln < rowCells.length; celln++){
				newhome=rowCells[celln].firstChild.nodeValue.split(',');
				newhomeLatLng = new google.maps.LatLng(newhome[0],newhome[1]);
				//alert(rown+' : '+celln+' : '+newhome[0]);
				addMarker(centerno,groupid,newhomeLatLng);
				}
			}
		}
	}

//
function addMarker(centerno,groupid,location){
	var marker = new google.maps.Marker({
		position: location,
		icon: iconsArray[centerno],
		map: thismap
		});
	if(groupsArray[groupid]){
		groupsArray[groupid].push(marker);
		}
	}

// Removes the overlays from the map, but keeps them in the array
function changeOverlays(inputId){
	if(document.getElementById(inputId).checked){
		showOverlays(inputId);
		}
	else{
		clearOverlays(inputId);
		}
	}

// Removes the overlays from the map, but keeps them in the array
function clearOverlays(groupid){
	if(groupsArray[groupid]){
		for(i in groupsArray[groupid]){
			groupsArray[groupid][i].setMap(null);
			}
		}
	}

// Shows any overlays currently in the array
function showOverlays(groupid){
	if(groupsArray){
		for(i in groupsArray[groupid]){
			groupsArray[groupid][i].setMap(thismap);
			}
		}
	}

var homeIcon0 = {
  path: 'M 125,5 155,90 245,90 175,145 200,230 125,180 50,230 75,145 5,90 95,90 z',
  fillColor: "red",
  fillOpacity: 0.8,
  scale: 0.005,
  strokeColor: "red",
  strokeWeight: 8
};

iconsArray.push(homeIcon0);

var homeIcon1 = {
  path: 'M 125,5 155,90 245,90 175,145 200,230 125,180 50,230 75,145 5,90 95,90 z',
  fillColor: "blue",
  fillOpacity: 0.8,
  scale: 0.005,
  strokeColor: "blue",
  strokeWeight: 8
};

iconsArray.push(homeIcon1);

var homeIcon2 = {
  path: 'M 125,5 155,90 245,90 175,145 200,230 125,180 50,230 75,145 5,90 95,90 z',
  fillColor: "green",
  fillOpacity: 0.8,
  scale: 0.005,
  strokeColor: "green",
  strokeWeight: 8
};

iconsArray.push(homeIcon2);
