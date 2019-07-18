<?php
namespace app\api\controller;

use think\Request;
use think\Validate;

class Info extends BaseHome
{
    public function index()
    {
        $url=parent::getUrl();

        $re=db("lb")->field("image")->where(["fid"=>5])->find();

        $re['image']=$url.$re['image'];

        $user=db("lb")->field("image")->where(["fid"=>7])->find();

        $user['image']=$url.$user['image'];

        $arr=[
            'error_code'=>0,
            'msg'=>"获取成功",
            'data'=>[
                'info'=>$re['image'],
                'user'=>$user['image'],
            ]
        ]; 
    
         return json($arr);

    }
    /**
    * 商家入驻
    *
    * @return void
    */
    public function save()
    {

    }
    /**
    * 钢材分类
    *
    * @return void
    */
    public function type()
    {
        $res=db("type")->field("id,name")->where("fid",0)->order(['sort asc',"id asc"])->select();

        $arr=[
            'error_code'=>0,
            'msg'=>"获取成功",
            'data'=>$res
        ]; 
    
         return json($arr);

    }
    /**
    * 信息发布
    *
    * @return void
    */
    public function save_info()
    {
         $uid=Request::instance()->header("uid");

         $data=input("post.");

        //  var_dump($data);exit;

         $data['image']=\implode(",",$data['image']);

         $data['uid']=$uid;

         $data['time']=time();

         $re=db("info")->insert($data);

         if($re){
            $arr=[
                'error_code'=>0,
                'msg'=>"发布成功",
                'data'=>[]
            ]; 
         }else{
            $arr=[
                'error_code'=>1,
                'msg'=>"发布失败",
                'data'=>[]
            ]; 
         }
         return json($arr);
    }
    /**
    * 上传图片
    *
    * @return void
    */
    public function add_img()
    {
        if(request()->file("image")){
            $image=uploads('image');

            if($image){
                $arr=[
                    'error_code'=>0,
                    'msg'=>"上传成功",
                    'data'=>$image
                ]; 
            }else{
                 $arr=[
                    'error_code'=>0,
                    'msg'=>"上传失败",
                    'data'=>''
                ]; 
            }
        }else{
             $arr=[
                'error_code'=>0,
                'msg'=>"上传失败",
                'data'=>''
            ]; 
        }
        
        return json($arr);
    }
}