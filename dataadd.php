
<?php

    $servername = "localhost";
    $username = "root";
    $password = "";
    $dataname = "sumo";

    $conn= mysqli_connect("localhost","root","","sumo");
    if (!$conn) {
        die("Connection failed: " . mysqli_connect_error());
    }
    echo "Connected successfully";
    
       
    $Name = $_POST['name'];
    $Uph = $_POST['uph'];
    $Cpu = $_POST['cpu'];
    $Input = $_POST['input'];
    $Output = $_POST['output'];
    $Reject = $_POST['reject'];
    $Pk = $_POST['pk'];
    

    $sql = "INSERT INTO `datatest` (`name`, `uph`, `cpu`, `input`, `output`, `reject`,`pk`) VALUES ('".$Name."', '".$Uph."', '". $Cpu."','".$Input."' ,'".$Output."', '". $Reject."','".$Pk."');";
    if ($conn->query($sql) === TRUE) {
        echo "Record updated successfully";
    } else {
        echo "Error updating record: " . $conn->error;
    }	
?>

