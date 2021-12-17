<?php
ini_set( 'display_errors', 0 );
require_once("../sakura/schedule/const/const.inc");
require_once("../sakura/schedule/func.inc");
if (!$_SESSION['ulogin']['teacher_id']) { header('location: login.php'); exit(); }
set_time_limit(60);

$teacher_id	= $_SESSION['ulogin']['teacher_id'];

$teacher_list = get_teacher_list($db, array(), array(), array());
$teacher = $teacher_list[$teacher_id];

if ($teacher['lesson_id'] != 2) {
	define ('STR_TITLE1',        '八王子さくらアカデミー');
	define ('STR_TITLE2',        '講師:&nbsp;');
	define ('STR_CURRENT_PASS',  '現在のパスワード');
	define ('STR_NEW_PASSWORD1', '新しいパスワード');
	define ('STR_NEW_PASSWORD2', '新しいパスワード（再入力）');
	define ('STR_CHANGE',        '変更する');
	define ('STR_SET_PASSWORD',  'パスワード設定');
	define ('STR_LOGOUT',        'ログアウト');
	define ('STR_MAINMENU',      'メインメニューへ戻る');
	define ('STR_ERROR1',        '現在のパスワードが違います。');
	define ('STR_ERROR2',        '新しいパスワードと新しいパスワード（再入力）が一致しません。');
	define ('STR_ERROR3',        '新しいパスワードが空です。');
	define ('STR_COMMENT',       'パスワードは40文字までの任意の半角英数字記号が使用できます。');
} else {
	define ('STR_TITLE1',        'HACHIOJI SAKURA ACADEMY');
	define ('STR_TITLE2',        'Teacher:&nbsp;');
	define ('STR_CURRENT_PASS',  'Current password');
	define ('STR_NEW_PASSWORD1', 'New password');
	define ('STR_NEW_PASSWORD2', 'New password (retype)');
	define ('STR_CHANGE',        'Change password');
	define ('STR_SET_PASSWORD',  'Set password');
	define ('STR_LOGOUT',        'Logout');
	define ('STR_MAINMENU',      'Back to Main Menu');
	define ('STR_ERROR1',        'Current password is wrong.');
	define ('STR_ERROR2',        'New password and New password (retype) is not same.');
	define ('STR_ERROR3',        'New password is empty.');
	define ('STR_COMMENT',       'You can use any alpha numeric character (MAX 40 characters).');
}

if (isset($_POST['add'])) {
	$action = 'add';
} else {
	$action = "";
}

if ($action == 'add') {
	
	$password0 = trim($_POST['password0']);
	$password1 = trim($_POST['password1']);
	$password2 = trim($_POST['password2']);

	if ($teacher['password'] != openssl_encrypt($password0, 'AES-128-ECB', PASSWORD_KEY)){
		$errArray[] = STR_ERROR1;
	} else if ($password1!=$password2) {
		$errArray[] = STR_ERROR2;
	} else if ($password1=='') {
		$errArray[] = STR_ERROR3;
	} else {
		try{
			$db->beginTransaction();
			$sql = "UPDATE tbl_teacher SET ".
						" password=? , update_timestamp=now()".
						" WHERE no=?";
			$stmt = $db->prepare($sql);
			$stmt->bindParam(1, $password);
			$stmt->bindParam(2, $no);
			$no = $teacher["no"];
			$password = openssl_encrypt($password1, 'AES-128-ECB', PASSWORD_KEY);
			$stmt->execute();
			$db->commit();
			header('Location: menu.php');
			exit;
		}catch (PDOException $e){
			$db->rollback();
			array_push($errArray, 'Error:'.$e->getMessage());
		}
	}
} else {
}

?>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<meta name="robots" content="index,follow">
</head>
<body>
<div align="center">
<h3><?= STR_SET_PASSWORD ?></h3>
<h3><?= STR_TITLE2.$teacher_list[$teacher_id]['name'] ?></h3>
<?php
	if (count($errArray) > 0) {
		foreach( $errArray as $error) {
			echo "<font color=\"red\" size=\"5\">$error</font><br>";
		}
	}
?>

<form method="post" name="password_form" action="set_password.php">
<input type="hidden" name="id" value="<?= $_SESSION['ulogin']['id'] ?>">
<table>
<tr><td><?= STR_CURRENT_PASS  ?></a></td><td><input type="password" name="password0" size="20" maxlength="40"></td></tr>
<tr><td><?= STR_NEW_PASSWORD1 ?></a></td><td><input type="password" name="password1" size="20" maxlength="40"></td></tr>
<tr><td><?= STR_NEW_PASSWORD2 ?></a></td><td><input type="password" name="password2" size="20" maxlength="40"></td></tr>
<tr><td colspan="2"><?= STR_COMMENT ?></td></tr>
<tr><td colspan="2" align="center"><input type="submit" name="add" value="<?= STR_CHANGE ?>"></td></tr>
<tr><td colspan="2">&nbsp;</td></tr>
<tr><td colspan="2" align="center"><a href="./menu.php"><?= STR_MAINMENU ?></a></td></tr>
<tr><td colspan="2" align="center"><a href="./login.php"><?= STR_LOGOUT ?></a></td></tr>
</table>
</form>
</div>
</body>
</html>