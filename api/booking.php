<?php 
include("../connect.php");

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);


if(isset($_GET['productcategory']))
{  
    $query = "Select id AS CategoryID, name As CategoryName from product_cat order by prodective_cell ";
    $result = mysqli_query($con, $query);

    if(mysqli_num_rows($result) > 0){
        while($row=mysqli_fetch_assoc($result)) {
            $datas[] = $row;

        }
    }

    $new_array = array();

    foreach ($datas as $data) {
        $new_array["data"] ["Category Details"][]  = array(
            "category_id" => $data["CategoryID"],
            "category_name" => $data["CategoryName"],
        );
        
    }

    echo json_encode($new_array, JSON_PRETTY_PRINT); 
}

if(isset($_GET['productdetails']))
{
    
    $query = "SELECT product_cat.id AS ProductCategoryID, product_cat.name AS ProductCategoryName , parduct_sub_cat.name AS ProductSubCategoryName, parduct_sub_cat.id AS ProductSubCategoryID, parduct_sub_cat.pmrp, parduct_sub_cat.punit, parduct_sub_cat.prate, parduct_sub_cat.cunit, parduct_sub_cat.cmrp, parduct_sub_cat.unit_no, parduct_sub_cat.discount, skus.id AS ProductID, skus.productname AS ProductName , skus.productid AS ProductShortName, skus.image AS ProductImage
    FROM skus
    INNER JOIN product_cat ON skus.catid = product_cat.id
    INNER JOIN parduct_sub_cat ON skus.scatid = parduct_sub_cat.id " ;
    $result = mysqli_query($con, $query);

    if(mysqli_num_rows($result) > 0){
        while($row=mysqli_fetch_assoc($result)) {
            $datas[] = $row;
            
        }
    }

    $new_array = array();

    foreach ($datas as $data) {
        $new_array["data"] [$data['ProductCategoryName']][][$data['ProductSubCategoryName']] = array(
            "product-details" => array(
                "category_id" => $data["ProductCategoryID"],
                "category_name" => $data["ProductCategoryName"],
                "subCategory_id" => $data["ProductSubCategoryID"],
                "subCategory_name" => $data["ProductSubCategoryName"],
                "product_id" => $data["ProductID"],
                "product_name" => $data["ProductName"],
                "product_short Name" => $data["ProductShortName"],
                "product_image" => $data["ProductImage"]
            )
        );
        
    }

    echo json_encode($new_array, JSON_PRETTY_PRINT); 

}

if(isset($_GET['productbyid']))
{
    extract($_GET);

    $query = "SELECT product_cat.id AS ProductCategoryID, product_cat.name AS ProductCategoryName , parduct_sub_cat.name AS ProductSubCategoryName, parduct_sub_cat.id AS ProductSubCategoryID, parduct_sub_cat.pmrp, parduct_sub_cat.punit, parduct_sub_cat.prate, parduct_sub_cat.cunit, parduct_sub_cat.cmrp, parduct_sub_cat.unit_no, parduct_sub_cat.discount, skus.id AS ProductID,skus.prodective_cell, skus.productname AS ProductName , skus.productid AS ProductShortName, skus.image AS ProductImage
    FROM skus
    INNER JOIN product_cat ON skus.catid = product_cat.id
    INNER JOIN parduct_sub_cat ON skus.scatid = parduct_sub_cat.id 
    WHERE parduct_sub_cat.id = '$productbyid' order by skus.prodective_cell" ;
    $result = mysqli_query($con, $query);
    $new_array = array();

    if(mysqli_num_rows($result) > 0){
    while($row=mysqli_fetch_assoc($result)) {
        $new_array["data"]["product-details"][] = array(
            "category_id" => $row["ProductCategoryID"],
            "category_name" => $row["ProductCategoryName"],
            "subCategory_id" => $row["ProductSubCategoryID"],
            "subCategory_name" => $row["ProductSubCategoryName"],
            "product_id" => $row["ProductID"],
            "product_name" => $row["ProductName"],
            "product_short_name" => $row["ProductShortName"],
            "product_image" => $row["ProductImage"]
        );
    }
    }else{
        http_response_code(400);
    }


    echo json_encode($new_array, JSON_PRETTY_PRINT); 

}

if(isset($_GET['subcategorydetails'])){

    $query = "SELECT product_cat.id AS ProductCategoryID , parduct_sub_cat.id AS ProductSubCategoryID, parduct_sub_cat.name, parduct_sub_cat.pmrp, parduct_sub_cat.punit, parduct_sub_cat.prate, parduct_sub_cat.cunit, parduct_sub_cat.cmrp, parduct_sub_cat.unit_no, parduct_sub_cat.discount
    FROM parduct_sub_cat
    JOIN product_cat ON parduct_sub_cat.cat_id = product_cat.id " ;
    $result = mysqli_query($con, $query);


    if(mysqli_num_rows($result) > 0){
        while($row=mysqli_fetch_assoc($result)) {
            $datas[] = $row;
            
        }
    }

    $new_array = array();

    foreach ($datas as $data) {
        $new_array["data"] [$data['ProductCategoryID']][][$data['ProductSubCategoryID']] = array(

            "category-details" => array(
                "subCategory_name" => $data["name"],
                "pmrp" => $data["pmrp"],
                "punit" => $data["punit"],
                "prate" => $data["prate"],
                "cunit" => $data["cunit"],
                "cmrp" => $data["cmrp"],    
                "unit_no" => $data["unit_no"],
                "discount" => $data["discount"]
            )

        );
        
    }

    echo json_encode($new_array, JSON_PRETTY_PRINT); 


}

