<?php
require_once("../sakura/schedule/const/const.inc");
require_once("../sakura/schedule/func.inc");
if (!$_SESSION['ulogin']['teacher_id']) { header('location: login.php'); exit(); }
$teacher_acount = 1; $mode='transport';
$last_month = strtotime(date('Y-m-1') . '-1 month');
$year = date('Y', $last_month); $month = date('n', $last_month);
require_once("../sakura/schedule/teacher_edit.php");
?>
