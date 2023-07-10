#index.php
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Realtime location tracker</title>

    <!-- leaflet css  -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.7.1/dist/leaflet.css" />

    <style>
        body {
            margin: 0;
            padding: 0;
        }

        #display_data {
            align: center;
            top: 25px;
            width: 50%;
            height: 25px;
            left: 400px; 
            border: 3px solid #73AD21;
        }

        #map {
            align: center;
            top: 100px;
            width: 50%;
            height: 500px;
            left: 400px; 
            border: 3px solid #73AD21;
        }
    </style>
</head>

<body bgcolor="Orange">
<center>
<h1 text="white">GPS Location ON MAP </h1>
</center>
        <div>
                <div style="margin: 31px 0 -80px 46%;">
                        <label for="map-lat">Latitude :</label>
                        <input type='text' id='map-lat' value='' />
                        <label for="map-lon">Longitude :</label>
                        <input type='text' id='map-lon' value='' />
                </div>
                <div id="map"></div>
        </div>
</body>
</html>

<!-- leaflet js  -->
<script src="https://unpkg.com/leaflet@1.7.1/dist/leaflet.js"></script>
<script>
    // Map initialization 
    var map = L.map('map').setView([18.9220, 72.8347], 6);

    //osm layer
    var osm = L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
    });
    osm.addTo(map);

    if(!navigator.geolocation) {
        console.log("Your browser doesn't support geolocation feature!")
    } else {
        setInterval(() => {
            navigator.geolocation.getCurrentPosition(getPosition)
        }, 5000);
    }

    var marker, circle;

        function showLatLon() {
                console.log('--------->');
                var xmlhttp = new XMLHttpRequest();
                xmlhttp.onreadystatechange = function() {
                        if (this.readyState == 4 && this.status == 200) {
                                positionResponse = JSON.parse(this.responseText);
                                console.log(positionResponse);
                                document.getElementById("map-lat").value = positionResponse['latitude'];
                                document.getElementById("map-lon").value = positionResponse['longitude'];

                                lat = positionResponse['latitude'];
                                long = positionResponse['longitude'];
                        }
                };
                xmlhttp.open("GET", "get_location.php", true);
                xmlhttp.send();
                console.log('--------->');
        }
    
    function getPosition(position){
        showLatLon();    
        var lat = document.getElementById("map-lat").value;
        var long = document.getElementById("map-lon").value;

        var accuracy = position.coords.accuracy

        if(marker) {
            map.removeLayer(marker)
        }

        if(circle) {
            map.removeLayer(circle)
        }

        marker = L.marker([lat, long])
        circle = L.circle([lat, long], {radius: accuracy})

        var featureGroup = L.featureGroup([marker, circle]).addTo(map)

        map.fitBounds(featureGroup.getBounds())

        console.log("Your coordinate is: Lat: "+ lat +" Long: "+ long+ " Accuracy: "+ accuracy)
    }

</script>