if(isset($_GET['subcategorybyid'])){

    extract($_GET);

    $query = "SELECT product_cat.id AS ProductCategoryID,parduct_sub_cat.prodective_cell, product_cat.name AS ProductCategoryName , parduct_sub_cat.id AS ProductSubCategoryID, parduct_sub_cat.name, parduct_sub_cat.pmrp, parduct_sub_cat.punit, parduct_sub_cat.prate, parduct_sub_cat.cunit, parduct_sub_cat.cmrp, parduct_sub_cat.unit_no, parduct_sub_cat.discount
    FROM parduct_sub_cat
    JOIN product_cat ON parduct_sub_cat.cat_id = product_cat.id 
    WHERE product_cat.id = '$subcategorybyid' order by parduct_sub_cat.prodective_cell" ;

    $result = mysqli_query($con, $query);
    $new_array = array();

    if(mysqli_num_rows($result) > 0){
        while($row=mysqli_fetch_assoc($result)) {
            $new_array["data"]["category-details"][]=array(
                    "category_id" => $row["ProductCategoryID"],
                    "category_name" => $row["ProductCategoryName"],
                    "subCategory_id" => $row["ProductSubCategoryID"],
                    "subCategory_name" => $row["name"],
                    "pmrp" => $row["pmrp"],
                    "punit" => $row["punit"],
                    "prate" => $row["prate"],
                    "cunit" => $row["cunit"],
                    "cmrp" => $row["cmrp"],    
                    "unit_no" => $row["unit_no"],
                    "discount" => $row["discount"]
                
            );
        }
    }

    echo json_encode($new_array, JSON_PRETTY_PRINT); 


}


if(isset($_GET['orderproduct'])){
   //$data=$_POST;
    extract($_POST);
    // $str=$_POST["name"];
//     print_r($data);
//      // $data=json_decode($data,true);
 // print_r($data);
//$data = json_decode(file_get_contents('php://input'), true);

 //print_r($data);
 
 
// $data=json_encode($data,true);
//echo $data;
//echo json_encode(['msg' => 'Success!','data'=>$data]);
   //die('testing');



    $msg = 0;

    if(@$outlet_id == " "){
        echo json_encode(['msg' => 'Please Enter Outlet Id']);
        $msg = 1 ;
    }elseif(@$user_id == " "){
        echo json_encode(['msg' => 'Please Enter User Id']);
        $msg = 1 ;
    // }elseif(@$offer_qty == " "){
    //     echo json_encode(['msg' => 'Please Enter Offer Qty']);
    //     $msg = 1 ;
    }elseif(@$total_amount == " "){
        echo json_encode(['msg' => 'Please Enter Total Amout']);
        $msg = 1 ;
    }elseif(@$total_qty == " "){
        echo json_encode(['msg' => 'Please Enter Total Qty']);
        $msg = 1 ;
    }

    if($msg != 1){
$offer_qty=0;
        $result = mysqli_query($con, "insert into booking(outlet_id, user_id, offer_qty, total_amount, total_qty) values('$outlet_id', '$userid', '$offer_qty', '$total_amount', '$total_qty') ");

        $last_id = mysqli_insert_id($con);
        
        if ($last_id !="") {
            
        $category_id=explode(",",$category_id);
        $subcategory_id=explode(",",$subcategory_id);
        $product_id=explode(",",$product_id);
        $product_name=explode(",",$product_name);
        $qty_no=explode(",",$qty_no);
        $new_price=explode(",",$new_price);
        $product_wise_total_price=explode(",",$product_wise_total_price);
       
            
            // print_r($nameValuePairs);
            // die;

            for($i=0;$i<count($product_id);$i++){
                
                //extract($value);
                $result2 = mysqli_query($con, "insert into booking_item(booking_id_fk, category_id, subcategory_id, product_id, product_name, qty_no, new_price,total_price) values('$last_id', '$category_id[$i]', '$subcategory_id[$i]','$product_id[$i]','$product_name[$i]','$qty_no[$i]','$new_price[$i]','$product_wise_total_price[$i]')" );   
            }
            
            echo json_encode(['msg' => 'Success']);
        }
        else{
            echo json_encode(['msg' => 'Something Went Wrong!']);
        }
    }else{
        echo json_encode(['msg' => 'Please Send all Required Field']);
    }
  

}


