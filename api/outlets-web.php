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

   if(isset($_GET['delete']))
   {
	   $ids=$_POST['ids'];
	   foreach($ids as $id)
	   {
		 mysqli_query($con,"delete from outlets where id='$id'");
		 mysqli_query($con,"delete from outletactivity where outletid='$id' and visittype='0' and activitytype='New Outlet Create'"); 	   }
	   echo "Outlets Move to trash Successfully...";
   }

   if(isset($_GET['changearea']))
   {
	   $ids=$_POST['ids'];
	   $areaid=$_POST['areaid'];
	   foreach($ids as $id)
	   {
		 mysqli_query($con,"update outlets set areaid='$areaid', routeid='$areaid'  where id='$id'");
	   }
	   echo "Area Assign Successfully to Selected Outlets.";
   }
   

   if(isset($_GET['changedistributeid']))
   {
	   $ids=$_POST['ids'];
	   $distributorid=$_POST['distributeid'];
	   foreach($ids as $id)
	   {
		 mysqli_query($con,"update outlets set distributorid='$distributorid' where id='$id'");
	   }
	   echo "Area Assign Successfully to Selected Outlets.";
   }


   
   if(isset($_GET['changerouteid']))
   {
	   $ids=$_POST['ids'];
	   $routeid=$_POST['routeid'];
	   foreach($ids as $id)
	   {
		 mysqli_query($con,"update outlets set routeid='$routeid' where id='$id'");
	   }
	   echo "Area Assign Successfully to Selected Outlets.";
   }



  
   if(isset($_GET['new']))
   {
	  
	  extract($_POST);
	  $filename=$_FILES['lastvisitpic']['name'];
	  $tmpname=$_FILES['lastvisitpic']['tmp_name'];
	  $filename=$name.$contact.$filename.".jpg";
	  $response=array();
	  
	  $lat=truncate_number($latitude,3);
	  $lng=truncate_number($longitude,3);
	  
	  $res=mysqli_query($con,"select * from outlets where outlettype='$outlettype' and state='$state' and city='$city' contactperson='$contactperson' and contact='$contact' ");
	  if($row=mysqli_fetch_array($res))
	   {
		 $response["message"]="already";
		 echo json_encode($response); 
		 return;   
	   }
	  
	  mysqli_query($con,"insert into outlets(name,address,lastvisitpic,contactperson,contact,pincode,gstnumber,outlettype,outletsubtype,distributorid,routeid,competitor_presense,street,locality,city,state,latitude,longitude,areaid,lastvisit,creationdate,createdby) values('$name','$address','$filename','$contactperson','$contact','$pincode','$gstnumber','$outlettype','$outletsubtype','1','0','$competitor_presense','$street','$locality','$city','$state','$latitude','$longitude','$areaid','$date','$datetime','$createdby')");
	  
	  if(mysqli_affected_rows($con)>0)
	  {
		  $outletid=mysqli_insert_id($con);
		  mysqli_query($con,"insert into outletactivity(userid,outletid,activitytype,battery,activitydate,activitytime) values('$createdby','$outletid','New Outlet','$battery%','$date','$time')")or die(mysqli_error($con));
		
		  if(move_uploaded_file($tmpname,"../imgoutlets/".$filename))
		  {
		    $response["message"]="success";
		  }
		  else
	      {
		   $response["message"]="error";
	      }
	  }
	  else
	  {
		   $response["message"]="error";
	  }
	   
	    echo json_encode($response); 
   }
   
   
   
      if(isset($_GET['edit']))
   {
	  
	  extract($_POST);
	  
	  $response=array();
	  
	  $lat=truncate_number($latitude,3);
	  $lng=truncate_number($longitude,3);
	  
	  $res=mysqli_query($con,"select * from outlets where outlettype='$outlettype' and name='$name' and contact='$contact' and  id!='$id'") or die(mysqli_error($con));
	  if($row=mysqli_fetch_array($res))
	   {
		 echo "already";
			return;   
	   }
	   
	  if(isset($_FILES['lastvisitpic']['name']))
	  {
	      $filename=$_FILES['lastvisitpic']['name'];
	      $tmpname=$_FILES['lastvisitpic']['tmp_name'];
	      $filesize=$_FILES['empimage']['size'];
	      $filetype=$_FILES['empimage']['type'];
	      if($filetype!="image/jpg" && $filetype!="image/png" && $filetype!="image/jpeg")
	      {
	         echo"filetype";
	         return;
	      }
	      if($filesize>800000)
	      {
	         echo"Image can't be Greater than 800KB .";
	         return;
	      }
	       $filename=$name.$contact.$filename.".jpg";
		   mysqli_query($con,"update outlets set name='$name',address='$address',lastvisitpic='$filename',contactperson='$contactperson',contact='$contact',pincode='$pincode',gstnumber='$gstnumber',outlettype='$outlettype',outletsubtype='$outletsubtype',distributorid='$distributorid',competitor_presense='$competitor_presense',street='$street',locality='$locality',city='$city',state='$state',latitude='$latitude',longitude='$longitude',areaid='$areaid' where id='$id'");
	  
		  if(mysqli_affected_rows($con)>0)
     	  {
		 
		     if(move_uploaded_file($tmpname,"../imgoutlets/".$filename))
		     {
		       echo"success";
		     }
		  else
	      {
		   echo"error";
	      }
	     }
	     else
	    {
		   echo"error";
		}
	  }
	  else{
	  mysqli_query($con,"update outlets set name='$name',address='$address',contactperson='$contactperson',contact='$contact',pincode='$pincode',gstnumber='$gstnumber',outlettype='$outlettype',outletsubtype='$outletsubtype',distributorid='$distributorid',competitor_presense='$competitor_presense',street='$street',locality='$locality',city='$city',state='$state',latitude='$latitude',longitude='$longitude',areaid='$areaid' where id='$id'");
	  
	     if(mysqli_affected_rows($con)>0)
     	  {
		    echo"success";
		  }
		 
	     else
	     {
		   echo"error";
		 }
	  
	  }
}

   
   
   if(isset($_GET['visitregister']))
   {
	  extract($_POST);
	  $filename=$_FILES['lastvisitpic']['name'];
	  $tmpname=$_FILES['lastvisitpic']['tmp_name'];
	  $filename=$name.$contact.$filename.".jpg";
	  $response=array();
	  	  
	  mysqli_query($con,"update  outlets set lastvisitpic='$filename',lastvisit=now() where id='$id'")or die(mysqli_error($con));
	  
	  if(mysqli_affected_rows($con)>0)
	  {
		  
		  mysqli_query($con,"insert into outletactivity(userid,outletid,activitytype,battery,activitydate,activitytime) values('$userid','$id','Visit','$battery%','$date','$time')")or die(mysqli_error($con));
		 
		   $entryid=mysqli_insert_id($con);
		  
		  if(move_uploaded_file($tmpname,"../imgoutlets/".$filename))
		  {
		    $response["message"]="success";
			$response["entryid"]=$entryid;
		  }
		  else
	      {
		    $response["message"]="error";
	      }
	  }
	  else
	  {
		   $response["message"]="error";
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
	   
	    echo json_encode($response); 
 
   }


   if(isset($_GET['show']))
   {
	   $res=mysqli_query($con,"select o.id,o.state,cities.city As city,o.locality,o.distributorid,o.name,o.address,o.lastvisitpic,o.contactperson,o.contact,o.pincode,o.gstnumber,o.outlettype,o.outletsubtype,o.routeid,o.latitude,o.longitude,o.areaid,o.lastvisit,o.creationdate,o.createdby,concat(d.name,' - ',d.empid) as 'distributor',concat(s.name,'-',s.empid) as 'stockist',s.id as 'stockistid', concat(e.name,'-',e.empid)as 'so',a.area,a.region from outlets o join employees d on d.id=o.distributorid join employees s on s.id=d.stockistid join employees e on e.id=o.createdby join area a on a.id=o.areaid order by o.id desc") or die(mysqli_error($con));   
	   $response=array();
	   $num=mysqli_field_count($con);
	   $total=0;$gt=0;$mt=0;$mtl=0;$milkbooth=0;
	   while($row=mysqli_fetch_array($res))
	   {
		   $rr=array();
		   $rr["id"]=$row["id"];
		   $rr["state"]=$row["state"];
		   $rr["city"]=$row["city"];
		   $rr["region"]=$row["region"];
		   $rr["distributorid"]=$row["distributorid"];
		   $rr["name"]=$row["name"];
		   $rr["address"]=$row['address'];
		   $rr["lastvisitpic"]=$row["lastvisitpic"];
		   $rr["contactperson"]=$row["contactperson"];
		   $rr["contact"]=$row["contact"];
		   $rr["pincode"]=$row["pincode"];
		   $rr["gstnumber"]=$row["gstnumber"];
		   $rr["outlettype"]=$row["outlettype"];
		   $rr["outletsubtype"]=$row["outletsubtype"];
		   $rr["routeid"]=$row["routeid"];
		   $rr["latitude"]=$row["latitude"];
		   $rr["longitude"]=$row["longitude"];
		   $rr["areaid"]=$row["areaid"];
		   $rr["lastvisit"]=$row["lastvisit"];
		   $rr["creationdate"]=$row["creationdate"];
		   $rr["createdby"]=$row["createdby"]; 
		   $rr["distributor"]=$row["distributor"]; 
		   $rr["stockist"]=$row["stockist"];
		   $rr["stockistid"]=$row["stockistid"];  
		   $rr["so"]=$row["so"];
		   $rr["area"]=$row["area"];  
		   
		   if($row["outlettype"]=="MTS")
		   {
			   $mt++;
		   }
		   if($row["outlettype"]=="G.T.")
		   {
			   $gt++;
		   }
		   if($row["outlettype"]=="Milk Booth")
		   {
			   $milkbooth++;
		   }
		   if($row["outlettype"]=="MTL")
		   {
			   $mtl++;
		   }
		   
		   $total++;
		   
		   $rr["mt"]=$mt;
	       $rr["gt"]=$gt;
	       $rr["mtl"]=$mtl;
	       $rr["milkbooth"]=$milkbooth;
	       $rr["total"]=$total;
	        array_push($response,$rr);
	   } 
	   
	   //$data=array();
	   $data=json_encode($response);
	   echo $data;
	   return; 
   }

if(isset($_GET['showmap']))
   {
	   $state=$_GET['state'];
	   $region=$_GET['region'];
	   $city=$_GET['city'];
	   $routeid=$_GET['area'];
       $distributor=trim($_GET['distributor']);

$selectQry = "
    SELECT 
        o.id,
        cities.city AS city,
        o.locality,
        o.distributorid,
        o.name,
        o.address,
        o.lastvisitpic,
        o.contactperson,
        o.contact,
        o.pincode,
        o.gstnumber,
        o.outlettype,
        o.outletsubtype,
        o.routeid,
        o.latitude,
        o.longitude,
        o.areaid,
        o.lastvisit,
        o.creationdate,
        a.area,
        o.createdby,
        CONCAT(d.name, ' - ', d.empid) AS distributor,
        regions.name AS region,
        states.name AS state
    FROM outlets o 
    JOIN area a ON a.id = o.routeid 
    JOIN employees d ON d.id = a.distributor_id
    LEFT JOIN states ON states.id = a.state 
    LEFT JOIN cities ON cities.id = a.city 
    LEFT JOIN regions ON regions.id = a.region
";

// Optional filters
$isSnd = 0;

 if ($distributor!="") {
			$prefix="where";
			$selectQry=$selectQry.$prefix." d.id ='$distributor'";
			$isSnd=1;
	 }else {

		if ($state != "") {
			$prefix = $isSnd == 0 ? " WHERE " : " AND ";
			$selectQry .= $prefix . "a.state = '$state'";
			$isSnd = 1;
		}

		if ($city != "") {
			$prefix = $isSnd == 0 ? " WHERE " : " AND ";
			$selectQry .= $prefix . "a.city = '$city'";
			$isSnd = 1;
		}

		if ($region != "") {
			$prefix = $isSnd == 0 ? " WHERE " : " AND ";
			$selectQry .= $prefix . "a.region = '$region'";
			$isSnd = 1;
		}

		if ($routeid != "") {
			$prefix = $isSnd == 0 ? " WHERE " : " AND ";
			$selectQry .= $prefix . "o.routeid = '$routeid'";
			$isSnd = 1;
		}
}

// Execute the query
$result = mysqli_query($con, $selectQry);

if (!$result) {
    die("Query failed: " . mysqli_error($con));
}

$response = array();
 $total=0;$gt=0;$mt=0;$mtl=0;$milkbooth=0;
while ($row = mysqli_fetch_array($result)) {
    $rr = array();
    $rr["id"] = $row["id"];
    $rr["state"] = $row["state"];
    $rr["city"] = $row["city"];
    $rr["region"] = $row["region"];
    $rr["name"] = $row["name"];
    $rr["address"] = $row["address"];
    $rr["contact"] = $row["contact"];
    $rr["pincode"] = $row["pincode"];
    $rr["gstnumber"] = $row["gstnumber"];
    $rr["outlettype"] = $row["outlettype"];
    $rr["outletsubtype"] = $row["outletsubtype"];
    $rr["routeid"] = $row["routeid"];
    $rr["latitude"] = $row["latitude"];
    $rr["longitude"] = $row["longitude"];
    $rr["areaid"] = $row["areaid"];
    $rr["area"] = $row["area"];

   if($row["outlettype"]=="MTS")
		   {
			   $mt++;
		   }
		   if($row["outlettype"]=="G.T.")
		   {
			   $gt++;
		   }
		   if($row["outlettype"]=="Milk Booth")
		   {
			   $milkbooth++;
		   }
		   if($row["outlettype"]=="MTL")
		   {
			   $mtl++;
		   }
		   
		   $total++;
		   
		   $rr["mt"]=$mt;
	       $rr["gt"]=$gt;
	       $rr["mtl"]=$mtl;
	       $rr["milkbooth"]=$milkbooth;
	       $rr["total"]=$total;
	
    
    array_push($response, $rr);
}
	   
	   //$data=array();
	   $data=json_encode($response);
	   echo $data;
	   return; 
   }


   if(isset($_GET['showduplicate']))
   {
	   $res=mysqli_query($con,"select o.id,o.state,cities.city As city,o.locality,o.distributorid,o.name,o.address,o.lastvisitpic,o.contactperson,o.contact,o.pincode,o.gstnumber,o.outlettype,o.outletsubtype,o.routeid,o.latitude,o.longitude,o.areaid,o.lastvisit,o.creationdate,o.createdby,concat(d.name,' - ',d.empid) as 'distributor',concat(s.name,'-',s.empid) as 'stockist',s.id as 'stockistid', concat(e.name,'-',e.empid)as 'so',a.area,a.region from outlets o join employees d on d.id=o.distributorid join employees s on s.id=d.stockistid join employees e on e.id=o.createdby join area a on a.id=o.areaid join (select name,contact,address,count(*) from outlets group by name,contact,address having count(*)>1) ob on o.name=ob.name and o.contact=ob.contact order by o.id desc") or die(mysqli_error($con));   
	   $response=array();
	   $num=mysqli_field_count($con);
	   $total=0;$gt=0;$mt=0;$mtl=0;$milkbooth=0;
	   while($row=mysqli_fetch_array($res))
	   {
		   $rr=array();
		   $rr["id"]=$row["id"];
		   $rr["state"]=$row["state"];
		   $rr["city"]=$row["city"];
		   $rr["region"]=$row["region"];
		   $rr["distributorid"]=$row["distributorid"];
		   $rr["name"]=$row["name"];
		   $rr["address"]=$row['address'];
		   $rr["lastvisitpic"]=$row["lastvisitpic"];
		   $rr["contactperson"]=$row["contactperson"];
		   $rr["contact"]=$row["contact"];
		   $rr["pincode"]=$row["pincode"];
		   $rr["gstnumber"]=$row["gstnumber"];
		   $rr["outlettype"]=$row["outlettype"];
		   $rr["outletsubtype"]=$row["outletsubtype"];
		   $rr["routeid"]=$row["routeid"];
		   $rr["latitude"]=$row["latitude"];
		   $rr["longitude"]=$row["longitude"];
		   $rr["areaid"]=$row["areaid"];
		   $rr["lastvisit"]=$row["lastvisit"];
		   $rr["creationdate"]=$row["creationdate"];
		   $rr["createdby"]=$row["createdby"]; 
		   $rr["distributor"]=$row["distributor"]; 
		   $rr["stockist"]=$row["stockist"];
		   $rr["stockistid"]=$row["stockistid"];  
		   $rr["so"]=$row["so"];
		   $rr["area"]=$row["area"];  
		   
		   if($row["outlettype"]=="MTS")
		   {
			   $mt++;
		   }
		   if($row["outlettype"]=="G.T.")
		   {
			   $gt++;
		   }
		   if($row["outlettype"]=="Milk Booth")
		   {
			   $milkbooth++;
		   }
		   if($row["outlettype"]=="MTL")
		   {
			   $mtl++;
		   }
		   
		   $total++;
		   
		   $rr["mt"]=$mt;
	       $rr["gt"]=$gt;
	       $rr["mtl"]=$mtl;
	       $rr["milkbooth"]=$milkbooth;
	       $rr["total"]=$total;
	        array_push($response,$rr);
	   } 
	   
	   //$data=array();
	   $data=json_encode($response);
	   echo $data;
	   return; 
   }



if(isset($_GET['search']))
   {
	   $state=trim($_GET['state']);
	   $city=trim($_GET['city']); 
	   $region=trim($_GET['region']); 
	   $routeid=trim($_GET['area']);
	   $so="";
	   $distributor=trim($_GET['distributor']);
	   $stockist="";
	   $selectQry="select o.id,
	   			  cities.city As city,
				  o.locality,
				  o.distributorid,
				  o.name,
				  o.address,
				  o.lastvisitpic,
				  o.contactperson,
				  o.contact,
				  o.pincode,
				  o.gstnumber,
				  o.outlettype,
				  o.outletsubtype,
				   o.routeid,
				  o.latitude,
				  o.longitude,
				  o.areaid,
				  o.lastvisit,
				  o.creationdate,
				  a.area,
				  o.createdby,
				  concat(d.name,' - ',d.empid) as 'distributor',
					a.area, regions.name As region,
					states.name As state  from outlets o 
					join area a on a.id=o.routeid 
					JOIN employees d ON d.id = a.distributor_id
					left join states on states.id= a.state 
					left join cities on cities.id= a.city left 
					join regions on regions.id= a.region ";
	  	
         $isSnd=0;




		 if ($distributor!="") {
			$prefix="where";
			$selectQry=$selectQry.$prefix." d.id ='$distributor'";
			$isSnd=1;
		 }else {

			if ($state!="") {
				$prefix=$isSnd==0?" where ":" and ";
				$selectQry=$selectQry.$prefix." a.state='$state'";
				$isSnd=1;
			 }
	
			 if ($city!="") {
				$prefix=$isSnd==0?" where ":" and ";
				$selectQry=$selectQry.$prefix." a.city ='$city'";
				$isSnd=1;
			 }

			 if ($region!="") {
				$prefix=$isSnd==0?" where ":" and ";
				$selectQry=$selectQry.$prefix." a.region ='$region'";
				$isSnd=1;
			 }
	
	
			 if ($routeid!="") {
				$prefix=$isSnd==0?" where ":" and ";
				$selectQry=$selectQry.$prefix." o.routeid = '$routeid'";
				$isSnd=1;
			 }
		 }


		 $query=$selectQry."order by o.id desc";
		     
  
	   $res=mysqli_query($con,$query) or die(mysqli_error($con));   
	   $response=array();
	   $num=mysqli_field_count($con);
	   $total=0;$gt=0;$mt=0;$mtl=0;$milkbooth=0;
	   while($row=mysqli_fetch_array($res))
	   {
		$currentDateTime = date('Y-m-d H:i:s');
		$previous30DateTime = date('Y-m-d H:i:s', strtotime('-30 days'));
		$previous180DateTime = date('Y-m-d H:i:s', strtotime('-180 days'));
		
		$outletId = $row["id"];
		
		// 30 days sum
		$query30 = "
			SELECT SUM(total_amount) AS total_amount 
			FROM booking 
			WHERE outlet_id = '$outletId' 
			  AND booking_time BETWEEN '$previous30DateTime' AND '$currentDateTime' 
			GROUP BY outlet_id
		";
		$res30days = mysqli_query($con, $query30);
		$outletSum30Row = mysqli_fetch_assoc($res30days);
		$outletSum30 = round(@$outletSum30Row['total_amount'],2) ?? 0;
		
		// 180 days sum
		$query180 = "
			SELECT SUM(total_amount) AS total_amount 
			FROM booking 
			WHERE outlet_id = '$outletId' 
			  AND booking_time BETWEEN '$previous180DateTime' AND '$currentDateTime' 
			GROUP BY outlet_id
		";
		$res180days = mysqli_query($con, $query180);
		$outletSum180Row = mysqli_fetch_assoc($res180days);
		$outletSum180 = @$outletSum180Row['total_amount']>0? round($outletSum180Row['total_amount']/6,2):0;
	



		   $rr=array();
		   $rr["id"]=$row["id"];
		   $rr["state"]=$row["state"];
		   $rr["city"]=$row["city"];
		   $rr["region"]=$row["region"];
		   $rr["routename"]=$row["area"];
		   $rr["distributor"]=$row["distributor"]; 
		   $rr["name"]=$row["name"];
		   $rr["lastvisit"]=$row["lastvisit"];


		   $rr["last_30_value"]=$outletSum30;
		   $rr["past_order_per_month"]=$outletSum180;

		   $rr["contactperson"]=$row["contactperson"];
		   $rr["contact"]=$row["contact"];
		   $rr["address"]=$row['address'];

	
		   
		   if($row["outlettype"]=="MTS")
		   {
			   $mt++;
		   }
		   if($row["outlettype"]=="G.T.")
		   {
			   $gt++;
		   }
		   if($row["outlettype"]=="Milk Booth")
		   {
			   $milkbooth++;
		   }
		   if($row["outlettype"]=="MTL")
		   {
			   $mtl++;
		   }
		   
		   $total++;
		   
		   $rr["mt"]=$mt;
	       $rr["gt"]=$gt;
	       $rr["mtl"]=$mtl;
	       $rr["milkbooth"]=$milkbooth;
	       $rr["total"]=$total;
		     
		   array_push($response,$rr);
	   } 
	   print_r($row);
	   //$data=array();
	   $data=json_encode($response);
	   echo $data;
	   return; 
   }



   
   if(isset($_GET['activityvisit']))
   {
	   $res=mysqli_query($con,"SELECT 
				o.id,
				o.name,
				o.address,
				o.lastvisitpic,
				o.contactperson,
				o.contact,
				o.gstnumber,
				o.outlettype,
				a.activitytype,
				a.activitydate,
				a.activitytime,
				a.feedback,
				a.battery,
				a.rating,
				area.area,
				area.id AS areaId,
				regions.name AS regionsname,
				cities.city,
				states.name AS statesname,
				e.name AS empname,
				e.empid,
				CASE 
					WHEN DATE(a.activitydate) = DATE(o.updated_at) THEN 'Yes'
					ELSE 'No'
				END AS is_updated_today,
				emp.name AS distributorname,
				SUM(IFNULL(b.total_amount, 0)) AS total_booking_amount
				 FROM outletactivity a 
				 JOIN outlets o ON a.outletid = o.id 
				 JOIN employees e ON e.id = a.userid 
				 JOIN area ON area.id = o.routeid 
				 JOIN employees emp ON emp.id = area.distributor_id
				 JOIN regions ON regions.id = area.region 
				 JOIN cities ON cities.id = regions.city_id 
				 JOIN states ON states.id = cities.state_id 
			LEFT JOIN booking b ON b.outlet_id = o.id AND DATE(b.booking_time) = a.activitydate
			 WHERE a.activitydate >= CURDATE() - INTERVAL 7 DAY
			GROUP BY o.id, a.activitydate, a.activitytime
			ORDER BY a.id DESC");   
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
		   $rr["empname"]=$row["empname"];
		   $rr["empid"]=$row["empid"];
		   $rr["distributorname"]=$row["distributorname"];
		   $rr["statesname"]=$row["statesname"];
		   $rr["city"]=$row["city"];
		   $rr["regionsname"]=$row["regionsname"];
		   $rr["area"]=$row["area"];
		   $rr['is_updated_today']=$row["is_updated_today"];
		   $rr['total_booking_amount']=$row["total_booking_amount"];
		   

		   $rr["areaId"]=$row["areaId"];

		   
		   array_push($response,$rr);
	   } 
	   //$data=array();
	   $data=json_encode($response);
	   echo $data;
	   return; 
   }


   if(isset($_GET['activityvisittoday']))
   {
	   $today=date('Y-m-d');
	   $res=mysqli_query($con,"select o.id,o.name,o.address,o.lastvisitpic,o.contactperson,o.contact,o.gstnumber,o.outlettype,a.activitytype,a.activitydate,a.activitytime,a.feedback,a.battery,a.rating,e.name as 'empname',e.empid from outletactivity a join outlets o on a.outletid=o.id join employees e  on e.id=a.userid where a.activitydate='$today' order by a.id desc");   
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
		   $rr["empname"]=$row["empname"];
		   $rr["empid"]=$row["empid"]; 
		   array_push($response,$rr);
	   } 
	   //$data=array();
	   $data=json_encode($response);
	   echo $data;
	   return; 
   }

       
   if(isset($_GET['getstate']))
   {
	   $res=mysqli_query($con,"select distinct a.state,st.name from area a join states st on a.state=st.id  order by st.name");
	   $states=array();
	   while($row=mysqli_fetch_array($res))
	   {
		   $rr=array();		    
          $rr["state"]=$row["state"];
          $rr["name"]=$row["name"];
		  array_push($states,$rr);		   		   
	   }
	   $data=json_encode($states);
	   echo $data;
   }
   
   if(isset($_GET['getregion']))
   {
	   $city=$_GET["city"];
	   
	   $res=mysqli_query($con,"select distinct id,name from regions where city_id='$city' order by name");
	   $regions=array();
	   while($row=mysqli_fetch_array($res))
	   {
	       $rr=array();	
	        $rr["region"]=$row["id"];
          $rr["name"]=$row["name"];
          array_push($regions,$rr);		   
	   }
	   
	   $data=json_encode($regions);
	   
	   echo $data;
   }
   
   if(isset($_GET['getcity']))
   {
	   $state=$_GET['state'];
	   $res=mysqli_query($con,"select distinct city , id from cities where state_id='$state' order by city ");
	   $cities=array();
	   while($row=mysqli_fetch_array($res))
	   {
		$rr["id"]=$row["id"];
		$rr["city"]=$row["city"];
          array_push($cities,$rr);		   
	   }
	   $data=json_encode($cities);
	   echo $data;
   }

