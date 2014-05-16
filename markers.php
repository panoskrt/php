<?php
   include 'db-connect.php'; // Connect to your database
   include 'functions.php'; // Declaration of various functions
?>
<!DOCTYPE html>
<html> 
<head>
  <meta http-equiv="content-type" content="text/html; charset=UTF-8" /> 
  <title>Google Maps Multiple Markers</title> 
  <script src="http://maps.google.com/maps/api/js?sensor=false" type="text/javascript"></script>
</head> 
<body>
<?php
  <div id="map" style="width: 600px; height: 500px;"></div>

  <script type="text/javascript">
    <?php
      $tasks = getAllTasks(); // This is declared in functions.php - you have to adjust this depending on your DB and task
      echo"var locations = [";
      while($row = mysql_fetch_array($tasks)) {
         $loc = greek_to_greeklish($row['location']);
         $url = ("https://maps.googleapis.com/maps/api/geocode/json?address=".$loc."&sensor=false&key=YOUR_KEY");
         $json = json_decode(file_get_contents($url));
         $geoloc =  $json->results[0];
         $latitude = geoloc->geometry->location->lat;
         $longitude = $geoloc->geometry->location->lng;

         echo "["."'".$row['title']."'".",".$latitude.", ".$longitude.", 1], "; // 'title' field need to change to what your DB field if there's one
      }
    echo "];";
    ?>
    var map = new google.maps.Map(document.getElementById('map'), {
      zoom: 0,6 // How far in or out you want the initial map
      center: new google.maps.LatLng(39.074, 21.824), // Initial coordinates for Greece
      mapTypeId: google.maps.MapTypeId.ROADMAP
    });

    var infowindow = new google.maps.InfoWindow();

    var marker, i;

    for (i = 0; i < locations.length; i++) {  
      marker = new google.maps.Marker({
        position: new google.maps.LatLng(locations[i][1], locations[i][2]),
        map: map
      });

      google.maps.event.addListener(marker, 'click', (function(marker, i) {
        return function() {
          infowindow.setContent(locations[i][0]);
          infowindow.open(map, marker);
        }
      })(marker, i));
    }
  </script>
</body>
</html>
