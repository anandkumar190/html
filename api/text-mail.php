<!-- 

use PHPMailer\PHPMailer\PHPMailer;

$mail = new PHPMailer();

$mail->isSMTP();
$mail->Host = 'smtp.gmail.com';
$mail->SMTPAuth = true;
$mail->Username = 'your@gmail.com';
$mail->Password = 'password';
$mail->SMTPSecure = 'tls';
$mail->Port = 587;

$mail->setFrom('your@gmail.com', 'System');
$mail->addAddress('manager@gmail.com');

$mail->Subject = 'Daily Report';
$mail->Body    = 'Today report attached';

$mail->send();

 -->


 <?php
die("HI");
require __DIR__ . '/../vendor/autoload.php';
require("connect.php");

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;


/*
|--------------------------------------------------------------------------
| Fetch Today's Sales
|--------------------------------------------------------------------------
*/

// $sql = "
// SELECT 
//     COUNT(id) as total_orders,
//     SUM(total_amount) as total_sales
// FROM orders
// WHERE DATE(created_at) = CURDATE()
// ";

// $result = $con->query($sql);
// $data = $result->fetch_assoc();

// $totalOrders = $data['total_orders'] ?? 0;
// $totalSales  = $data['total_sales'] ?? 0;

    $totalOrders = 10;
    $totalSales  = 10;

/*
|--------------------------------------------------------------------------
| Email Content
|--------------------------------------------------------------------------
*/

$message = "
<h2>Daily Sales Report</h2>

<table border='1' cellpadding='10'>
    <tr>
        <th>Total Orders</th>
        <th>Total Sales</th>
    </tr>

    <tr>
        <td>{$totalOrders}</td>
        <td>₹{$totalSales}</td>
    </tr>
</table>
";

/*
|--------------------------------------------------------------------------
| PHPMailer Setup
|--------------------------------------------------------------------------
*/

$mail = new PHPMailer(true);

try {

    $mail->isSMTP();

    $mail->Host       = 'smtp.gmail.com';
    $mail->SMTPAuth   = true;
    $mail->Username   = 'vivanfoods2@gmail.com';
    $mail->Password   = 'cartroute';

    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;

    $mail->Port       = 587;

    /*
    |--------------------------------------------------------------------------
    | Sender & Receiver
    |--------------------------------------------------------------------------
    */

    $mail->setFrom('anandkumar.kumar190@gmail.com', 'Sales System');

    $mail->addAddress('eazroch@gmail.com');

    /*
    |--------------------------------------------------------------------------
    | Email Content
    |--------------------------------------------------------------------------
    */

    $mail->isHTML(true);

    $mail->Subject = 'Daily Sales Report - ' . date('Y-m-d');

    $mail->Body = $message;

    $mail->send();

    echo "Report Sent Successfully";

} catch (Exception $e) {

    echo "Mailer Error: {$mail->ErrorInfo}";
}
