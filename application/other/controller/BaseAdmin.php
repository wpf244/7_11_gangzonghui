<?php

namespace app\other\controller;

use think\Controller;
use app\other\model\Sever;

class BaseAdmin extends Controller{
    function _initialize(){
        if (!defined('CONTROLLER_NAME')) {
            define('CONTROLLER_NAME', $this->request->controller());
        }
        if (!defined('ACTION_NAME')) {
            define('ACTION_NAME', $this->request->action());
        }
        if(empty(session('uid'))){
            $this->redirect("login/index");
        }
        $sys=db('sys')->where("id=1")->find();
        $this->assign("sys",$sys);
        
        $uid=session('uid');
        $admin=db('shop')->where("id=$uid")->find();
        $this->assign("admin",$admin);

        
        $this->logs=new Sever();
        

        
    }
}