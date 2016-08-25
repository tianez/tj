<?php
namespace Home\Controller;
use Think\Controller;
class IndexController extends Controller {
	public function _initialize() {
		header('Content-Type: text/html; charset=UTF-8');
		cookie('browser', getbrowser());
		cookie('system', getplat());
	}

	public function index() {
		$cur_user = cookie('cur_user');
		if (!empty($cur_user)) {
			redirect(U('home/index/result'));
		}
		//		$Mobile_Detect = Mobile_Detect();
		//		dump($Mobile_Detect->isTablet());
		if (IS_POST) {
			$code = md5(I('post.yzm'));
			$yzm_code = cookie('yzm_code');
			// if ($code == $yzm_code) {
				$map['client_number'] = I('post.id');
				$user = M('members') -> where($map) -> find();
				if (is_array($user)) {
					// $password = md5(I('post.password'));
					$password = I('post.password');
					if ($password === $user['password']) {
						if ($user['views'] > (O('search_num') - 1)) {
							$this -> error('你已经超过查询次数限制！');
						} else {
							M('members') -> where($map) -> setInc('views', 1);
							M('setup') -> where(array('subject' => 'totle_view')) -> setInc('parameter', 1);
							$data['client_number'] = $user['client_number'];
							$data['client_name'] = $user['client_name'];
							$data['query_views'] = $user['query_views']+1;
							$data['client_ip'] = get_client_ip();
							$data['browser'] = cookie('browser');
							$data['operating_system'] = cookie('system');
							M('client_log') -> add($data);
							cookie('cur_user', $user);
							redirect(U('home/index/result'));
						}
					} else {
						$this -> error('查询密码错误！');
					}
				} else {
					$this -> error('该体检单号不存在！');
				}
			// } else {
			// 	$this -> error('验证码错误！');
			// }
		}
		M('setup') -> where(array('subject' => 'totle_pv')) -> setInc('parameter', 1);
		$this -> display();
	}

	public function result() {
		$cur_user = cookie('cur_user');
		if (empty($cur_user)) {
			redirect(U('index'));
		}
		M('setup') -> where(array('subject' => 'view_pv')) -> setInc('parameter', 1);
		$map['client_number'] = $cur_user['client_number'];
		$results = M('report') -> where($map) -> select();
		$res = array();
		foreach ($results as $result) {
			if ($result['result'] == ' 空')
				continue;
			if (empty($res[$result['item']])) {
				$res[$result['item']] = $result['result'];
			} else {
				$res[$result['item']] .= '<br>' . $result['result'];
			}
		}
		$this -> assign('cur_user', $cur_user);
		$this -> assign('results', $res);
		$this -> display();
	}

	public function user_out() {
		action_log('登出');
		cookie('cur_user', null);
		redirect(U('index'));
	}

	public function login() {
		echo md5('123456');
		echo "<br />";
		echo encrypt('tianez');
		$user = cookie('user');
		if (!empty($user)) {
			redirect(U('admin/index'));
		}
		if (IS_POST) {
			$code = I('post.yzm');
			$code = md5($code);
			$yzm_code = cookie('yzm_code');
			if ($code == $yzm_code) {
				$data['user_account'] = encrypt(I('post.username'));
				// $data['user_account'] = I('post.username');
				$data['password'] = md5(I('post.password'));
				$M = M('sys');
				$user = $M -> where($data) -> find();
				if (!empty($user)) {
					$M -> where(array('id' => $user[id])) -> setInc('login_totals', 1);
					cookie('user', $user);
					action_log('登录');
					mark('登录成功！');
					redirect(U('admin/index'));
				} 
				else {
					$this -> error('用户名或密码错误！');
				}
			} else {
				$this -> error('验证码错误！');
			}
		}
		$this -> display();
	}

	public function verify() {
		$num1 = floor(mt_rand(10, 99));
		$num2 = floor(mt_rand(10, 99));
		if($num1>$num2){
			$role = '-';
			$num = $num1 - $num2;
		}else{
			$role = '+';
			$num = $num1 + $num2;
		}
		cookie('yzm_code', md5($num));
		$img = imagecreate(240, 100);
		$black = imagecolorallocate($img, 245, 245, 245);
		$white = imagecolorallocate($img, 100, 100, 100);
		$font = "./public/fonts/msyh.ttf";
		imagettftext($img, 40, 0, 10, 70, $white, $font, $num1);
		imagettftext($img, 40, 0, 80, 70, $white, $font, $role);
		imagettftext($img, 40, 0, 120, 70, $white, $font, $num2);
		imagettftext($img, 40, 0, 190, 70, $white, $font, '=');

		header('Cache-Control: private, max-age=0, no-store, no-cache, must-revalidate');
		header('Cache-Control: post-check=0, pre-check=0', false);
		header('Pragma: no-cache');
		header("content-type: image/png");
		imagepng($img);
		imagedestroy($img);
		die();
	}

	//创建数据库表
	public function install() {
		//读取SQL文件
		$sql = file_get_contents(MODULE_PATH . 'Data/install.sql');
		$sql = str_replace("\r", "\n", $sql);
		$sql = explode(";\n", $sql);
		//替换表前缀
		$sql = str_replace("es9e", C('DB_PREFIX'), $sql);
		//开始安装
		$this -> show_msg('开始安装数据库...');
		foreach ($sql as $value) {
			$value = trim($value);
			if (empty($value))
				continue;
			if (substr($value, 0, 12) == 'CREATE TABLE') {
				$name = preg_replace("/^CREATE TABLE `(\w+)` .*/s", "\\1", $value);
				$msg = "创建数据表{$name}";
				if (false !== M() -> execute($value)) {
					$this -> show_msg($msg . '...成功');
				} else {
					$this -> show_msg($msg . '...失败！', 'error');
					session('error', true);
				}
			} else {
				M() -> execute($value);
			}
		}
	}

	private function show_msg($msg) {
		echo $msg . "<br>";
		ob_flush();
		flush();
	}

	//	图片上传
	public function uploadimg() {
		$imgs = $this -> upload_img();
		if (is_array($imgs)) {
			echo json_encode($imgs);
		} else {
			echo $imgs;
		}
	}

	//图片上传
	private function upload_img() {
		$saveName = I('post.saveName') ? iconv_system(I('post.saveName')) . '-' . date('Ymd') . '-' . mtime() : array('uniqid', '');
		$subName = I('post.subName') ? I('post.subName') : date('Ym');
		$subName = iconv_system($subName);
		$config = array('maxSize' => 3145728, 'rootPath' => './Public/', 'savePath' => 'uploads/image/', 'saveName' => $saveName, 'exts' => array('jpg', 'gif', 'png', 'jpeg'), 'subName' => $subName,
		//		'replace'    =>    true,
		);
		$upload = new \Think\Upload($config);
		$info = $upload -> upload();
		if (!$info) {// 上传错误提示错误信息
			return $upload -> getError();
		} else {// 上传成功
			foreach ($info as $key => $file) {
				$file['rootPath'] = $config['rootPath'];
				$file['savename'] = iconv_app($file['savename']);
				$file['savepath'] = iconv_app($file['savepath']);
				$imgs[] = $file;
			}
			return $imgs;
		}
	}

}
