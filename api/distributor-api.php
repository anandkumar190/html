<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
if(!isset($_SESSION['tittu']))
{
	echo"invalid";
	exit();
}

    function mysql_native_password_hash($plain) {
        return '*' . strtoupper(sha1(sha1($plain, true)));
    }
   
  function genrateId($con)
  {  
	  $query = "select empid from employees where usertype='3' order by empid desc";
	  //echo($pshort);
	  $res=mysqli_query($con,$query);
	  $id="";
	  if($row=mysqli_fetch_array($res)){
			$id=$row["empid"];
	  }
	  if($id=="")
	  return "DIS0001";
	  $id=(int)substr($id,3);
	  $id=$id+1;
	  
	  if($id>0 &&$id<10)
	  {
		  return"DIS000".$id;
	  }
	  if($id>9 &&$id<100)
	  {
		  return"DIS00".$id;
	  }
	  if($id>99 &&$id<1000)
	  {
		  return"DIS0".$id;
	  }
	  if($id>999)
	  {
		  return"DIS".$id;
	  }
  }


  $userid=$_SESSION['id'];
  $usertype=$_SESSION['usertype'];
  require_once __DIR__ . '/../connect.php';

$time=date("H:i:s"); 
  $datetime = date("Y-m-d H:i:s");
  $date=date("Y-m-d");
  if(isset($_GET['insert']))
  {
	  //$_POST=json_decode(file_get_contents("php://input"));
	  extract($_POST);
      $empcode=genrateId($con);	
	  //var_dump($_POST);
	  
	  $query = "select empid from employees where  empid = '$empcode' and usertype='3'";
	  //echo($pshort);
	  $res=mysqli_query($con,$query);
	  if( mysqli_num_rows($res)> 0 ){
			echo "empcode";
			return;
	  }
	  $query = "select email from employees where email = '$empemail' and usertype='3'";
	  $res=mysqli_query($con,$query);
	  if( mysqli_num_rows($res)> 0 ){
			echo "empemail";
			return;
	  }
	  $query = "select contact from employees where contact = '$empcontact' and usertype='3'";
	  $res=mysqli_query($con,$query);
	  if( mysqli_num_rows($res)> 0 ){
			echo "empcontact";
			return;
	  }

	
	$filename="image";
	
 $password = mysql_native_password_hash($empcontact);
	mysqli_query($con,"insert into employees(image,name,contactperson,sortname,empid,email,contact,password,address,designationid,roleid,managerid,usertype,salary,commission,city,state,reportsto,latitude,longitude,battery,region,doj,creationdate,createdby,lastlogin) values('$filename','$empname','$empcontactname','$empsortname','$empcode','$empemail','$empcontact','$password','$empaddress','0','0','0','3','0','0','$empcity','$empstate','0','$emplat','$emplng','','','$datetime','$datetime','$userid','$datetime')") or die(mysqli_error($con));
	
	if(mysqli_affected_rows($con)>0)
	{
	  echo"success";

	}else
	  {
        echo"error";
	  }
	
  }

  else if(isset($_GET['show']))
  {
	$query = "SELECT 
            e.id, 
            e.name, 
            e.email, 
            e.contactperson, 
            e.contact, 
            e.usertype,
			e.address, 
            c.city, 
            s.name AS state 
          FROM employees e 
          LEFT JOIN states s ON e.state = s.id 
          LEFT JOIN cities c ON e.city = c.id 
          WHERE e.usertype = '3'";
	$res=mysqli_query($con,$query);

	if (!$res) {
		die("Query failed: " . mysqli_error($con));
	}

		$response=array();   
		$result=mysqli_query($con,"select a.distributor_id,
					COUNT( DISTINCT o.id) AS total_outlet_count ,
					COUNT( DISTINCT a.id) AS total_route_count 
					from outlets o 
					left JOIN area a ON o.routeid=a.id
					GROUP BY a.distributor_id");

		$arrayRoute= $arrayOutlet = array();

		while($outlets=mysqli_fetch_array($result))
		{
		$arrayOutlet[$outlets['distributor_id']]=$outlets['total_outlet_count'];
		$arrayRoute[$outlets['distributor_id']]=$outlets['total_route_count'];
		}

	 while($row=mysqli_fetch_array($res))
	 {
		 		 $rr=array(
					"id"=>$row["id"],
					"name"=>$row["name"],
					"email"=>$row["email"],
					"contact"=>$row["contact"],
					"address"=>$row["address"],	
					"city"=>$row["city"],
					"state"=>$row["state"],
					"contactperson"=>$row["contactperson"],
					"no_of_outlets"=>@$arrayOutlet[$row["id"]]??0,
					"no_of_routes"=>@$arrayRoute[$row["id"]]??0,
				
				);
		 $response[]=$rr;
     }	 
	 $data=json_encode($response);
	 echo $data;
  }

  else if(isset($_GET['edit']))
  {
	  //$_POST=json_decode(file_get_contents("php://input"));
	  extract($_POST);
	
	  //var_dump($_POST);
	  
	  //$query = "select empid from employees where  empid = '$empcode' && id!='$id' and usertype='3'";
	  //echo($pshort);
	  //$res=mysqli_query($con,$query);
	  //if( mysqli_num_rows($res)> 0 ){
	//		echo "empcode";
	//		return;
	//  }
	  $query = "select email from employees where email = '$empemail' and id!='$id' and usertype='3'";
	  $res=mysqli_query($con,$query);
	  if( mysqli_num_rows($res)> 0 ){
			echo "empemail";
			return;
	  }
	  $query = "select contact from employees where contact = '$empcontact' and id!='$id' and usertype='3'";
	  $res=mysqli_query($con,$query);
	  if(mysqli_num_rows($res)> 0 ){
			echo "empcontact";
			return;
	  }
	
	 if(isset($_FILES['empimage']['name']) && $_FILES['empimage']['name']!="" && !isset($_POST['empimage']) )
	  {
	     $filename=$_FILES['empimage']['name'];
	     $tmpname=$_FILES['empimage']['tmp_name'];
	     $filesize=$_FILES['empimage']['size'];
	     $filetype=$_FILES['empimage']['type'];
	     
		 if($filetype!="image/jpg" && $filetype!="image/png" &&   $filetype!="image/jpeg")
	     {
	       echo"Please Upload Images(PNG,JPG & JPEG) Files Only...";
 	       return;
      	 }
	     if($filesize>800000)
	     {
	       echo"Image can't be Greater than 800KB .";
	       return;
	     }
		  
	      $filename=$empname.$id.".jpg";
		  
		  if(file_exists("../imgusers".$filename))
		  {
			  unlink("../imgusers".$filename);
		  }
		   $password = mysql_native_password_hash($empcontact);
		  
	      	      mysqli_query($con,"update  employees set name='$empname',
				  email='$empemail',
				  contact='$empcontact',
				  password='$password',
				  address='$empaddress',
				  latitude='$emplat',
				  longitude='$emplng',
				  city='$empcity',
				  state='$empstate',
				  lastlogin='$datetime',
				  contactperson='$empcontactname',
				  sortname='$empsortname' where id='$id'") or die(mysqli_error($con));

	      if(mysqli_affected_rows($con)>0)
       	  {
	       if(move_uploaded_file($tmpname,"../imgusers/".$filename))
	        {
	          echo"success";
	        }
	       else
	        {
             echo"Image error";
	        }
	      }
		  else
		  {
			 if(move_uploaded_file($tmpname,"../imgusers/".$filename))
	        {
	          echo"success";
	        }
	       else
	        {
             echo"Image error";
	        } 
		  }
	   }
	   else
	   {
		 $password = mysql_native_password_hash($empcontact);

	      	mysqli_query($con,"update  employees set name='$empname',
			email='$empemail',
			contact='$empcontact',
			password='$password',
			address='$empaddress',
			city='$empcity',
			state='$empstate',
			latitude='$emplat',
			longitude='$emplng',
			lastlogin='$datetime',
			contactperson='$empcontactname',
			sortname='$empsortname'
			where id='$id'") or die(mysqli_error($con));

	      if(mysqli_affected_rows($con)>0)
       	  {
	          echo"success";  
	      }
		  else
		  {
			  echo"No changes affected...";
		  }
	   }
  }
  
  
if(isset($_GET['import']))
  {
	 $filetype=$_FILES["file1"]["type"];	 
     $filename=$_FILES["file1"]["tmp_name"];
     if($_FILES["file1"]["size"] > 0)
	 {
		 $file = fopen($filename, "r");
        $count=0;
        while (($areaData = fgetcsv($file, 10000, ",")) !== FALSE)
        {
			$imgpath="deafult-user.png";
			$empcode=genrateId($con);  
			$count++;
			if($count>1)
			{
				$areaid=getArea($areaData[8],$areaData[9],$con);
				$stockistid=getStockistId($areaData[12],$con);
				mysqli_query($con,"insert into employees(image,name,empid,email,contact,address,designationid,roleid,managerid,usertype,salary,commission,city,state,reportsto,latitude,longitude,battery,region,doj,creationdate,createdby,stockistid,areaid,lastlogin) values('$imgpath','$areaData[0]','$empcode','$areaData[1]','$areaData[2]','$areaData[3]','0','0','0','3','0','0','$areaData[4]','$areaData[5]','0','$areaData[10]','$areaData[11]','$areaData[7]','$areaData[6]','$datetime','$datetime','$userid','$stockistid','$areaid','$datetime')") or die(mysqli_error($con));
			}
   		}
		fclose($file);
        echo "success";
	  }
	  else
	  {
		echo "error"; 
	  }	 
  }
  
  function getArea($areaname,$region,$con)
  {
	 $res=mysqli_query($con,"select id from area where area='$areaname' and region='$region'");
	 $row=mysqli_fetch_array($res);
	 return $row["id"];
  }
  
  function getStockistId($stockistid,$con)
  {
	 $res=mysqli_query($con,"select id from employees where empid='$stockistid'");
	 $row=mysqli_fetch_array($res);
	 return $row["id"];
  }
  
   if(isset($_GET['delete']))
  {
	$ids=$_POST['ids'];
	foreach($ids as $id)
	{
      mysqli_query($con,"delete from employees where id='$id'");
	}
     echo "Distributors Delete Succesfully...";
  }

  else if(isset($_GET['getstate']))
  {
      $res = mysqli_query($con, "SELECT DISTINCT s.id AS state, s.name FROM employees e JOIN states s ON e.state = s.id WHERE e.usertype = '3' ORDER BY s.name");
      $response = array();
      while($row = mysqli_fetch_assoc($res)) {
          $response[] = $row;
      }
      header('Content-Type: application/json');
      echo json_encode($response);
      exit;
  }

  else if(isset($_GET['getcity']))
  {
      $state = isset($_GET['state']) ? mysqli_real_escape_string($con, trim($_GET['state'])) : '';
      $where = "WHERE e.usertype = '3'";
      if($state != '') {
          $where .= " AND e.state = '$state'";
      }
      $res = mysqli_query($con, "SELECT DISTINCT c.id, c.city FROM employees e JOIN cities c ON e.city = c.id $where ORDER BY c.city");
      $response = array();
      while($row = mysqli_fetch_assoc($res)) {
          $response[] = $row;
      }
      header('Content-Type: application/json');
      echo json_encode($response);
      exit;
  }

  else if(isset($_GET['getregion']))
  {
      $city = isset($_GET['city']) ? mysqli_real_escape_string($con, trim($_GET['city'])) : '';
      $where = "WHERE e.usertype = '3'";
      if($city != '') {
          $where .= " AND e.city = '$city'";
      }
      $res = mysqli_query($con, "SELECT DISTINCT r.id AS region, r.name FROM employees e JOIN regions r ON e.region = r.id $where ORDER BY r.name");
      $response = array();
      while($row = mysqli_fetch_assoc($res)) {
          $response[] = $row;
      }
      header('Content-Type: application/json');
      echo json_encode($response);
      exit;
  }

  else if(isset($_GET['getdistributor']))
  {
      $res = mysqli_query($con, "SELECT id, name, empid FROM employees WHERE usertype = '3' ORDER BY name");
      $response = array();
      while($row = mysqli_fetch_assoc($res)) {
          $response[] = $row;
      }
      header('Content-Type: application/json');
      echo json_encode($response);
      exit;
  }

  else if(isset($_GET['showmap']) || isset($_GET['list-of-distributor']))
  {
      $state = isset($_GET['state']) ? trim($_GET['state']) : '';
      $city = isset($_GET['city']) ? trim($_GET['city']) : '';
      $region = isset($_GET['region']) ? trim($_GET['region']) : '';
      $distributor = isset($_GET['distributor']) ? trim($_GET['distributor']) : '';

      $selectQry = "
          SELECT 
              e.id, 
              e.name, 
              e.empid,
              e.email, 
              e.contactperson, 
              e.contact, 
              e.address, 
              e.latitude,
              e.longitude,
              e.image,
              c.city, 
              s.name AS state,
              r.name AS region
          FROM employees e 
          LEFT JOIN states s ON e.state = s.id 
          LEFT JOIN cities c ON e.city = c.id 
          LEFT JOIN regions r ON e.region = r.id
          WHERE e.usertype = '3'
      ";

      if ($distributor != "") {
          $selectQry .= " AND e.id = '" . mysqli_real_escape_string($con, $distributor) . "'";
      } else {
          if ($state != "") {
              $selectQry .= " AND e.state = '" . mysqli_real_escape_string($con, $state) . "'";
          }
          if ($city != "") {
              $selectQry .= " AND e.city = '" . mysqli_real_escape_string($con, $city) . "'";
          }
          if ($region != "") {
              $selectQry .= " AND e.region = '" . mysqli_real_escape_string($con, $region) . "'";
          }
      }

      $selectQry .= " ORDER BY e.name";
      $result = mysqli_query($con, $selectQry);
      if (!$result) {
          die("Query failed: " . mysqli_error($con));
      }

      // Count outlets and routes for each distributor
      $outletsResult = mysqli_query($con, "
          SELECT 
              a.distributor_id,
              COUNT(DISTINCT o.id) AS total_outlet_count,
              COUNT(DISTINCT a.id) AS total_route_count 
          FROM area a
          LEFT JOIN outlets o ON o.routeid = a.id
          GROUP BY a.distributor_id
      ");

      $arrayRoute = array();
      $arrayOutlet = array();
      if ($outletsResult) {
          while ($outlets = mysqli_fetch_assoc($outletsResult)) {
              $arrayOutlet[$outlets['distributor_id']] = $outlets['total_outlet_count'];
              $arrayRoute[$outlets['distributor_id']] = $outlets['total_route_count'];
          }
      }

      $response = array();
      $total = 0;
      $withCoords = 0;
      $statesMap = array();
      $citiesMap = array();

      while ($row = mysqli_fetch_assoc($result)) {
          $total++;
          $lat = trim($row['latitude'] ?? '');
          $lng = trim($row['longitude'] ?? '');
          if ($lat != '' && $lng != '' && is_numeric($lat) && is_numeric($lng)) {
              $withCoords++;
          }
          if (!empty($row['state'])) {
              $statesMap[$row['state']] = true;
          }
          if (!empty($row['city'])) {
              $citiesMap[$row['city']] = true;
          }

          $response[] = array(
              "id" => $row["id"],
              "empid" => $row["empid"] ?? '',
              "name" => $row["name"] ?? '',
              "email" => $row["email"] ?? '',
              "contactperson" => $row["contactperson"] ?? '',
              "contact" => $row["contact"] ?? '',
              "address" => $row["address"] ?? '',
              "city" => $row["city"] ?? '',
              "state" => $row["state"] ?? '',
              "region" => $row["region"] ?? '',
              "latitude" => $row["latitude"] ?? '',
              "longitude" => $row["longitude"] ?? '',
              "image" => $row["image"] ?? '',
              "no_of_outlets" => $arrayOutlet[$row["id"]] ?? 0,
              "no_of_routes" => $arrayRoute[$row["id"]] ?? 0
          );
      }

      // Add summary object at the end
      $response[] = array(
          "total" => $total,
          "mapped" => $withCoords,
          "states_count" => count($statesMap),
          "cities_count" => count($citiesMap)
      );

      header('Content-Type: application/json');
      echo json_encode($response);
      exit;
  }
?>