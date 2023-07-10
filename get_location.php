#get_location.php

<?php
//require __DIR__ . '/vendor/autoload.php';

$servername = "RDS_HOST_ARN";
$username = "USER_NAME";
$password = "PASSWORD";
$dbSelect = 'DB_NAME';

$location = [];
// Create connection
$conn = new mysqli($servername, $username, $password, $dbSelect);

// Check connection
if ($conn->connect_error) {
  die("Connection failed: " . $conn->connect_error);
}
//echo "Connected successfully";

$sql = "select * from gpsdata order by id desc limit 1";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
  // output data of each row
  while($row = $result->fetch_assoc()) {
         // echo '<pre>'; print_r($row);
          //echo "id: " . $row["id"]. " - Name: " . $row["firstname"]. " " . $row["lastname"]. "<br>";
         $location = [
            'latitude' => $row['lat'],
            'longitude' => $row['lon']
        ]; 
         
  }
} else {
  //echo "0 results";
}
$conn->close();

echo json_encode($location);
?>