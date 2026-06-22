<?php
  
   include("../connect.php");




   $time=date("H:i:s"); 
   $datetime = date("Y-m-d H:i:s");
   $date=date("Y-m-d");
   function truncate_number( $number, $precision = 2) {
    // Zero causes issues, and no need to truncate
    if ( 0 == (int)$number ) {
        return $number;
    }
    // Are we negative?
    $negative = $number / abs($number);
    // Cast the number to a positive to solve rounding
    $number = abs($number);
    // Calculate precision number for dividing / multiplying
    $precision = pow(10, $precision);
    // Run the math, re-applying the negative value to ensure returns correctly negative / positive
    return floor( $number * $precision ) / $precision * $negative;
    }
  
  
	function distance($lat1, $lon1, $lat2, $lon2, $unit) {
		if (($lat1 == $lat2) && ($lon1 == $lon2)){
			return 0;
		}
		else {
			$theta = $lon1 - $lon2;
			$dist = sin(deg2rad($lat1)) * sin(deg2rad($lat2)) +  cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * cos(deg2rad($theta));
			$dist = acos($dist);
			$dist = rad2deg($dist);
			$miles = $dist * 60 * 1.1515;
			$unit = strtoupper($unit);

			if ($unit == "K") {
			return ($miles * 1.609344);
			} else if ($unit == "N") {
			return ($miles * 0.8684);
			} else {
			return $miles;
			}
		}
	}

  
  
   if(isset($_GET['new']))
   {
      $response = array();
      try {
          // 1. Safely retrieve and sanitize inputs instead of extract()
          $name = isset($_POST['name']) ? trim($_POST['name']) : '';
          $address = isset($_POST['address']) ? trim($_POST['address']) : '';
          $contactperson = isset($_POST['contactperson']) ? trim($_POST['contactperson']) : '';
          $contact = isset($_POST['contact']) ? trim($_POST['contact']) : '';
          $pincode = isset($_POST['pincode']) ? trim($_POST['pincode']) : '';
          $gstnumber = isset($_POST['gstnumber']) ? trim($_POST['gstnumber']) : '';
          $outlettype = isset($_POST['outlettype']) ? trim($_POST['outlettype']) : '';
          $outletsubtype = isset($_POST['outletsubtype']) ? trim($_POST['outletsubtype']) : '';
          $latitude = isset($_POST['latitude']) ? trim($_POST['latitude']) : '';
          $longitude = isset($_POST['longitude']) ? trim($_POST['longitude']) : '';
          $createdby = isset($_POST['createdby']) ? trim($_POST['createdby']) : '';
          $battery = isset($_POST['battery']) ? trim($_POST['battery']) : '';
          
          $areaId = isset($_POST['areaId']) ? trim($_POST['areaId']) : (isset($_POST['areaid']) ? trim($_POST['areaid']) : '');

          if (empty($name)) {
              throw new Exception("Missing required field: name");
          }
          if (empty($contact)) {
              throw new Exception("Missing required field: contact");
          }

          // Data Validation
          if ($latitude !== '') {
              if (!is_numeric($latitude) || floatval($latitude) < -90 || floatval($latitude) > 90) {
                  throw new Exception("Invalid latitude value. Must be a number between -90 and 90.");
              }
          }
          if ($longitude !== '') {
              if (!is_numeric($longitude) || floatval($longitude) < -180 || floatval($longitude) > 180) {
                  throw new Exception("Invalid longitude value. Must be a number between -180 and 180.");
              }
          }
          
          $clean_contact = preg_replace('/[^0-9]/', '', $contact);
          if (strlen($clean_contact) < 10 || strlen($clean_contact) > 15) {
              throw new Exception("Invalid contact number. Must contain between 10 and 15 digits.");
          }

          if (!empty($pincode)) {
              if (!preg_match("/^[0-9]{6}$/", $pincode)) {
                  throw new Exception("Invalid pincode. Must be exactly 6 digits.");
              }
          }

          if (!empty($gstnumber) && !in_array(strtoupper($gstnumber), ["NA", "N/A", "NONE"])) {
              $gst_upper = strtoupper($gstnumber);
              if (!preg_match("/^[0-9]{2}[A-Z]{5}[0-9]{4}[A-Z]{1}[1-9A-Z]{1}Z[0-9A-Z]{1}$/", $gst_upper)) {
                  throw new Exception("Invalid GST number format.");
              }
              $gstnumber = $gst_upper;
          }

          // 2. Prevent duplicate entries using Prepared Statement
          $stmt = $con->prepare("SELECT id FROM outlets WHERE outlettype = ? AND name = ? AND contact = ?");
          if (!$stmt) {
              throw new Exception("Prepare statement failed for check duplicate: " . $con->error);
          }
          $stmt->bind_param("sss", $outlettype, $name, $contact);
          if (!$stmt->execute()) {
              throw new Exception("Execute failed for check duplicate: " . $stmt->error);
          }
          $res = $stmt->get_result();
          if ($res->fetch_assoc()) {
              $response["message"] = "already";
              echo json_encode($response);
              $stmt->close();
              return;
          }
          $stmt->close();

          // 3. Handle File Upload Safely
          $filename = "";
          
          if (isset($_FILES['empimage']['name']) && $_FILES['empimage']['name'] !== '') {
              if ($_FILES['empimage']['error'] !== UPLOAD_ERR_OK) {
                  throw new Exception("File upload failed with error code: " . $_FILES['empimage']['error']);
              }
              
              $file_tmp = $_FILES['empimage']['tmp_name'];
              $original_name = basename($_FILES['empimage']['name']);
              $ext = strtolower(pathinfo($original_name, PATHINFO_EXTENSION));
              
              // Validate extension
              $allowed_extensions = array("jpg", "jpeg", "png");
              if (!in_array($ext, $allowed_extensions)) {
                  throw new Exception("Invalid file type. Only JPG, JPEG, and PNG allowed.");
              }

              // Validate MIME type to ensure it is actually an image (compatible with PHP 7.x and 8.x)
              $finfo = finfo_open(FILEINFO_MIME_TYPE);
              $mime = finfo_file($finfo, $file_tmp);
              finfo_close($finfo);
              if (strpos($mime, 'image/') !== 0) {
                  throw new Exception("Uploaded file is not a valid image MIME type.");
              }

              // Create a safe, unique filename to prevent overwrites & traversal
              $safe_name_prefix = preg_replace("/[^a-zA-Z0-9]/", "", $name . $contact);
              $filename = $safe_name_prefix . "_" . uniqid() . "." . $ext;
          }

          // 4. Insert into Outlets using Prepared Statement
          $stmt_insert = $con->prepare("
              INSERT INTO outlets (
                  name, address, lastvisitpic, contactperson, contact, pincode, gstnumber, 
                  outlettype, outletsubtype, routeid, competitor_presense, street, locality, 
                  city, state, latitude, longitude, areaid, lastvisit, creationdate, createdby
              ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, '0', ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
          ");
          
          if (!$stmt_insert) {
              throw new Exception("Prepare statement failed for insert: " . $con->error);
          }

          $stmt_insert->bind_param(
              "ssssssssssssssssssss",
              $name, $address, $filename, $contactperson, $contact, $pincode, $gstnumber,
              $outlettype, $outletsubtype, $areaId, $street, $locality,
              $city, $state, $latitude, $longitude, $areaId, $datetime, $datetime, $createdby
          );

          if (!$stmt_insert->execute()) {
              throw new Exception("Execute failed for insert: " . $stmt_insert->error);
          }

          if ($stmt_insert->affected_rows > 0) {
              $outletid = $con->insert_id;
              $response["id"] = $outletid;

              // Move the uploaded file if applicable
              if ($filename !== "") {
                  $upload_dir = dirname(__DIR__) . "/imgoutlets/";
                  
                  // Ensure target directory exists and is writable
                  if (!is_dir($upload_dir)) {
                      if (!mkdir($upload_dir, 0755, true)) {
                          throw new Exception("Upload directory does not exist and could not be created: " . $upload_dir);
                      }
                  }
                  
                  if (!is_writable($upload_dir)) {
                      throw new Exception("Upload directory is not writable: " . $upload_dir);
                  }

                  // Perform the file move and capture PHP warnings if it fails
                  if (!move_uploaded_file($file_tmp, $upload_dir . $filename)) {
                      $err = error_get_last();
                      $details = ($err && isset($err['message'])) ? " Details: " . $err['message'] : "";
                      throw new Exception("Failed to move uploaded file to target directory." . $details);
                  }
              }

              // 5. Insert activity log using Prepared Statement
              $battery_val = $battery . "%";
              $activity_type = "New Outlet Create";
              
              $stmt_activity = $con->prepare("
                  INSERT INTO outletactivity (
                      userid, outletid, activitytype, battery, activitydate, activitytime, Latitude, Longitude
                  ) VALUES (?, ?, ?, ?, ?, ?, ?, ?)
              ");
              if (!$stmt_activity) {
                  throw new Exception("Prepare statement failed for activity log: " . $con->error);
              }
              $stmt_activity->bind_param("sissssss", $createdby, $outletid, $activity_type, $battery_val, $date, $time, $latitude, $longitude);
              if (!$stmt_activity->execute()) {
                  throw new Exception("Execute failed for activity log: " . $stmt_activity->error);
              }
              $stmt_activity->close();

              $response["message"] = "success";
          } else {
              throw new Exception("No rows inserted.");
          }

          $stmt_insert->close();
          echo json_encode($response);

      } catch (Throwable $e) {
          $response["message"] = "error";
          $response["error_details"] = $e->getMessage();
          echo json_encode($response);
      }
   }
   
   if(isset($_GET['edit']))
   {
      $response = array();
      try {
          // 1. Safely retrieve and sanitize inputs instead of extract()
          $id = isset($_POST['id']) ? intval($_POST['id']) : 0;
          $name = isset($_POST['name']) ? trim($_POST['name']) : '';
          $address = isset($_POST['address']) ? trim($_POST['address']) : '';
          $contactperson = isset($_POST['contactperson']) ? trim($_POST['contactperson']) : '';
          $contact = isset($_POST['contact']) ? trim($_POST['contact']) : '';
          $pincode = isset($_POST['pincode']) ? trim($_POST['pincode']) : '';
          $gstnumber = isset($_POST['gstnumber']) ? trim($_POST['gstnumber']) : '';
          $outlettype = isset($_POST['outlettype']) ? trim($_POST['outlettype']) : '';
          $outletsubtype = isset($_POST['outletsubtype']) ? trim($_POST['outletsubtype']) : '';

          $latitude = isset($_POST['latitude']) ? trim($_POST['latitude']) : '';
          $longitude = isset($_POST['longitude']) ? trim($_POST['longitude']) : '';
          $createdby = isset($_POST['createdby']) ? trim($_POST['createdby']) : '';
          $username = isset($_POST['username']) ? trim($_POST['username']) : '';
          
          $areaId = isset($_POST['areaId']) ? trim($_POST['areaId']) : (isset($_POST['areaid']) ? trim($_POST['areaid']) : '');
                   $distributorid =$areaId;
		  $street = isset($_POST['street']) ? trim($_POST['street']) : '';
          $locality = isset($_POST['locality']) ? trim($_POST['locality']) : '';
          $city = isset($_POST['city']) ? trim($_POST['city']) : '';
          $state = isset($_POST['state']) ? trim($_POST['state']) : '';

          if ($id <= 0) {
              throw new Exception("Invalid or missing outlet ID");
          }

          if (empty($name)) {
              throw new Exception("Missing required field: name");
          }
          if (empty($contact)) {
              throw new Exception("Missing required field: contact");
          }

          // Data Validation
          if ($latitude !== '') {
              if (!is_numeric($latitude) || floatval($latitude) < -90 || floatval($latitude) > 90) {
                  throw new Exception("Invalid latitude value. Must be a number between -90 and 90.");
              }
          }
          if ($longitude !== '') {
              if (!is_numeric($longitude) || floatval($longitude) < -180 || floatval($longitude) > 180) {
                  throw new Exception("Invalid longitude value. Must be a number between -180 and 180.");
              }
          }
          
          $clean_contact = preg_replace('/[^0-9]/', '', $contact);
          if (strlen($clean_contact) < 10 || strlen($clean_contact) > 15) {
              throw new Exception("Invalid contact number. Must contain between 10 and 15 digits.");
          }

          if (!empty($pincode)) {
              if (!preg_match("/^[0-9]{6}$/", $pincode)) {
                  throw new Exception("Invalid pincode. Must be exactly 6 digits.");
              }
          }

          if (!empty($gstnumber) && !in_array(strtoupper($gstnumber), ["NA", "N/A", "NONE"])) {
              $gst_upper = strtoupper($gstnumber);
              if (!preg_match("/^[0-9]{2}[A-Z]{5}[0-9]{4}[A-Z]{1}[1-9A-Z]{1}Z[0-9A-Z]{1}$/", $gst_upper)) {
                  throw new Exception("Invalid GST number format.");
              }
              $gstnumber = $gst_upper;
          }

          // 2. Handle File Upload Safely
          $filename = "";
          
          if (isset($_FILES['empimage']['name']) && $_FILES['empimage']['name'] !== '') {
              if ($_FILES['empimage']['error'] !== UPLOAD_ERR_OK) {
                  throw new Exception("File upload failed with error code: " . $_FILES['empimage']['error']);
              }
              
              $file_tmp = $_FILES['empimage']['tmp_name'];
              $original_name = basename($_FILES['empimage']['name']);
              $ext = strtolower(pathinfo($original_name, PATHINFO_EXTENSION));
              
              // Validate extension
              $allowed_extensions = array("jpg", "jpeg", "png");
              if (!in_array($ext, $allowed_extensions)) {
                  throw new Exception("Invalid file type. Only JPG, JPEG, and PNG allowed.");
              }

              // Validate MIME type
              $finfo = finfo_open(FILEINFO_MIME_TYPE);
              $mime = finfo_file($finfo, $file_tmp);
              finfo_close($finfo);
              if (strpos($mime, 'image/') !== 0) {
                  throw new Exception("Uploaded file is not a valid image MIME type.");
              }

              // Create a safe, unique filename
              $safe_name_prefix = preg_replace("/[^a-zA-Z0-9]/", "", $name . $contact);
              $filename = $safe_name_prefix . "_" . uniqid() . "." . $ext;
          }

          // 3. Update outlet using Prepared Statement (Conditional on image upload)
          if ($filename !== "") {
              $stmt_update = $con->prepare("
                  UPDATE outlets SET 
                      name = ?, address = ?, lastvisitpic = ?, contactperson = ?, contact = ?, 
                      pincode = ?, gstnumber = ?, outlettype = ?, outletsubtype = ?, distributorid = ?, 
                      routeid = ?, areaid = ?, competitor_presense = '0', street = ?, locality = ?, 
                      city = ?, state = ?, latitude = ?, longitude = ?, lastvisit = ?, 
                      updated_at = ?, createdby = ? 
                  WHERE id = ?
              ");
              if (!$stmt_update) {
                  throw new Exception("Prepare statement failed for update: " . $con->error);
              }
              $stmt_update->bind_param(
                  "sssssssssssssssssssssi",
                  $name, $address, $filename, $contactperson, $contact,
                  $pincode, $gstnumber, $outlettype, $outletsubtype, $distributorid,
                  $areaId, $areaId, $street, $locality,
                  $city, $state, $latitude, $longitude, $datetime,
                  $datetime, $createdby, $id
              );
          } else {
              $stmt_update = $con->prepare("
                  UPDATE outlets SET 
                      name = ?, address = ?, contactperson = ?, contact = ?, 
                      pincode = ?, gstnumber = ?, outlettype = ?, outletsubtype = ?, distributorid = ?, 
                      routeid = ?, areaid = ?, competitor_presense = '0', street = ?, locality = ?, 
                      city = ?, state = ?, latitude = ?, longitude = ?, lastvisit = ?, 
                      updated_at = ?, createdby = ? 
                  WHERE id = ?
              ");
              if (!$stmt_update) {
                  throw new Exception("Prepare statement failed for update: " . $con->error);
              }
              $stmt_update->bind_param(
                  "sssssssssssssssssssi",
                  $name, $address, $contactperson, $contact,
                  $pincode, $gstnumber, $outlettype, $outletsubtype, $distributorid,
                  $areaId, $areaId, $street, $locality,
                  $city, $state, $latitude, $longitude, $datetime,
                  $datetime, $createdby, $id
              );
          }

          if ($stmt_update->execute()) {
              // 4. Log the action
              $descrip = "Outlet $name , $address details updated by $username";
              $stmt_log = $con->prepare("INSERT INTO log (description, creationdate, createdby) VALUES (?, ?, ?)");
              if ($stmt_log) {
                  $stmt_log->bind_param("sss", $descrip, $datetime, $username);
                  $stmt_log->execute();
                  $stmt_log->close();
              }

              // 5. Move the uploaded file if applicable
              if ($filename !== "") {
                  $upload_dir = dirname(__DIR__) . "/imgoutlets/";
                  
                  if (!is_dir($upload_dir)) {
                      if (!mkdir($upload_dir, 0755, true)) {
                          throw new Exception("Upload directory does not exist and could not be created: " . $upload_dir);
                      }
                  }
                  
                  if (!is_writable($upload_dir)) {
                      throw new Exception("Upload directory is not writable: " . $upload_dir);
                  }

                  if (!move_uploaded_file($file_tmp, $upload_dir . $filename)) {
                      $err = error_get_last();
                      $details = ($err && isset($err['message'])) ? " Details: " . $err['message'] : "";
                      throw new Exception("Failed to move uploaded file to target directory." . $details);
                  }
              }

              $response["message"] = "success";
          } else {
              throw new Exception("Failed to execute update: " . $stmt_update->error);
          }

          $stmt_update->close();
          echo json_encode($response);

      } catch (Throwable $e) {
          $response["message"] = "error";
          $response["error_details"] = $e->getMessage();
          echo json_encode($response);
      }
   }




      
   if(isset($_GET['visitregister']))
   {
	  $response = array();

	  // Safely get all request parameters
	  $id = isset($_POST['id']) ? $_POST['id'] : '';
	  $name = isset($_POST['name']) ? $_POST['name'] : '';
	  $contact = isset($_POST['contact']) ? $_POST['contact'] : '';
	  $outlettype = isset($_POST['outlettype']) ? $_POST['outlettype'] : '';
	  $userid = isset($_POST['userid']) ? $_POST['userid'] : '';
	  $outletvisit = isset($_POST['outletvisit']) ? $_POST['outletvisit'] : '';
	  $battery = isset($_POST['battery']) ? $_POST['battery'] : '';
	  $latitude = isset($_POST['latitude']) ? $_POST['latitude'] : '';
	  $longitude = isset($_POST['longitude']) ? $_POST['longitude'] : '';

	  // Validation: Next visit can only be saved after 3 minutes (180 seconds)
	  if (!empty($userid)) {
		 $stmt_check = $con->prepare("SELECT activitydate, activitytime FROM outletactivity WHERE userid = ? ORDER BY id DESC LIMIT 1");
		 if ($stmt_check) {
			$stmt_check->bind_param("s", $userid);
			$stmt_check->execute();
			$res_check = $stmt_check->get_result();
			if ($row_check = $res_check->fetch_assoc()) {
			   $last_datetime_str = $row_check['activitydate'] . ' ' . $row_check['activitytime'];
			   $last_timestamp = strtotime($last_datetime_str);
			   $current_timestamp = time();
			   $diff_seconds = $current_timestamp - $last_timestamp;
			   if ($diff_seconds >= 0 && $diff_seconds < 180) { // 3 minutes = 180 seconds
				  $remaining = 180 - $diff_seconds;
				  $response["message"] = "waiting";
				  $response["remaining"] = $remaining;
				  echo json_encode($response);
				  exit;
			   }
			}
			$stmt_check->close();
		 }
	  }

	  if (!empty($_POST['datetime'])) {
		 $datetime = $_POST['datetime'];
	  } else {
		 $datetime = date("Y-m-d H:i:s");
	  }

	  $date = date("Y-m-d");
	  $time = date("H:i:s");

	  if ($outlettype == "4")
	  {
		 $outlettype = "0"; // Fixed comparison bug
	  }

	  $update_executed = false;

	  if(isset($_FILES['lastvisitpic']['name']) && $_FILES['lastvisitpic']['name'] != '')
	  {
		 $original_filename = $_FILES['lastvisitpic']['name'];
		 $tmpname = $_FILES['lastvisitpic']['tmp_name'];
		 
		 // Clean filename to prevent path traversal
		 $clean_uploaded_filename = basename($original_filename);
		 $filename = $name . $contact . $clean_uploaded_filename . ".jpg";
		 
		 $folder = "imgoutlets";	  

		 if($outlettype == "2")
		 { 		  
			$folder = "imgusers";
			$stmt = $con->prepare("UPDATE employees SET image = ?, lastlogin = ? WHERE id = ? AND usertype = '2'");
			$stmt->bind_param("sss", $filename, $datetime, $id);
		 }
		 elseif($outlettype == "3")
		 {		  
			$folder = "imgusers";
			$stmt = $con->prepare("UPDATE employees SET image = ?, lastlogin = ? WHERE id = ? AND usertype = '3'");
			$stmt->bind_param("sss", $filename, $datetime, $id);
		 }
		 else
		 {
			$stmt = $con->prepare("UPDATE outlets SET lastvisitpic = ?, lastvisit = ? WHERE id = ?");
			$stmt->bind_param("sss", $filename, $datetime, $id);
		 }

		 if ($stmt) {
			if ($stmt->execute()) {
			   move_uploaded_file($tmpname, "../" . $folder . "/" . $filename);
			   $update_executed = true;
			}
			$stmt->close();
		 }
	  } 
	  else 
	  {
		 $stmt = $con->prepare("UPDATE outlets SET lastvisit = ? WHERE id = ?");
		 $stmt->bind_param("ss", $datetime, $id);
		 if ($stmt) {
			if ($stmt->execute()) {
			   $update_executed = true;
			}
			$stmt->close();
		 }
	  }
	  
	  $battery_val = $battery . "%";
	  $stmt2 = $con->prepare("INSERT INTO outletactivity(userid, outletid, activitytype, battery, activitydate, activitytime, Latitude, Longitude, visittype) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
	  
	  if ($stmt2) {
		 $stmt2->bind_param("sssssssss", $userid, $id, $outletvisit, $battery_val, $date, $time, $latitude, $longitude, $outlettype);
		 if ($stmt2->execute()) {
			$entryid = $con->insert_id;
			$response["message"] = "success";
			$response["entryid"] = $entryid;
		 }
		 else {
			// Fallback update as in the original code
			$stmt_err = $con->prepare("UPDATE outlets SET lastvisit = ? WHERE id = ?");
			if ($stmt_err) {
			   $stmt_err->bind_param("ss", $datetime, $id);
			   $stmt_err->execute();
			   $stmt_err->close();
			}
			$response["message"] = "error";
		 }
		 $stmt2->close();
	  } else {
		 $response["message"] = "error";
	  }

	  echo json_encode($response); 
   }


if(isset($_GET['feedback']))
   {
	  extract($_POST);
	  $response=array();
	  	  
	  mysqli_query($con,"update  outletactivity set feedback='$feedback', rating='$rating' where id='$id'")or die(mysqli_error($con));
	  
	  if(mysqli_affected_rows($con)>0)
	  {
		    $response["message"]="success";
	  }
	  else
	  {
		   $response["message"]="error";
	  }
	   $response["message"]="success";
	    echo json_encode($response); 
 
   }

    if(isset($_GET['outletshow']))
	{
		extract($_REQUEST);
	   //$res=mysqli_query($con,"select * from outlets where areaid='$areaid'");
	   //$lat=truncate_number($lat,1);
	   //$lng=truncate_number($lng,1);
	  
	   //$res=mysqli_query($con,"select * from outlets where truncate(latitude,1)='$lat' and truncate(longitude,1)='$lng' order by lastvisit desc");
	   //$res=mysqli_query($con,"select * from outlets");
		$res=mysqli_query($con,"select *, round( ( 6371  * acos( least(1.0,  cos( radians($lat) ) * cos( radians(latitude) ) * cos( radians(longitude) - radians($lng) ) + sin( radians($lat) ) * sin( radians(latitude) ) ) ) ), 3) as distance from outlets having distance <= 0.5 order by distance asc");

	   $response=array();
	   $num=mysqli_field_count($con);
	   while($row=mysqli_fetch_array($res))
	   {
		   $rr=array();
		   $rr["id"]=$row["id"];
		   $rr["name"]=$row["name"];
		   $rr["address"]=$row['address'];
		   $rr["lastvisitpic"]=$row["lastvisitpic"];
		   $rr["contactperson"]=$row["contactperson"];
		   $rr["contact"]=$row["contact"];
		   $rr["pincode"]=$row["pincode"];
		   $rr["gstnumber"]=$row["gstnumber"];
		   $rr["outlettype"]=$row["outlettype"];
		   $rr["competitor_presense"]=$row["competitor_presense"];
		   $rr["distributorid"]=$row["distributorid"];
		   $rr["salesmanagerid"]=$row["salesmanagerid"];
		   $rr["rsmid"]=$row["rsmid"];
		   $rr["routeid"]=$row["routeid"];
		   $rr["street"]=$row["street"];
		   $rr["locality"]=$row["locality"];
		   $rr["city"]=$row["city"];
		   $rr["state"]=$row["state"];
		   $rr["latitude"]=$row["latitude"];
		   $rr["longitude"]=$row["longitude"];
		   $rr["areaid"]=$row["areaid"];
		   $rr["lastvisit"]=$row["lastvisit"];
		   $rr["creationdate"]=$row["creationdate"];
		   $rr["createdby"]=$row["createdby"]; 
		   array_push($response,$rr);
	   } 
	   
	   $data=array();
	   $data["data"]=$response;
	   echo json_encode($data);
	   return;
	   
	}
    

    if(isset($_GET['distributorshow']))
	{
		extract($_REQUEST);
	   //$res=mysqli_query($con,"select * from outlets where areaid='$areaid'");
	   //$lat=truncate_number($lat,1);
	   //$lng=truncate_number($lng,1);
	  
	   //$res=mysqli_query($con,"select * from outlets where truncate(latitude,1)='$lat' and truncate(longitude,1)='$lng' order by lastvisit desc");
	   //$res=mysqli_query($con,"select * from outlets");
	   $res=mysqli_query($con,"select *, round( ( 6371  * acos( least(1.0,  cos( radians($lat) ) * cos( radians(latitude) ) * cos( radians(longitude) - radians($lng) ) + sin( radians($lat) ) * sin( radians(latitude) ) ) ) ), 3) as distance from employees where usertype=3 having distance <= 0.5  order by distance asc");
	   $response=array();
	   $num=mysqli_field_count($con);
	   while($row=mysqli_fetch_array($res))
	   {
		   $rr=array();
		   $rr["id"]=$row["id"];
		   $rr["name"]=$row["name"];
		//    $rr["address"]=$row['address'];
		//    $rr["lastvisitpic"]=$row["lastvisitpic"];
		//    $rr["contactperson"]=$row["contactperson"];
		//    $rr["contact"]=$row["contact"];
		//    $rr["pincode"]=$row["pincode"];
		//    $rr["gstnumber"]=$row["gstnumber"];
		//    $rr["outlettype"]=$row["outlettype"];
		   $rr["empid"]=$row["empid"];
		//    $rr["distributorid"]=$row["distributorid"];
		//    $rr["salesmanagerid"]=$row["salesmanagerid"];
		//    $rr["rsmid"]=$row["rsmid"];
		//    $rr["routeid"]=$row["routeid"];
		//    $rr["street"]=$row["street"];
		//    $rr["locality"]=$row["locality"];
		//    $rr["city"]=$row["city"];
		//    $rr["state"]=$row["state"];
		   $rr["latitude"]=$row["latitude"];
		   $rr["longitude"]=$row["longitude"];
		   $rr["areaid"]=$row["areaid"];
		//    $rr["lastvisit"]=$row["lastvisit"];
		//    $rr["creationdate"]=$row["creationdate"];
		//    $rr["createdby"]=$row["createdby"]; 
		   array_push($response,$rr);
	   } 
	   
	   $data=array();
	   $data["data"]=$response;
	   echo json_encode($data);
	   return;
	   
	}	

   if(isset($_GET['show']))
   {
	   extract($_REQUEST);
	   //$res=mysqli_query($con,"select * from outlets where areaid='$areaid'");
	   //$lat=truncate_number($lat,1);
	   //$lng=truncate_number($lng,1);
	  
	   //$res=mysqli_query($con,"select * from outlets where truncate(latitude,1)='$lat' and truncate(longitude,1)='$lng' order by lastvisit desc");
	   //$res=mysqli_query($con,"select * from outlets");
	  // $res=mysqli_query($con,"select *, round( ( 6371  * acos( least(1.0,  cos( radians($lat) ) * cos( radians(latitude) ) * cos( radians(longitude) - radians($lng) ) + sin( radians($lat) ) * sin( radians(latitude) ) ) ) ), 3) as distance from outlets where areaid='$areaId having distance <= 0.5 order by distance asc");
	   $res=mysqli_query($con,"select *, round( ( 6371  * acos( least(1.0,  cos( radians($lat) ) * cos( radians(latitude) ) * cos( radians(longitude) - radians($lng) ) + sin( radians($lat) ) * sin( radians(latitude) ) ) ) ), 3) as distance  from outlets  where routeid='$areaId' order by distance asc ");
	   $response=array();

      $distributorid = $distributorName = '';

     // Escape $areaId to prevent issues if it's from user input
		

		$distributorid = $distributorName = '';

		$res2 = mysqli_query($con, "
			SELECT employees.name AS distributor_name, employees.id AS id 
			FROM area 
			JOIN employees ON area.distributor_id = employees.id  
			WHERE area.id = '$areaId'
		");

		if ($res2 && mysqli_num_rows($res2) > 0) {
			$resRoute = mysqli_fetch_assoc($res2);
			if ($resRoute) {
				$distributorName = $resRoute['distributor_name'];
				$distributorid = $resRoute['id'];
			}
		}

	   $num=mysqli_field_count($con);
	   while($row=mysqli_fetch_array($res))
	   {
		   $rr=array();
		   $rr["id"]=$row["id"];
		   $rr["name"]=$row["name"];
		   $rr["address"]=$row['address'];
		   $rr["lastvisitpic"]=$row["lastvisitpic"];
		   $rr["contactperson"]=$row["contactperson"];
		   $rr["contact"]=$row["contact"];
		   $rr["pincode"]=$row["pincode"];
		   $rr["gstnumber"]=$row["gstnumber"];
		   $rr["outlettype"]=$row["outlettype"];
		   $rr["competitor_presense"]=$row["competitor_presense"];
		   $rr["distributorid"]=$distributorid;
		   $rr["salesmanagerid"]=$row["salesmanagerid"];
		   $rr["rsmid"]=$row["rsmid"];
		   $rr["routeid"]=$row["routeid"];
		   $rr["street"]=$row["street"];
		   $rr["locality"]=$row["locality"];
		   $rr["city"]=$row["city"];
		   $rr["state"]=$row["state"];
		   $rr["latitude"]=$row["latitude"];
		   $rr["longitude"]=$row["longitude"];
		   $rr["areaid"]=$row["areaid"];
		   $rr["lastvisit"]=$row["lastvisit"];
		   $rr["creationdate"]=$row["creationdate"];
		   $rr["createdby"]=$row["createdby"]; 
		   $rr["distributor_name"]=$distributorName; 

		   array_push($response,$rr);
	   } 
	   
	   
	   //$res=mysqli_query($con,"select * from employees where truncate(latitude,1)='$lat' and truncate(longitude,1)='$lng' and (usertype='2' or usertype='3')");
	   ////$res=mysqli_query($con,"select * from outlets");
	   
	   //while($row=mysqli_fetch_array($res))
	   //{
		  // $rr=array();
		  // $rr["id"]=$row["id"];
		  // $rr["name"]=$row["name"];
		  // $rr["address"]=$row['address'];
		  // $rr["lastvisitpic"]=$row["image"];
		  // $rr["contactperson"]=$row["empid"];
		  // $rr["contact"]=$row["contact"];
		  // $rr["pincode"]=$row["battery"];;
		  // $rr["gstnumber"]=$row["region"];
		  // $rr["outlettype"]=$row["usertype"]=="2"?"STOCKIST":"DISTRIBUTOR";
		  // $rr["competitor_presense"]=$row["email"];
		  // $rr["distributorid"]=$row["stockistid"];
		  // $rr["salesmanagerid"]="0";
		  // $rr["rsmid"]="0";
		  // $rr["routeid"]="0";
		  // $rr["street"]=$row["locality"];
		  // $rr["locality"]=$row["locality"];
		  // $rr["city"]=$row["city"];
		  // $rr["state"]=$row["state"];
		  // $rr["latitude"]=$row["latitude"];
		  // $rr["longitude"]=$row["longitude"];
		  // $rr["areaid"]=$row["areaid"];
		  // $rr["lastvisit"]=$row["lastlogin"];
		  // $rr["creationdate"]=$row["creationdate"];
		  // $rr["createdby"]=$row["createdby"]; 
		  // array_push($response,$rr);
	   //} 
	   
	   
	   $data=array();
	   $data["data"]=$response;
	   echo json_encode($data);
	   return; 
   }
   

      if(isset($_GET['show_limited']))
   {
	   extract($_REQUEST);
	   $res=mysqli_query($con,"select *, round( ( 6371  * acos( least(1.0,  cos( radians($lat) ) * cos( radians(latitude) ) * cos( radians(longitude) - radians($lng) ) + sin( radians($lat) ) * sin( radians(latitude) ) ) ) ), 3) as distance  from outlets  where routeid='$areaId' order by distance asc LIMIT 25");
	   $response=array();

        $distributorid = $distributorName = '';

		$res2 = mysqli_query($con, "
			SELECT employees.name AS distributor_name, employees.id AS id 
			FROM area 
			JOIN employees ON area.distributor_id = employees.id  
			WHERE area.id = '$areaId'
		");

		if ($res2 && mysqli_num_rows($res2) > 0) {
			$resRoute = mysqli_fetch_assoc($res2);
			if ($resRoute) {
				$distributorName = $resRoute['distributor_name'];
				$distributorid = $resRoute['id'];
			}
		}

	   $num=mysqli_field_count($con);
	   while($row=mysqli_fetch_array($res))
	   {
		   $rr=array();
		   $rr["id"]=$row["id"];
		   $rr["name"]=$row["name"];
		   $rr["address"]=$row['address'];
		   $rr["lastvisitpic"]=$row["lastvisitpic"];
		   $rr["contactperson"]=$row["contactperson"];
		   $rr["contact"]=$row["contact"];
		   $rr["pincode"]=$row["pincode"];
		   $rr["gstnumber"]=$row["gstnumber"];
		   $rr["outlettype"]=$row["outlettype"];
		   $rr["competitor_presense"]=$row["competitor_presense"];
		   $rr["distributorid"]=$distributorid;
		   $rr["salesmanagerid"]=$row["salesmanagerid"];
		   $rr["rsmid"]=$row["rsmid"];
		   $rr["routeid"]=$row["routeid"];
		   $rr["street"]=$row["street"];
		   $rr["locality"]=$row["locality"];
		   $rr["city"]=$row["city"];
		   $rr["state"]=$row["state"];
		   $rr["latitude"]=$row["latitude"];
		   $rr["longitude"]=$row["longitude"];
		   $rr["areaid"]=$row["areaid"];
		   $rr["lastvisit"]=$row["lastvisit"];
		   $rr["creationdate"]=$row["creationdate"];
		   $rr["createdby"]=$row["createdby"]; 
		   $rr["distributor_name"]=$distributorName; 

		   array_push($response,$rr);
	   } 
	
	   
	   $data=array();
	   $data["data"]=$response;
	   echo json_encode($data);
	   return; 
   }
   
      if(isset($_GET['activityvisit']))
   {
	   $userid=$_POST["userid"];
	   $res=mysqli_query($con,"select a.id,o.name,o.address,o.lastvisitpic,o.contactperson,o.contact,o.gstnumber,o.outlettype,a.Latitude,a.Longitude,a.activitytype,a.activitydate,a.activitytime,a.feedback,a.battery,a.rating,e.name as 'empname',e.empid from outletactivity a join outlets o on a.outletid=o.id join employees e  on e.id=a.userid where a.userid='$userid' and a.visittype='0' and a.activitydate='$date' order by a.id desc");   
	   $response=array();
	   $num=mysqli_field_count($con);
	   while($row=mysqli_fetch_array($res))
	   {
		   $rr=array();
		   $rr["id"]=$row["id"];
		   $rr["name"]=$row["name"];
		   $rr["address"]=$row['address'];
		   $rr["lastvisitpic"]=$row["lastvisitpic"];
		   $rr["contactperson"]=$row["contactperson"];
		   $rr["contact"]=$row["contact"];
		   $rr["gstnumber"]=$row["gstnumber"];
		   $rr["outlettype"]=$row["outlettype"];
		   $rr["activitytype"]=$row["activitytype"];
		   $rr["activitydate"]=$row["activitydate"];
		   $rr["activitytime"]=$row["activitytime"];
		   $rr["feedback"]=$row["feedback"];
		   $rr["battery"]=$row["battery"];
		   $rr["rating"]=$row["rating"]; 
		   $rr["latitude"]=$row["Latitude"];
		   $rr["longitude"]=$row["Longitude"];
		   $rr["empname"]=$row["empname"];
		   $rr["empid"]=$row["empid"];
           		   
		   array_push($response,$rr);
	   }
	   
	   $res=mysqli_query($con,"select a.id,o.name,o.address,o.image,o.empid,o.contact,o.region,a.Latitude,a.Longitude,a.activitytype,a.activitydate,a.activitytime,a.feedback,a.battery,a.rating,e.name as 'empname',e.empid from outletactivity a join employees o on a.outletid=o.id join employees e  on e.id=a.userid where a.userid='$userid' and a.visittype='2' and a.activitydate='$date' order by a.id desc");   
	   
	   $num=mysqli_field_count($con);
	   while($row=mysqli_fetch_array($res))
	   {
		   $rr=array();
		   $rr["id"]=$row["id"];
		   $rr["name"]=$row["name"];
		   $rr["address"]=$row['address'];
		   $rr["lastvisitpic"]=$row["image"];
		   $rr["contactperson"]=$row["empid"];
		   $rr["contact"]=$row["contact"];
		   $rr["gstnumber"]=$row["region"];
		   $rr["outlettype"]="Stockist";
		   $rr["activitytype"]=$rr["activitytype"];
		   $rr["activitydate"]=$row["activitydate"];
		   $rr["activitytime"]=$row["activitytime"];
		   $rr["feedback"]=$row["feedback"];
		   $rr["battery"]=$row["battery"];
		   $rr["rating"]=$row["rating"]; 
		   $rr["latitude"]=$row["Latitude"];
		   $rr["longitude"]=$row["Longitude"];
		   $rr["empname"]=$row["empname"];
		   $rr["empid"]=$row["empid"];
           		   
		   array_push($response,$rr);
	   }
	   
	   	   $res=mysqli_query($con,"select a.id,o.name,o.address,o.image,o.empid,o.contact,o.region,a.Latitude,a.Longitude,a.activitytype,a.activitydate,a.activitytime,a.feedback,a.battery,a.rating,e.name as 'empname',e.empid from outletactivity a join employees o on a.outletid=o.id join employees e  on e.id=a.userid where a.userid='$userid' and a.visittype='3' and a.activitydate='$date' order by a.id desc");   
	   
	   $num=mysqli_field_count($con);
	   while($row=mysqli_fetch_array($res))
	   {
		   $rr=array();
		   $rr["id"]=$row["id"];
		   $rr["name"]=$row["name"];
		   $rr["address"]=$row['address'];
		   $rr["lastvisitpic"]=$row["image"];
		   $rr["contactperson"]=$row["empid"];
		   $rr["contact"]=$row["contact"];
		   $rr["gstnumber"]=$row["region"];
		   $rr["outlettype"]="Distributor";
		   $rr["activitytype"]=$row["activitytype"];
		   $rr["activitydate"]=$row["activitydate"];
		   $rr["activitytime"]=$row["activitytime"];
		   $rr["feedback"]=$row["feedback"];
		   $rr["battery"]=$row["battery"];
		   $rr["rating"]=$row["rating"]; 
		   $rr["latitude"]=$row["Latitude"];
		   $rr["longitude"]=$row["Longitude"];
		   $rr["empname"]=$row["empname"];
		   $rr["empid"]=$row["empid"];
           		   
		   array_push($response,$rr);
	   }
	   
	   
	   $res=mysqli_query($con,"select a.id,a.companyname,a.address,a.image,a.contactperson,a.contactno,a.area,a.city,a.state,a.Latitude,a.Longitude,a.activitytype,a.activitydate,a.activitytime,a.feedback,a.battery,a.rating,e.name as 'empname',e.empid from outletactivity a join employees e  on e.id=a.userid where a.userid='$userid' and a.visittype='4'  and a.activitydate='$date' order by a.id desc");   
	   
	   $num=mysqli_field_count($con);
	   while($row=mysqli_fetch_array($res))
	   {
		   $rr=array();
		   $rr["id"]=$row["id"];
		   $rr["name"]=$row["companyname"];
		   $rr["address"]=$row['address'];
		   $rr["lastvisitpic"]=$row["image"];
		   $rr["contactperson"]=$row["contactperson"];
		   $rr["contact"]=$row["contactno"];
		   $rr["gstnumber"]=$row["area"];
		   $rr["outlettype"]="Tour Visit";
		   $rr["activitytype"]=$row["activitytype"];
		   $rr["activitydate"]=$row["activitydate"];
		   $rr["activitytime"]=$row["activitytime"];
		   $rr["feedback"]=$row["feedback"];
		   $rr["battery"]=$row["battery"];
		   $rr["rating"]=$row["rating"]; 
		   $rr["latitude"]=$row["Latitude"];
		   $rr["longitude"]=$row["Longitude"];
		   $rr["empname"]=$row["empname"];
		   $rr["empid"]=$row["empid"];
           		   
		   array_push($response,$rr);
	   }
	   
	    
	   /*$dist=0.0;
	   for($i=0;$i<count($response)-1;$i++)
	   {
		   $rr=$response[$i];
		   $rr1=$response[$i+1];
		   $dist+=distance($rr["latitude"],$rr["longitude"],$rr1["latitude"],$rr1["longitude"],"K");
	   }
	   */
	   $data=array();
	   //array_multisort($response[0]['id'],SORT_DESC,SORT_NUMERIC);
	   $data["data"]=$response;
	  
	   echo json_encode($data);
	   //echo round($dist,2);
	   return;
   }
   
   if(isset($_GET['tourvisitregister']))
   {
	  extract($_POST);
	  $filename=$_FILES['lastvisitpic']['name'];
	  $tmpname=$_FILES['lastvisitpic']['tmp_name'];
	  $filename=$name.$contact.$filename.".jpg";
	  $response=array();
	  $folder="imgoutlets";	  
	  mysqli_query($con,"insert into outletactivity(userid,outletid,activitytype,battery,activitydate,activitytime,Latitude,Longitude,visittype,companyname,contactperson,contactno,address,area,city,state,image) values('$userid','0','Tour Visit($reason)','$battery%','$date','$time','$lat','$lng','$outlettype','$companyname','$contactperson','$contactno','$address','$area','$city','$state','$filename')")or die(mysqli_error($con)); 
	  $entryid=mysqli_insert_id($con);
	  if(move_uploaded_file($tmpname,"../".$folder."/".$filename))
		{
		  $response["message"]="success";
		  $response["entryid"]=$entryid;
		}
	  else
	    {
		  $response["message"]="error";
		  $response["entryid"]="0";
	    }
	    echo json_encode($response); 
   }


if(isset($_GET['sync']))
   {
	  
	  extract($_POST);
	  $response="";
	  $res=mysqli_query($con,"select * from outlets where outlettype='$outlettype' and name='$name'  and contact='$contact'");
	  if($row=mysqli_fetch_array($res)) 
	   {
		 $response="already";
		 echo $response; 
		 return;   
	   }
	  
	  
	  
	  $lat=truncate_number($latitude,3);
	  $lng=truncate_number($longitude,3);
	  
	  
	  
	  mysqli_query($con,"insert into outlets(name,address,lastvisitpic,contactperson,contact,pincode,gstnumber,outlettype,outletsubtype,distributorid,routeid,competitor_presense,street,locality,city,state,latitude,longitude,areaid,lastvisit,creationdate,createdby) values('$name','$address','','$contactperson','$contact','$pincode','$gstnumber','$outlettype','$outletsubtype','$distributorid','0','0','$street','$locality','$city','$state','$latitude','$longitude','$areaid','$time','$date','$createdby')");
	  
	  if(mysqli_affected_rows($con)>0)
	  {
		  $outletid=mysqli_insert_id($con);
		  //$response["id"]=$outletid;
		  mysqli_query($con,"insert into outletactivity(userid,outletid,activitytype,battery,activitydate,activitytime,Latitude,Longitude) values('$createdby','$outletid','New Outlet Create','$battery%','$date','$time','$latitude','$longitude')")or die(mysqli_error($con));
		
		  
		   $response="success";
		  
	      
	  }
	  else
	  {
		   $response="error";
	  }
	   
	    echo $response; 
   }
   
?>
