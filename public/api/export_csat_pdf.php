<?php
session_start();
require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../models/Feedback.php';
require_once __DIR__ . '/../../vendor/autoload.php';

requireLogin();
requireRole('admin');

use Dompdf\Dompdf;
use Dompdf\Options;

if (!isset($_GET['id'])) {
    die("Feedback ID is required");
}

$id = (int)$_GET['id'];
$db = Database::getInstance()->getConnection();
$stmt = $db->prepare("
    SELECT f.*, u.full_name as user_name, w.window_number, w.window_name, t.ticket_number, s.service_name
    FROM feedback f
    LEFT JOIN users u ON f.user_id = u.id
    LEFT JOIN tickets t ON f.ticket_id = t.id
    LEFT JOIN services s ON t.service_id = s.id
    LEFT JOIN windows w ON f.window_id = w.id
    WHERE f.id = ?
");
$stmt->execute([$id]);
$f = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$f) {
    error_log("Export failed: Feedback ID $id not found or relations broken. Check user, ticket, and service existence.");
    die("Feedback not found or access denied. (ID: $id)");
}

// Ensure extraction path exists
$extractPath = __DIR__ . '/../../temp_docx_extract';
if (!is_dir($extractPath . '/word/media')) {
    $docxPath = __DIR__ . '/../../csat_template.docx';
    if (file_exists($docxPath)) {
        if (!is_dir($extractPath)) mkdir($extractPath, 0777, true);
        $zip = new ZipArchive;
        if ($zip->open($docxPath) === TRUE) {
            $zip->extractTo($extractPath);
            $zip->close();
        }
    }
}

// Load Images
$baseDir = __DIR__ . '/../../temp_docx_extract/word/media/';
$img_ispsc = file_exists($baseDir . 'image2.png') ? 'data:image/png;base64,' . base64_encode(file_get_contents($baseDir . 'image2.png')) : '';
$img_bagong = file_exists($baseDir . 'image1.png') ? 'data:image/png;base64,' . base64_encode(file_get_contents($baseDir . 'image1.png')) : '';
$img_wuri = file_exists($baseDir . 'image5.png') ? 'data:image/png;base64,' . base64_encode(file_get_contents($baseDir . 'image5.png')) : '';
$img_greenmetric = file_exists($baseDir . 'image6.png') ? 'data:image/png;base64,' . base64_encode(file_get_contents($baseDir . 'image6.png')) : '';
$img_impact = file_exists($baseDir . 'image7.jpg') ? 'data:image/jpeg;base64,' . base64_encode(file_get_contents($baseDir . 'image7.jpg')) : '';
$img_iso = file_exists($baseDir . 'image8.png') ? 'data:image/png;base64,' . base64_encode(file_get_contents($baseDir . 'image8.png')) : '';

// Helper functions
$chk = function($val, $target) {
    return ($val == $target) ? '<div class="cb cb-checked"></div>' : '<div class="cb"></div>';
};
$circ = function($val, $target) {
    $class = ($val == $target) ? 'circle-checked' : '';
    return '<div class="circle-answer ' . $class . '">' . $target . '</div>';
};

