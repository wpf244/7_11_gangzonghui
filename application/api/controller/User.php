<?php
namespace app\api\controller;

use think\Request;

class User extends BaseHome
{
    /**
    * 帮助中心
    *
    * @return void
    */
    public function help()
    {
        $res=db("help")->where(["status"=>1])->order(["sort asc","id desc"])->select();

        foreach($res as $k => $v){
            $res[$k]['content']=strip_tags($v['content']);
        }

        $arr=[
            'error_code'=>0,
            'msg'=>"获取成功",
            'data'=>$res
        ];
        return json($arr);
    }
    /**
    * 个人信息
    *
    * @return void
    */
    public function info()
    {
        $uid=Request::instance()->header("uid");

        $re=db("user")->field("nickname,image")->where("uid",$uid)->find();

        $arr=[
            'error_code'=>0,
            'msg'=>"获取成功",
            'data'=>$re
        ];
        return json($arr);
    }
}