if(isset($_GET['search'])){
    
    $employee=trim($_GET['employee']);
    $reservation=trim($_GET['reservation']);
    $outlet=trim($_GET['outlet']);
    $distibuter=trim($_GET['distibuter']);
    $dates=explode("-",$reservation);
    $start=strtotime(trim($dates[0]));
    $end=strtotime(trim($dates[1]));
    $start=date("Y-m-d",$start);
    $end=date("Y-m-d",$end);

    $sqlqry='where bk.id > 0 ';
    if(!empty($employee)){
         $sqlqry.="and bk.user_id = '$employee'";  
    }

    if(!empty($end) and !is_null($start)){
        $end=$end." 23:59:00";
        $start=$start." 00:00:00";
        $sqlqry.=" and bk.booking_time >= '$start' and bk.booking_time <= '$end' " ;
     }

 
    if(!empty($outlet)){
        $sqlqry.=" and bk.outlet_id = '$outlet'";  
     } 
    if(!empty($distibuter)){
        $sqlqry.=" and outlet.distributorid = '$distibuter'";  
     }
    $salesmans=array();
    $outlets=array();
    $res=mysqli_query($con,"select * from employees where usertype='1'");
    while($row=mysqli_fetch_array($res)){
        $salesmans[$row['id']]= $row['name'];  
    }
 
        $query = "SELECT 
            outlets.id AS outlet_id,
            employees.name AS distributor_name,
            outlets.name AS name,
            outlets.address AS address,
            outlets.contact AS contact,
            area.area AS route_name
        FROM outlets
        JOIN area ON outlets.areaid = area.id
        JOIN employees ON area.distributor_id = employees.id";

        $res1 = mysqli_query($con, $query);

        $outlets = [];

        while ($row1 = mysqli_fetch_array($res1)) {
            $id = $row1['outlet_id'];
            $outlets[$id] = $row1['name'];
            $outlets[$id . "_address"] = $row1['address'];
            $outlets[$id . "_contact"] = $row1['contact'];
            $outlets[$id . "_distributor_name"] = $row1['distributor_name'];
            $outlets[$id . "_route_name"] = $row1['route_name'];
        }
 
    $res5=mysqli_query($con,"select id,name from employees where usertype='3'");
    while($row5=mysqli_fetch_array($res5)){
        $distibuters[$row5['id']]= $row5['name'];    
    }

   $booking=mysqli_query($con,"SELECT bk.id,bk.outlet_id,bk.user_id,bk.offer_qty,bk.offer_qty,bk.total_amount,bk.total_qty,bk.booking_time FROM booking bk join outlets outlet on bk.outlet_id=outlet.id ".$sqlqry);

        $total=0; 
        $bookinglist=array();
        
        while($row2=mysqli_fetch_array($booking)){ 
            $tt=array();
            $b_id=$row2['id'];
            $booking_items=mysqli_query($con,"select * from booking_item  where booking_id_fk='$b_id'");
            $items=array();
            $row3=0;
            while( $row3=mysqli_fetch_array($booking_items)){
                $items['qty_no'][$row3['product_id']]=$row3['qty_no'];
                $items['new_price'][$row3['product_id']]=$row3['new_price'];
                $items['total_price'][$row3['product_id']]=$row3['total_price'];
            }
            if (!empty($row2) ) {
                $tt['booking_time'] = @$row2['booking_time'];
                $tt['outlet_id']= @$outlets[$row2['outlet_id']];

                $tt['outlet_id']= @$outlets[$row2['outlet_id']];
                $tt['outlet_address']= @$outlets[$row2['outlet_id'].'_address'];
                $tt['outlet_contact']= @$outlets[$row2['outlet_id'].'_contact'];
                $tt['distibuter']= @$outlets[$row2['outlet_id'].'_distributor_name'];
                $tt['outlet_route_name']= @$outlets[$row2['outlet_id'].'_route_name'];
           
            
                $tt['user_id']=@$salesmans[$row2['user_id']];
            }else {
                $tt['booking_time'] = '';
                $tt['outlet_id']= '';
                $tt['user_id']='';
                $tt['distibuter']='';
                $tt['outlet_address']='';
                $tt['outlet_contact']= '';
                $tt['outlet_route_name']='';
           
            
            }

            $cat=mysqli_query($con,"select id,name from product_cat"); 
        
            while($catrow=mysqli_fetch_array($cat)){
                $subcat=mysqli_query($con,"select id,name from parduct_sub_cat where cat_id=".$catrow['id']."  "); 
                while($subcatrow=mysqli_fetch_array($subcat)){   
                    $catTotal=0.00;
                    $tempQty=0;
                    $res=mysqli_query($con,"select id,productid from skus where scatid=".$subcatrow['id']." "); 
                    while($row=mysqli_fetch_array($res)){

                            $tempQty+=$tt[@$subcatrow['id'].$row['id']]=@$items['qty_no'][@$row['id']];
                            $catTotal=@$items['total_price'][@$row['id']]+$catTotal;
                                
                        if(!empty($items['new_price'][$row['id']])){
                                $tt['price'.$subcatrow['id']]=@$items['new_price'][@$row['id']];
                            }   

                    }
                    if(empty($tt['price'.$subcatrow['id']])){
                        $tt['price'.$subcatrow['id']]=0;
                    }
                    $tt['subcattotal'.$subcatrow['id']] =@$tt['price'.$subcatrow['id']]*$tempQty;
                   // $tt['cattotal'.$subcatrow['id']]=$catTotal;
                 }
            } 
            $tt['total']=$row2['total_amount'];
            $total+=$row2['total_amount'];
            array_push($bookinglist,$tt);
        }
        
        $data=json_encode($bookinglist);
	   echo $data;
	   return; 
}


