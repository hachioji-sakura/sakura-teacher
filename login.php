<?php
require_once("../sakura/schedule/const/const.inc");
require_once("../sakura/schedule/func.inc");

unset($_SESSION['ulogin']);
if (isset($_POST['button'])) {
	$id=$_POST['id'];
	$password=$_POST['password'];
	$cmd = "SELECT * FROM tbl_teacher WHERE mail_address='$id'";
	$stmt = $db->prepare($cmd);
	$stmt->execute();
	$teachers = $stmt->fetch(PDO::FETCH_ASSOC);
	if ($id && $password && $teachers["password"] == openssl_encrypt($password, 'AES-128-ECB', PASSWORD_KEY)) {
		$_SESSION['ulogin'] = array('id' => $id, 'teacher_id' => $teachers["no"]);
		header('location: menu.php');
		exit();//忘れずに
	}
}
?>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF8">
<meta name="robots" content="noindex,nofollow">
<title>Sakura LOGIN</title>
<script type = "text/javascript">
<!--
//-->
</script>
<style>
</style>
</head>
<body>

<div id="content" align="center">
<form method="post" name="login" action="login.php">
<h2>八王子さくらアカデミー</h2>
<table>
	<tr>
		<th>ID</th>
		<td>
			<input type="text" name="id">
		</td>
	</tr>
	<tr>
		<th>パスワード</th>
		<td>
			<input type="password" name="password">
		</td>
	</tr>
	<tr>
		<td colspan="2" align="center">
			<input type="submit" value="ログイン" name="button">
		</td>
	</tr>
</table>
</form>
</div>
</body>
</html>