$html = '
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>CSAT Export - '.$f['ticket_number'].'</title>
    <style>
        @page { margin: 110px 50px 80px 50px; }
        header { position: fixed; top: -95px; left: 0px; right: 0px; height: 90px; }
        .header-line { width: 100%; height: 5px; display: table; table-layout: fixed; margin-bottom: 5px; }
        .header-line div { display: table-cell; }
        .green { background-color: #0c4b05; }
        .yellow { background-color: #EEEA82; }
        .red { background-color: #b91c1c; }
        footer { position: fixed; bottom: -60px; left: 0px; right: 0px; height: 50px; text-align: center; border-top: 5px solid #0c4b05; padding-top: 5px; }
        body { font-family: "Helvetica", "Arial", sans-serif; font-size: 11px; color: #111; margin: 0; padding: 0; }
        .header-content { text-align: center; position: relative; }
        .header-text { margin-top: 5px; line-height: 1.2; }
        .header-text span.rp { font-family: "Times New Roman", Times, serif; font-size: 11px; }
        .header-text span.inst { font-family: "Times New Roman", Times, serif; font-size: 16px; font-weight: bold; color: #0C4B05; text-transform: uppercase; }
        .header-text span.addr { font-family: "Times New Roman", Times, serif; font-size: 9px; font-weight: bold;}
        .title-block { text-align: center; margin-bottom: 5px; }
        .title-block p { margin: 2px; font-size: 10px; font-style: italic; }
        .info-table { width: 100%; border: 1px solid #000; border-collapse: collapse; margin-bottom: 5px; }
        .info-table td { border: 1px solid #000; padding: 3px 5px; vertical-align: top; }
        .yellow-bar { background-color: #EEEA82; font-weight: bold; padding: 4px; font-size: 10px; border: 1px solid #000; border-bottom: none; }
        .cc-table { width: 100%; border: 1px solid #000; border-collapse: collapse; margin-bottom: 5px; font-size: 9px;}
        .cc-table td { padding: 4px; vertical-align: top; border: none; width: 50%;}
        .csat-table { width: 100%; border: 1px solid #000; border-collapse: collapse; margin-bottom: 10px; font-size: 10px;}
        .csat-table th, .csat-table td { border: 1px solid #000; padding: 4px; text-align: center; }
        .csat-table th { background-color: #f0f0f0; }
        .csat-table td.question { text-align: left; width: 55%; font-style: italic;}
        .circle-answer { display: inline-block; width: 14px; height: 14px; border-radius: 50%; border: 1px solid #000; text-align: center; line-height: 14px; font-size: 9px; }
        .circle-checked { background-color: #b91c1c; color: #fff; border-color: #b91c1c; }
        .emoji-head { font-size: 18px; line-height: 1; }
        .cb { display: inline-block; width: 8px; height: 8px; border: 1px solid #000; border-radius: 50%; margin-right: 3px; position:relative; top: 1px;}
        .cb-checked { background-color: #000; }
    </style>
</head>
<body>
    <header>
        <div class="header-line"><div class="green"></div><div class="yellow"></div><div class="red"></div></div>
        <div class="header-content">
            <div style="position: absolute; left: 0; top: 0;">'.($img_ispsc ? '<img src="'.$img_ispsc.'" style="width: 60px;">' : '').'</div>
            <div style="position: absolute; right: 0; top: 0;">'.($img_bagong ? '<img src="'.$img_bagong.'" style="width: 60px;">' : '').'</div>
            <div class="header-text">
                <span class="rp">Republic of the Philippines</span><br>
                <span class="inst">Ilocos Sur Polytechnic State College</span><br>
                <span class="addr">Website: www.ispsc.edu.ph | Email: ispsc@ispsc.edu.ph</span>
            </div>
        </div>
    </header>
    <footer>
        <div class="footer-badges">
            '.($img_wuri ? '<img src="'.$img_wuri.'" style="height: 25px;">' : '').'
            '.($img_greenmetric ? '<img src="'.$img_greenmetric.'" style="height: 25px;">' : '').'
            '.($img_impact ? '<img src="'.$img_impact.'" style="height: 25px;">' : '').'
            '.($img_iso ? '<img src="'.$img_iso.'" style="height: 25px;">' : '').'
        </div>
    </footer>
    <main>
        <div class="title-block">
            <p>Client satisfaction is our priority and we continually seek ways to improve our services.</p>
            <p>Please complete this form and let us know how we are doing.</p>
        </div>
        <table class="info-table">
            <tr>
                <td style="width: 60%;">Date of visit: <strong>' . date('M d, Y', strtotime($f['created_at'])) . '</strong><br>Ticket #: <strong>' . htmlspecialchars($f['ticket_number']) . '</strong></td>
                <td style="width: 40%;">Time: <strong>' . date('h:i A', strtotime($f['created_at'])) . '</strong><br>Office: <strong>' . htmlspecialchars($f['window_name'] ?? 'N/A') . '</strong></td>
            </tr>
        </table>
        <div style="margin: 5px 0;"><strong>Please check (/) the appropriate circle that corresponds to your answer.</strong></div>
        <div style="margin-bottom: 5px;">
            Type of Client: 
            &nbsp; ' . $chk($f['client_type'], 'Student') . ' Student 
            &nbsp; ' . $chk($f['client_type'], 'Non-Teaching') . ' Non-Teaching 
            &nbsp; ' . $chk($f['client_type'], 'Faculty') . ' Faculty 
            &nbsp; ' . $chk($f['client_type'], 'Alumni') . ' Alumni 
            &nbsp; ' . $chk($f['client_type'], 'Parent/Guardian') . ' Parent/Guardian 
            &nbsp; ' . $chk($f['client_type'], 'Others') . ' Others: <u style="text-decoration:underline;">' . ($f['client_type'] === 'Others' ? htmlspecialchars($f['client_type_others']) : '            ') . '</u>
        </div>
        <div class="yellow-bar"><div style="float:left; width: 50%;">Service/s Requested</div><div style="float:left; width: 50%;">Means of contacting the office/person concerned:</div><div style="clear:both;"></div></div>
        <table class="cc-table" style="border-top: none;">
            <tr>
                <td style="border-right: 1px solid #000; border-bottom: 1px solid #000;"><u>' . htmlspecialchars($f['service_name']) . '</u></td>
                <td style="border-bottom: 1px solid #000;">' . $chk($f['contact_means'], 'In person') . ' In person&nbsp;&nbsp;&nbsp;' . $chk($f['contact_means'], 'Over the Telephone') . ' Over the Telephone<br>' . $chk($f['contact_means'], 'University Help Desk') . ' University Help Desk&nbsp;&nbsp;&nbsp;' . $chk($f['contact_means'], 'Others') . ' Others (Specify): <u>' . ($f['contact_means'] === 'Others' ? htmlspecialchars($f['contact_means_others']) : '            ') . '</u></td>
            </tr>
        </table>
        <div class="yellow-bar"><div style="float:left; width: 50%;">A. Which of the following most describes your awareness<br>&nbsp;&nbsp;&nbsp;&nbsp;of a Citizens Charter (CC)?</div><div style="float:left; width: 50%;">B. If aware of CC (answered 1-3 in A), would you say that the CC<br>&nbsp;&nbsp;&nbsp;&nbsp;of this office was.....?</div><div style="clear:both;"></div></div>
        <table class="cc-table" style="border-top: none;">
            <tr>
                <td style="border-right: 1px solid #000; border-bottom: 1px solid #000;">
                    ' . $chk($f['cc_awareness'], '1') . ' 1. I know what a CC is and I saw this office\'s CC<br>
                    ' . $chk($f['cc_awareness'], '2') . ' 2. I know what a CC is but I did NOT see this office\'s CC<br>
                    ' . $chk($f['cc_awareness'], '3') . ' 3. I learned of the CC only when I saw this office\'s CC<br>
                    ' . $chk($f['cc_awareness'], '4') . ' 4. I do not know what a CC is and I did not see one
                </td>
                <td style="border-bottom: 1px solid #000;">
                    ' . $chk($f['cc_visibility'], '4') . ' Easy to see &nbsp;&nbsp; ' . $chk($f['cc_visibility'], '3') . ' Somewhat easy &nbsp;&nbsp; ' . $chk($f['cc_visibility'], '2') . ' Difficult<br>
                    ' . $chk($f['cc_visibility'], '1') . ' Not visible &nbsp;&nbsp; ' . $chk($f['cc_visibility'], '5') . ' N/A<br>
                    <div style="background-color: #EEEA82; font-weight: bold; padding: 2px; margin-top: 5px; margin-left: -5px; margin-right: -5px; border-top: 1px solid #000; border-bottom: 1px solid #000;">C. If aware of CC (answered code 1-3 in A), How much did the CC help you?</div>
                    ' . $chk($f['cc_helpfulness'], '3') . ' Helped very much &nbsp; ' . $chk($f['cc_helpfulness'], '2') . ' Somewhat &nbsp; ' . $chk($f['cc_helpfulness'], '1') . ' No help &nbsp; ' . $chk($f['cc_helpfulness'], '4') . ' N/A
                </td>
            </tr>
        </table>
        <div style="font-weight: bold; margin: 5px 0;">INSTRUCTION: Please circle the number that corresponds to your answer:</div>
        <table class="csat-table">
            <tr><th class="question" style="border-top:none; border-left:none; background-color:#fff;"></th><th><div class="emoji-head">&#128513;</div>Str. Agree</th><th><div class="emoji-head">&#128578;</div>Agree</th><th><div class="emoji-head">&#128528;</div>Neutral</th><th><div class="emoji-head">&#128577;</div>Disagree</th><th><div class="emoji-head">&#128545;</div>Str. Disagree</th></tr>
            <tr><td class="question">1. Satisfied with service</td><td>'.$circ($f['rating_responsiveness_1'], 5).'</td><td>'.$circ($f['rating_responsiveness_1'], 4).'</td><td>'.$circ($f['rating_responsiveness_1'], 3).'</td><td>'.$circ($f['rating_responsiveness_1'], 2).'</td><td>'.$circ($f['rating_responsiveness_1'], 1).'</td></tr>
            <tr><td class="question">2. Reasonable time</td><td>'.$circ($f['rating_responsiveness_2'], 5).'</td><td>'.$circ($f['rating_responsiveness_2'], 4).'</td><td>'.$circ($f['rating_responsiveness_2'], 3).'</td><td>'.$circ($f['rating_responsiveness_2'], 2).'</td><td>'.$circ($f['rating_responsiveness_2'], 1).'</td></tr>
            <tr><td class="question">3. Requirements followed</td><td>'.$circ($f['rating_reliability'], 5).'</td><td>'.$circ($f['rating_reliability'], 4).'</td><td>'.$circ($f['rating_reliability'], 3).'</td><td>'.$circ($f['rating_reliability'], 2).'</td><td>'.$circ($f['rating_reliability'], 1).'</td></tr>
            <tr><td class="question">4. Steps easy/simple</td><td>'.$circ($f['rating_access'], 5).'</td><td>'.$circ($f['rating_access'], 4).'</td><td>'.$circ($f['rating_access'], 3).'</td><td>'.$circ($f['rating_access'], 2).'</td><td>'.$circ($f['rating_access'], 1).'</td></tr>
            <tr><td class="question">5. Found info easily</td><td>'.$circ($f['rating_communication'], 5).'</td><td>'.$circ($f['rating_communication'], 4).'</td><td>'.$circ($f['rating_communication'], 3).'</td><td>'.$circ($f['rating_communication'], 2).'</td><td>'.$circ($f['rating_communication'], 1).'</td></tr>
            <tr><td class="question">6. Reasonable fees</td><td>'.$circ($f['rating_costs'], 5).'</td><td>'.$circ($f['rating_costs'], 4).'</td><td>'.$circ($f['rating_costs'], 3).'</td><td>'.$circ($f['rating_costs'], 2).'</td><td>'.$circ($f['rating_costs'], 1).'</td></tr>
            <tr><td class="question">7. Fair to everyone</td><td>'.$circ($f['rating_integrity'], 5).'</td><td>'.$circ($f['rating_integrity'], 4).'</td><td>'.$circ($f['rating_integrity'], 3).'</td><td>'.$circ($f['rating_integrity'], 2).'</td><td>'.$circ($f['rating_integrity'], 1).'</td></tr>
            <tr><td class="question">8. Treated courteously</td><td>'.$circ($f['rating_courtesy'], 5).'</td><td>'.$circ($f['rating_courtesy'], 4).'</td><td>'.$circ($f['rating_courtesy'], 3).'</td><td>'.$circ($f['rating_courtesy'], 2).'</td><td>'.$circ($f['rating_courtesy'], 1).'</td></tr>
            <tr><td class="question">9. Got what I needed</td><td>'.$circ($f['rating_outcome'], 5).'</td><td>'.$circ($f['rating_outcome'], 4).'</td><td>'.$circ($f['rating_outcome'], 3).'</td><td>'.$circ($f['rating_outcome'], 2).'</td><td>'.$circ($f['rating_outcome'], 1).'</td></tr>
        </table>
        <div>Comments/Feedback:</div>
        <div style="border-bottom: 1px solid #999; padding: 5px; min-height: 40px; font-style: italic;">' . htmlspecialchars($f['comment']) . '</div>
        
        <div style="margin-top: 10px; text-align: right; font-size: 9px; color: #666;">
            AI sentiment analysis: <strong>' . strtoupper(str_replace('_', ' ', $f['sentiment'] ?? 'NEUTRAL')) . '</strong> 
            (Score: ' . number_format($f['sentiment_score'] ?? 0, 2) . ')
        </div>

        <div style="text-align: center; margin-top: 20px;"><strong>Thank you for your time!</strong><br>Please drop at the designated drop box</div>
    </main>
</body>
</html>';

$options = new Options();
$options->set('isHtml5ParserEnabled', true);
$options->set('isRemoteEnabled', true);
$dompdf = new Dompdf($options);
$dompdf->loadHtml($html);
$dompdf->setPaper('A4', 'portrait');
$dompdf->render();

header('Content-Type: application/pdf');
header('Content-Disposition: attachment; filename="CSAT_Feedback_'.$f['ticket_number'].'.pdf"');
echo $dompdf->output();
exit;