if(isset($_GET['routevistsummary']) or isset($_GET['sendreport']) ){ 
    
    $employee=trim($_GET['salesman']);
    $area_id=trim($_GET['route_id']);


    $sqlqry='where bk.id > 0 ';
    if(!empty($employee)){
         $sqlqry.="and bk.user_id = '$employee'";  
    }

 
    $date = new DateTime();
    $start =$date->format("Y-m-d 00:00:00");
    $sqlqry.=" and bk.booking_time >= '$start' " ;
    $sqlqry.=" and outlet.routeid = '$area_id' " ;




    $res1=mysqli_query($con,"select  COUNT(id) AS total_count  from outlets where routeid = $area_id ");
    $row1=mysqli_fetch_array($res1);
     $totalOutlet= @$row1[0];  


    $res2=mysqli_query($con,"select  COUNT(id) AS total_count from outlets where routeid = $area_id and lastvisit >= '$start' ");
    $row2=mysqli_fetch_array($res2);
    $totalVistedOutlet = @$row2[0];  


    $res3=mysqli_query($con,"select  COUNT(id) AS total_count from outlets where routeid = $area_id and creationdate >= '$start'");
    $row3=mysqli_fetch_array($res3);
    $totalNewOutlet= @$row3[0]??0;  


    $bookingSum=mysqli_query($con,"SELECT SUM(bk.total_amount) totalSum FROM booking bk join outlets outlet on bk.outlet_id=outlet.id ".$sqlqry);
    $bookingSumArr=mysqli_fetch_array($bookingSum);
    $totalSumAmount= @$bookingSumArr[0]??0;  



    $totalProdectiveOutletQry=mysqli_query($con,"SELECT outlet.id,SUM(bk.total_amount) totalSum FROM booking bk join outlets outlet on bk.outlet_id=outlet.id ".$sqlqry." Group BY outlet.id HAVING totalSum > 0 ");
    $totalProdectiveOutlet=mysqli_num_rows($totalProdectiveOutletQry);
    

   $booking=mysqli_query($con,"SELECT bki.subcategory_id ,SUM(bki.qty_no) AS total_qty  FROM booking_item bki join booking bk on bki.booking_id_fk=bk.id  join outlets outlet on bk.outlet_id=outlet.id ".$sqlqry." GROUP BY bki.subcategory_id ");
     
   	$bookinglist=$unitName= $subCategory=array();
        $query=mysqli_query($con,"SELECT psc.id,psc.name as sub_cat_name ,su.name as unit_name FROM parduct_sub_cat psc  join sku_unit su on  psc.punit = su.id");
   	 	 while($row=mysqli_fetch_array($query))
   	     {
            $subCategory[$row["id"]]=$row["sub_cat_name"];
            $unitName[$row["id"]]=$row["unit_name"];
           
        }	

 

        while($row2=mysqli_fetch_array($booking)){ 
            $tt=array();
      
            $tt['subcategory'] = @$subCategory[$row2['subcategory_id']]??0;
            $tt['unit']= @$unitName[$row2['subcategory_id']]??0;
            $tt['total_qty']=@$row2['total_qty']??0;
            $tt['subcategory_id']=$row2['subcategory_id']??0;
            array_push($bookinglist,$tt);
        }
           
        $prodectiveCell = ($totalProdectiveOutlet > 0 && $totalOutlet > 0) ? (($totalProdectiveOutlet / $totalOutlet) * 100) : 0;

        $data=json_encode([
            'bookinglist'=>$bookinglist,
            'total_outlet'=>round($totalOutlet-$totalNewOutlet,2),
            'total_visted_outlet'=>$totalVistedOutlet,
            'total_new_outlet'=>$totalNewOutlet,
            'total_sum_amount'=>$totalSumAmount,
            'total_prodective_outlet'=>$totalProdectiveOutlet,
            'prodective_cell'=>round($prodectiveCell,2)."%",
            'no_outlet_after_new_'=>($totalOutlet),
            'outlet_not_visted'=>round($totalOutlet-$totalVistedOutlet)

          ]);

      if(isset($_GET['sendreport']) ){ 

          $resRouteQuery = mysqli_query($con, "SELECT area as name FROM area WHERE id = $area_id");
            $route = '';
            if ($resRouteQuery && mysqli_num_rows($resRouteQuery) > 0) {
                $resRoute = mysqli_fetch_array($resRouteQuery);
                $route = $resRoute['name'];
            } 


                $subject = "Route Vist Summary Detail( $employee - $route)";
            	  $to_email = "sales@fricbergen.com";
	              $from_email = "sales@fricbergen.com";

                    $headers  = "MIME-Version: 1.0\r\n";
                    $headers .= "Content-type: text/html; charset=utf-8\r\n";
                    $headers .= "To: <$to_email>\r\n";
                    $headers .= "From: <$from_email>\r\n";
                    // Start HTML message
                    $message = "<html><body>";

                    // Booking List Table
                    $message .= "<h3>Booking Summary</h3>";
                    $message .= "<table border='1' cellpadding='8' cellspacing='0' style='border-collapse: collapse;'>
                    <thead>
                        <tr>
                            <th>Subcategory</th>
                            <th>Total Quantity</th>
                            <th>Unit</th>
                        </tr>
                    </thead>
                    <tbody>";

                    foreach ($bookinglist as $item) {
                        $message .= "<tr>
                            <td>" . htmlspecialchars($item['subcategory']) . "</td>
                            <td>" . htmlspecialchars($item['total_qty']) . "</td>      
                            <td>" . htmlspecialchars($item['unit']) . "</td>
                        </tr>";
                    }

                    $message .= "</tbody></table><br>";

                    // Summary Table
                    $message .= "<h3>Outlet Summary</h3>";
                    $message .= "<table border='1' cellpadding='8' cellspacing='0' style='border-collapse: collapse;'>";
                    $message .= "<tr><th>Total Outlet</th><td>" . ($totalOutlet - $totalNewOutlet) . "</td></tr>";
                    $message .= "<tr><th>New Outlet</th><td>" . $totalNewOutlet . "</td></tr>";
                    $message .= "<tr><th>Outlets After New</th><td>" . $totalOutlet . "</td></tr>";
                    $message .= "<tr><th>Visited Outlet</th><td>" . $totalVistedOutlet . "</td></tr>";
                    $message .= "<tr><th>Productive Outlet</th><td>" . $totalProdectiveOutlet . "</td></tr>";
                    $message .= "<tr><th>Not Visited Outlet</th><td>" . ($totalOutlet - $totalVistedOutlet) . "</td></tr>";
                    $message .= "<tr><th>Productive Cell</th><td>" . $prodectiveCell . "</td></tr>";
                    $message .= "<tr><th>Total Amount</th><td>" . $totalSumAmount . "</td></tr>";
                    $message .= "</table>";

                    // End HTML message
                    $message .= "</body></html>";
                    // echo $message ; 
                    //  return;

                    // Send the email
              if (mail($to_email, $subject, $message, $headers)) {
                    echo "✅ Email sent (PHP mail() returned true)";
                } else {
                    echo "❌ Email failed (PHP mail() returned false)";
                }

                     return;
       }
	   echo $data;
	   return; 
}