// getdistributor
if(isset($_GET['getdistributor']))
{

	$res=mysqli_query($con,"select name ,id  from employees where usertype=3 order by  name ");
	$regions=array();
	while($row=mysqli_fetch_array($res))
	{
		$rr=array();	
		 $rr["id"]=$row["id"];
	   $rr["name"]=$row["name"];
	   array_push($regions,$rr);		   
	}
	
	$data=json_encode($regions);
	
	echo $data;
}




// getrouter
if(isset($_GET['getrouter']))
{
	$region=$_GET["region"];
	
	$res=mysqli_query($con,"select id,area from area where region='$region' order by area ");
	$regions=array();
	while($row=mysqli_fetch_array($res))
	{
		$rr=array();	
		$rr["id"]=$row["id"];
	   	$rr["area"]=$row["area"];
	   	array_push($regions,$rr);		   
	}
	
	$data=json_encode($regions);
	
	echo $data;
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
			//$empcode=genrateId($con);  
			$count++;
			if($count>1)
			{
				//echo $areaData[13]."  ".$areaData[14]."   ".$areaData[9]."  ".$areaData[8];
				$routeid=getRoute($areaData[9],$con);
				//echo "RouteId ".$routeid;
				$areaid=getArea($areaData[13],$areaData[14],$con);
				//echo $areaData[13]."  ".$areaData[14]." id ".$areaid;
				$distid=getDistId($areaData[8],$con);
				
				mysqli_query($con,"insert into outlets(name,address,lastvisitpic,contactperson,contact,pincode,gstnumber,outlettype,outletsubtype,distributorid,routeid,competitor_presense,street,locality,city,state,latitude,longitude,areaid,lastvisit,creationdate,createdby) values('$areaData[0]','$areaData[1]','$imgpath','$areaData[2]','$areaData[3]','$areaData[4]','$areaData[5]','$areaData[6]','$areaData[7]','$distid','$routeid','$areaData[8]','$areaData[1]','$areaData[10]','$areaData[11]','$areaData[12]','$areaData[15]','$areaData[16]','$areaid','$date','$datetime','$createdby')") or die(mysqli_error($con));
	  
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
   
  function getRoute($routename,$con)
  {
	 $routename=strtoupper($routename);
	 
	 $result=mysqli_query($con,"select id from route where routename='$routename'") or die(mysqli_error($con));
	 $row=mysqli_fetch_array($result);
	 return $row["id"];
  }
   
   function getArea($areaname,$region,$con)
  {
	 $res=mysqli_query($con,"select id from area where area='$areaname' and region='$region'");
	 $row=mysqli_fetch_array($res);
	 return $row["id"];
  }
  
  function getDistId($distid,$con)
  {
	 $res=mysqli_query($con,"select id from employees where empid='$distid'");
	 $row=mysqli_fetch_array($res);
	 return $row["id"];
  } 

 
?>