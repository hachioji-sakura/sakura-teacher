<?php
ini_set( 'display_errors', 0 );
require_once("../sakura/schedule/const/const.inc");
require_once("../sakura/schedule/func.inc");
if (!$_SESSION['ulogin']['teacher_id']) { header('location: login.php'); exit(); }
set_time_limit(60);

$year = date("Y");
$month = date("n");

switch ($month) {
	case 1:
	case 2:	$selectm1 = 'selected'; break;
	case 3:
	case 4:	$selectm3 = 'selected'; break;
	case 5:
	case 6:	$selectm5 = 'selected'; break;
	case 7:
	case 8:	$selectm7 = 'selected'; break;
	case 9:
	case 10:	$selectm9 = 'selected'; break;
	case 11:
	case 12:	$selectm11 = 'selected'; break;
}

$payy = $year;
switch ($month) {
	case 1: $paym11 = 'selected'; $payy--; break;
	case 2: $paym12 = 'selected'; $payy--; break;
	case 3: $paym1 = 'selected'; break;
	case 4: $paym2 = 'selected'; break;
	case 5: $paym3 = 'selected'; break;
	case 6: $paym4 = 'selected'; break;
	case 7: $paym5 = 'selected'; break;
	case 8: $paym6 = 'selected'; break;
	case 9: $paym7 = 'selected'; break;
	case 10: $paym8 = 'selected'; break;
	case 11: $paym9 = 'selected'; break;
	case 12: $paym10 = 'selected'; break;
}

$teacher_id	= $_SESSION['ulogin']['teacher_id'];

$teacher_list = get_teacher_list($db, array(), array(), array());

if ($teacher_list[$teacher_id]['lesson_id'] != 2) {
	define (STR_TITLE1,       '八王子さくらアカデミー');
	define (STR_TITLE2,       '講師:');
	define (STR_MAINMENU,     'メインメニュー');
	define (STR_SHUSSEKIBO,   '出席簿');
	define (STR_SEASON_CLASS_ENTRY,'期間講習・土日講習登録');
	define (STR_SEASON_CLASS_SCHEDULE,'期間講習スケジュール');
	define (STR_SAT_SUN_CLASS_SCHEDULE,'土日講習スケジュール');
	define (STR_SEASON_CLASS_SCHEDULE1,'期間講習生徒配布用スケジュール');
	define (STR_SAT_SUN_CLASS_SCHEDULE1,'土日講習生徒配布用スケジュール');
	define (STR_PAYSLIP,      '給料明細');
	define (STR_TATEKAE,      '立替経費申請');
	define (STR_TRANSPORT_COST,'交通費申請');
	define (STR_SET_PASSWORD, 'パスワード設定');
	define (STR_LOGOUT,       'ログアウト');
} else {
	define (STR_TITLE1,       'HACHIOJI SAKURA ACADEMY');
	define (STR_TITLE2,       'Teacher: ');
	define (STR_MAINMENU,     'Main Menu');
	define (STR_SHUSSEKIBO,   'Attendance record');
	define (STR_SEASON_CLASS_ENTRY,'Season/Saturdy/Sunday Class Application');
	define (STR_SEASON_CLASS_SCHEDULE,'Season Class Schedule');
	define (STR_SAT_SUN_CLASS_SCHEDULE,'Saturdy/Sunday Class Schedule');
	define (STR_SEASON_CLASS_SCHEDULE1,'Season Class Schedule for student');
	define (STR_SAT_SUN_CLASS_SCHEDULE1,'Saturdy/Sunday Class Schedule for student');
	define (STR_PAYSLIP,      'Payslip');
	define (STR_TATEKAE,      'Reimbursed Expenses Application');
	define (STR_TRANSPORT_COST,'Transportation Expenses Application');
	define (STR_SET_PASSWORD, 'Set password');
	define (STR_LOGOUT,       'Logout');
}
?>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<meta name="robots" content="index,follow">
</head>
<body>
<div align="center">
<h3><?= STR_TITLE1 ?></h3>
<h3><?= STR_TITLE2.$teacher_list[$teacher_id]['name'] ?></h3>
<h3><?= STR_MAINMENU ?></h3>
<table>
<tr><td>1. <a href="./check_work_time.php"><?= STR_SHUSSEKIBO ?></a></td></tr>
<tr><td>2. <a href="./season_class_teacher_entry.php?class_type=sat_sun_class"><?= STR_SEASON_CLASS_ENTRY ?></a></td></tr>
<tr><td>3. <a href="./season_class_schedule.php?class_type=season_class"><?= STR_SEASON_CLASS_SCHEDULE ?></a></td></tr>
<tr><td>4. <a href="./season_class_schedule.php?class_type=sat_sun_class"><?= STR_SAT_SUN_CLASS_SCHEDULE ?></a></td></tr>
<tr><td>5. <?= STR_SEASON_CLASS_SCHEDULE1 ?>
				<form method="post" action="season_class_student_schedule.php" target="_blank">
				<input type="hidden" name="class_type" value="season_class">
				<input type="hidden" name="mode" value="1">
				　　　　<input type="submit" value="表示">
				</form>
</td></tr>
<tr><td>6. <?= STR_SAT_SUN_CLASS_SCHEDULE1 ?>
				<form method="post" action="season_class_student_schedule.php" target="_blank">
				　　　　<input type="text" name="y" value="<?php echo $year; ?>" size="4">年&nbsp;
				<select name="m">
				<option value="1" <?= $selectm1 ?>>1-2月</option>
				<option value="3" <?= $selectm3 ?>>3-4月</option>
				<option value="5" <?= $selectm5 ?>>5-6月</option>
				<option value="7" <?= $selectm7 ?>>7-8月</option>
				<option value="9" <?= $selectm9 ?>>9-10月</option>
				<option value="11" <?= $selectm11 ?>>11-12月</option>
				</select>&nbsp;
				<input type="hidden" name="class_type" value="sat_sun_class">
				<input type="hidden" name="mode" value="1">
				<input type="submit" value="表示">
				</form>
</td></tr>
<tr><td>7. <?= STR_PAYSLIP ?>
				<form method="post" action="payslip.php">
				　　　　<input type="text" name="y" value="<?php echo $payy; ?>" size="4">年&nbsp;
				<select name="m">
				<option value="7" <?= $paym7 ?>>7月</option>
				<option value="8" <?= $paym8 ?>>8月</option>
				<option value="9" <?= $paym9 ?>>9月</option>
				<option value="10" <?= $paym10 ?>>10月</option>
				<option value="11" <?= $paym11 ?>>11月</option>
				<option value="12" <?= $paym12 ?>>12月</option>
				</select>&nbsp;
				<input type="submit" value="表示">
				</form>
<tr><td>8. <a href="./tatekae_edit.php"><?= STR_TATEKAE ?></a></td></tr>
<tr><td>9. <a href="./teacher_edit.php"><?= STR_TRANSPORT_COST ?></a></td></tr>
<tr><td>10. <a href="./set_password.php"><?= STR_SET_PASSWORD ?></a></td></tr>
<tr><td>　</td></tr>
<tr><td align="center"><a href="./login.php"><?= STR_LOGOUT ?></a></td></tr>
</table>
</div>
</body>
</html>