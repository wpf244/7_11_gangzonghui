<?php

namespace app\other\controller;


use think\Request;

class Login extends Common
{
    public function  index(){
        return view('index');
    }
    public function check(){
       // $data = input('post.');
        if(!captcha_check(input('post.verify'))) {
            // 校验失败
            $this->error('验证码不正确');exit;
        }
        $unm=input('post.username');
        $pwd=input('post.password');
        $re=db("shop")->where(array('phone'=>$unm,'pwd'=>$pwd))->find();
        if($re){
           if($re['status'] == 1 && $re['statu'] == 1){
            session('uid',$re['id']);
            \session('username',$re['username']);
           
            $ip=Request::instance()->ip();
           
            //增加操作日志
            $arr=array();
            $arr['type']="后台登录";
            $arr['time']=date('Y-m-d H:i:s',time());
            $arr['admin']=$re['username'];
            $arr['ip']=$ip;
            $this->logs->add_logs($arr);
            
            $this->success('登陆成功 ^_^',url('Index/index'));
           }else{
            $this->error('登录失败：此账号未激活。',url('Login/index'));
           } 
          
        }else{
            $this->error('登录失败：用户名或密码错误。',url('Login/index'));
        }
    }
    public function logout(){
        session("uid",null);
        $this->redirect('Login/index');
    }
    function modify(){
        if (! defined('CONTROLLER_NAME')) {
            define('CONTROLLER_NAME', $this->request->controller());
        }
        if (! defined('ACTION_NAME')) {
            define('ACTION_NAME', $this->request->action());
        }
        $id=\session('uid');
        $re=db("shop")->where("id=$id")->find();
        $this->assign("re",$re);
        return view('modify');
    }
    function save(){
       
        $ob=db("shop");
        $old_pwd=input('old_pwd');
        $id=input('id');
        $re=$ob->where("id=$id and pwd='$old_pwd'")->find();
        if($re){
            $data['pwd']=input('pwd');
            $res=$ob->where("id=$id")->update($data);
            if($res){
                $this->success("修改成功！");
            }else{
                $this->error("修改失败！");
            }
        }else{
            $this->error("原密码错误！");
        }

        
    }
 


}