if(isset($_GET['outletwisesummary'])){
    
    $employee=trim($_GET['salesman']);
    $area_id=trim($_GET['route_id']);


    $sqlqry='where bk.id > 0 ';
    if(!empty($employee)){
         $sqlqry.="and bk.user_id = '$employee'";  
    }

 
    $date = new DateTime();
    $start =$date->format("Y-m-d 00:00:00");
    $sqlqry.=" and bk.booking_time >= '$start' " ;
    $sqlqry.=" and outlet.routeid = '$area_id' " ;



    $bookingSum=mysqli_query($con,"SELECT SUM(bk.total_amount) totalSum FROM booking bk join outlets outlet on bk.outlet_id=outlet.id ".$sqlqry);
    $bookingSumArr=mysqli_fetch_array($bookingSum);
    $totalSumAmount= @$bookingSumArr[0]??0;  



    $totalProdectiveOutletQry=mysqli_query($con,"SELECT outlet.id,outlet.name,outlet.address,SUM(bk.total_amount) total_sum FROM booking bk join outlets outlet on bk.outlet_id=outlet.id ".$sqlqry." Group BY outlet.id HAVING total_sum > 0 ");
    $bookinglist=[];
        while($row2=mysqli_fetch_array($totalProdectiveOutletQry)){ 
            $tt=array();
      
            $tt['name'] = $row2['name'];
            $tt['address']= $row2['address'];
            $tt['total_sum']=$row2['total_sum']??0;
            array_push($bookinglist,$tt);
        }
        
        $data=json_encode([

            'total_sum_amount'=>$totalSumAmount,
            'bookinglist'=>$bookinglist

          ]);

	   echo $data;
	   return; 
}




