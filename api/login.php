<?php
  
  define('API_ACCESS_KEY','AIzaSyA7zvsKyfieA6XOhgbQJfyYMfcFlOvlHPY');
  $fcmUrl = 'https://fcm.googleapis.com/fcm/send';

  include("../connect.php");
   $time=date("H:i:s"); 
  $datetime = date("Y-m-d H:i:s");
  $date=date("Y-m-d");

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

   
   if(isset($_GET['login']))
   {
	  extract($_POST);
	  $check =false;
	  
	  $query1="select * from employees where email='$user' and password=password('$pass') and usertype='1'";
	  

	  $res=mysqli_query($con,$query1) or die(mysqli_error($con));
	  if($row=mysqli_fetch_array($res))
	  {
	  
	   $query1="select * from devices where userid='$row[0]' and status='1'";
	   $res1=mysqli_query($con,$query1) or die(mysqli_error($con));
	    if($row1=mysqli_fetch_array($res1))
		{
		  if($row1[1]==$name && $row1[2]==$modelno)
		  {
			  $check="same";

			  $sql = "DELETE FROM outletactivity WHERE activitydate < CURDATE() - INTERVAL 200 DAY";
				$con->query($sql);
			


		  }
		  else
		  {
			  $check="notsame";
		  }
		}
		else
		{
			$check="new";
		}
		
	  }



	   
	    if($check=="notsame")
		{
		  echo "already"; 	
		  return;
		}
		
		
	  $query1="select * from employees where email='$user' and password=password('$pass') and usertype='1' and status='1'";

	  $res=mysqli_query($con,$query1) or die(mysqli_error($con));
	  
	  //$response=array();
	  if($row=mysqli_fetch_array($res))
	  {
		
		if($check=="new")
		{
			mysqli_query($con,"insert into devices(name,modelno,appversion,osversion,logindate,userid,status,track,usertoken) values('$name','$modelno','$appversion','$osversion','$datetime','$row[0]','1','0','$token')");
				
		}
		else
		{
		 mysqli_query($con,"update devices  set name='$name',modelno='$modelno',appversion='$appversion',osversion='$osversion',logindate='$datetime',usertoken='$token' where userid='$row[0]'");
		}
		  
		  //$rr=array("id"=>$row["id"],"name"=>$row["name"],"empid"=>$row["empid"],"email"=>$row["email"],"contact"=>$row["contact"],"address"=>$row["address"],"designation"=>$row["designation"],"role"=>$row["role"],"managerid"=>$row["managerid"],"salary"=>$row["salary"],"usertype"=>$row["usertype"],"commission"=>$row["commission"],"city"=>$row["city"],"latitude"=>$row["latitude"],"longitude"=>$row["longitude"],"region"=>$row["region"],"doj"=>$row["doj"],"dol"=>$row["dol"],"reportsto"=>$row["reportsto"],"image"=>$row["image"]);
		//$response[]=$rr;
		// echo json_encode($response);
		 
		$data=json_encode($row);
		echo $data;
		return;
		 
	  }
	  else
	  {
		 echo "error";
		 return;
	  }
   }





