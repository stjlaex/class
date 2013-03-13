  //Variables for display map and store the geocoded address
  var geocoder;
  var map;
  //Variables for displaying routes
  var directionsDisplay;
  var directionsService=new google.maps.DirectionsService();


  //Initializes the map, place the marker and calculates times calling the functions
  function initAddressMap() {
    directionsDisplay=new google.maps.DirectionsRenderer();
    //Gets the contact latitute and longitude
    lat=document.getElementById("lat").value;
    lon=document.getElementById("lon").value;
    geocoder=new google.maps.Geocoder();
    latlng=new google.maps.LatLng(lat,lon);
    var mapOptions= {
      zoom: 16,	
      center: latlng,
      mapTypeId: google.maps.MapTypeId.ROADMAP
    }
    codeAddress();
    //calculateDistances();
    map=new google.maps.Map(document.getElementById("map_canvas"), mapOptions);
    setmarker(latlng);
    directionsDisplay.setMap(map);
    calcPublicRoute();
    calcCarRoute();
  }


  //Translates the name of the address to latlng and sets the map's limits
  function codeAddress() {
    origin="("+lat+","+lon+")";
    //Gets the address name
    var complete_address=document.getElementById("address_map").value;
    if(lat==0 && lon==0 || lat>=85.0511 || lat<=-85.0511 && complete_address!=null) {
      origin=complete_address;
      geocoder.geocode( { "address": complete_address}, function(results, status) {
        if (status==google.maps.GeocoderStatus.OK) {
          map.setCenter(results[0].geometry.location);
          setmarker(results[0].geometry.location);
        } 
      });
    }
  }


  //Returns the route between the student's home and school (works for Transit, Driving and Walking)
  //Car time and route
  function calcCarRoute() {
	var car_request= {
      		origin: origin,
      		destination: destination,
		travelMode: google.maps.TravelMode.DRIVING
 	};
	directionsService.route(car_request, function(car_result, status) {
		var outputCarDiv=document.getElementById('car_time');
    		if (status==google.maps.DirectionsStatus.OK) {  //outputs the duration and shows/hides route
 			outputCarDiv.innerHTML="<strong>Car: </strong>";
      			outputCarDiv.innerHTML+=car_result.routes[0].legs[0].duration.text;
      			if(document.getElementById('display_car_route').checked==true) { 
				directionsDisplay.setDirections(car_result);
				document.getElementById('display_public_route').checked=false;
			}
                        if(document.getElementById('display_car_route').checked==false) {
    				directionsDisplay.setDirections({routes: []});
				map.setZoom(16);
				var latlong=car_result.routes[0].legs[0].steps[0].start_location;
				map.setCenter(latlong);
			}
    		}
		//if (status==google.maps.DirectionsStatus.OVER_QUERY_LIMIT) {
		//	alert(status+' Limit per second reached');
		//}
		if (status==google.maps.DirectionsStatus.ZERO_RESULTS) {
			document.getElementById('car').style.visibility='hidden';
		}
  	});
  }


  //Public transport time and route
  function calcPublicRoute() {
  	var public_request= {
      		origin: origin,
      		destination: destination,
		travelMode: google.maps.TravelMode.TRANSIT
 	};
	directionsService.route(public_request, function(public_result, status) {
		var outputPublicDiv=document.getElementById('public_time');
    		if (status==google.maps.DirectionsStatus.OK) {  //outputs the duration and shows/hides route
 			outputPublicDiv.innerHTML="<strong>Public transport: </strong>";
      			outputPublicDiv.innerHTML+=public_result.routes[0].legs[0].duration.text;
      			if(document.getElementById('display_public_route').checked==true) { 
				directionsDisplay.setDirections(public_result);
				document.getElementById('display_car_route').checked=false;
			}
                        if(document.getElementById('display_public_route').checked==false) {
    				//directionsDisplay.setMap(null);
    				directionsDisplay.setDirections({routes: []});
				map.setZoom(16);
				var latlong=public_result.routes[0].legs[0].steps[0].start_location;
				map.setCenter(latlong);
			}
    		}
		//if (status==google.maps.DirectionsStatus.OVER_QUERY_LIMIT) {
		//	alert(status+' Limit per second reached');
		//}
		if (status==google.maps.DirectionsStatus.ZERO_RESULTS) {
			document.getElementById('transit').style.visibility='hidden';
		}
  	});
  }


  //Sets the marker for student's home
  function setmarker(latlon) {
	var marker=new google.maps.Marker({
		position: latlon,
		title: 'Student',
		zIndex: 1000
  	});      
	marker.setMap(map);
  }
