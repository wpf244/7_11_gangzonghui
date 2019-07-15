<?php
/**
 * 分类信息
 */
namespace app\api\controller;

use think\Request;

class Genre extends BaseHome
{
    public function index()
    {
        $url=parent::getUrl();

        //轮播
        $banner=db("lb")->field("url,image")->where(["fid"=>6,"status"=>1])->order(["sort asc","id desc"])->select();

        foreach($banner as &$v){
            $v['image']=$url.$v['image'];
        }

        //分类
        $type=db("type")->field("id,name")->where(["fid"=>0,"status"=>1])->order(["sort asc","id asc"])->select();

        $arr=[
            'error_code'=>0,
            'msg'=>"操作成功",
            'data'=>[
                'banner'=>$banner,
                'type'=>$type
            ]
        ];

        return json($arr);


    }
    /**
    * 信息列表
    *
    * @return void
    */
    public function lister()
    {
        $url=parent::getUrl();
        
        $id=input("id");

        $map=[];

        if($id){
            $map['tid']=['eq',$id];
        }
        $res=db("info")->alias("a")->where(["status"=>2])->where($map)->field("a.*,b.nickname,b.image as head_photo")->join("user b","a.uid=b.uid")->order("id desc")->select();

        foreach($res as &$v){
            $image=$v['image'];

            $images=explode(",",$image);

            foreach($images as $vs){
                $img[]=$url.$vs;
            }
            $v['image']=$img;

            $up=db("info_up")->alias("a")->field("b.image as head_photo")->where(["nid"=>$v['id']])->join("user b","a.uid=b.uid")->order("id desc")->select();

            $v['up']=$up;
        }

        $arr=[
            'error_code'=>0,
            'msg'=>"获取成功",
            'data'=>$res
        ];

        return json($arr);
    }
    /**
    * 点赞
    *
    * @return void
    */
    public function info_up()
    {
        $uid=Request::instance()->header("uid");

        $id=input("id");

        $re=db("info_up")->where(["uid"=>$uid,"nid"=>$id])->find();

        if($re){
            $arr=[
                'error_code'=>1,
                'msg'=>"请勿重复点赞",
                'data'=>[]
            ];
        }else{
            $data['uid']=$uid;
            $data['nid']=$id;

            $rea=db("info_up")->insert($data);

            if($rea){
                $arr=[
                    'error_code'=>0,
                    'msg'=>"点赞成功",
                    'data'=>[]
                ];
            }else{
                $arr=[
                    'error_code'=>2,
                    'msg'=>"点赞失败",
                    'data'=>[]
                ];
            }
        }
        return json($arr);
    }
    /**
    * 保存评论
    *
    * @return void
    */
    public function save_assess()
    {
        $uid=Request::instance()->header("uid");

        $data=input("post.");
        $data['uid']=$uid;
        $data['time']=time();
        $data['type']=2;
        
        $rea=db("assess")->insert($data);

        if($rea){
            $arr=[
                'error_code'=>0,
                'msg'=>"评论成功",
                'data'=>[]
            ];
        }else{
            $arr=[
                'error_code'=>1,
                'msg'=>"评论失败",
                'data'=>[]
            ];
        }
        return json($arr);
    }
    /**
    * 信息详情
    *
    * @return void
    */
    public function detail()
    { 
        $url=parent::getUrl();
        
        $id=input("id");

        $re=db("info")->alias("a")->where("id",$id)->field("a.*,b.nickname,b.image as head_photo")->join("user b","a.uid=b.uid")->find();

     
            $image=$re['image'];

            $images=explode(",",$image);

            foreach($images as $vs){
                $img[]=$url.$vs;
            }
            $re['image']=$img;

            $up=db("info_up")->alias("a")->field("b.image as head_photo")->where(["nid"=>$re['id']])->join("user b","a.uid=b.uid")->order("id desc")->select();

            $re['up']=$up;
        

        $arr=[
            'error_code'=>0,
            'msg'=>"获取成功",
            'data'=>$re
        ];

        return json($arr);

    }
    /**
    * 用户评价
    *
    * @return void
    */
    public function assess()
    {
        $id=input("id");

        $res=db("assess")->alias("a")->where(["sid"=>$id,"status"=>1,"type"=>2])->field("content,nickname,image")->join("user b","a.uid=b.uid")->order("id desc")->select();

        $arr=[
            'error_code'=>0,
            'msg'=>"获取成功",
            'data'=>$res
        ];

        return json($arr);
    }
}