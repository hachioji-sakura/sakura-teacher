<?php

ini_set( 'display_errors', 0 );
require_once "../sakura/schedule/const/const.inc";
require_once "../sakura/schedule/func.inc";
ini_set('include_path', CLIENT_LIBRALY_PATH);
require_once "Google/autoload.php";
if (!$_SESSION['ulogin']['teacher_id']) { header('location: login.php'); exit(); }

mb_regex_encoding("UTF-8");

?>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<meta name="robots" content="index,follow">
<script type = "text/javascript">
<!--
//-->
</script>
</head>
<body>

<?php

$calender_id = $_GET['cal_id'];
$event_id    = $_GET['event_id'];
$name        = $_GET['name'];
$old_st      = $_GET['old_st'];
$new_st      = $_GET['new_st'];
$year        = $_GET['year'];
$month       = $_GET['month'];
$date        = $_GET['date'];
$time        = $_GET['time'];
$teacher_id  = $_GET['teacher_id'];
$member_no   = $_GET['member_no'];
$seq_no      = $_GET['seq_no'];

echo "$calender_id, $event_id<br>";
echo "$name, $old_st, $new_st,$year, $month, $date, $time, $teacher_id,$member_no<br>";
//log-event
file_put_contents('./log-set_attendance.txt', date("Y/m/d H:i:s -- ")."$calender_id, $event_id, $name, $old_st, $new_st, $year, $month, $date, $time, $teacher_id, $member_no\n", FILE_APPEND);