if(isset($_GET['notvistdasdasasd'])){
    
    $employee=trim($_GET['employee']);
    $reservation=trim($_GET['reservation']);
    $outlet=trim($_GET['outlet']);
    $distibuter=trim($_GET['distibuter']);
    $dates=explode("-",$reservation);
    $start=strtotime(trim($dates[0]));
    $end=strtotime(trim($dates[1]));
    $start=date("Y-m-d h:i:s ",$start);
    $end=date("Y-m-d h:i:s",$end);
	   
    $sqlqry='where bk.id > 0 ';

    if(!empty($end) and !is_null($start)){
    $sqlqry.=" and bk.booking_time >= '$start' and bk.booking_time <= '$end' " ;  
    }
    
    if(!empty($outlet)){
    $sqlqry.=" and bk.outlet_id = '$outlet'";  
    } 
    if(!empty($distibuter)){
    $sqlqry.=" and outlet.distributorid = '$distibuter'";  
    }

    $salesmans=array();
    $outlets=array();
    $res=mysqli_query($con,"select * from employees where usertype='1'");

    while($row=mysqli_fetch_array($res)){
        $salesmans[$row['id']]= $row['name'];   
    }
 
    $res1=mysqli_query($con,"select * from outlets");
    while($row1=mysqli_fetch_array($res1)){
        $outlets[$row1['id']]= $row1['name'];   
    }
 
    $res5=mysqli_query($con,"select id,name from employees where usertype='3'");
    while($row5=mysqli_fetch_array($res5)){
        $distibuters[$row5['id']]= $row5['name'];  
    }

   $booking=mysqli_query($con,"SELECT * FROM outlets where id not in (SELECT DISTINCT outlet.id FROM booking bk join outlets outlet on bk.outlet_id=outlet.id ".$sqlqry.")");
    $total=0; 
    $bookinglist=array();
    
    while($row2=mysqli_fetch_array($booking)){ 
        $tt=array();
        $tt['name'] = $row2['name'];
        $tt['address'] = $row2['address'];
        $tt['contactperson'] = $row2['contactperson'];
        $tt['contact'] = $row2['contact'];
        $tt['lastvisit'] = $row2['lastvisit'];
        $tt['distributorid']=$distibuters[$row2['distributorid']];             
        array_push($bookinglist,$row2);
    } 
     $data=json_encode($bookinglist);
    echo $data;
    return; 
}


// if(isset($_GET['notvist']))
//     {

//         $reservation=trim($_GET['reservation']);
//         $dates=explode("-",$reservation);
//         $start=strtotime(trim($dates[0]));
//         $end=strtotime(trim($dates[1]));
//         // $start=date("Y-m-d h:i:s ",$start);
//         // $end=date("Y-m-d h:i:s",$end);

//         // Parse and normalize start date to midnight
//         $start = strtotime(trim($dates[0])); 
//         $start = date("Y-m-d 00:00:00", $start);

//         // Parse and normalize end date to end of day
//         $end = strtotime(trim($dates[1]));
//         $end = date("Y-m-d 23:59:59", $end);


           
//         $sqlqry='where bk.id > 0 ';
    
//         if(!empty($end) and !is_null($start)){
//         $sqlqry.=" and bk.booking_time >= '$start' and bk.booking_time <= '$end' " ;  
//         }


//         $state=trim($_GET['state']);
//         $city=trim($_GET['region']); 
//         $routeid=trim($_GET['area']);
//         $so="";
//         $distributor=trim($_GET['distributor']);
//         $stockist="";
//         $selectQry="select o.id,
//                       cities.city As city,
//                    o.locality,
//                    o.distributorid,
//                    o.name,
//                    o.address,
//                    o.lastvisitpic,
//                    o.contactperson,
//                    o.contact,
//                    o.pincode,
//                    o.gstnumber,
//                    o.outlettype,
//                    o.outletsubtype,
//                     o.routeid,
//                    o.latitude,
//                    o.longitude,
//                    o.areaid,
//                    o.lastvisit,
//                    o.creationdate,
//                    a.area,
//                    o.createdby,
//                    concat(d.name,' - ',d.empid) as 'distributor',
//                      a.area, regions.name As region,
//                      states.name As state  from outlets o 
//                      join area a on a.id=o.routeid 
//                      JOIN employees d ON d.id = a.distributor_id
//                      left join states on states.id= a.state 
//                      left join cities on cities.id= a.city left 
//                      join regions on regions.id= a.region 
//                     WHERE o.id NOT IN (
//                         SELECT DISTINCT bk.outlet_id
//                         FROM booking bk
//                         WHERE bk.booking_time >= '$start' 
//                         AND bk.booking_time <= '$end'
//                     )";
           
//           $isSnd=1;
 
 
 
 
//           if ($distributor!="") {
//              $prefix="where";
//              $selectQry=$selectQry.$prefix." d.id ='$distributor'";
//              $isSnd=1;
//           }else {
 
//              if ($state!="") {
//                  $prefix=$isSnd==0?" where ":" and ";
//                  $selectQry=$selectQry.$prefix." a.state='$state'";
//                  $isSnd=1;
//               }
     
