<?php
/**
 * 用户组视图模型
 * @author tianenzhong <saber_tz@163.com>
 **/ 
 
namespace Common\Model;
use Think\Model\ViewModel;
class UsersViewModel extends ViewModel {
   public $viewFields = array(
     'members'=>array('id','user_name','password','checktime',
         '_type'=>'left'),
     'results'=>array('item','result',
         '_on'=>'members.id=results.id'),
   );
 }
