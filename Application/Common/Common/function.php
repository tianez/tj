<?php
include_once ('Mobile_Detect.php');

//验证验证码
function check_verify($code, $id = '') {
	$verify = new \Think\Verify();
	return $verify -> check($code, $id);
}

//检测设备
function Mobile_Detect() {
	$detect = new Mobile_Detect;
	return $detect;
}

function mark($mark) {
	if ($mark) {
		cookie('mark_2014_4_6', $mark);
	} else {
		$mark_2014_4_6 = cookie('mark_2014_4_6');
		if (!empty($mark_2014_4_6)) {
			echo '<div class="note note-danger">';
			echo cookie('mark_2014_4_6');
			echo '</div>';
		}
		cookie('mark_2014_4_6', null);
	}
}

//获取系统配置
function O($option_name) {
	$option = M('setup') -> where(array('subject' => $option_name)) -> find();
	return $option['parameter'];
}

//保存系统配置
function O2($name, $value) {
	$data['subject'] = $name;
	$option = M('setup') -> where($data) -> find();
	$data['parameter'] = $value;
	if (is_array($option)) {
		$res = M('setup') -> save($data);
	} else {
		$res = M('setup') -> add($data);
	}
	return $res;
}

//记录log
function action_log($action,$remark='') {
	$user = cookie('user');
	$data['user_id'] = $user['id'];
	$data['user_account'] = $user['user_account'];
	$data['user_name'] = $user['user_name'];
	$data['user_ip'] = get_client_ip();
	$data['event'] = $action;
	$data['explicit'] = $remark;
	$data['browser'] = cookie('browser');
	$data['operating_system'] = cookie('system');
	M('sys_log') -> add($data);
}

//加密解密
function encrypt($string, $operation = "E", $key = '今点科技') {
	$key = md5($key);
	$key_length = strlen($key);
	$string = $operation == 'D' ? base64_decode($string) : substr(md5($string . $key), 0, 8) . $string;
	$string_length = strlen($string);
	$rndkey = $box = array();
	$result = '';
	for ($i = 0; $i <= 255; $i++) {
		$rndkey[$i] = ord($key[$i % $key_length]);
		$box[$i] = $i;
	}
	for ($j = $i = 0; $i < 256; $i++) {
		$j = ($j + $box[$i] + $rndkey[$i]) % 256;
		$tmp = $box[$i];
		$box[$i] = $box[$j];
		$box[$j] = $tmp;
	}
	for ($a = $j = $i = 0; $i < $string_length; $i++) {
		$a = ($a + 1) % 256;
		$j = ($j + $box[$a]) % 256;
		$tmp = $box[$a];
		$box[$a] = $box[$j];
		$box[$j] = $tmp;
		$result .= chr(ord($string[$i]) ^ ($box[($box[$a] + $box[$j]) % 256]));
	}
	if ($operation == 'D') {
		if (substr($result, 0, 8) == substr(md5(substr($result, 8) . $key), 0, 8)) {
			return substr($result, 8);
		} else {
			return '';
		}
	} else {
		return str_replace('=', '', base64_encode($result));
	}
}

function encrypt_username($name){
	$name = encrypt($name,'D');
	return substr_replace($name, "*", 3, 3);
}

function encrypt_china_id($china_id){
	$china_id = encrypt($china_id,'D');
	return substr_replace($china_id, "********", 6, 8);
}

function encrypt_mobile_phone($mobile_phone){
	$mobile_phone = encrypt($mobile_phone,'D');
	return substr_replace($mobile_phone, "********", 3, 4);
}


//获取精确时间
function mtime() {
	$t = explode(' ', microtime());
	$time = $t[0] + $t[1];
	return $time;
}