//               if ($city!="") {
//                  $prefix=$isSnd==0?" where ":" and ";
//                  $selectQry=$selectQry.$prefix." a.region ='$city'";
//                  $isSnd=1;
//               }
     
     
//               if ($routeid!="") {
//                  $prefix=$isSnd==0?" where ":" and ";
//                  $selectQry=$selectQry.$prefix." o.routeid = '$routeid'";
//                  $isSnd=1;
//               }
//           }
 
 
//           $query=$selectQry."order by o.id desc";
              
   
//         $res=mysqli_query($con,$query) or die(mysqli_error($con));   
//         $response=array();
//         $num=mysqli_field_count($con);
//         $total=0;$gt=0;$mt=0;$mtl=0;$milkbooth=0;
//         while($row=mysqli_fetch_array($res))
//         {
//          $currentDateTime = date('Y-m-d H:i:s');
//          $previous30DateTime = date('Y-m-d H:i:s', strtotime('-30 days'));
//          $previous180DateTime = date('Y-m-d H:i:s', strtotime('-180 days'));
         
//          $outletId = $row["id"];
         
//          // 30 days sum
//          $query30 = "
//              SELECT SUM(total_amount) AS total_amount 
//              FROM booking 
//              WHERE outlet_id = '$outletId' 
//                AND booking_time BETWEEN '$previous30DateTime' AND '$currentDateTime' 
//              GROUP BY outlet_id
//          ";
//          $res30days = mysqli_query($con, $query30);
//          $outletSum30Row = mysqli_fetch_assoc($res30days);
//          $outletSum30 = round(@$outletSum30Row['total_amount'],2) ?? 0;
         
//          // 180 days sum
//          $query180 = "
//              SELECT SUM(total_amount) AS total_amount 
//              FROM booking 
//              WHERE outlet_id = '$outletId' 
//                AND booking_time BETWEEN '$previous180DateTime' AND '$currentDateTime' 
//              GROUP BY outlet_id
//          ";
//          $res180days = mysqli_query($con, $query180);
//          $outletSum180Row = mysqli_fetch_assoc($res180days);
//          $outletSum180 = @$outletSum180Row['total_amount']>0? round($outletSum180Row['total_amount']/6,2):0;
     
 
 
 
//             $rr=array();
//             $rr["id"]=$row["id"];
//             $rr["state"]=$row["state"];
//             $rr["city"]=$row["city"];
//             $rr["region"]=$row["region"];
//             $rr["routename"]=$row["area"];
//             $rr["distributor"]=$row["distributor"]; 
//             $rr["name"]=$row["name"];
//             $rr["lastvisit"]=$row["lastvisit"];
 
 
//             $rr["last_30_value"]=$outletSum30;
//             $rr["past_order_per_month"]=$outletSum180;
 
//             $rr["contactperson"]=$row["contactperson"];
//             $rr["contact"]=$row["contact"];
//             $rr["address"]=$row['address'];
 
     
            
//             if($row["outlettype"]=="MTS")
//             {
//                 $mt++;
//             }
//             if($row["outlettype"]=="G.T.")
//             {
//                 $gt++;
//             }
//             if($row["outlettype"]=="Milk Booth")
//             {
//                 $milkbooth++;
//             }
//             if($row["outlettype"]=="MTL")
//             {
//                 $mtl++;
//             }
            
//             $total++;
            
//             $rr["mt"]=$mt;
//             $rr["gt"]=$gt;
//             $rr["mtl"]=$mtl;
//             $rr["milkbooth"]=$milkbooth;
//             $rr["total"]=$total;
              
//             array_push($response,$rr);
//         } 
//         print_r($row);
//         //$data=array();
//         $data=json_encode($response);
//         echo $data;
//         return; 
//     }