try {
	$db->beginTransaction();
	
	if ($event_id) {
		$calender_auth = new GoogleCalenderAuth();
		$service = $calender_auth->getCalenderService();

		$event = $service->events->get($calender_id, $event_id);
		$old_event_summary = $event->getSummary();
		echo $old_event_summary; echo'!!<br>';
		
		$new_event_summary = str_replace(array('　','（','）','：','︰'), array(' ','(',')',':',':'), $old_event_summary);
		$blocks[1]=array($new_event_summary);
		if (preg_match('/^(グループ|ファミリー)/iu',$new_event_summary)) {
			$ret = preg_match_all('/\((.*?)\)/u', $new_event_summary, $blocks);
		}
		if (!preg_match('/^ファミリー/iu',$new_event_summary)) {
			foreach ($blocks[1] as $key=>$block) {
				$ret = preg_match( '/([^():]+?)様/u', $block, $name_cal );
				if (!$ret) { continue; }
				if (str_replace(' ','',$name) != str_replace(' ','',$name_cal[1])) { continue; }
				$tmp = preg_replace( "/(\s*休み[12１２]\s*:\s*|\s*振替\s*:\s*|:\s*当日|:\s*休講|:\s*規定回数以上)/u", "", $block );
				if ($tmp!==false) {$block=$tmp;}
				$tmp = preg_replace( "/(\s*absent[12]\s*:\s*|\s*alternative\s*:\s*|\s*make.?up\s*:\s*|:\s*today|:\s*over.*?limit|:\s*no.*class)/iu", "", $block );
				if ($tmp!==false) {$block=$tmp;}
				switch ($new_st) {
					case "出席":
						break;
					case "休み１":
					case "休み２":
					case "振替":
						$block = $new_st.':'.$block;
						break;
					case "休み１当日":
						$block = '休み１:'.$block.':当日';
						break;
					case "休み１休講":
						$block = '休み１:'.$block.':休講';
						break;
					case "休み２当日":
						$block = '休み２:'.$block.':当日';
						break;
					case "休み２規定回数以上":
						$block = '休み２:'.$block.':規定回数以上';
						break;
					case "Attend":
						break;
					case "Absent1":
					case "Absent2":
					case "make-up":
						$block = $new_st.':'.$block;
						break;
					case "Absent1 Today":
						$block = 'Absent1:'.$block.':Today';
						break;
					case "Absent1 No class":
						$block = 'Absent1:'.$block.':No_class';
						break;
					case "Absent2 Today":
						$block = 'Absent2:'.$block.':Today';
						break;
					case "Absent2 over limit":
						$block = 'Absent2:'.$block.':Over_limit';
						break;
					default:
				}
				$new_event_summary = str_replace($blocks[1][$key], $block, $new_event_summary);
			}
		} else {
			$first_name = preg_replace('/^\S+ /u','',$name);
			$str0 = $blocks[1][0];
			$ret = preg_match_all('/(\S+)/u', $str0, $blocks);
			if (!$ret) { $blocks[1]=array($str0); }
			foreach ($blocks[1] as $key=>$block) {
				if ($block == '様') { break; }
				$tmp = preg_replace( "/(\s*休み[12１２]\s*:\s*|\s*振替\s*:\s*|:\s*当日|:\s*休講|:\s*規定回数以上)/u", "", $block );
				if ($tmp!==false) {$block=$tmp;}
				$tmp = preg_replace( "/(\s*absent[12]\s*:\s*|\s*alternative\s*:\s*|\s*make.?up\s*:\s*|:\s*today|:\s*over.*?limit|:\s*no.*?class)/iu", "", $block );
				if ($tmp!==false) {$block=$tmp;}
				if ($key>0) {
					if ($block != $first_name) { continue; }
					switch ($new_st) {
						case "出席":
							break;
						case "休み１":
						case "休み２":
						case "振替":
							$block = $new_st.':'.$block;
							break;
						case "休み１当日":
							$block = '休み１:'.$block.':当日';
							break;
						case "休み１休講":
							$block = '休み１:'.$block.':休講';
							break;
						case "休み２当日":
							$block = '休み２:'.$block.':当日';
							break;
						case "休み２規定回数以上":
							$block = '休み２:'.$block.':規定回数以上';
							break;
						case "Attend":
							break;
						case "Absent1":
						case "Absent2":
						case "make-up":
							$block = $new_st.':'.$block;
							break;
						case "Absent1 Today":
							$block = 'Absent1:'.$block.':Today';
							break;
						case "Absent1 No class":
							$block = 'Absent1:'.$block.':No_class';
							break;
						case "Absent2 Today":
							$block = 'Absent2:'.$block.':Today';
							break;
						case "Absent2 over limit":
							$block = 'Absent2:'.$block.':Over_limit';
							break;
						default:
					}
				}
				$new_event_summary = str_replace($blocks[1][$key], $block, $new_event_summary);
			}
			$ret = preg_match_all('/\((.*?)\)/', $new_event_summary, $blocks0);
			if (!$ret) { $blocks0[1]=array($new_event_summary); }
			$str0 = $blocks0[1][0];
			$ret = preg_match_all('/(\S+)/u', $str0, $blocks);
			if (!$ret) { $blocks[1]=array($str0); }
			$prefix_count = array();
			$postfix_count = array();
			$member_count = 0;
			$flag = 0;
			foreach ($blocks[1] as $key=>&$block) {
				if ($key == 0 || $flag) {
					$tmp = preg_replace( "/(\s*休み[12１２]\s*:\s*|\s*振替\s*:\s*|:\s*当日|:\s*休講|:\s*規定回数以上)/u", "", $block );
					if ($tmp!==false) {$block=$tmp;}
					$tmp = preg_replace( "/(\s*absent[12]\s*:\s*|\s*alternative\s*:\s*|\s*make.?up\s*:\s*|:\s*today|:\s*over.*?limit|:\s*no.*?class)/iu", "", $block );
					if ($tmp!==false) {$block=$tmp;}
					continue;
				}
				if ($block == '様') { $flag = 1; continue; }
				$member_count++;
				if (preg_match('/休み[1１]\s*:/u',$block)) { $prefix_count['休み１']++; }
				if (preg_match('/休み[2２]\s*:/u',$block)) { $prefix_count['休み２']++; }
				if (preg_match('/振替\s*:/u',$block))      { $prefix_count['振替']++; }
				if (preg_match('/absent1\s*:/iu',$block))  { $prefix_count['Absent1']++; }
				if (preg_match('/absent2\s*:/iu',$block))  { $prefix_count['Absent2']++; }
				if (preg_match('/alternative\s*:/iu',$block)) { $prefix_count['make-up']++; }
				if (preg_match('/make.?up\s*:/iu',$block)) { $prefix_count['make-up']++; }

				if (preg_match('/:\s*当日/u',$block))      { $postfix_count['当日']++; }
				if (preg_match('/:\s*休講/u',$block))      { $postfix_count['休講']++; }
				if (preg_match('/:\s*today/iu',$block))    { $postfix_count['Today']++; }
				if (preg_match('/:\s*No.*class/u',$block)) { $postfix_count['No_class']++; }
			}
			unset($block);
			$key = array_search( $member_count, $prefix_count );
			if ($key) { 
/*
				foreach($blocks[1] as &$block) {
					$tmp = preg_replace("/{$key}\s*:/", "", $block);
					if ($tmp!==false) { $block = $tmp; }
				}
				unset($block);
*/
				$blocks[1][0] = $key.':'.$blocks[1][0];
			}
			$key = array_search( $member_count, $postfix_count );
			if ($key) {
/*
				foreach($blocks[1] as &$block) {
					$tmp = preg_replace("/:\s*{$key}/", "", $block);
					if ($tmp!==false) { $block = $tmp; }
				}
				unset($block);
*/
				$blocks[1][] = ':'.$key;
			}
			$block = implode( ' ', $blocks[1] );
			$new_event_summary = str_replace($blocks0[1][0], $block, $new_event_summary);
		}
		echo $new_event_summary; echo'!!!<br>';

		$event->setSummary($new_event_summary);
		if (DB_NAME=='hachiojisakura_management' ||
				CLIENT_ID=='14999639448-ri4h8jg9m96n9t4djraq2hqcn7m1plj7.apps.googleusercontent.com') {
			$updatedEvent = $service->events->update($calender_id, $event_id, $event);
			// Print the updated date.
			echo $updatedEvent->getUpdated(); echo'<br>';
		}
	}
	
	$sql = "INSERT tbl_teacher_presence_report (teacher_id, year, month, date, time, member_no, name, presence, insert_timestamp, update_timestamp) ".
					"VALUES (?, ?, ?, ?, ?, ?, ?, ?, now(), now()) ".
					"ON DUPLICATE KEY UPDATE name=?, presence=?, update_timestamp=now()";
	$stmt = $db->prepare($sql);
	$stmt->execute(array($teacher_id, $year, $month, $date, $time, $member_no, $name, $new_st, $name, $new_st));
	$db->commit();

} catch (Exception $e) {
	$db->rollback();
	echo'error exit!!<br>';
	echo $e->getMessage();
}

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
	}
}

?>

<script type = "text/javascript">
<!--
	window.parent.set_attendance_done(<?= $seq_no ?>);
//-->
</script>
<?= $seq_no ?>-END
</body>
</html>
