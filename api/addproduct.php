<?php
  require_once(__DIR__ . '/../connect.php');
  $time=date("H:i:s"); 
  $datetime = date("Y-m-d H:i:s");
  $date=date("Y-m-d");
  if(isset($_GET['insert']))
  {

/*	  var_dump($_POST);
	  $_POST=json_decode(file_get_contents("php://input"));*/

	extract($_POST);
	  $query = "select productid from skus where productid = '$pshort'";
	  //echo($pshort);
	  $res=mysqli_query($con,$query);
	  if( mysqli_num_rows($res)> 0 ){
			echo "get_value";
			return;
	  }
	$filename=$_FILES['pimage']['name'];
	$tmpname=$_FILES['pimage']['tmp_name'];
	$filesize=$_FILES['pimage']['size'];
	$filetype=$_FILES['pimage']['type'];
	$fileExt = strtolower(pathinfo($filename, PATHINFO_EXTENSION));

	if($filetype!="image/jpg" && $filetype!="image/png" && $filetype!="image/jpeg" && !in_array($fileExt, ['jpg', 'jpeg', 'png', 'webp']))
	{
	  echo"Please Upload Images(PNG,JPG & JPEG) Files Only...";
	  return;
	}
	if($filesize>800000)
	{
	  echo"Image can't be Greater than 800KB .";
	  return;
	}
	
	// Ensure target upload directory exists and is writable
	$uploadDir = dirname(__DIR__) . "/imgproduct/";
	if (!is_dir($uploadDir)) {
		@mkdir($uploadDir, 0777, true);
	}
	if (is_dir($uploadDir) && !is_writable($uploadDir)) {
		@chmod($uploadDir, 0777);
	}

	$cleanBaseName = preg_replace("/[^a-zA-Z0-9_-]/", "", $pname.$pshort);
	if (empty($cleanBaseName)) {
		$cleanBaseName = "prod_" . time();
	}
	$filename = $cleanBaseName . "_" . time() . "." . ($fileExt === 'jpeg' ? 'jpg' : $fileExt);
	$destination = $uploadDir . $filename;
	
	$prodective_cell = isset($prodective_cell) ? $prodective_cell : 0;
	
	mysqli_query($con,"insert into skus(productname,productid,catid,scatid, brandname,quantity,image,creationdate,createdby,prodective_cell) values('$pname','$pshort','$catid','$scatid','FRIC BERGEN','0','$filename','$datetime','0','$prodective_cell')") or die(mysqli_error($con));
	if(mysqli_affected_rows($con)>0)
	{  
		if (move_uploaded_file($tmpname, $destination)) {
			@chmod($destination, 0644);
			echo "success";
		} else {
			$lastErr = error_get_last();
			$details = "";
			if (!is_dir($uploadDir)) {
				$details = ": Directory $uploadDir does not exist";
			} else if (!is_writable($uploadDir)) {
				$details = ": Directory $uploadDir is not writable. Please execute: chmod 777 $uploadDir";
			} else if (!is_uploaded_file($tmpname)) {
				$details = ": Temporary uploaded file is missing or invalid";
			} else if ($lastErr && isset($lastErr['message'])) {
				$details = ": " . $lastErr['message'];
			}
			echo "Image upload error" . $details;
		}
	}
	
  }

  else if(isset($_GET['show']))
  {
	 $res = mysqli_query($con, "
SELECT 
    skus.id, 
    skus.productname, 
    skus.productid, 
    sku_unit.name AS unit,
    skus.rate, 
    skus.brandname, 
    parduct_sub_cat.cmrp AS mrp,
    product_cat.prodective_cell AS cat_order,
    product_cat.name AS catname, 
    parduct_sub_cat.name AS sub_catname,
    parduct_sub_cat.prodective_cell AS sub_cat_order,
    skus.image, 
    skus.prodective_cell
FROM skus
LEFT JOIN product_cat ON product_cat.id = skus.catid
LEFT JOIN parduct_sub_cat ON parduct_sub_cat.id = skus.scatid
LEFT JOIN sku_unit ON sku_unit.id = parduct_sub_cat.cunit
ORDER BY  cat_order ,sub_cat_order ,skus.prodective_cell ASC
") or die("SQL Error: " . mysqli_error($con));

	 $response=array();
	 while($row=mysqli_fetch_array($res))
	 {
		 $rr=array("id"=>$row["id"],
		 "productname"=>$row["productname"],
		 "productid"=>$row["productid"],
		 "unit"=>$row["unit"],
		 "mrp"=>$row["mrp"],
		 "rate"=>$row["rate"],
		 "catname"=>$row["catname"],
		 "sub_catname"=>$row["sub_catname"],
		 "brandname"=>$row["brandname"],
		 "image"=>$row["image"],
		 "prodective_cell"=>$row["prodective_cell"]);
		 $response[]=$rr;
     }
	 $data=json_encode($response);
	 echo $data;
  }

  else if(isset($_GET['deleteskus']))
  { $id=$_GET['id'];
  	if(!empty($id)){
      $result=mysqli_query($con," DELETE FROM `skus` WHERE id='$id'");
      if($result) {
		header('Location: ' . $_SERVER['HTTP_REFERER']);
		exit;
     }	
  	}
  }

  else if(isset($_GET['edit']))
  {
	  extract($_POST);
	  
	  $query = "select productid from skus where productid = '$pshort' && id='$pid'";
	  $res=mysqli_query($con,$query);
	  if( mysqli_num_rows($res)<=0 ){
			$query = "select productid from skus where productid = '$pshort'";
	     $res=mysqli_query($con,$query);
	     if( mysqli_num_rows($res)> 0 ){
	 		echo "get_value";
			return;
	    }
	  }
	  
	  $prodective_cell = isset($prodective_cell) ? $prodective_cell : 0;
	  
	  if(isset($_FILES['pimage']) && !empty($_FILES['pimage']['name']) && $_FILES['pimage']['error'] === UPLOAD_ERR_OK)
	  {
	     $origName = $_FILES['pimage']['name'];
	     $tmpname  = $_FILES['pimage']['tmp_name'];
	     $filesize = $_FILES['pimage']['size'];
	     $fileExt  = strtolower(pathinfo($origName, PATHINFO_EXTENSION));

	     $allowedExts = ['jpg', 'jpeg', 'png', 'webp'];
	     if(!in_array($fileExt, $allowedExts))
	     {
	       echo "Please Upload Images(PNG, JPG, JPEG & WEBP) Files Only...";
 	       return;
      	 }
	     if($filesize > 800000)
	     {
	       echo "Image can't be Greater than 800KB.";
	       return;
	     }

		  // Ensure target upload directory exists and is writable
		  $uploadDir = dirname(__DIR__) . "/imgproduct/";
		  if (!is_dir($uploadDir)) {
			  @mkdir($uploadDir, 0777, true);
		  }
		  if (is_dir($uploadDir) && !is_writable($uploadDir)) {
			  @chmod($uploadDir, 0777);
		  }

		  // Generate clean unique filename with extension and timestamp to bust browser cache
		  $cleanBaseName = preg_replace("/[^a-zA-Z0-9_-]/", "", $pname . $pshort);
		  if (empty($cleanBaseName)) {
			  $cleanBaseName = "prod_" . (!empty($pid) ? $pid : time());
		  }
		  $filename = $cleanBaseName . "_" . time() . "." . ($fileExt === 'jpeg' ? 'jpg' : $fileExt);
		  $destination = $uploadDir . $filename;

		  // Move uploaded file first
		  if (move_uploaded_file($tmpname, $destination)) {
			  @chmod($destination, 0644);

			  // Fetch old image to delete old file from disk
			  $oldImgRes = mysqli_query($con, "SELECT image FROM skus WHERE id='$pid'");
			  if($oldRow = mysqli_fetch_assoc($oldImgRes)) {
				  $oldFile = $uploadDir . $oldRow['image'];
				  if(!empty($oldRow['image']) && file_exists($oldFile) && $oldFile != $destination) {
					  @unlink($oldFile);
				  }
			  }

			  // Update database with new image filename
			  $query = "UPDATE skus SET productname=?, productid=?, catid=?, scatid=?, image=?, prodective_cell=? WHERE id=?";
			  $stmt = mysqli_prepare($con, $query);
			  mysqli_stmt_bind_param($stmt, "ssissii", $pname, $pshort, $catid, $scatid, $filename, $prodective_cell, $pid);
			  mysqli_stmt_execute($stmt);
			  mysqli_stmt_close($stmt);

			  echo "success";
		  } else {
			  $lastErr = error_get_last();
			  $details = "";
			  if (!is_dir($uploadDir)) {
				  $details = ": Directory $uploadDir does not exist";
			  } else if (!is_writable($uploadDir)) {
				  $details = ": Directory $uploadDir is not writable. Please execute: chmod 777 $uploadDir";
			  } else if (!is_uploaded_file($tmpname)) {
				  $details = ": Temporary uploaded file is missing or invalid";
			  } else if ($lastErr && isset($lastErr['message'])) {
				  $details = ": " . $lastErr['message'];
			  }
			  echo "Image upload error" . $details;
		  }
	   }
	   else
	   {
	      $stmt = mysqli_prepare($con, "UPDATE skus SET productname=?, productid=?, catid=?, scatid=?, prodective_cell=? WHERE id=?");
	      mysqli_stmt_bind_param($stmt, "ssisii", $pname, $pshort, $catid, $scatid, $prodective_cell, $pid);
	      mysqli_stmt_execute($stmt);
	      mysqli_stmt_close($stmt);

	      echo "success";
	   }
  }

?>
