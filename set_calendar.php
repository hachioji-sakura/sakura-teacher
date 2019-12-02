<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF8">
<meta name="robots" content="noindex,nofollow">
<title>事務システム</title>
<style type="text/css">
<!--
 -->
</style>
<script type = "text/javascript">
<!--
-->
</script>
<link rel="stylesheet" type="text/css" href="./script/style.css">
<script type="text/javascript" src="./script/calender.js"></script>
</head>

<?php 
ini_set( 'display_errors', 0 );
require_once "./const/const.inc";
ini_set('include_path', CLIENT_LIBRALY_PATH);
require_once "Google/autoload.php";
$result = date_default_timezone_set('Asia/Tokyo');

// ****** メイン処理ここから ******

$calender_auth = new GoogleCalenderAuth();
$service = $calender_auth->getCalenderService();

mb_regex_encoding("UTF-8");

$data_array = @file('./log-event');

$i=0;
foreach ($data_array as $data) {
	$data_set[$i] = trim($data);
	$i++; if ($i<5) { continue; }
	$i=0;
	var_dump($data_set);echo'<br>';
	$event = new Google_Service_Calendar_Event(array(
		'summary' => $data_set[0],
		'start' => array(
			'dateTime' => $data_set[1],// 開始日時
			'timeZone' => 'Asia/Tokyo',
		),
		'end' => array(
			'dateTime' => $data_set[3], // 終了日時
			'timeZone' => 'Asia/Tokyo',
		),
	));
	var_dump($event);echo'<br>';
	$event = $service->events->insert('5cq09s0nsbu1qksfoceicl6sqc@group.calendar.google.com', $event);
	var_dump($event);echo'<br>';
}

// ****** メイン処理ここまで ******


class GoogleCalenderAuth {

	private static $client;
	private static $service;

	public static function getCalenderService() {
		if (! isset(self::$client)) {
			self::createClient();
		}
		
		if (! isset(self::$service)) {
			self::$service = new Google_Service_Calendar(self::$client);
		}
		return self::$service;
	}

	private static function createClient() {
		self::$client = new Google_Client();
		//self::$client->setApplicationName('Application Name');
		self::$client->setClientId(CLIENT_ID);

		$credential = new Google_Auth_AssertionCredentials(
												SERVICE_ACCOUNT_NAME,
												array('https://www.googleapis.com/auth/calendar'),
												file_get_contents(KEY_FILE)
											);
		self::$client->setAssertionCredentials($credential);
/*
	self::$client->setApplicationName( "calendar test" );
	self::$client->setAuthConfig( '/home/hachiojisakura/www/sakura00/schedule/const/calender-project-4228ed635fab.json' );
	self::$client->setScopes( ['https://www.googleapis.com/auth/calendar'] );
*/
	}
}

?>

<body>
<div align="center">

<?php
	if (count($errArray) > 0) {
		foreach( $errArray as $error) {
?>
			<font color="red"><?= $error ?></font><br><br>
<?php
		}
	}
?>
END
</div>
</body>
</html>