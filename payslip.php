<?php
include "../sakura/TCPDF/tcpdf.php";
ini_set( 'display_errors', 0 );
require_once("../sakura/schedule/const/const.inc");
require_once("../sakura/schedule/func.inc");
if (!$_SESSION['ulogin']['teacher_id']) { header('location: login.php'); exit(); }
set_time_limit(60);

$teacher_id	= $_SESSION['ulogin']['teacher_id'];

$teacher_list = get_teacher_list($db, array(), array(), array());
$teacher = $teacher_list[$teacher_id];

$year = $_POST['y'];
$month = $_POST['m'];

if ($teacher['lesson_id'] != 2) {
	define (STR_TITLE1,        '八王子さくらアカデミー');
	define (STR_TITLE2,        '講師:&nbsp;');
	define (STR_PAYSLIP,       '給料明細');
	define (STR_MAINMENU,      'メインメニューへ戻る');
} else {
	define (STR_TITLE1,        'HACHIOJI SAKURA ACADEMY');
	define (STR_TITLE2,        'Teacher:&nbsp;');
	define (STR_PAYSLIP,       'Payslip');
	define (STR_MAINMENU,      'Back to Main Menu');
}


$file = "./pay_pdf/pay-$year-$month-t$teacher_id.html";
if (file_exists($file)) {
	
	$tcpdf = new TCPDF();
	if (!$tcpdf) $errArray[] = 'PDF作成エラー';
	$tcpdf->AddPage();
	$tcpdf->SetFont("kozgopromedium", "", 10);
	$htmldata = file_get_contents($file);
	if (!$htmldata) $errArray[] = "ファイルオープンエラー ($file)";
	preg_match('/[st]\d+/', $file, $matches);
	$id = $matches[0];
	$tcpdf->writeHTML($htmldata);
	$tcpdf->Output(__DIR__."/pay_pdf/pay-$year-$month-$id.pdf",'I');

} else { 
?>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<meta name="robots" content="index,follow">
</head>
<body>
<h3><?= STR_PAYSLIP ?></h3>
<h3><?= $year.'/'.$month ?></h3>
現在、データがありません。<br>
Data is not available now.<br><br>
<a href="./menu.php"><?= STR_MAINMENU ?></a>
</body>
</html>
<?php } ?>