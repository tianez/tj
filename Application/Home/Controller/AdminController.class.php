<?php
namespace Home\Controller;
use Think\Controller;
class AdminController extends Controller {
	public function _initialize() {
		header('Content-Type: text/html; charset=UTF-8');
		cookie('browser', getbrowser());
		cookie('system', getplat());
		$user = cookie('user');
		if (empty($user)) {
			redirect(U('index/login'));
		} else {
			$this -> assign('user', cookie('user'));
		}
	}

	//后台首页
	public function index() {
		$data['num'] = M('members') -> count();
		$map['views'] = array('gt', 0);
		$data['already'] = M('members') -> where($map) -> count();
		$data['updata_time'] = date("Y年m月d日 h:i:s A", O('updata_time'));
		$data['totle_num'] = O('totle_num');
		$data['totle_view'] = O('totle_view');
		$data['totle_pv'] = O('totle_pv');
		$data['view_pv'] = O('view_pv');
		$this -> assign('data', $data);
		$this -> assign('title', '基本信息');
		$this -> assign('cur', 'index');
		$this -> display();
	}

	//数据导入
	public function import() {
		//		header('Content-Type: text/html; charset=UTF-8');
		if (IS_POST) {
			$filename = $_FILES["ff"]['name'];
			$type = substr($filename, strrpos($filename, '.') + 1);
			if ($type != 'sql') {
				mark("上传的数据格式不对，请上传.sql格式的数据文件。");
			} else {
				$sql = file_get_contents($_FILES['ff']['tmp_name']);
				$a = explode(";", $sql);
				array_pop($a);
				$table = I('post.table');
				$add = 0;
				$updata = 0;
				$curt = mtime();
				if ($table == 'member') {
					foreach ($a as $b) {
						$c = $this -> str($b);
						$data['client_number'] = str_replace(' ', '', $c[0]);
						$c[1] = str_replace(' ', '', $c[1]);
						$data['client_name'] = substr_replace($c[1], "■", 3, 3);
						$data['client_name'] = encrypt($data['client_name']);
						$c[2] = str_replace(' ', '', $c[2]);
						$data['china_id'] = substr_replace($c[2], "■", 3, 3);
						$data['china_id'] = encrypt($data['china_id']);
						// $data['password'] = md5(str_replace(' ', '', $c[3]));
						$data['password'] = str_replace(' ', '', $c[3]);
						$data['frist_data'] = $c[4];
						$d = M('members') -> where(array('client_number' => $c[0])) -> find();
						if (is_array($d)) {
							$result = M('members') -> save($data);
							$updata++;
						} else {
							$result = M('members') -> add($data);
							$add++;
						}
					}
					$time = mtime() - $curt;
					$msg = "体检用户导入成功！新增" . $add . "条数据，更新" . $updata . "条数据，耗时" . $time . "秒";
					mark($msg);
					if ($add > 0) {
						M('setup') -> where(array('subject' => 'totle_num')) -> setInc('parameter', $add);
					}
					action_log('数据导入', $msg);
					O2('updata_time', time());
				} elseif ($table == 'result') {
					$sql = $sqlc = "INSERT INTO `" . C('DB_PREFIX') . "report` (`client_number`, `item`, `result`) VALUES ";
					foreach ($a as $b) {
						$c = $this -> str($b);
						$data['client_number'] = str_replace(' ', '', $c[0]);
						$data['item'] = $c[1];
						$data['result'] = $c[2];
						$d = M('report') -> where($data) -> find();
						if (!is_array($d)) {
							$sql .= "('" . $data['client_number'] . "', '" . $data['item'] . "', '" . $data['result'] . "'),";
							$add++;
						}
					}
					if ($sql != $sqlc) {
						$sql = substr($sql, 0, strlen($sql) - 1);
						M() -> execute($sql);
					}
					$time = mtime() - $curt;
					$msg = "体检结果导入成功！新增" . $add . "条数据，耗时" . $time . "秒";
					mark($msg);
					action_log('数据导入', $msg);
					O2('updata_time', time());
				} else {
					mark("出错啦!");
				}
			}
		}
		$this -> assign('title', '资料导入');
		$this -> assign('cur', 'import');
		$this -> display();
	}

	//  安全日志
	public function safe() {
		$action_log = M('sys_log');
		$res = $action_log -> where('date_sub(curdate(), INTERVAL '.O('reserve').' DAY) >= date(`event_time`)') -> delete();
		$datas = $action_log -> order('event_time desc') -> page(I('get.p')) -> limit(10) -> select();
		$count = count($datas);
		$Page = new \Think\Page($count, 5);
		$show = $Page -> show();
		$this -> assign('datas', $datas);
		$this -> assign('show', $show);
		$this -> assign('title', '安全日志');
		$this -> assign('cur', 'safe');
		$this -> display();
	}

