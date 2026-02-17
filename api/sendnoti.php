<?php
define('API_ACCESS_KEY','AIzaSyA7zvsKyfieA6XOhgbQJfyYMfcFlOvlHPY');
$fcmUrl = 'https://fcm.googleapis.com/fcm/send';
include("../connect.php");
   $time=date("H:i:s"); 
  $datetime = date("Y-m-d H:i:s");
  $date=date("Y-m-d");
if(isset($_GET['team']))
{
	$ids=$_POST['ids'];
	$title=$_POST['title'];
	$message=$_POST['message'];
	foreach($ids as $id)
	{
	  $res=mysqli_query($con,"select * from devices where id='$id'");
	  if($row=mysqli_fetch_array($res))
	  {
		  $token=$row['usertoken'];
		  $userid=$row['userid'];
		  $notification= array(
            'title'=>$title,
            'body'=>$message,
			'icon'=>'fg',
			'sound'=>'gfh',
			'priority'=>'high'
            );
          $extraNotificationData=array("message"=>$notification,"moredata"=>'dd');
        $fcmNotification=array( 
            //'registration_ids' => $tokenList, //multple token array
            'to'        => $token, //single token
            'notification' => $notification,
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
		mysqli_query($con,"insert into notifications(title,message,userid,date,time) values('$title','$message','$userid','$date','$time')");
	  }
	}
	
	echo "Notification Push Successfully...";
}

if(isset($_GET['show']))
{
   $res=mysqli_query($con,"select n.id,n.title,n.message,n.date,n.time,e.name,e.empid from notifications n join employees e on n.userid=e.id order by n.id desc");
   $response=array();
   while($row=mysqli_fetch_array($res))
   {
	   $rr=array();
	   $rr['id']=$row['id'];
	   $rr['title']=$row['title'];
	   $rr['message']=$row['message'];
	   $rr['date']=$row['date'];
	   $rr['time']=$row['time'];
	   $rr['name']=$row['name'];
	   $rr['empid']=$row['empid'];
	   array_push($response,$rr);
   }
   echo json_encode($response);
}

if(isset($_GET['delete']))
{
	$ids=$_POST['ids'];
	foreach($ids as $id)
	{
      mysqli_query($con,"delete from notifications where id='$id'");
	}
   echo "Notifications Delete Succesfully...";
}


?>