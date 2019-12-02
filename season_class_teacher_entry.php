<?php
require_once("../sakura/schedule/const/const.inc");
require_once("../sakura/schedule/func.inc");
if (!$_SESSION['ulogin']['teacher_id']) { header('location: login.php'); exit(); }
$teacher_acount = 1;
require_once("../sakura/schedule/season_class_teacher_entry.php");
?>