	//  用户查询记录
	public function view() {
		$view_log = M('client_log');
		$user = cookie('user');
		$page_number = $user['page_number'];
		$res = $view_log -> where('date_sub(curdate(), INTERVAL '.O('reserve').' DAY) >= date(`query_time`)') -> delete();
		$datas = $view_log -> order('query_time desc') -> page(I('get.p')) -> limit($page_number) -> select();
		$count = count($datas);
		$Page = new \Think\Page($count, 5);
		$show = $Page -> show();
		$this -> assign('datas', $datas);
		$this -> assign('show', $show);
		$this -> assign('title', '查询记录');
		$this -> assign('cur', 'view');
		$this -> display();
	}

	//  数据清理
	public function clear() {
		if (IS_POST) {
			$this -> clear_up();
		}
		$this -> assign('title', '数据清理');
		$this -> assign('cur', 'clear');
		$this -> assign('reserve', O('reserve'));
		$this -> display();

	}

	private function clear_up() {
		$res = M('members') -> where('date_sub(curdate(), INTERVAL '.O('reserve').' DAY) >= date(`frist_data`)') -> select();
		$count = count($res);
		if ($count > 0) {
			foreach ($res as $re) {
				$map['id'] = $re['id'];
				M('members') -> where($map) -> delete();
				M('report') -> where($map) -> delete();
			}
			$msg = '数据清理成功，清除 <b>' . $count . '</b> 个用户的体检数据！';
			action_log('数据清理', $msg);
			mark($msg);
		} else {
			mark('没有数据需要清除！');
		}
	}

	//管理员
	public function user() {
		$user = cookie('user');
		if (IS_POST) {
			$info = I('post.info');
			if ($info == 'password') {
				$pre_password = I('post.pre_password');
				$password = I('post.npassword');
				$repassword = I('post.repassword');
				if (!empty($password)) {
					if ($password !== $repassword) {
						mark('两次输入的密码不一致，请重新确认！');
					} else {
						$user['id'] = $user['id'];
						$user['password'] = md5($pre_password);
						$user = M('sys') -> where($data) -> find();
						if (empty($user)) {
							mark('旧密码验证错误！');
						} else {
							$user['password'] = md5($password);
							M('sys') -> save($user);
							mark('密码更新成功！');
						}
					}
				}
			} elseif ($info == 'info') {
				$post = I('post.');
				$data['id'] = $user['id'];
				if($post['user_name']!=encrypt_username($user['user_name'])){
					$data['user_name'] =  encrypt($post['user_name']);
				}
				if($post['china_id']!=encrypt_china_id($user['china_id'])){
					$data['china_id'] =  encrypt($post['china_id']);
				}
				if($post['mobile_phone']!=encrypt_mobile_phone($user['mobile_phone'])){
					$data['mobile_phone'] =  encrypt($post['mobile_phone']);
				}
				if($post['email']!=encrypt($user['email'],'D')){
					$data['email'] =  encrypt($post['email']);
				}
				if($post['qq_number']!=encrypt($user['qq_number'],'D')){
					$data['qq_number'] =  encrypt($post['qq_number']);
				}
				$data['office_phone'] =  $post['office_phone'];
				$data['head_image'] =  $post['head_image'];
				M('sys') -> save($data);
				mark('信息保存成功！');
			} else {
				mark('没有任何信息需要保存！');
			}
		}
		$map['id'] = $user['id'];
		$user = M('sys') -> where($map) -> find();
		cookie('user',$user);
		$this -> assign('user', $user);
		$this -> assign('title', '管理员信息');
		$this -> assign('cur', 'user');
		$this -> display();
	}

	//	设置
	public function setting() {
		if (IS_POST) {
			$search_num = I('post.search_num');
			O2('search_num', $search_num);
			mark('设置保存成功！');
		}
		$this -> assign('title', '系统设置');
		$this -> assign('cur', 'setting');
		$this -> display();
	}

	//登出
	public function logout() {
		action_log('登出');
		cookie('user', null);
		redirect(U('index/login'));
	}

	//  分解数据字段
	private function str($arr) {
		$length = strpos($arr, "(") + 1;
		$arr = substr_replace($arr, "", 0, $length);
		$arr = substr_replace($arr, "", -1, 1);
		$a = array("'" => "");
		$arr = strtr($arr, $a);
		$arr = explode(",", $arr);
		return $arr;
	}

}
