<?php
  require('../connect.php');
  $time=date("H:i:s"); 
  $datetime = date("Y-m-d H:i:s");
  $date=date("Y-m-d"); 

 error_reporting(E_ALL);
ini_set('display_errors', 1);

function truncate_number($number, $decimals = 3) {
    return floor($number * pow(10, $decimals)) / pow(10, $decimals);
}

if(isset($_GET['distributor_visit']))
{
    extract($_POST);
    $response = array();
    

    // Validate required parameters
    if(empty($distributorid)) {
        $response["status"] = "0";
        $response["message"] = "Distributor ID is required";
        echo json_encode($_POST);
        return;
    }
    
    if(empty($userid)) {
        $response["status"] = "0";
        $response["message"] = "User ID is required";
        echo json_encode($response);
        return;
    }
    
    // Verify distributor exists
    $res = mysqli_query($con, "SELECT email,contact FROM employees WHERE id='$distributorid' and usertype=3");

    if(!mysqli_fetch_array($res)) {
        $response["status"] = "0";
        $response["message"] = "Invalid distributor id";
        echo json_encode($response);
        return;
    }
    
    // Validate visit type
    if($visit_type != 'Distributor Visit') {
        $response["status"] = "0";
        $response["message"] = "Invalid visit type";
        echo json_encode($response);
        return;
    }
    
    // Validate reason type
    $valid_reasons = array('General Reporting', 'Stock Visit', 'Other');
    if(!in_array($reason_type, $valid_reasons)) {
        $response["status"] = "0";
        $response["message"] = "Invalid reason type ";
        $response["reason-type"] = $valid_reasons;
        echo json_encode($response);
        return;
    }
    
    // Truncate coordinates if needed
    $lat = truncate_number($latitude, 3);
    $lng = truncate_number($longitude, 3);
    
    // Insert distributor visit record
    mysqli_query($con, "INSERT INTO distributor_visits
        (distributor_id, user_id, visit_type, reason_type, reason, 
         lat, lng, latitude, longitude, area_id, battery, visit_date, visit_time, created_at) 
        VALUES
        ('$distributorid', '$userid', '$visit_type', '$reason_type', '$reason', 
         '$lat', '$lng', '$latitude', '$longitude', '$areaid', '$battery', '$date', '$time', NOW())") 
        or die(mysqli_error($con));
    
    if(mysqli_affected_rows($con) > 0)
    {
        $entryid = mysqli_insert_id($con);
        
        $response["status"] = "1";
        $response["message"] = "success";
        $response["entryid"] = "$entryid";
    }
    else
    {
        $response["status"] = "0";
        $response["message"] = "Failed to create visit entry";
    }
    
    echo json_encode($response);
}

?>