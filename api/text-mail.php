<?php

error_reporting(E_ALL);
ini_set('display_errors', 1);

require __DIR__ . '/../vendor/autoload.php';
require 'daly-report-os.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use Dompdf\Dompdf;
use Dompdf\Options;


/*
|--------------------------------------------------------------------------
| Generate Report HTML
|--------------------------------------------------------------------------
*/

$html =
    exportEmployeeReport($con, false);

/*
|--------------------------------------------------------------------------
| PDF File
|--------------------------------------------------------------------------
*/

$pdfFileName =
    "All_Salesmen_Report_" .
    date('d_M_Y') .
    ".pdf";

$savePath =
    sys_get_temp_dir() .
    "/" .
    $pdfFileName;

/*
|--------------------------------------------------------------------------
| Dompdf Setup
|--------------------------------------------------------------------------
*/

$options = new Options();

$options->set('isRemoteEnabled', true);

$dompdf = new Dompdf($options);

/*
|--------------------------------------------------------------------------
| Load HTML
|--------------------------------------------------------------------------
*/

$dompdf->loadHtml($html);

/*
|--------------------------------------------------------------------------
| Paper Size
|--------------------------------------------------------------------------
*/

$dompdf->setPaper(
    'A4',
    'landscape'
);

/*
|--------------------------------------------------------------------------
| Render PDF
|--------------------------------------------------------------------------
*/

$dompdf->render();

/*
|--------------------------------------------------------------------------
| Save PDF
|--------------------------------------------------------------------------
*/

file_put_contents(
    $savePath,
    $dompdf->output()
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
    | Attach PDF
    |--------------------------------------------------------------------------
    */

    $mail->addAttachment(
        $savePath,
        $pdfFileName
    );

    /*
    |--------------------------------------------------------------------------
    | Send Mail
    |--------------------------------------------------------------------------
    */

    $mail->send();

    /*
    |--------------------------------------------------------------------------
    | Delete Temp PDF
    |--------------------------------------------------------------------------
    */

    if (file_exists($savePath)) {

        unlink($savePath);
    }

    echo "Report Sent Successfully";

} catch (Exception $e) {

    echo "Mailer Error: {$mail->ErrorInfo}";
}