if (isset($_GET['notvist'])) {

$reservation = isset($_GET['reservation']) ? trim($_GET['reservation']) : '';
$start = $end = null;

if ($reservation !== '') {
    // Split by hyphen with optional spaces
    $parts = preg_split('/\s*-\s*/', $reservation);
    if (count($parts) >= 2) {
        $startInput = trim($parts[0]);
        $endInput   = trim($parts[1]);

        // Try multiple formats: m/d/Y, d/m/Y, Y-m-d
        $tryParse = function ($s) {
            $fmts = ['m/d/Y', 'd/m/Y', 'Y-m-d'];
            foreach ($fmts as $fmt) {
                $dt = DateTime::createFromFormat($fmt, $s);
                if ($dt && $dt->format($fmt) === $s) return $dt;
            }
            // Fallback to strtotime (handles e.g. "2025-08-13")
            $ts = strtotime($s);
            return $ts !== false ? (new DateTime())->setTimestamp($ts) : null;
        };

        $startDT = $tryParse($startInput);
        $endDT   = $tryParse($endInput);

        if ($startDT) { $startDT->setTime(0, 0, 0);   $start = $startDT->format('Y-m-d H:i:s'); }
        if ($endDT)   { $endDT->setTime(23, 59, 59);  $end   = $endDT->format('Y-m-d H:i:s'); }
    }
}

// …build your base SELECT as before up to the WHERE…

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
        CONCAT(d.name,' - ',d.empid) AS distributor,
        a.area,
        regions.name AS region,
        states.name AS state
    FROM outlets o
    JOIN area a       ON a.id = o.routeid
    JOIN employees d  ON d.id = a.distributor_id
    LEFT JOIN states  ON states.id  = a.state
    LEFT JOIN cities  ON cities.id  = a.city
    LEFT JOIN regions ON regions.id = a.region
    WHERE 1=1
";

// Apply the “not visited in range” logic only if we have a valid window
if ($start && $end) {
    $selectQry .= "
      AND NOT EXISTS (
           SELECT 1
           FROM booking bk
           WHERE bk.outlet_id = o.id
             AND bk.booking_time BETWEEN '$start' AND '$end'
      )
      AND (
           o.lastvisit IS NULL
           OR o.lastvisit < '$start'
           OR o.lastvisit > '$end'
      )
    ";
}

    // --- Additional filters (use AND because we already have a WHERE above) ---
    $filters = [];

    // Escape all incoming values
    $state       = isset($_GET['state'])       ? mysqli_real_escape_string($con, trim($_GET['state']))       : '';
    $city        = isset($_GET['region'])      ? mysqli_real_escape_string($con, trim($_GET['region']))      : '';
    $routeid     = isset($_GET['area'])        ? mysqli_real_escape_string($con, trim($_GET['area']))        : '';
    $distributor = isset($_GET['distributor']) ? mysqli_real_escape_string($con, trim($_GET['distributor'])) : '';

    if ($distributor !== '') { $filters[] = "d.id = '$distributor'"; }
    if ($state !== '')       { $filters[] = "a.state = '$state'"; }
    // NOTE: You filtered city against a.region previously; if you intend "region" filter, keep as a.region.
    // If you actually want to filter by city, change to "a.city = '$city'".
    if ($city !== '')        { $filters[] = "a.region = '$city'"; }
    if ($routeid !== '')     { $filters[] = "o.routeid = '$routeid'"; }

    if (!empty($filters)) {
        $selectQry .= " AND " . implode(" AND ", $filters) . " ";
    }

    $query = $selectQry . " ORDER BY o.id DESC";

    // --- Execute ---
    $res = mysqli_query($con, $query) or die(mysqli_error($con));

    $response = [];
    $total = $gt = $mt = $mtl = $milkbooth = 0;

    // Precompute rolling windows once
    $currentDateTime      = date('Y-m-d H:i:s');
    $previous30DateTime   = date('Y-m-d H:i:s', strtotime('-30 days'));
    $previous180DateTime  = date('Y-m-d H:i:s', strtotime('-180 days'));

    while ($row = mysqli_fetch_assoc($res)) {
        $outletId = $row['id'];

        // 30 days sum
        $query30 = "
            SELECT SUM(total_amount) AS total_amount
            FROM booking
            WHERE outlet_id = '$outletId'
              AND booking_time BETWEEN '$previous30DateTime' AND '$currentDateTime'
            GROUP BY outlet_id
        ";
        $res30days = mysqli_query($con, $query30);
        $outletSum30Row = $res30days ? mysqli_fetch_assoc($res30days) : null;
        $outletSum30 = isset($outletSum30Row['total_amount']) ? round((float)$outletSum30Row['total_amount'], 2) : 0;

        // 180 days sum (monthly avg over 6 months)
        $query180 = "
            SELECT SUM(total_amount) AS total_amount
            FROM booking
            WHERE outlet_id = '$outletId'
              AND booking_time BETWEEN '$previous180DateTime' AND '$currentDateTime'
            GROUP BY outlet_id
        ";
        $res180days = mysqli_query($con, $query180);
        $outletSum180Row = $res180days ? mysqli_fetch_assoc($res180days) : null;
        $outletSum180 = (!empty($outletSum180Row['total_amount']) && $outletSum180Row['total_amount'] > 0)
            ? round(((float)$outletSum180Row['total_amount']) / 6, 2)
            : 0;

        // Tally types
        if ($row['outlettype'] === 'MTS')        { $mt++; }
        if ($row['outlettype'] === 'G.T.')       { $gt++; }
        if ($row['outlettype'] === 'Milk Booth') { $milkbooth++; }
        if ($row['outlettype'] === 'MTL')        { $mtl++; }

        $total++;

        $response[] = [
            'id'                  => $row['id'],
            'state'               => $row['state'],
            'city'                => $row['city'],
            'region'              => $row['region'],
            'routename'           => $row['area'],
            'distributor'         => $row['distributor'],
            'name'                => $row['name'],
            'lastvisit'           => $row['lastvisit'],
            'last_30_value'       => $outletSum30,
            'past_order_per_month'=> $outletSum180,
            'contactperson'       => $row['contactperson'],
            'contact'             => $row['contact'],
            'address'             => $row['address'],
            // Running totals
            'mt'                  => $mt,
            'gt'                  => $gt,
            'mtl'                 => $mtl,
            'milkbooth'           => $milkbooth,
            'total'               => $total,
        ];
    }

    // Removed: print_r($row); // $row is undefined here
    echo json_encode($response);
    return;
}




?>