//获得浏览器名称和版本
function getbrowser() {
	global $_SERVER;
	$agent = $_SERVER['HTTP_USER_AGENT'];
	$browser = '';
	$browser_ver = '';

	if (preg_match('/OmniWeb\/(v*)([^\s|;]+)/i', $agent, $regs)) {
		$browser = 'OmniWeb';
		$browser_ver = $regs[2];
	}

	if (preg_match('/Netscape([\d]*)\/([^\s]+)/i', $agent, $regs)) {
		$browser = 'Netscape';
		$browser_ver = $regs[2];
	}

	if (preg_match('/Chrome\/([^\s]+)/i', $agent, $regs)) {
		$browser = 'Chrome';
		$browser_ver = $regs[1];
	}

	if (preg_match('/MSIE\s([^\s|;]+)/i', $agent, $regs)) {
		$browser = 'Internet Explorer';
		$browser_ver = $regs[1];
	}

	if (preg_match('/Opera[\s|\/]([^\s]+)/i', $agent, $regs)) {
		$browser = 'Opera';
		$browser_ver = $regs[1];
	}

	if (preg_match('/NetCaptor\s([^\s|;]+)/i', $agent, $regs)) {
		$browser = '(Internet Explorer ' . $browser_ver . ') NetCaptor';
		$browser_ver = $regs[1];
	}

	if (preg_match('/Maxthon/i', $agent, $regs)) {
		$browser = '(Internet Explorer ' . $browser_ver . ') Maxthon';
		$browser_ver = '';
	}
	if (preg_match('/360SE/i', $agent, $regs)) {
		$browser = '(Internet Explorer ' . $browser_ver . ') 360SE';
		$browser_ver = '';
	}
	if (preg_match('/SE 2.x/i', $agent, $regs)) {
		$browser = '(Internet Explorer ' . $browser_ver . ') 搜狗';
		$browser_ver = '';
	}

	if (preg_match('/FireFox\/([^\s]+)/i', $agent, $regs)) {
		$browser = 'FireFox';
		$browser_ver = $regs[1];
	}

	if (preg_match('/Lynx\/([^\s]+)/i', $agent, $regs)) {
		$browser = 'Lynx';
		$browser_ver = $regs[1];
	}

	if ($browser != '') {
		return $browser . ' ' . $browser_ver;
	} else {
		return 'Unknow browser';
	}
}

//获得客户端的操作系统
function getplat() {
	$agent = $_SERVER['HTTP_USER_AGENT'];
	$os = false;
	if (eregi('win', $agent) && strpos($agent, '95')) {
		$os = 'Windows 95';
	} else if (eregi('win 9x', $agent) && strpos($agent, '4.90')) {
		$os = 'Windows ME';
	} else if (eregi('win', $agent) && ereg('98', $agent)) {
		$os = 'Windows 98';
	} else if (eregi('win', $agent) && eregi('nt 5.1', $agent)) {
		$os = 'Windows XP';
	} else if (eregi('win', $agent) && eregi('nt 5', $agent)) {
		$os = 'Windows 2000';
	} else if (eregi('win', $agent) && eregi('nt', $agent)) {
		$os = 'Windows NT';
	} else if (eregi('win', $agent) && ereg('32', $agent)) {
		$os = 'Windows 32';
	} else if (eregi('linux', $agent)) {
		$os = 'Linux';
	} else if (eregi('unix', $agent)) {
		$os = 'Unix';
	} else if (eregi('sun', $agent) && eregi('os', $agent)) {
		$os = 'SunOS';
	} else if (eregi('ibm', $agent) && eregi('os', $agent)) {
		$os = 'IBM OS/2';
	} else if (eregi('Mac', $agent) && eregi('PC', $agent)) {
		$os = 'Macintosh';
	} else if (eregi('PowerPC', $agent)) {
		$os = 'PowerPC';
	} else if (eregi('AIX', $agent)) {
		$os = 'AIX';
	} else if (eregi('HPUX', $agent)) {
		$os = 'HPUX';
	} else if (eregi('NetBSD', $agent)) {
		$os = 'NetBSD';
	} else if (eregi('BSD', $agent)) {
		$os = 'BSD';
	} else if (ereg('OSF1', $agent)) {
		$os = 'OSF1';
	} else if (ereg('IRIX', $agent)) {
		$os = 'IRIX';
	} else if (eregi('FreeBSD', $agent)) {
		$os = 'FreeBSD';
	} else if (eregi('teleport', $agent)) {
		$os = 'teleport';
	} else if (eregi('flashget', $agent)) {
		$os = 'flashget';
	} else if (eregi('webzip', $agent)) {
		$os = 'webzip';
	} else if (eregi('offline', $agent)) {
		$os = 'offline';
	} else {
		$os = 'Unknown';
	}
	return $os;
}

//中文转换
function iconv_app($str){
    $config['app_charset']	 ='utf-8';
    $config['check_charset'] = 'ASCII,UTF-8,GBK';
	$result = iconv($config['system_charset'], $config['app_charset'], $str);
	if (strlen($result)==0) {
		$result = $str;
	}
	return $result;
}
function iconv_system($str){
    $config['app_charset']	 ='utf-8';
    $config['check_charset'] = 'ASCII,UTF-8,GBK';
	$result = iconv($config['app_charset'], $config['system_charset'], $str);
	if (strlen($result)==0) {
		$result = $str;
	}
	return $result;
}