if (isset($_GET['new_login'])) {

    // Get POST data safely
	$pass = trim($_POST['pass']); // remove spaces before and after
	$user = trim($_POST['user']); 

    $name       = $_POST['name'] ?? '';
    $modelno    = $_POST['modelno'] ?? '';
    $appversion = $_POST['appversion'] ?? '';
    $osversion  = $_POST['osversion'] ?? '';
    $token      = $_POST['token'] ?? '';
    $datetime   = date("Y-m-d H:i:s");

    if (empty($user) || empty($pass)) {
        echo "error";
        exit;
    }

    // ✅ Hash password same as your working login
    function mysql_native_password_hash($plain) {
        return '*' . strtoupper(sha1(sha1($plain, true)));
    }

    $hash = mysql_native_password_hash($pass);
    $check = false;


    // ✅ Step 1: Check employee login
    $stmt = $con->prepare("
        SELECT *
        FROM employees
        WHERE email = ? AND password = ? AND status='1'
        LIMIT 1
    ");

  if (!$stmt) { die(json_encode(["status"=>"error","message"=>$con->error])); }
    $stmt->bind_param("ss", $user, $hash);
    $stmt->execute();
    $res = $stmt->get_result();

    if ($row = $res->fetch_assoc()) {
 
      $userid = $row['id'];

        // ✅ Step 2: Check device
        $stmt2 = $con->prepare("
            SELECT id, name, modelno
            FROM devices
            WHERE userid = ? AND status = 1
            LIMIT 1
        ");
 if (!$stmt2) { die(json_encode(["status"=>"error","message"=>$con->error])); }

        $stmt2->bind_param("i", $userid);
        $stmt2->execute();
        $res2 = $stmt2->get_result();

        if ($row2 = $res2->fetch_assoc()) {
            
		$name = trim($_POST['name']);
		$modelno = trim($_POST['modelno']);

		$db_name = trim($row2['name']);
		$db_modelno = trim($row2['modelno']);

		if ($db_name == $name && $db_modelno == $modelno) {
                $check = "same";

                // Clean old data
                $con->query("DELETE FROM outletactivity WHERE activitydate < CURDATE() - INTERVAL 200 DAY");

            } else {
                $check = "notsame";
            }
        } else {
            $check = "new";
        }

        if ($check == "notsame") {
            echo "already";
            exit;
        }

        // ✅ Step 3: Insert/update device
        if ($check == "new") {
            $stmt3 = $con->prepare("
                INSERT INTO devices(name, modelno, appversion, osversion, logindate, userid, status, track, usertoken)
                VALUES (?, ?, ?, ?, ?, ?, 1, 0, ?)
            ");
 if (!$stmt3) { die(json_encode(["status"=>"error","message"=>$con->error])); }
            $stmt3->bind_param("ssssiss", $name, $modelno, $appversion, $osversion, $datetime, $userid, $token);
            $stmt3->execute();
        } else {
            $stmt3 = $con->prepare("
                UPDATE devices
                SET name=?, modelno=?, appversion=?, osversion=?, logindate=?, usertoken=?
                WHERE userid=?
            ");
 if (!$stmt3) { die(json_encode(["status"=>"error","message"=>$con->error])); }
            $stmt3->bind_param("ssssssi", $name, $modelno, $appversion, $osversion, $datetime, $token, $userid);
            $stmt3->execute();
        }

        // ✅ Step 4: Return employee data as JSON
        echo json_encode($row);
        exit;

    } else {
        echo "error";
        exit;
    }
}



      if(isset($_GET['loginchk']))
      {
	     extract($_POST);
	     $check =false;
	  
	    $query1="select * from employees where email='$user' and id='$id' and usertype='1'";
	    $res=mysqli_query($con,$query1) or die(mysqli_error($con));
	    if($row=mysqli_fetch_array($res))
	    {
	      $status=$row["status"];
		  if($status==0)
		  {
			 $check="block";
		  }
	    }
	    if($check=="block")
		{
		  echo "error"; 	 
		}
		else
		{
			echo "success";
		}
   }

   if(isset($_GET['update']))
   {
        extract($_POST);
	  $query1="update employees set latitude='$latitude',longitude='$longitude',locationdate='$datetime' where id='$id'";
	  $res=mysqli_query($con,$query1) or die(mysqli_error($con));   
   }
   
   if(isset($_GET['get']))
   {
        $userid=$_GET['userid'];
		$response=array();
	  $query1="select latitude,longitude,locationdate,name from employees where id='$userid'";
	  $res=mysqli_query($con,$query1) or die(mysqli_error($con));
	  if($row=mysqli_fetch_array($res))
	  {
		 $rr=array("latitude"=>$row["latitude"],"longitude"=>$row["longitude"],"locationdate"=>$row["locationdate"],"name"=>$row["name"]);
		 array_push($response,$rr);
         $data=json_encode($response);
	     echo $data;  
	  }
   }
   
   
   if(isset($_GET['trace']))
   {
        $userid=$_POST['userid'];
		
	  $query1="select * from devices where userid='$userid' and status='1' and track='1'";
	  $res=mysqli_query($con,$query1) or die(mysqli_error($con));
	  if($row=mysqli_fetch_array($res))
	  {
		 echo"trace";  
	  }
	  else
	  {
		  echo"nottrace";
	  }
   }
   
   if(isset($_GET['devices']))
   {
      $status=$_GET['status'];
	  $response=array();	
	  $query1="select d.id,d.name,d.modelno,d.appversion,d.osversion,d.logindate,e.name as 'username',e.empid,d.status,d.track,d.usertoken from devices d join employees e on d.userid=e.id where  d.status='$status'";
	  $res=mysqli_query($con,$query1) or die(mysqli_error($con));
	  while($row=mysqli_fetch_array($res))
	  {
		  $rr=array();
		   $rr["id"]=$row["id"];
		   $rr["name"]=$row["name"];
		   $rr["modelno"]=$row['modelno'];
		   $rr["appversion"]=$row["appversion"];
		   $rr["osversion"]=$row["osversion"];
		   $rr["logindate"]=$row["logindate"];
		   $rr["username"]=$row["username"];
		   $rr["empid"]=$row["empid"];
		   $rr["status"]=$row["status"];
		   $rr["track"]=$row["track"];
		   $rr["usertoken"]=$row["usertoken"];
		    
		   array_push($response,$rr); 
	  }
	  echo json_encode($response);
	 
   }

   if(isset($_GET['devicestatus']))
   {
      $status=$_GET['status'];
	  $id=$_GET['id'];
	  
	  $query1="update devices set status='$status' where id='$id'";
	  mysqli_query($con,$query1) or die(mysqli_error($con));
	  if(mysqli_affected_rows($con)>0)
	  {  
		echo"success"; 
	  }
	  else
	  {
	    echo "error";
	  }
   }
   

   if(isset($_GET['userstatus']))
   {
      $status=$_GET['status'];
	  $id=$_GET['id'];
	  $query="select * from devices where userid='$id' and status='1'";
	  $res=mysqli_query($con,$query) or die(mysqli_error($con));
	   if($row=mysqli_fetch_array($res))
	   {
		   $token=$row['usertoken'];
		   $notification= array(
            'title'=>'logout',
            'body'=>'123',
			'icon'=>'fg',
			'sound'=>'gfh',
			'priority'=>'high'
            );
          $extraNotificationData=array("message"=>$notification,"moredata"=>'dd');
          $fcmNotification=array( 
            //'registration_ids' => $tokenList, //multple token array
            'to'        => $token, //single token
            'notification'=>$notification,
            'data' => $extraNotificationData
           );
        $headers = array(
            'Authorization: key=' . API_ACCESS_KEY,
            'Content-Type: application/json'
         );


        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL,$fcmUrl);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($fcmNotification));
        $result = curl_exec($ch);
        curl_close($ch);

	   }
	  $query1="update employees set status='$status' where id='$id' and usertype='1'";
	  mysqli_query($con,$query1) or die(mysqli_error($con));
	  if(mysqli_affected_rows($con)>0)
	  {  
		echo"success"; 
	  }
	  else
	  {
	    echo "error";
	  }
   }
   



   if(isset($_GET['devicetrack']))
   {
      $track=$_GET['track'];
	  $id=$_GET['id'];
	  	
	  $query1="update devices set track='$track' where id='$id'";
	  mysqli_query($con,$query1) or die(mysqli_error($con));
	  if(mysqli_affected_rows($con)>0)
	  {  
		echo"success"; 
	  }
	  else
	  {
	    echo "error";
	  }
   }



   if (isset($_GET['delete_devices']) && !empty($_GET['id'])) {
    $id = intval($_GET['id']); // Ensures ID is an integer
    $stmt = $con->prepare("DELETE FROM `devices` WHERE id = ?");
    $stmt->bind_param("i", $id);

    if ($stmt->execute()) {
        $stmt->close();
    }

	header('Location: ' . $_SERVER['HTTP_REFERER'], true, 303);
	exit;
}
   
?>
