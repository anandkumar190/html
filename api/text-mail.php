<?php

error_reporting(E_ALL);
ini_set('display_errors', 1);

require __DIR__ . '/../vendor/autoload.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

/*
|--------------------------------------------------------------------------
| Report URL
|--------------------------------------------------------------------------
|
| This URL should directly generate Excel report
|
*/

$reportUrl =
    "https://cartroute.com/api/daly-report-os.php";

/*
|--------------------------------------------------------------------------
| Report File Name
|--------------------------------------------------------------------------
*/

$reportFileName =
    "All_Salesmen_Report_" .
    date('d_M_Y') .
    ".xls";

/*
|--------------------------------------------------------------------------
| Reports Directory
|--------------------------------------------------------------------------
*/

$reportsDir = __DIR__ . "/reports";

/*
|--------------------------------------------------------------------------
| Create Reports Folder If Missing
|--------------------------------------------------------------------------
*/

if (!is_dir($reportsDir)) {

    mkdir($reportsDir, 0777, true);
}

/*
|--------------------------------------------------------------------------
| Full Save Path
|--------------------------------------------------------------------------
*/

$savePath =
    $reportsDir . "/" . $reportFileName;

/*
|--------------------------------------------------------------------------
| Download Report
|--------------------------------------------------------------------------
*/

$reportContent =
    file_get_contents($reportUrl);

if ($reportContent === false) {

    die("Unable to generate report.");
}

/*
|--------------------------------------------------------------------------
| Save Report File
|--------------------------------------------------------------------------
*/

file_put_contents(
    $savePath,
    $reportContent
);

/*
|--------------------------------------------------------------------------
| PHPMailer
|--------------------------------------------------------------------------
*/

$mail = new PHPMailer(true);

try {

    $mail->isSMTP();

    $mail->Host = 'smtp.gmail.com';

    $mail->SMTPAuth = true;

    $mail->Username   = 'vivanfoods2@gmail.com';
    $mail->Password   = 'mxfbczbfrtobuaec';


    $mail->SMTPSecure =
        PHPMailer::ENCRYPTION_STARTTLS;

    $mail->Port = 587;

    /*
    |--------------------------------------------------------------------------
    | Sender
    |--------------------------------------------------------------------------
    */

    $mail->setFrom(
        'vivanfoods2@gmail.com',
        'Sales System'
    );

    /*
    |--------------------------------------------------------------------------
    | Receiver
    |--------------------------------------------------------------------------
    */

    $mail->addAddress(
        'eazroch@gmail.com'
    );

    /*
    |--------------------------------------------------------------------------
    | Mail Content
    |--------------------------------------------------------------------------
    */

    $mail->isHTML(true);

    $mail->Subject =
        'All Salesmen Daily Report - ' .
        date('d-M-Y');

    $mail->Body = "
        <h2>
            All Salesmen Daily Report
        </h2>

        <p>
            Please find attached today's report.
        </p>
    ";

    /*
    |--------------------------------------------------------------------------
    | Attach Report
    |--------------------------------------------------------------------------
    */

    $mail->addAttachment(
        $savePath,
        $reportFileName
    );

    /*
    |--------------------------------------------------------------------------
    | Send Mail
    |--------------------------------------------------------------------------
    */

    $mail->send();

    /*
    |--------------------------------------------------------------------------
    | Optional Cleanup
    |--------------------------------------------------------------------------
    |
    | Delete report after sending
    |
    */

    if (file_exists($savePath)) {

        unlink($savePath);
    }

    echo "Report Sent Successfully";

} catch (Exception $e) {

    echo "Mailer Error: {$mail->ErrorInfo}";
}