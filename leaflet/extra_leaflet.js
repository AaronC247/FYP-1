	//creates the map and sets the view to be centred on Ireland
	var map = L.map('map').setView([53.5, -7.5], 7);

	//loads the tile map server to generate the cartographic map displayed
	L.tileLayer('https://api.tiles.mapbox.com/v4/{id}/{z}/{x}/{y}.png?access_token=pk.eyJ1IjoiamFob2J5IiwiYSI6ImNqZmRjNG45aDJsd2sycW1tdmF1YWY2ZDUifQ.uXB0N8LE4BVrsU5ZvK_8Mw', {
		maxZoom: 18,
		id: 'mapbox.light'
	}).addTo(map);
	
	//loads the generated output created by the PHP from our search
	geojson = L.geoJson(smallAreas, {
		style: style,
		onEachFeature: onEachFeature
	}).addTo(map);
	
	// control that shows state info on hover
	var info = L.control();

	info.onAdd = function (map) {
		this._div = L.DomUtil.create('div', 'info');
		this.update();
		return this._div;
	};

	info.update = function (area) {
		this._div.innerHTML = '<h4>Area Info</h4>' +  (area ?
			'<b>' + area.edname + '</b> <br/>' +
			' Overall Score: <b>' + area.score + '</b> <br/>' +
			'<br/> Score Breakdown <br/>' +
			+ area.housing_score + ' Housing Score' + '</b> <br/>'
			+ area.location_score + ' Location Score' + '</b> <br/>'
			+ area.healthcare_score + ' Healthcare Score' + '</b> <br/>'
			+ area.education_score + ' Education Score' + '</b> <br/>'
			+ area.amenities_score + ' Amenities Score' + '</b> <br/>'
			+ area.people_score + ' People Score' + '</b> <br/>'
			+ area.transport_score + ' Transport Score' + '</b> <br/>' 
			: 'Hover over a place');
			
	};
	info.addTo(map);

	//function to assign the colour given to each feature
	function getColour(score) {
		return score > 90 ? '#005a32' :
				score > 80  ? '#238443' :
				score > 70  ? '#41ab5d' :
				score > 60  ? '#78c679' :
				score > 50   ? '#addd8e' :
				score > 40   ? '#d9f0a3' :
				score > 30   ? '#f7fcb9' :
							'#ffffe5';
	}
	
	
	//function to process the appearance given to each feature
	//color is the boundary colour while fillColor is the contained within the boundary of the feature
	function style(feature) {
		return {
			weight: .1,
			opacity: 0.5,
			color: getColour(feature.properties.score),
			fillOpacity: 0.7,
			fillColor: getColour(feature.properties.score)
		};
	}
	
	//function highlights the area being hovered over by the user 
	function highlightFeature(e) {
		var layer = e.target;
		layer.setStyle({
			weight: 5,
			color: '#666',
			dashArray: '',
			fillOpacity: 0.7
		});

		if (!L.Browser.ie && !L.Browser.opera && !L.Browser.edge) {
			layer.bringToFront();
		}
		info.update(layer.feature.properties);
	}
	
	var geojson;
	var coords;
	var global_long_lat =[];
	var markers = new L.FeatureGroup();
	
	//gets the coords of the area clicked on the map
	function getLatLong(e) {
    	global_long_lat = [e.latlng.lat,e.latlng.lng];
	 	coords = getSWNE(e.latlng.lat,e.latlng.lng);
	 	console.log("getLatLong" + coords);
	}
	
	//adjusts the coords from the given coords in order to generate 
	//a value north east and south west
	function getSWNE(lat,long){
	
		//coord offset values
		lat_diff = 0.01322;
		long_diff = 0.01822;
	
		var coords = [];
		//sw coords
		coords[0] = lat - lat_diff;
		coords[1] = long - long_diff;
		//ne_coords
		coords[2] = lat + lat_diff;
		coords[3] = long + long_diff;
		
		return coords;
	}
	
	//creates the link used to pull the Daft information
	function createLink(type){
	
	//the type of the property is sent from the button which calls this function
	if(type == 1){
		rent_or_sale = 'sale';
	}
	else if(type == 2){
		rent_or_sale = 'rental';
	}
	else if(type == 3){
		rent_or_sale = 'sharing';
	}
		//string is made up of several elements and is then concatenated 
		var string_start = 'http://anyorigin.com/go?url=http%3A//www.daft.ie/ajax_endpoint.php%3Faction%3Dmap_nearby_properties%26type%3D';
    	var string_rent_or_sale = rent_or_sale;
    	var string_sw = '%26sw%3D%28';
    	var string_sw_lat = coords[0];
    	var string_join = '%252C+';
    	var string_sw_long = coords[1];
    	var string_ne = '%29%26ne%3D%28';
    	var string_ne_lat = coords[2];
    	var string_join1 = '%252C+';
    	var string_ne_long = coords[3];
    	var string_final = '%29&callback=?';
    	
    	var res = string_start.concat(string_rent_or_sale, 
    									string_sw,
    									string_sw_lat,
    									string_join,
    									string_sw_long,
    									string_ne,
    									string_ne_lat,
    									string_join1,
    									string_ne_long,
    									string_final);

		console.log(res);
		//the generated link is sent to the findhomes function in order to pull the data
		findHomes(res,rent_or_sale);
	}
	
	//find homes pulls the data from the daft url and displays it on the map
	function findHomes(link,rent_or_sale){
	
	var raw_data = new Array();
	var homes = [];
	var found = 0;
	//data is pulled using getJSON function
	$.getJSON(link, function(data){
	$('#output').html(data.contents);
	raw_data = data.contents;
	numHomes = raw_data.length;     

	console.log(raw_data);
	
	//for loop to process the data we want from the raw data
	for(var x = 0; x<raw_data.length; x++){
		var temp = [];
		temp[0] = raw_data[x].long;
		temp[1] = raw_data[x].lat;
		temp[2] = raw_data[x].link;
		temp[3] = raw_data[x].photo;
		if(rent_or_sale == 'sale'){
			temp[4] = '€'+raw_data[x].price;
		}
		else{
			temp[4] = '€'+raw_data[x].rent;
		}
		temp[5] = raw_data[x].summary;
		homes[x] = temp;
	}
	
	//clears any previous markers from older searches
	//from the map before adding new ones to the map
	markers.clearLayers();
    map.addLayer(markers);
	
	//creates the markers and the data stored inside the popup when the
	//marker is clicked on the map
	for(var i=0; i<raw_data.length; i++) {      
    	var lon = homes[i][0];
        var lat = homes[i][1];
            	
        var popupText = "<img src='"+homes[i][3]+"&previewImage=true'</img></a><br>"+
            	"<b>Description: </b>"+homes[i][5]+"<br>"+
				"<b>Price: </b>"+homes[i][4]+"<br>"+
				"<b>Link:<a href='https://daft.ie"+homes[i][2]+"' target=\"_blank\"></b>"+' Click Here'+"</a>";
				
        var markerLocation = new L.LatLng(lat, lon);
    	var marker = new L.Marker(markerLocation);
        marker.bindPopup(popupText);
        marker.addTo(markers);          	       
        
        //number of homes found
        found = homes.length;
        
        }
        //popup created to display the number of homes found from the search
        var popup = L.popup()
    	popup.setLatLng(global_long_lat)
    	popup.setContent(found+" properties for "+ rent_or_sale +"<br>found in this area")
    	popup.openOn(map);
        map.setZoom(13);
		
    });
    	//adds the markers to the map
    	markers.addTo(map);
    	geojson.eachLayer(function(layer) {
    	layer.invoke('closePopup');
	    });
	    
	}
	
	function resetHighlight(e) {
		geojson.resetStyle(e.target);
		info.update();
	}

	function zoomToFeature(e) {
		map.fitBounds(e.target.getBounds());
	}
	
	//function that lists the actives that occur when a
	//feature is interacted with in different ways
	function onEachFeature(feature, layer) {
		layer.on({
			mouseover: highlightFeature,
			mouseout: resetHighlight,
			click: zoomToFeature
		});
		layer.on("click", getLatLong);

	}
	
	
	//creates the deafault popup that is shown when any feature is clicked
	var tempID = 1;
	geojson.eachLayer(function(layer) {
  	layer.feature.properties.layerID = tempID;
  	tempID+=1;
  			layer.bindPopup("Find Homes near <b>" + layer.feature.properties.edname 
  			+ "</b><br><br><button type='button' onclick='createLink(1)'>For Sale</button>" 
  			+ "<br><button type='button' onclick='createLink(2)'>For Rent</button>"
			+ "<br><button type='button' onclick='createLink(3)'>Sharing</button>");	
	
	});
	
	//button in the top 15 list that zooms to the location on the map
	zoomToLocationOnMap = function (geojson, ID) {
		area_ID = parseInt(top15List[ID]);
		geojson.eachLayer(function(layer) {
  		if (layer.feature.properties.layerID === area_ID) {
    		map.fitBounds(layer.getBounds());
        	var test = layer.feature.geometry.coordinates[0][0][0];
     		coords = getSWNE(test[1],test[0])
     		global_long_lat = [test[1],test[0]];
        	layer.openPopup();
    	}
  	});
	}
	
	//displays the scoring legend
	var legend = L.control({position: 'bottomright'});

	legend.onAdd = function (map) {
		var div = L.DomUtil.create('div', 'info legend'),
			scores = [0, 30, 40, 50, 60, 70, 80, 90],
			title = ['<strong>Score Legend</strong>'],
			from, to;

		for (var i = 0; i < scores.length; i++) {
			from = scores[i];
			to = scores[i + 1];

			title.push(
				'<i style="background:' + getColour(from + 1) + '"></i> ' +
				from + (to ? '&ndash;' + to : '&ndash;100'));
		}

		div.innerHTML = title.join('<br>');
		return div;
	};

	legend.addTo(map);