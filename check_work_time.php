<?php
ini_set( 'display_errors', 0 );
require_once(dirname(__FILE__)."/../sakura/schedule/const/const.inc");
require_once(dirname(__FILE__)."/../sakura/schedule/func.inc");
ini_set('include_path', CLIENT_LIBRALY_PATH);
require_once "Google/autoload.php";
set_time_limit(60);

if (!$teacher_acount)	$mode = $_GET['mode'];

if ($_SESSION['login']['id'] == 'hachiojisakura' && $_GET['tid']) {
	$teacher_id = $_GET['tid'];
	if (!$mode) $mode = 'pay_viewonly';
} else {
	$teacher_id	= $_SESSION['ulogin']['teacher_id'];
	if (!$teacher_id) {
		header('location: login.php');
		exit();
	}
}
$errArray = array();
$errFlag = 0;

if (!$year)		$year = $_POST['y'];
if (!$month)	$month = $_POST['m'];
if (is_null($year) === true || empty($year) === true)   { $year = $_GET['y']; }
if (is_null($month) === true || empty($month) === true) { $month = $_GET['m']; }
if (is_null($year) === true || empty($year) === true)   { $year = date("Y"); }
if (is_null($month) === true || empty($month) === true) { $month = date("n"); }

$y1=$year; $m1=$month-1; if ($m1<1) { $y1--; $m1=12; }
$y2=$year; $m2=$month+1; if ($m2>12) { $y2++; $m2=1; }

$date_list = $date_list_array["$year/$month"];
if (!$date_list)	$date_list = $date_list_array["$y1/$m1"];
if (!$date_list)	$date_list = array();

$season_class_date_list = $date_list;
$date_list = array_merge($date_list, $sat_sun_class_date_list);
$date_list = array_filter($date_list,function($d){global $year,$month; return (str_replace('/0','/',substr($d,0,7))=="$year/$month");});

$date_list_string = "("; $flag=0;
foreach ($date_list as $item) {
	if ($flag==0) { $date_list_string .= "'$item'"; } else { $date_list_string .= ",'$item'"; }
	$flag = 1;
}
$date_list_string = $date_list_string.")";

class Kana2Romaji{
    function convert($str)
    {
        $str = mb_convert_kana($str, 'cHV', 'utf-8');
 
        $kana = array(
            'きゃ', 'きぃ', 'きゅ', 'きぇ', 'きょ',
            'ぎゃ', 'ぎぃ', 'ぎゅ', 'ぎぇ', 'ぎょ',
            'くぁ', 'くぃ', 'くぅ', 'くぇ', 'くぉ',
            'ぐぁ', 'ぐぃ', 'ぐぅ', 'ぐぇ', 'ぐぉ',
            'しゃ', 'しぃ', 'しゅ', 'しぇ', 'しょ',
            'じゃ', 'じぃ', 'じゅ', 'じぇ', 'じょ',
            'ちゃ', 'ちぃ', 'ちゅ', 'ちぇ', 'ちょ',
            'ぢゃ', 'ぢぃ', 'ぢゅ', 'ぢぇ', 'ぢょ',
            'つぁ', 'つぃ', 'つぇ', 'つぉ',
            'てゃ', 'てぃ', 'てゅ', 'てぇ', 'てょ',
            'でゃ', 'でぃ', 'でぅ', 'でぇ', 'でょ',
            'とぁ', 'とぃ', 'とぅ', 'とぇ', 'とぉ',
            'にゃ', 'にぃ', 'にゅ', 'にぇ', 'にょ',
            'ヴぁ', 'ヴぃ', 'ヴぇ', 'ヴぉ',
            'ひゃ', 'ひぃ', 'ひゅ', 'ひぇ', 'ひょ',
            'ふぁ', 'ふぃ', 'ふぇ', 'ふぉ',
            'ふゃ', 'ふゅ', 'ふょ',
            'びゃ', 'びぃ', 'びゅ', 'びぇ', 'びょ',
            'ヴゃ', 'ヴぃ', 'ヴゅ', 'ヴぇ', 'ヴょ',   
            'ぴゃ', 'ぴぃ', 'ぴゅ', 'ぴぇ', 'ぴょ',
            'みゃ', 'みぃ', 'みゅ', 'みぇ', 'みょ',   
            'りゃ', 'りぃ', 'りゅ', 'りぇ', 'りょ',
            'うぃ', 'うぇ', 'いぇ'
        );
         
        $romaji  = array(
            'kya', 'kyi', 'kyu', 'kye', 'kyo',
            'gya', 'gyi', 'gyu', 'gye', 'gyo',
            'qwa', 'qwi', 'qwu', 'qwe', 'qwo',
            'gwa', 'gwi', 'gwu', 'gwe', 'gwo',
            'sya', 'syi', 'syu', 'sye', 'syo',
            'ja', 'jyi', 'ju', 'je', 'jo',
            'cha', 'cyi', 'chu', 'che', 'cho',
            'dya', 'dyi', 'dyu', 'dye', 'dyo',
            'tsa', 'tsi', 'tse', 'tso',
            'tha', 'ti', 'thu', 'the', 'tho',
            'dha', 'di', 'dhu', 'dhe', 'dho',
            'twa', 'twi', 'twu', 'twe', 'two',
            'nya', 'nyi', 'nyu', 'nye', 'nyo',
            'va', 'vi', 've', 'vo',
            'hya', 'hyi', 'hyu', 'hye', 'hyo',
            'fa', 'fi', 'fe', 'fo',
            'fya', 'fyu', 'fyo',
            'bya', 'byi', 'byu', 'bye', 'byo',
            'vya', 'vyi', 'vyu', 'vye', 'vyo',
            'pya', 'pyi', 'pyu', 'pye', 'pyo',
            'mya', 'myi', 'myu', 'mye', 'myo',
            'rya', 'ryi', 'ryu', 'rye', 'ryo',
            'wi', 'we', 'ye'
        );
         
        $str = $this->kana_replace($str, $kana, $romaji);
 
        $kana = array(
            'あ', 'い', 'う', 'え', 'お',
            'か', 'き', 'く', 'け', 'こ',
            'さ', 'し', 'す', 'せ', 'そ',
            'た', 'ち', 'つ', 'て', 'と',
            'な', 'に', 'ぬ', 'ね', 'の',
            'は', 'ひ', 'ふ', 'へ', 'ほ',
            'ま', 'み', 'む', 'め', 'も',
            'や', 'ゆ', 'よ',
            'ら', 'り', 'る', 'れ', 'ろ',
            'わ', 'ゐ', 'ゑ', 'を', 'ん',
            'が', 'ぎ', 'ぐ', 'げ', 'ご',
            'ざ', 'じ', 'ず', 'ぜ', 'ぞ',
            'だ', 'ぢ', 'づ', 'で', 'ど',
            'ば', 'び', 'ぶ', 'べ', 'ぼ',
            'ぱ', 'ぴ', 'ぷ', 'ぺ', 'ぽ'
        );
         
        $romaji = array(
            'a', 'i', 'u', 'e', 'o',
            'ka', 'ki', 'ku', 'ke', 'ko',
            'sa', 'shi', 'su', 'se', 'so',
            'ta', 'chi', 'tsu', 'te', 'to',
            'na', 'ni', 'nu', 'ne', 'no',
            'ha', 'hi', 'fu', 'he', 'ho',
            'ma', 'mi', 'mu', 'me', 'mo',
            'ya', 'yu', 'yo',
            'ra', 'ri', 'ru', 're', 'ro',
            'wa', 'wyi', 'wye', 'wo', 'n',
            'ga', 'gi', 'gu', 'ge', 'go',
            'za', 'ji', 'zu', 'ze', 'zo',
            'da', 'ji', 'du', 'de', 'do',
            'ba', 'bi', 'bu', 'be', 'bo',
            'pa', 'pi', 'pu', 'pe', 'po'
        );
         
        $str = $this->kana_replace($str, $kana, $romaji);
         
        $str = preg_replace('/(っ$|っ[^a-z])/u', "xtu", $str);
        $res = preg_match_all('/(っ)(.)/u', $str, $matches);
        if(!empty($res)){
            for($i=0;isset($matches[0][$i]);$i++){
                if($matches[0][$i] == 'っc') $matches[2][$i] = 't';
                $str = preg_replace('/' . $matches[1][$i] . '/u', $matches[2][$i], $str, 1);
            }
        }
         
        $kana = array(
            'ぁ', 'ぃ', 'ぅ', 'ぇ', 'ぉ',
            'ヵ', 'ヶ', 'っ', 'ゃ', 'ゅ', 'ょ', 'ゎ', '、', '。', '　'
        );
         
        $romaji = array(
            'a', 'i', 'u', 'e', 'o',
            'ka', 'ke', 'xtu', 'xya', 'xyu', 'xyo', 'xwa', ', ', '.', ' '
        );
        $str = $this->kana_replace($str, $kana, $romaji);
         
        $str = preg_replace('/^ー|[^a-z]ー/u', '', $str);
        $res = preg_match_all('/(.)(ー)/u', $str, $matches);
 
        if($res){
            for($i=0;isset($matches[0][$i]);$i++){
                if( $matches[1][$i] == "a" ){ $replace = 'â'; }
                else if( $matches[1][$i] == "i" ){ $replace = 'î'; }
                else if( $matches[1][$i] == "u" ){ $replace = 'û'; }
                else if( $matches[1][$i] == "e" ){ $replace = 'ê'; }
                else if( $matches[1][$i] == "o" ){ $replace = 'ô'; }
                else { $replace = ""; }
                 
                $str = preg_replace('/' . $matches[0][$i] . '/u', $replace, $str, 1);
            }
        }
         
        return $str;
    }
 
    function kana_replace($str, $kana, $romaji)
    {
        $patterns = array();
        foreach($kana as $value){
            $patterns[] = '/' . $value . '/';
        }
         
        $str = preg_replace($patterns, $romaji, $str);
        return $str;
    }
}

$kana2romaji = new Kana2Romaji;

function eng_name1($str) {
	$str = ucwords($str);
	$array = explode(' ',$str,3);
	$str = $array[1].' '.$array[0].' '.$array[2];
	return $str;
}

try {
	
$stmt = $db->query("SELECT fixed FROM tbl_fixed WHERE year=\"$year\" AND month=\"$month\"");
$rslt = $stmt->fetch(PDO::FETCH_ASSOC);
if ($rslt['fixed']) $fixed = 1;

if ($mode!='pay_viewonly' && $mode!='check' && $mode!='transport') {
$sql = "
CREATE TEMPORARY TABLE `tbl_calender_event` (
  `event_id` varchar(100) NOT NULL DEFAULT '',
  `event_start_timestamp` varchar(20) DEFAULT NULL,
  `event_end_timestamp` varchar(20) DEFAULT NULL,
  `calender_id` varchar(100) DEFAULT NULL,
  `calender_summary` varchar(80) DEFAULT NULL,
  `event_summary` varchar(200) DEFAULT NULL,
  `event_location` varchar(80) DEFAULT NULL,
  `event_description` varchar(200) DEFAULT NULL,
  `event_updated_timestamp` varchar(80) DEFAULT NULL,
  `seikyu_year` int(4) DEFAULT NULL,
  `seikyu_month` int(2) DEFAULT NULL,
  `insert_datetime` datetime DEFAULT NULL,
  `update_datetime` datetime DEFAULT NULL,
  `recurringEvent` varchar(1) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8";
$res = $db->query($sql);
if (!$res) {
	echo 'CREATE TEMPORARY TABLE `tbl_calender_event` error';
	exit();
}

$sql = "
CREATE TEMPORARY TABLE `tbl_event` (
  `event_no` int(11) NOT NULL AUTO_INCREMENT,
  `event_id` varchar(100) NOT NULL DEFAULT '',
  `member_no` varchar(20) DEFAULT NULL,
  `member_id` varchar(20) DEFAULT NULL,
  `member_cal_name` varchar(80) DEFAULT NULL,
  `member_kind` varchar(10) DEFAULT NULL,
  `event_year` varchar(4) DEFAULT NULL,
  `event_month` varchar(2) DEFAULT NULL,
  `event_day` varchar(2) DEFAULT NULL,
  `event_start_timestamp` varchar(20) DEFAULT NULL,
  `event_start_hour` varchar(2) DEFAULT NULL,
  `event_start_minute` varchar(2) DEFAULT NULL,
  `event_end_timestamp` varchar(20) DEFAULT NULL,
  `event_end_hour` varchar(2) DEFAULT NULL,
  `event_end_minute` varchar(2) DEFAULT NULL,
  `event_diff_hours` float(4,2) DEFAULT '0.00',
  `lesson_id` varchar(2) DEFAULT NULL,
  `subject_id` varchar(2) DEFAULT NULL,
  `course_id` varchar(2) DEFAULT NULL,
  `teacher_id` varchar(2) DEFAULT NULL,
  `place_id` varchar(2) DEFAULT NULL,
  `absent_flag` varchar(2) DEFAULT NULL,
  `trial_flag` varchar(2) DEFAULT NULL,
  `interview_flag` varchar(2) DEFAULT NULL,
  `alternative_flag` varchar(2) DEFAULT NULL,
  `absent1_num` int(4) DEFAULT NULL,
  `absent2_num` int(4) DEFAULT NULL,
  `trial_num` int(4) DEFAULT NULL,
  `repeat_flag` varchar(2) DEFAULT NULL,
  `cal_id` varchar(100) DEFAULT NULL,
  `cal_summary` varchar(80) DEFAULT NULL,
  `cal_evt_summary` varchar(200) DEFAULT NULL,
  `cal_attendance_data` varchar(100) DEFAULT NULL,
  `cal_evt_location` varchar(80) DEFAULT NULL,
  `cal_evt_description` varchar(200) DEFAULT NULL,
  `cal_evt_updated_timestamp` varchar(80) DEFAULT NULL,
  `insert_datetime` datetime DEFAULT NULL,
  `update_datetime` datetime DEFAULT NULL,
  `seikyu_year` int(4) DEFAULT NULL,
  `seikyu_month` int(2) DEFAULT NULL,
  `recurringEvent` varchar(1) DEFAULT NULL,
  `grade` varchar(20) DEFAULT NULL,
  `monthly_fee_flag` tinyint(1) DEFAULT NULL,
  PRIMARY KEY (`event_no`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8";
$res = $db->query($sql);
if (!$res) {
	echo 'CREATE TEMPORARY TABLE `tbl_event` error';
	exit();
}

$result = true;
$err_flag = false;
$target_teacher_id = $teacher_id;
$tid = $teacher_id;
$kari_ignore = '1';

require_once dirname(__FILE__)."/../sakura/schedule/get_calender_data.inc";
require_once dirname(__FILE__)."/../sakura/schedule/edit_calender_data.inc";
require_once dirname(__FILE__)."/../sakura/schedule/check_calender_data.inc";

$teacher_id = $target_teacher_id;
}
$course_list = get_course_list($db);
$member_list = get_member_list($db, array(), array(), array(), 1);

if (!$teacher_acount) {
	// 先生一覧を取得
	$teacher_list = get_teacher_list($db, array(), array(), array(), 1);
	$teacher = $teacher_list[$teacher_id];
	$teacher['transport_dcost1'][0] = $teacher['transport_dcost1_Sun'];
	$teacher['transport_dcost1'][1] = $teacher['transport_dcost1_Mon'];
	$teacher['transport_dcost1'][2] = $teacher['transport_dcost1_Tue'];
	$teacher['transport_dcost1'][3] = $teacher['transport_dcost1_Wen'];
	$teacher['transport_dcost1'][4] = $teacher['transport_dcost1_Thr'];
	$teacher['transport_dcost1'][5] = $teacher['transport_dcost1_Fri'];
	$teacher['transport_dcost1'][6] = $teacher['transport_dcost1_Sat'];
}

$attendStatusList_jp = $attendStatusList;

if ($teacher['lesson_id'] != 2 
		|| ($teacher['lesson_id2'] && $teacher['lesson_id2'] != 2)) {
	define(STR_SHUSSEKIBO,             '出席簿');
	define(STR_YEN,                    '円');
	define(STR_SHUSSEKI,               '出席');
	define(STR_FURIKAE,                '振替');
	define(STR_TOUJITSU,               '当日');
	define(STR_KYUUKOU,                '休講');
	define(STR_YASUMI,                 '休み');
	define(STR_CHANGE_CONFIRM,         'カレンダー設定を変更してよろしいですか？ ');
	define(STR_FURIKAE_CONFIRM1,       '毎週繰り返し予定の授業ではありません。「振替」ではなく「出席」でよろしいですか？');
	define(STR_FURIKAE_CONFIRM2,       '毎週繰り返し予定の授業です。「出席」ではなく「振替」でよろしいですか？');
	define(STR_OVERLOAD_ERROR,         '過負荷エラー発生、再登録してください。');
	define(STR_YEAR,                   '年');
	define(STR_MONTH,                  '月');
	define(STR_PREVIOUS_MONTH,         '前月');
	define(STR_NEXT_MONTH,             '翌月');
	define(STR_YASUMI1,                '休み１');
	define(STR_YASUMI2,                '休み２');
	define(STR_CALENDAR_ERROR,         'カレンダー登録エラー');
	define(STR_CALENDAR_NAME,          'カレンダー名');
	define(STR_DATE,                   '日付');
	define(STR_START_TIME,             '開始時間');
	define(STR_END_TIME,               '終了時間');
	define(STR_TITLE,                  'タイトル');
	define(STR_ERROR,                  'エラー');
	define(STR_PAYMENT_DISPLAY_SWITCH, '給与表示ON/OFF');
	define(STR_LOGOUT,                 'ログアウト');
	define(STR_TIME,                   '時刻');
	define(STR_HOURS,                  '時間');
	define(STR_KYOUSHITSU,             '教室');
	define(STR_KAMOKU,                 '科目');
	define(STR_COURSE,                 'コース');
	define(STR_NAME,                   '生徒名');
	define(STR_ATTENDANCE,             '生徒出欠');
	define(STR_CALNDAR_STATUS,         'カレンダー/事務登録');
	define(STR_WAGE,                   '時給');
	define(STR_PAYMENT,                '給与');
	define(STR_COMMENT1,               '＊赤字はお休みの生徒です。');
	define(STR_COMMENT2,               '＊青字は体験生徒です。');
	define(STR_COMMENT3,               '＊背景淡緑色は毎週繰り返し予定でないスポットの授業です。');
	define(STR_COMMENT4,               '＊時給未設定の項目については、支給時までに確定します。');
	define(STR_GOTO_MENU,              'メニューへ戻る');
	define(STR_PLACE,                  '校舎');
	define(STR_WAGE_UNDECIDED,         '時給未設定');
	define(STR_GRADE_UNKNOWN,          '学年不明');
	define(STR_TRANSPORT,              '曜日別交通費');
	define(STR_TRANSPORT_CORRECT,      '修正欄');
	define(STR_TRANSPORT_COMMENT,      '備考欄');
} else {
	$attendStatusList = $attendStatusList_eng;
	$weekday_array = $weekday_array_eng;
	define(STR_SHUSSEKIBO,             'Attendance record');
	define(STR_YEN,                    'yen');
	define(STR_SHUSSEKI,               'Attend');
	define(STR_FURIKAE,                'make-up');
	define(STR_TOUJITSU,               'Today');
	define(STR_KYUUKOU,                'No class');
	define(STR_YASUMI,                 'Absent');
	define(STR_CHANGE_CONFIRM,         'Aye you sure you change calendar status? ');
	define(STR_FURIKAE_CONFIRM1,       'It is not a regularly class. Are you sure you select Absent instead of make-up?');
	define(STR_FURIKAE_CONFIRM2,       'It is a regularly class. Are you sure you select make-up instead of Absent?');
	define(STR_OVERLOAD_ERROR,         'Overload error. Please retry.');
	define(STR_YEAR,                   '/');
	define(STR_MONTH,                  '');
	define(STR_PREVIOUS_MONTH,         'Previous month');
	define(STR_NEXT_MONTH,             'Next month');
	define(STR_YASUMI1,                'Absent1');
	define(STR_YASUMI2,                'Absent2');
	define(STR_CALENDAR_ERROR,         'Calendar error');
	define(STR_CALENDAR_NAME,          'Calendar name');
	define(STR_DATE,                   'date');
	define(STR_START_TIME,             'Start time');
	define(STR_END_TIME,               'End time');
	define(STR_TITLE,                  'Title');
	define(STR_ERROR,                  'Error');
	define(STR_PAYMENT_DISPLAY_SWITCH, 'Payment display ON/OFF');
	define(STR_LOGOUT,                 'Logout');
	define(STR_TIME,                   'Time');
	define(STR_HOURS,                  'Hours');
	define(STR_KYOUSHITSU,             'Class');
	define(STR_KAMOKU,                 'Subject');
	define(STR_COURSE,                 'Course');
	define(STR_NAME,                   'Name');
	define(STR_ATTENDANCE,             'Attendance');
	define(STR_CALNDAR_STATUS,         'Calendar data');
	define(STR_WAGE,                   'Wage');
	define(STR_PAYMENT,                'Payment');
	define(STR_COMMENT1,               '* Red name is an absent student.');
	define(STR_COMMENT2,               '* Blue name is an trial student.');
	define(STR_COMMENT3,               '* Light green is a spot (irregularly) class.');
	define(STR_COMMENT4,               "* Wage 'undecided' is fixed untill pay day.");
	define(STR_GOTO_MENU,              'Go to menu');
	define(STR_PLACE,                  'place');
	define(STR_WAGE_UNDECIDED,         'undecided');
	define(STR_GRADE_UNKNOWN,          'grade unknown');
	define(STR_TRANSPORT,              'transportation expenses');
	define(STR_TRANSPORT_CORRECT,      'correct expenses');
	define(STR_TRANSPORT_COMMENT,      'comment');
}

if ($mode != 'transport') {

?>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<meta name="robots" content="index,follow">
<style type="text/css">
<!--
#loading-view {
 /* 領域の位置やサイズに関する設定 */
 width: 100%;
 height: 100%;
 z-index: 9999;
 position: fixed;
 top: 0;
 left: 0;
 /* 背景関連の設定 */
 background-color: #FFFFFF;
 opacity: 0.5;
 background-image: url(./loading.gif);
 background-position: center center;
 background-repeat: no-repeat;
 background-attachment: fixed;
}
 -->
</style>
<script type = "text/javascript">
<!--
var loadingflag=0;
function loadingView(flag) {
	if (loadingflag) return;
	if (flag) {
		document.getElementById('loading-view').style.display = 'block';
	} else {
		document.getElementById('loading-view').style.display = 'none';
	}
}

var wage_list = [],diff_hours_list = [];
var URIstr=['','','','','','','','','',''];
var pay_display_flag = 0;
function pay_display() {
	var pay = document.getElementsByName('pay');
	for (var i=0;i<pay.length;i++) {
		pay[i].style.display = (pay_display_flag)? "":"none";
	}
	pay_display_flag = 1-pay_display_flag;
	return false;
}
function update_totalpay() {
	var sum=0;
	for (var i=1;i<wage_list.length;i++) {
		var str = document.getElementById('pay'+i).innerHTML.replace("<?= STR_YEN ?>","");
		if (str) { sum += parseFloat(str); }
	}
	document.getElementById('pay_total').innerHTML = Math.floor(sum)+"<?= STR_YEN ?>";
}
function set_pay(id1){
	var course_name = document.getElementsByName('course_name');
	var stSelect = document.getElementsByName('stSelect'+id1);
	var flag1=0,flag2=0,flag3=1;
	var pay_hours = parseFloat(diff_hours_list[id1]);
	var wage = parseInt(wage_list[id1]);
	if (stSelect.length) {
		for (var i=0;i<stSelect.length;i++) {
			var str = (stSelect[i].nodeName=='SELECT')?stSelect[i].options[stSelect[i].selectedIndex].value:str = stSelect[i].innerHTML;
			if (str.indexOf("<?= STR_SHUSSEKI ?>") !== -1) { flag1=1; }
			if (str.indexOf("<?= STR_FURIKAE  ?>") !== -1) { flag1=1; }
			if (str.indexOf("<?= STR_TOUJITSU ?>") !== -1) { flag2=1; }
			if (str=='') { flag3=0; }
		}
		if (course_name[id1-1].innerText.indexOf("講習")!=-1) {
			var str = (stSelect[0].nodeName=='SELECT')?stSelect[0].options[stSelect[0].selectedIndex].value:str = stSelect[0].innerHTML;
			if (str.indexOf("<?= STR_YASUMI ?>") !== -1 && str.indexOf("<?= STR_TOUJITSU ?>") == -1) { wage = 0; }
		} else {
			if (flag1 == 0) { 
				if (flag2) {
					wage *= 0.6; if (wage<1000) wage=1000; 
				} else {
					wage = 0;
				}
			}
		}
	}
	if (stSelect.length>0 && !flag3) {
		document.getElementById('pay'+id1).innerHTML = '';
	} else {
		document.getElementById('pay'+id1).innerHTML = (wage * pay_hours) + "<?= STR_YEN ?>";
	}
}
function set_attendance(obj, cal_id, event_id, name, old_index, year, month, date, time, teacher_id, member_no, recurringEvent, id1, id2, trial) {
	var i;
	var new_st = obj.options[obj.selectedIndex].value;
	if (!obj.current_index) { obj.current_index = old_index+1; }
	var old_st = obj.options[obj.current_index-1].value;
	var cal_st = document.getElementById('cal'+id2);
	var seasonClassFlag = (document.getElementById('name'+id2).parentNode.previousElementSibling.innerText.indexOf("講習")!=-1);
	if  (cal_st && !seasonClassFlag) {
		if (!(cal_st.innerHTML == '　　　　　　' && new_st == "<?= STR_SHUSSEKI ?>") && (cal_st.innerHTML != new_st)) {
			if (!confirm("<?= STR_CHANGE_CONFIRM ?>"+cal_st.innerHTML+"->"+new_st)) {
				obj.selectedIndex = obj.current_index-1; return;
			}
		}
		if  (!recurringEvent && new_st=="<?= STR_SHUSSEKI ?>" && !trial) {
			if (!confirm("<?= STR_FURIKAE_CONFIRM1 ?>")) {
				obj.selectedIndex = obj.current_index-1; return;
			}
		}
		if  (recurringEvent && new_st=="<?= STR_FURIKAE  ?>") {
			if (!confirm("<?= STR_FURIKAE_CONFIRM2 ?>")) {
				obj.selectedIndex = obj.current_index-1; return;
			}
		}
	}
	var flag=1;
	for (i=0;i<10;i++) { if (URIstr[i]!='') { flag=0; } }
	for (i=0;i<10;i++) {
		if (URIstr[i]=='') {
			URIstr[i] = encodeURI(
				'./set_attendance.php?cal_id='+cal_id+'&event_id='+event_id+'&name='+name+'&old_st='+old_st+'&new_st='+new_st+
				'&year='+year+'&month='+month+'&date='+date+'&time='+time+'&teacher_id='+teacher_id+'&member_no='+member_no+
				'&seq_no='+i);
//    var div_element = document.createElement("span");
//    div_element.innerText ='+'+i+'+';
// 		document.getElementById('debug').appendChild(div_element);
			break;
		}
	}
	if (i>=10) { alert("<?= STR_OVERLOAD_ERROR ?>"); loadingView(true); loadingflag=1; location.reload(); }
	if (i>5) {
		loadingView(true);
	}
	if (flag) {
			document.getElementsByName('frame1')[0].contentWindow.location.replace( URIstr[i] );
	}
	obj.current_index = obj.selectedIndex+1;
	document.getElementById('name'+id2).style.color = (new_st.indexOf("<?= STR_YASUMI ?>")!=-1)? "red": "black";
	if (trial) { document.getElementById('name'+id2).style.color = "blue"; }
	if (cal_st && !seasonClassFlag) { cal_st.innerHTML = (new_st!="<?= STR_SHUSSEKI ?>")? new_st: ''; cal_st.style = "background-color:#FFFFFF;"; }

	if (seasonClassFlag) { set_exercise(id1,(new_st.indexOf("<?= STR_YASUMI ?>")!=-1 && new_st!="<?= STR_YASUMI2 ?><?= STR_TOUJITSU ?>")); }
	set_pay(id1);
	update_totalpay();
}

function set_attendance_done(seq_no) {
	var i;
	URIstr[seq_no] = ''; 
//    var div_element = document.createElement("span");
//    div_element.innerText ='-'+seq_no+'-';
// 		document.getElementById('debug').appendChild(div_element);
	for (i=0;i<10;i++) {
		if (URIstr[i]!='') {
			document.getElementsByName('frame1')[0].contentWindow.location.replace( URIstr[i] );
			return;
		}
	}
	loadingView(false);
}
function set_exercise(id0, flag) {
	var course_name = document.getElementsByName('course_name');
	var node_tr0 = course_name[id0-1].parentNode;
	var node_tr1 = course_name[id0-1].parentNode;
	while (node_tr1.children[1].innerText.indexOf("/")==-1) { node_tr1=node_tr1.previousElementSibling; }
	var node_td1 = node_tr1.children[5];
	while (node_td1.innerText.indexOf("演習")==-1) {
		node_tr1 = node_tr1.nextElementSibling;
		node_td1 = node_tr1.children[4];
		if (node_tr1.children[1].innerText.indexOf("/")!==-1) { break; }
	}
	node_td1 = (node_tr1.children[1].innerText.indexOf("/")!==-1)?node_tr1.children[2]:node_tr1.children[1];
	var ret, diff_hours;
	if (node_tr0.children[1].innerText.indexOf("/")!==-1) {
		ret = updateExerciseTime(node_td1.innerHTML,node_tr0.children[2].innerHTML,flag);
	} else {
		ret = updateExerciseTime(node_td1.innerHTML,node_tr0.children[1].innerHTML,flag);
	}
	node_td1.innerHTML = ret[0];
	diff_hours = ret[1];
	node_td1.nextElementSibling.innerText = diff_hours;
	var id1 = node_tr1.children[0].innerText;
	diff_hours_list[id1] = diff_hours;
	set_pay(id0);
	set_pay(id1);
}
function updateExerciseTime(str1, str2, flag) {
	var array=[];
	for (var i=0,j=0;i<48;i++) { array[i] = 0; }
	var str3="", count=0;
	var nums=str1.match(/\d\d/g);
	var sn=[],en=[];
	if (nums) {
		for (var i=0;i<nums.length;i+=4) {
			sn.push(nums[i]*2+(nums[i+1]/30));
			en.push(nums[i+2]*2+(nums[i+3]/30));
		}
		for (var i=0,j=0;i<48;i++) {
			if (sn[j]<=i && i<en[j]) { array[i]=1; }
			if (i==en[j] && j<sn.length) { j++; }
		}
	}
	sn = []; en = [];
	nums = str2.match(/\d\d/g);
	if (nums) {
		for (var i=0;i<nums.length;i+=4) {
			sn.push(nums[i]*2+(nums[i+1]/30));
			en.push(nums[i+2]*2+(nums[i+3]/30));
		}
		for (var i=0,j=0;i<48;i++) {
			if (sn[j]<=i && i<en[j]) { array[i]=flag; }
			if (i==en[j] && j<sn.length) { j++; }
		}
	}
	for (var i=1;i<48;i++) {
		var strh, strm;
		if (!array[i-1] && array[i]) {
			strh = Math.floor(i/2); if (strh<10) { strh += "0"; }
			strm = (i%2)?'30':'00';
			if (str3) { str3 += "<br>"; }
			str3 += (strh+':'+strm+' ～ ');
		}
		if (array[i-1] && !array[i]) {
			strh = Math.floor(i/2); if (strh<10) { strh += "0"; }
			strm = (i%2)?'30':'00';
			str3 += (strh+':'+strm+'');
		}
		if (array[i]) { count++; }
	}
	return new Array(str3,count/2);
}
//-->
</script>
</head>
<body>
<div id="content" align="center">
<h3><?= ($mode=='pay_viewonly')?'給与計算詳細':STR_SHUSSEKIBO ?></h3>
<h3><?= $teacher["name"] ?></h3>
<h3><?= $year.STR_YEAR.$month.STR_MONTH ?></h3>
<?php if ($mode!='pay_viewonly') { ?>
<a href='check_work_time.php?y=<?= $y1 ?>&m=<?= $m1 ?>' onclick="loadingView(true)"><?= STR_PREVIOUS_MONTH ?></a>&nbsp;&nbsp;&nbsp;&nbsp;
<a href='check_work_time.php?y=<?= $y2 ?>&m=<?= $m2 ?>' onclick="loadingView(true)"><?= STR_NEXT_MONTH ?></a>
<br>
<?php } ?>
<div id="loading-view" style="display:none"></div>
<iframe name="frame1" width=1000 height=400 style="display:none;"></iframe>
<div id="debug"></div>
<!--
<input type="button" value="test1" onclick="test(1)"><input type="button" value="test2" onclick="test(2)"><br>
-->
<?php
if (count($errArray) > 0) {
?>
	<font color="red"><?= STR_CALENDAR_ERROR ?></font><br>
<table border="1">
	<tr>
		<th><?= STR_CALENDAR_NAME ?></th><th><?= STR_DATE ?></th><th><?= STR_START_TIME ?></th><th><?= STR_END_TIME ?></th><th><?= STR_TITLE ?></th><th><?= STR_ERROR ?></th>
	</tr>
<?php
	foreach ($errArray as $error) {
?>
	<tr>
		<td>
			<?= $error["calender_summary"]?>
		</td>
		<td>
			<?= $error["date"]?>
		</td>
		<td>
			<?= $error["start_time"]?>
		</td>
		<td>
			<?= $error["end_time"]?>
		</td>
		<td>
			<?= $error["summary"]?>
		</td>
		<td>
			<?= $error["message"]?>
		</td>
	</tr>
<?php
	}
?>
</table>
<?php
$errArray=array();
}
} // mode != 'transport'

$sql = "SELECT e.cal_summary, e.cal_evt_summary, e.cal_id, e.course_id, e.event_end_timestamp, e.event_start_timestamp, ".
		"e.grade, e.lesson_id, e.member_cal_name, e.member_no, e.recurringEvent, e.subject_id, e.trial_flag, ".
		"e.absent_flag, e.event_id, m.name, m.furigana, m.grade, e.grade as tgrade, e.place_id ".
		"FROM tbl_event e LEFT OUTER JOIN tbl_member m ".
		"on e.member_no=m.no where e.event_year=? and e.event_month=? and e.teacher_id=? ".
		"order by e.event_start_timestamp, e.cal_evt_summary";
$stmt = $db->prepare($sql);
$stmt->execute(array($year, $month, $teacher_id));
$event_list = $stmt->fetchAll(PDO::FETCH_ASSOC);

foreach ($event_list as &$event) {
	
	$event["date"] = date("n月j日", $event['event_start_timestamp']);
	$event["time"] = date("H:i", $event['event_start_timestamp']) ." ～ ". date("H:i", $event['event_end_timestamp']);

			// 教室
			$lesson_name = $lesson_list[$event["lesson_id"]];
			// 科目
			if ($event["subject_id"] == "0") {
				$subject_name = "　";
			} else {
				if ($event["subject_id"] == 8) {
					$subject_name = "　";
				} else {
					$subject_name = $subject_list[$event["subject_id"]];
				}
			}
			// タイプ
			if ($event["course_id"] == "0") {
				$course_name = "";
			} else {
				$course_name = $course_list[$event["course_id"]]["course_name"];
			}

	$event["course_name"]  = $course_name;
	$event["lesson_name"]  = $lesson_name;
	$event["subject_name"] = $subject_name;
	
	foreach ($work_type_list as $key=>$work_type) {
		if (!$work_type) { continue; }
		if (strpos($event['cal_evt_summary'], $work_type)!==false) {
			$event["course_name"]  = '';
			$event["lesson_name"]  = $lesson_name;
			$event["subject_name"] = $work_type;
			$event["work_type"]    = $key;
			break;
		}
	}
	
	$name = $event["name"];
	if ($name=='体験生徒') { $name = $event['member_cal_name']; }
	$attendStatusCal[$event['date']][$event['time']][$name] = '';
	$str0 = str_replace(array('　','（','）','：','︰'), array(' ','(',')',':',':'), $event['cal_evt_summary']);
	if (preg_match('/(グループ|Group)/iu',$str0) || preg_match('/(ファミリー|family)/iu',$str0)) {
		$ret = preg_match_all('/\((.*?)\)/u', $str0, $blocks);
		if (!$ret) { $blocks[1]=array($str0); }
	} else {
		$blocks[1]=array($str0);
	}
	if (!preg_match('/(ファミリー|family)/iu',$str0)) {
		foreach ($blocks[1] as $key=>$block) {
			$ret = preg_match( '/([^():]+?)様([A-Za-z ]+)?/u', $block, $name_cal );
			if (!$ret) { continue; }
			if (str_replace(' ','',$name) != str_replace(' ','',$name_cal[1])) { continue; }
			$event['eng_name'] = $name_cal[2];
			if (preg_match('/^休み[1１]\s*:/u',$block)) { $attendStatusCal[$event['date']][$event['time']][$name] = '休み１'; }
			if (preg_match('/^休み[2２]\s*:/u',$block)) { $attendStatusCal[$event['date']][$event['time']][$name] = '休み２'; }
			if (preg_match('/^振替\s*:/u',$block))      { $attendStatusCal[$event['date']][$event['time']][$name] = '振替'; }
			if (preg_match('/:\s*当日/u',$block))      { $attendStatusCal[$event['date']][$event['time']][$name] .= '当日'; }
			if (preg_match('/:\s*休講/u',$block))      { $attendStatusCal[$event['date']][$event['time']][$name] .= '休講'; }
			if (preg_match('/^absent1\s*:/iu',$block))  { $attendStatusCal[$event['date']][$event['time']][$name] = 'Absent1'; }
			if (preg_match('/^absent2\s*:/iu',$block))  { $attendStatusCal[$event['date']][$event['time']][$name] = 'Absent2'; }
			if (preg_match('/^alternative\s*:/iu',$block)) { $attendStatusCal[$event['date']][$event['time']][$name] = 'make-up'; }
			if (preg_match('/^make.?up\s*:/iu',$block)) { $attendStatusCal[$event['date']][$event['time']][$name] = 'make-up'; }
			if (preg_match('/:\s*today/iu',$block))    { $attendStatusCal[$event['date']][$event['time']][$name] .= ' Today'; }
			if (preg_match('/:\s*No.*class/iu',$block)) { $attendStatusCal[$event['date']][$event['time']][$name] .= ' No class'; }
			if (preg_match('/:\s*規定回数以上/u',$block))    { $attendStatusCal[$event['date']][$event['time']][$name] .= '規定回数以上'; }
			if (preg_match('/:\s*over.*?limit/iu',$block))    { $attendStatusCal[$event['date']][$event['time']][$name] .= ' over limit'; }
		}
	} else {
		$allPreFix = '';
		if (preg_match('/^休み[1１]\s*:/u',$blocks[1][0])) { $allPreFix = '休み１'; }
		if (preg_match('/^休み[2２]\s*:/u',$blocks[1][0])) { $allPreFix = '休み２'; }
		if (preg_match('/^振替\s*:/u',$blocks[1][0]))     { $allPreFix = '振替'; }
		if (preg_match('/^absent1\s*:/iu',$blocks[1][0]))     { $allPreFix = 'Absent1'; }
		if (preg_match('/^absent2\s*:/iu',$blocks[1][0]))     { $allPreFix = 'Absent2'; }
		if (preg_match('/^alternative\s*:/iu',$blocks[1][0])) { $allPreFix = 'make-up'; }
		if (preg_match('/^make.?up\s*:/iu',$blocks[1][0]))    { $allPreFix = 'make-up'; }
		$allPostFix = '';
		if (preg_match('/ 様 .*:\s*当日/u',$blocks[1][0])) { $allPostFix = '当日'; }
		if (preg_match('/ 様 .*:\s*休講/u',$blocks[1][0])) { $allPostFix = '休講'; }
		if (preg_match('/ 様 .*:\s*today/iu',$blocks[1][0])) { $allPostFix = ' Today'; }
		if (preg_match('/ 様 .*:\s*No.*class/iu',$blocks[1][0])) { $allPostFix = ' No class'; }
		if (preg_match('/ 様 .*:\s*規定回数以上/u',$blocks[1][0])) { $allPostFix = '規定回数以上'; }
		if (preg_match('/ 様 .*:\s*over.*?limit/iu',$blocks[1][0])) { $allPostFix = ' over limit'; }
		$str0 = $blocks[1][0];
		$ret = preg_match_all('/(\S+)/u', $str0, $blocks);
		if (!$ret) { $blocks[1]=array($str0); }
		$flag = 0;
		$event['eng_name'] = '';
		foreach ($blocks[1] as $key=>$block) {
			if ($block == '様') { $flag = 1; continue; }
			if ($flag == 0) {
				$name0 = $block;
				$tmp = preg_replace( "/(\s*休み[12１２]\s*:\s*|\s*振替\s*:\s*|:\s*当日|:\s*休講|:\s*規定回数以上)/u", "", $name0 );
				if ($tmp!==false) {$name0=$tmp;}
				$tmp = preg_replace( "/(\s*absent[12]\s*:\s*|\s*alternative\s*:\s*|\s*make.?up\s*:\s*|:\s*today|:\s*over.*?limit|:\s*no.*?class)/iu", "", $name0 );
				if ($tmp!==false) {$name0=$tmp;}
				if ($key==0) {
					$family_name = $name0;
				} else {
					$name0 = $family_name.' '.$name0;
					$attendStatusCal[$event['date']][$event['time']][$name0] = '';
					if ($allPreFix) { $attendStatusCal[$event['date']][$event['time']][$name0] = $allPreFix; }
					else if (preg_match('/^休み[1１]\s*:/u',$block)) { $attendStatusCal[$event['date']][$event['time']][$name0] = '休み１'; }
					else if (preg_match('/^休み[2２]\s*:/u',$block)) { $attendStatusCal[$event['date']][$event['time']][$name0] = '休み２'; }
					else if (preg_match('/^振替\s*:/u',$block))     { $attendStatusCal[$event['date']][$event['time']][$name0] = '振替'; }
					else if (preg_match('/^absent1\s*:/iu',$block))  { $attendStatusCal[$event['date']][$event['time']][$name0] = 'Absent1'; }
					else if (preg_match('/^absent2\s*:/iu',$block))  { $attendStatusCal[$event['date']][$event['time']][$name0] = 'Absent2'; }
					else if (preg_match('/^alternative\s*:/iu',$block)) { $attendStatusCal[$event['date']][$event['time']][$name0] = 'make-up'; }
					else if (preg_match('/^make.?up\s*:/iu',$block)) { $attendStatusCal[$event['date']][$event['time']][$name0] = 'make-up'; }
					if ($allPostFix) { $attendStatusCal[$event['date']][$event['time']][$name0] .= $allPostFix; }
					else if (preg_match('/:\s*当日/u',$block))      { $attendStatusCal[$event['date']][$event['time']][$name0] .= '当日'; }
					else if (preg_match('/:\s*休講/u',$block))      { $attendStatusCal[$event['date']][$event['time']][$name0] .= '休講'; }
					else if (preg_match('/:\s*today/iu',$block))    { $attendStatusCal[$event['date']][$event['time']][$name0] .= ' Today'; }
					else if (preg_match('/:\s*No.*class/iu',$block)) { $attendStatusCal[$event['date']][$event['time']][$name0] .= ' No class'; }
					else if (preg_match('/:\s*規定回数以上/u',$block)) { $attendStatusCal[$event['date']][$event['time']][$name0] .= '規定回数以上'; }
					else if (preg_match('/:\s*over.*?limit/iu',$block)) { $attendStatusCal[$event['date']][$event['time']][$name0] .= ' over limit'; }
				}
			} else {
				$event['eng_name'] .= $block.' ';
			}
		}
	}
			
	$event["comment"] = $comment;
	$event['diff_hours'] = ($event["event_end_timestamp"] - $event["event_start_timestamp"]) /  (60*60);;
}
unset($event);

$stmt = $db->query(
		"SELECT * FROM tbl_teacher_presence_report ".
		"WHERE teacher_id=\"$teacher_id\" AND year=\"$year\" AND month=\"$month\"");
$rslt = $stmt->fetchAll(PDO::FETCH_ASSOC);
foreach ($rslt as $item) {
	$attendStatus[$item['date']][$item['time']][$item['name']] = $item['presence'];
}
// 期間講習追加
$season_exercise = array();
if ($date_list_string != '()') {
	$sql = "SELECT * FROM tbl_season_schedule s LEFT OUTER JOIN tbl_member m ON s.member_no=m.no WHERE s.date IN {$date_list_string} AND s.teacher_no={$teacher_id}";
	$stmt = $db->prepare($sql);
	$stmt->execute();
	$schedules = $stmt->fetchAll(PDO::FETCH_ASSOC);
	foreach ($schedules as $schedule) {
		$date = str_replace('/','月',substr(str_replace('/0','/',$schedule['date']),5)).'日';
		$event = array();
		$event['course_id']    = $season_course_id;
		$event['course_name']  = $course_list[$event["course_id"]]["course_name"];
		if (!$season_class_date_list || array_search($schedule['date'], $season_class_date_list)===false) $event['course_name'] = '土日講習';
		$event['lesson_id']    = $schedule['lesson_id'];
		$event['lesson_name']  = $lesson_list[$schedule['lesson_id']];
		$event['subject_id']   = $schedule['subject_id'];
		$event['subject_name'] = $subject_list[$schedule['subject_id']];
		$event['member_no']    = $schedule['member_no'];
		$event['name']         = $schedule['name'];
		$event['furigana']     = $schedule['furigana'];
		$event['grade']        = $schedule['grade'];
		$event['date']         = $date;
		$event['date1']        = $schedule['date'];
		$event['time']         = "{$schedule['stime']} ～ {$schedule['etime']}";
		$event['event_start_timestamp'] = DateTime::createFromFormat('Y/m/d H:i', "{$schedule['date']} {$schedule['stime']}")->getTimestamp();
		$event['event_end_timestamp']   = DateTime::createFromFormat('Y/m/d H:i', "{$schedule['date']} {$schedule['etime']}")->getTimestamp();
		$event['recurringEvent'] = 0;
		$event['trial_flag']     = 0;
		$event['absent_flag']    = 0;
		$event['diff_hours'] = ($event["event_end_timestamp"] - $event["event_start_timestamp"]) / (60*60);
		$event["cal_evt_summary"] = "{$event['course_name']}:{$event['lesson_name']}:{$event['subject_name']}:{$event['name']}";
		$event["work_type"]       = '';
		if ($event['member_no']) {
			$event_list[] = $event;
		} else {
//			if (!$season_exercise[$date]) {
//				$season_exercise[$date] = array();
//				$event_list[] = $event;
//			}
//			$season_exercise[$date][] = array('stime'=>$schedule['stime'], 'etime'=>$schedule['etime']);
		}
	}
	
	foreach ($date_list as $date) {
		$date0 = str_replace('/','月',substr(str_replace('/0','/',$date),5)).'日';
		$schedules1 = array_filter($schedules, function($s){ global $date,$teacher_id; return ($s['date']==$date && $s['teacher_no']==$teacher_id); });
		$stmt = $db->query("SELECT * FROM tbl_season_class_entry_date WHERE date=\"$date\"");
		$rslt = $stmt->fetchAll(PDO::FETCH_ASSOC);
		foreach ($rslt as $item) {
			foreach ($schedules1 as $schedule) {
				if ($schedule['member_no'] != $item['member_id']) { continue;}
				$time1 = "{$schedule['stime']} ～ {$schedule['etime']}";
				$attendStatusCal[$date0][$time1][$member_list[$schedule['member_no']]['name']] = ($item['attend_status']=='出席')?'':$item['attend_status'];
				foreach ($event_list as $key=>$event) {
					if ($event['date']==$date0 && $event['time']==$time1 && $event['member_no']==$schedule['member_no']) {
						$event_list[$key]['cal_evt_summary'] .= ':'.$attendStatusCal[$date0][$time1][$member_list[$schedule['member_no']]['name']];
						$event_list[$key]['place'] = $item['place'];
					}
				}
			}
		}

		$cmd = "SELECT times FROM tbl_season_class_teacher_entry1 WHERE no = \"{$teacher_id}\" AND date = \"{$date}\"";
		$stmt = $db->query($cmd);
		$rslt = $stmt->fetch(PDO::FETCH_ASSOC);
		if ($rslt && array_search($date, array_column($event_list,'date1'))!==false) {
			$schedules1 = array_filter($schedules, function($s){ global $date; return $s['date']==$date; });
			$event = array();
			$event['course_id']    = $season_course_id;
			if (!$season_class_date_list || array_search($date, $season_class_date_list)===false)
				$event['course_name'] = '土日講習';
			else
				$event['course_name']  = $course_list[$event["course_id"]]["course_name"];
			$event['lesson_name']  = $lesson_list[1];
			$event['subject_name'] = '演習';
			$event['member_no']    = '';
			$event['date']         = $date0;
			$event['date1']        = $date;
			$event["work_type"]    = array_search('演習',$work_type_list);
			foreach ($time_list as $stimekey=>$stime) {
				if (strpos($rslt['times'],$stime)===false) { continue; }
				if (array_filter($schedules1,
							function($s){
								global $date0,$stime,$attendStatus,$attendStatusCal,$member_list,$mode;
								$time = "{$s['stime']} ～ {$s['etime']}";
								$name = $member_list[$s['member_no']]['name'];
								$st = ($mode == 'pay_viewonly')?$attendStatusCal[$date0][$time][$name]:$attendStatus[$date0][$time][$name];
								return ($s['stime']<=$stime && $stime<$s['etime'] && (strpos($st,'休み')===false || strpos($st,'休み２当日')!==false)); }))
						continue;
				$season_exercise[$date][] = array('stime'=>$stime, 'etime'=>$time_list[$stimekey+1]);
			}
			if ($season_exercise[$date]) {
				$event['event_start_timestamp'] = DateTime::createFromFormat('Y/m/d H:i', "{$date} {$season_exercise[$date][0]['stime']}")->getTimestamp();
				$time_str = ''; $lastetime = '';
				foreach ($season_exercise[$event['date1']] as $item) {
					if ($item['stime'] != $lastetime) {
						$time_str .= $lastetime;
						if ($lastetime) { $time_str .= '<br>'; }
						$time_str .= $item['stime'].' ～ ';
					}
					$lastetime = $item['etime'];
				}
				$time_str .= $lastetime;
				$event['time'] = $time_str;
				$event['diff_hours'] = count($season_exercise[$event['date1']]) * 0.5;
				$event["cal_evt_summary"] = "{$event['course_name']}:{$event['lesson_name']}:{$event['subject_name']}:{$event['name']}";
				foreach ($event_list as $event0) {
					if ($event0['date']==$date0 && $event0['place']) {
						$event['place'] = $event0['place'];
						break;
					}
				}
				$event_list[] = $event;
			}
		}
		
		$cmd = "SELECT stime, etime FROM tbl_season_class_teacher_entry WHERE no = \"{$teacher_id}\" AND date = \"{$date}\"";
		$stmt = $db->query($cmd);
		$rslt = $stmt->fetch(PDO::FETCH_ASSOC);
		if ($rslt && array_search($date,$season_class_date_list)!==false && array_search($date, array_column($event_list,'date1'))!==false) {
			$schedules1 = array_filter($schedules, function($s){ global $date; return $s['date']==$date; });
			$event = array();
			$event['course_id']    = $season_course_id;
			$event['course_name']  = $course_list[$event["course_id"]]["course_name"];
			$event['lesson_name']  = $lesson_list[1];
			$event['subject_name'] = '演習';
			$event['member_no']    = '';
			$event['date']         = $date0;
			$event['date1']        = $date;
			$event["work_type"]    = array_search('演習',$work_type_list);
			foreach ($time_list as $stimekey=>$stime) {
				if ($stime<$rslt['stime'] || $stime>=$rslt['etime']) { continue; }
				if (array_filter($schedules1,
							function($s){
								global $date0,$stime,$attendStatus,$attendStatusCal,$member_list,$mode;
								$time = "{$s['stime']} ～ {$s['etime']}";
								$name = $member_list[$s['member_no']]['name'];
								$st = ($mode == 'pay_viewonly')?$attendStatusCal[$date0][$time][$name]:$attendStatus[$date0][$time][$name];
								return ($s['stime']<=$stime && $stime<$s['etime'] && (strpos($st,'休み')===false || strpos($st,'休み２当日')!==false)); }))
						continue;
				$season_exercise[$date][] = array('stime'=>$stime, 'etime'=>$time_list[$stimekey+1]);
			}
			$event['event_start_timestamp'] = DateTime::createFromFormat('Y/m/d H:i', "{$date} {$season_exercise[$date][0]['stime']}")->getTimestamp();
			$time_str = ''; $lastetime = '';
			foreach ($season_exercise[$event['date1']] as $item) {
				if ($item['stime'] != $lastetime) {
					$time_str .= $lastetime;
					if ($lastetime) { $time_str .= '<br>'; }
					$time_str .= $item['stime'].' ～ ';
				}
				$lastetime = $item['etime'];
			}
			$time_str .= $lastetime;
			$event['time'] = $time_str;
			$event['diff_hours'] = count($season_exercise[$event['date1']]) * 0.5;
			$event["cal_evt_summary"] = "{$event['course_name']}:{$event['lesson_name']}:{$event['subject_name']}:{$event['name']}";
			foreach ($event_list as $event0) {
				if ($event0['date']==$date0 && $event0['place']) {
					$event['place'] = $event0['place'];
					break;
				}
			}
			$event_list[] = $event;
		}
	}
}

$lesson_array = array();
foreach ($event_list as $key => $value) {
    $sort1[$key] = $value['date'];
    $sort2[$key] = $value['time'];
		$sort3[$key] = $value['cal_evt_summary'];
		$sort4[$key] = $value['furigana'];
		$lesson_array[$value['date']][] = $value['time'].$value['cal_evt_summary'];
}

array_multisort(
	$sort1, SORT_ASC, SORT_NATURAL, $sort2, SORT_ASC, SORT_NATURAL,
	$sort3, SORT_ASC, SORT_NATURAL, $sort4, SORT_ASC, SORT_NATURAL, $event_list );
	
$lesson_count = array();
foreach ($lesson_array as $key=>$item) {
	$lesson_count[$key] = count ( array_unique($item) );

}

} catch (Exception $e) {
	// 処理を中断するほどの致命的なエラー
	array_push($errArray, $e->getMessage());
}

function cmp_date_furigana($a, $b) {
	if ($a["event_start_timestamp"] == $b["event_start_timestamp"]) {
		if ($a["furigana"] == $b["furigana"]) {
			return 0;
		}
		return ($a["furigana"] > $b["furigana"]) ? +1 : -1;
		}
	return ($a["event_start_timestamp"] > $b["event_start_timestamp"]) ? +1 : -1;
}

if ($mode != 'transport') {
	if (count($errArray) > 0) {
		foreach( $errArray as $error) {
?>
			<font color="red" size="3"><?= $error ?></font><br><br>
<?php
		}
		exit();
	}
?>
<?php
		foreach( $log as $msg) {
?>
			<font color="red" size="3"><?= $msg ?></font><br><br>
<?php
		}
if ($mode!='pay_viewonly') {
?>
<br>
<input id="" name="" type="button" value="<?= STR_PAYMENT_DISPLAY_SWITCH ?>" onclick="return pay_display();">
<input id="" name="" type="button" value="<?= STR_LOGOUT ?>" onclick="location.replace( './login.php' )">
<a href="menu.php"><?= STR_GOTO_MENU ?></a>
<br>
<br>
<?php } ?>
<div class="menu_box">
<font color="red"><?= STR_COMMENT1 ?></font><br>
<font color="blue"><?= STR_COMMENT2 ?></font><br>
<span style="background-color:#c0ffc0"><?= STR_COMMENT3 ?></span><br>
<?= STR_COMMENT4 ?>
</div>
<br>
<?php } //$mode!='transport' ?>
<table border="1">
<tr>
<?php if ($mode=='transport') { ?>
	<th><?= STR_DATE ?></th><th><?= STR_PLACE ?></th><th><?= STR_TRANSPORT ?></th><th><?= STR_TRANSPORT_CORRECT ?></th><th><?= STR_TRANSPORT_COMMENT ?></th>
<?php } else { ?>
	<th></th><th><?= STR_DATE ?></th><th><?= STR_TIME ?></th><th><?= STR_HOURS ?></th><th><?= STR_PLACE ?></th><th><?= STR_KYOUSHITSU ?></th><th><?= STR_KAMOKU ?></th><th><?= STR_COURSE ?></th>
	<th><?= STR_NAME ?></th><th><?= STR_ATTENDANCE ?></th>
		<?php if ($mode!='pay_viewonly') { ?>
		<th><?= STR_CALNDAR_STATUS ?></th>
		<?php } ?>
	<th name="pay"><?= STR_WAGE ?></th><th name="pay"><?= STR_PAYMENT ?></th>
<?php } ?>
</tr>
<?php
$no=0; $i=0; $member_count = 0; $rowspan=1;
$event = reset($event_list);
while ($event) {
	$diff_hours = $event['diff_hours'];
	$DOW = (int)date_format(date_create($year.'-'.str_replace('月', '-', str_replace('日','',$event["date"]))),'w');
	switch ($DOW) {
	case 0: $DOW = "<font color=red>(".$weekday_array[$DOW].")</font>"; break;
	case 6: $DOW = "<font color=blue>(".$weekday_array[$DOW].")</font>"; break;
	default: $DOW = "(".$weekday_array[$DOW].")";
	}
	$bgcolor = ($event['recurringEvent'])? '"#ffffff"': '"#c0ffc0"';
	$rowspan--;
	if ($mode != 'check' && $rowspan == 0)	ob_start();
?>
	<tr bgcolor=<?= $bgcolor ?>>
		<td align="left"><?php echo ++$no; ?></td>
<?php
	if ($rowspan == 0) {
		$rowspan = $lesson_count[$event["date"]];
		$rowspan0 = (($mode == 'transport')?1:$rowspan);
		$date_cell = "<td align=\"left\" style=\"padding: 0px 10px 0px 10px;\" bgcolor=\"#ffffff\" rowspan=\"$rowspan0\">".
			str_replace(array('月','日'),array('/',''),$event["date"])."$DOW</td>";
		echo $date_cell;
	}
	if ($event['course_id'] != $season_course_id)
		$place_name = ($event['lesson_id']!=2)?$place_list[$event['place_id']]['name']: 
			mb_substr($event["cal_summary"],mb_strpos($event["cal_summary"],'_')+1);
	else
		$place_name = str_replace('校舎','校',str_replace('八王子','',$event['place']));
?>
	<td align="left" style="padding: 0px 10px 0px 10px;"><?= $event["time"] ?></td>
	<td align="left" style="padding: 0px 10px 0px 10px;"><?= sprintf( "%4.2f", $diff_hours ) ?></td>
	<td align="left" style="padding: 0px 10px 0px 10px;"><?= $place_name ?></td>
	<td align="left" style="padding: 0px 10px 0px 10px;"><?= ($event["lesson_name"]=='英会話')?English:$event["lesson_name"] ?></td>
	<td align="left" style="padding: 0px 10px 0px 10px;"><?= ($event["subject_name"]=='英会話')?English:$event["subject_name"] ?></td>
	<td align="left" style="padding: 0px 10px 0px 10px;" name="course_name"><?php
		if ($teacher['lesson_id'] != 2) {
			echo $event["course_name"];
		} else {
			switch ($event["course_name"]) {
			case 'マンツーマン': echo 'Man to man'; break;
			case 'グループ':   echo 'Group'; break;
			case 'ファミリー':   echo 'Family'; break;
			default:        echo $event["course_name"];
			}
		}
	?></td>
<?php
	$next_event = $event;
	do {
		$event = $next_event;
		if ($event["member_no"]) {
			$name = $event["name"];
			if ($name=='体験生徒') { $name = $event['member_cal_name']; }

			if ($event["course_id"] == 3) {
				$tmp0 = explode(' ',$name);
				$family_name = $tmp0[0]; array_shift($tmp0); $names = array();
				foreach ($tmp0 as $str0) { $names[] = $family_name.' '.$str0; }
				$tmp0 = explode(' ',$event['eng_name']? $event['eng_name']: eng_name1($kana2romaji->convert($event["furigana"])));
				$family_name_eng = $tmp0[0]; array_shift($tmp0); $names_eng = array();
				foreach ($tmp0 as $str0) { $names_eng[] = $family_name_eng.' '.$str0; }
			} else {
				$names = array($name);
				$names_eng = array( trim($event['eng_name'])? $event['eng_name']: eng_name1($kana2romaji->convert($event["furigana"])) );
			}
			foreach ($names as $key=>$name) {
				$st = ($mode=='pay_viewonly')?$attendStatusCal[$event["date"]][$event["time"]][$name]:$attendStatus[$event["date"]][$event["time"]][$name];
				$st_index = ($st)? array_search($st, $attendStatusList)+1: 0;
				if (array_search($st, $attendStatusList)===false) {
					$st_index = ($st)? array_search($st, $attendStatusList_eng)+1: 0;
					if ($st_index) $st = $attendStatusList[$st_index-1];
				}
				$color = (strpos($st,STR_YASUMI)===false)? "black" : "red" ;
				if ($event["trial_flag"]) { $color = "blue"; }
				$name0 = $name;
				if (($teacher['lesson_id'] == 2) && $names_eng[$key]) {
					$name0 = $names_eng[$key];
				}
				$nameCol .= "<font id=\"name$i\" color=\"$color\">$name0</font><br>";
				if ($mode=='pay_viewonly') { 
					$calst0 = $attendStatusCal[$event["date"]][$event["time"]][$name];
					if ($calst0 == '') $calst0 = STR_SHUSSEKI;
					$stSelect .= "<span name=\"stSelect{$no}\">$calst0</span><br>";
				} else {
					disp_pulldown_menu($attendStatusList, "stSelect{$no}", $st,
						"set_attendance(this,\"{$event['cal_id']}\",\"{$event['event_id']}\",\"$name\",{$st_index},".
						"\"$year\",\"$month\",\"{$event["date"]}\",\"{$event['time']}\",\"$teacher_id\",".
						"\"{$event['member_no']}\",{$event['recurringEvent']}, \"{$no}\", \"{$i}\",{$event['trial_flag']})", $str);
					if ($fixed) $str = "<span name=\"stSelect{$no}\">$st</span>";
					if (!$name || $event['work_type']) { $str=''; }
					$stSelect .= "$str<br>";
				$calst0 = $attendStatusCal[$event["date"]][$event["time"]][$name];
				if (attendStatusList != $attendStatusList_eng) {
					$index1 = array_search($calst0, $attendStatusList_eng);
					$calst1 = $attendStatusList_jp[$index1];
				} else {
					$index1 = array_search($calst0, $attendStatusList_jp);
					$calst1 = $attendStatusList_eng[$index1];
				}
				if (($st==STR_SHUSSEKI && $calst0=='') || ($st!=STR_SHUSSEKI && ($st==$calst0 || $st==$calst1))) {
					$bgcolor = '';
				} else {
					$bgcolor = "style=\"background-color:#FFFF00;\"";
				}
				if ($calst0 == '') { $calst0 = '　　　　　　'; }
				$calStatus .= "<span id=\"cal$i\" $bgcolor>".$calst0.'</span><br>';
				}
				$i++;
				if (!($event['course_id'] == 2 && $event["trial_flag"])) { $member_count++; }
				
				if ($st == STR_SHUSSEKI || $st == STR_FURIKAE)	$attendPlaceList[] = $place_name;
			}
		} else {
			if (event['work_type']) {
				$nameCol = $event['member_cal_name'];
			}
		}
		
		$lastdate=$event["date"]; $lasttime=$event["time"]; $last_cal_evt_summary = $event["cal_evt_summary"];
		$next_event = next($event_list);
	} while (($next_event) && ($next_event["date"] == $lastdate) && ($next_event["time"] == $lasttime) && ($next_event["cal_evt_summary"] == $last_cal_evt_summary));
	if ($event['course_id'] == 2 && $member_count == 0) { $member_count = 1; }
?>
		<td align="left" style="padding: 0px 10px 0px 10px;"><?= $nameCol ?></td>
		<td align="left" style="padding: 0px 10px 0px 10px;"><?= $stSelect ?></td>
<?php if ($mode!='pay_viewonly') { ?>
		<td align="left" style="padding: 0px 10px 0px 10px;"><?= $calStatus ?></td>
<?php } ?>
		<td name="pay" align="left" style="padding: 0px 10px 0px 10px;">
<?php
	$nameCol = ''; $stSelect = ''; $calStatus = ''; 
	if ($event['work_type']) {
		$wage_no = 0;
		$stmt = $db->query("SELECT * FROM tbl_wage WHERE teacher_id=\"{$teacher_id}\" AND wage_no=\"{$wage_no}\" AND work_type=\"{$event['work_type']}\"");
		$wage_array = $stmt->fetch(PDO::FETCH_ASSOC);			
		$wage = $wage_array['hourly_wage'];
		echo "{$wage}円";
	} else if ($event["member_no"]) {
		
		$wage_no = -1; $lesson_id = $event['lesson_id'];
		switch ($lesson_id) {
		case 1:
			$wage_type_list = $jyuku_wage_type_list;
			if ($event['trial_flag']) {
				$grade = $event['tgrade'];
			} else {
				$grade = $event['grade'];
				if ($grade) {
					if ($year==date('Y') && date('n') >= 4 && $month < 4 && $grade > 1) { $grade--; }
				}
			}
			if ($grade) {
				if ($grade < 8) {
					if ($member_count >= 5) {
						$wage_no = 13;
					} else if ($member_count == 4) {
						$wage_no = 12;
					} else if ($member_count == 3) {
						$wage_no = 11;
					} else if ($member_count == 2) {
						$wage_no = 1;
					} else if ($member_count == 1) {
						if ($member_list[$event["member_no"]]['jyukensei']) {
							$wage_no = 14;
						} else {
							$wage_no = 0;
						}
					}
				} else if ($grade <= 10) {
					if ($member_count >= 5) {
						$wage_no = 7;
					} else if ($member_count == 4) {
						$wage_no = 6;
					} else if ($member_count == 3) {
						$wage_no = 5;
					} else if ($member_count == 2) {
						$wage_no = 4;
					} else if ($member_count == 1) {
						if ($grade == 10) {
							$wage_no = 3;
						} else {
							$wage_no = 2;
						}
					}
				} else if ($grade <= 13) {
					if ($member_count == 2) {
						$wage_no = 9;
					} else if ($member_count == 1) {
						if ($grade == 13) {
							$wage_no = 10;
						} else {
							$wage_no = 8;
						}
					}
				} else if ($grade == 14) {
					if ($member_count == 2) {
						$wage_no = 9;
					} else if ($member_count == 1) {
						$wage_no = 8;
					}
				}
			}
			break;
		case 2:
			$wage_type_list = $eng_wage_type_list;
			if ($member_count >= 5) { 
				$wage_no = 2;
			} else if ($member_count == 2) {
				$wage_no = 1;
			} else if ($member_count == 3) {
				$wage_no = 4;
			} else if ($member_count == 4) {
				$wage_no = 5;
			} else if ($member_count == 1 ) {
				$wage_no = 0;
			}
			break;
		case 3:
			$wage_type_list = $piano_wage_type_list;
			$wage_no = 0;
			break;
		case 4:
			$wage_type_list = $naraigoto_wage_type_list;
			$wage_no = 0;
		}
		//echo "$lesson_id,$grade,$member_count,$wage_no<br>";
		if ($wage_no>-1) {
			$stmt = $db->query("SELECT * FROM tbl_wage WHERE teacher_id=\"{$teacher_id}\" AND wage_no=\"{$wage_no}\" AND lesson_id=\"{$lesson_id}\" AND work_type=0");
			$wage = $stmt->fetch(PDO::FETCH_ASSOC);
			if ($wage) {
					if ( $wage["hourly_wage"]) {
						$wage = $wage["hourly_wage"];
						echo "{$wage}円<br> {$wage_type_list[$wage_no]}";
					} else {
						echo STR_WAGE_UNDECIDED;
					}
				} else {
					echo STR_WAGE_UNDECIDED;
				}
		} else {
			if ($lesson_id == 1 && $grade == '') {
				echo STR_GRADE_UNKNOWN;
			} else {
				echo STR_WAGE_UNDECIDED;
			}
		}
	} else {
	}
	$wage_list[$no] = ($wage)?$wage:0; $diff_hours_list[$no] = ($diff_hours)?$diff_hours:0;
?>
		</td>
		<td name="pay" align="right" style="padding: 0px 10px 0px 10px;">
		<span id="pay<?= $no ?>"></span><br>
		</td>
<?php
	if ($rowspan == $rowspan0) {
		echo "<TRANSPORT_COST_STRING>";
	}
	echo '</tr>';
	if ($rowspan == 1 || !$next_event) {
		if ($mode!='check')	{ $html_str = ob_get_contents(); ob_end_clean(); }
		$transport_cost = 0;
		if ($attendPlaceList) {
			if ($teacher['transport_DOW']) {
				if (strpos($teacher["transport_DOW"], date('w',$event['event_start_timestamp'])) !== false)
					$transport_cost = $teacher['transport_cost'];
			} else {
				$DOW = date('w',$event['event_start_timestamp']);
				$transport_cost = $teacher['transport_dcost1'][$DOW];
			}
			$str0 = implode(',',array_diff(array_unique($attendPlaceList), array('')));
			$transport_cell = "<td name=\"pay\" align=\"right\" rowspan=\"$rowspan0\">$transport_cost</td>";
		} else {
			$str0 = "";
			$transport_cell = "<td name=\"pay\" rowspan=\"$rowspan0\"></td>";
		}
		$transport_cell .= "<input type=\"hidden\" name=\"transport0[]\" value=\"$transport_cost\">";
		
		if ($mode == 'transport') {
			$transport_cell = str_replace("name=\"pay\" ", "", $transport_cell);
			$date = date('Y-m-d',$event['event_start_timestamp']);
			echo "<tr>$date_cell<td>$str0</td>$transport_cell<td>".
					"<input type=\"hidden\" name=\"date[]\" value=\"$date\">".
					"<input type=\"text\" name=\"transport1[]\" style=\"width:80px;\" value=\"{$teacher['transport_correct_cost'][$date]}\"></td>".
					"<td><input type=\"text\" name=\"transport_comment[]\" size=40 value=\"{$teacher['transport_comment'][$date]}\"></td></tr>\n";
		} else if ($mode!='check')	{
			echo str_replace("<TRANSPORT_COST_STRING>", "", $html_str);
		}
		
		$attendPlaceList = array();
	}

	$member_count = 0; $event = $next_event;
}
echo "</tr>";

if ($mode=='transport') { ?>

</table><br><br>

<?php } else { ?>

	</tr>
	<tr name="pay"><td colspan="<?= ($mode!='pay_viewonly')?12:11 ?>"></td><td align="right"><span id="pay_total"></span></td></tr>
</table><br><br>
</form>
</div>

<script type = "text/javascript">
<!--
<?php
if ($mode=='pay_viewonly') { echo "pay_display_flag = 1;"; }
foreach ($wage_list as $key=>$wage) { echo "wage_list[$key]=$wage; diff_hours_list[$key]={$diff_hours_list[$key]};"; }
$i--;
echo "pay_display();";
echo "for (var i=1;i<={$no};i++) { set_pay(i); }";
echo "update_totalpay();";
?>
		document.getElementById('loading-view').style.display = 'none';
//-->
</script>

<?php } // $mode=='transport' ?>

</body></html>
