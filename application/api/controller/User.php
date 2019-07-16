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
    /**
    * 图标列表
    *
    * @return void
    */
    public function lister()
    {
        $url=parent::getUrl();

        $collect=db("lb")->field("name,image")->where(["fid"=>8])->find();

        $collect['image']=$url.$collect['image'];

        $addr=db("lb")->field("name,image")->where(["fid"=>9])->find();

        $addr['image']=$url.$addr['image'];

        $relese=db("lb")->field("name,image")->where(["fid"=>10])->find();

        $relese['image']=$url.$relese['image'];

        $custo=db("lb")->field("name,image")->where(["fid"=>11])->find();

        $custo['image']=$url.$custo['image'];

        $help=db("lb")->field("name,image")->where(["fid"=>12])->find();

        $help['image']=$url.$help['image'];

        $join=db("lb")->field("name,image")->where(["fid"=>13])->find();

        $join['image']=$url.$join['image'];

        $shop=db("lb")->field("name,image")->where(["fid"=>14])->find();

        $shop['image']=$url.$shop['image'];

        $arr=[
            'error_code'=>0,
            'msg'=>"获取成功",
            'data'=>[
                'collect'=>$collect,
                'addr'=>$addr,
                'relese'=>$relese,
                'custo'=>$custo,
                'help'=>$help,
                'join'=>$join,
                'shop'=>$shop,
                ]
        ];
        return json($arr);
    }
    /**
    * 我的收藏
    *
    * @return void
    */
    public function collect()
    {
        $uid=Request::instance()->header("uid");

        $res=db("collect")->alias("a")->field("b.id,b.name,b.logo,b.phone,b.addr,b.manage")->where(["a.uid"=>$uid])->join("shop b","a.sid=b.id")->select();

        $url=parent::getUrl();

        foreach($res as &$v){
            $v['logo']=$url.$v['logo'];
        }

        $arr=[
            'error_code'=>0,
            'msg'=>"获取成功",
            'data'=>$res
        ];
        return json($arr);
    }
    /**
    * 我的发布
    *
    * @return void
    */
    public function relese()
    {
        $uid=Request::instance()->header("uid");

        $status=input("status");

        $map=[];

        if($status){
            $map['a.status']=["eq",$status];
        }
        $res=db("info")->alias("a")->field("a.id,a.content,a.time,a.status,b.name as tname")->where(["uid"=>$uid])->where($map)->join("type b","a.tid=b.id")->select();

        $arr=[
            'error_code'=>0,
            'msg'=>"获取成功",
            'data'=>$res
        ];
        return json($arr);
    }
    /**
    * 保存收货地址
    *
    * @return void
    */
    public function save_addr(){
        
        $uid=Request::instance()->header("uid");

        $data=input('post.');
        
        $data['uid']=$uid;
        $rea=db("addr")->insert($data);
        if($rea){
            $arr=[
                'error_code'=>0,
                'msg'=>"保存成功",
                'data'=>[]
            ];
        }else{
            $arr=[
                'error_code'=>1,
                'msg'=>"保存失败",
                'data'=>[]
            ];
        }
        return json($arr);
    }
    public function addr()
    {
        $uid=Request::instance()->header("uid");
        $res=db("addr")->where("uid",$uid)->order("aid asc")->select();
        $arr=[
            'error_code'=>0,
            'msg'=>"保存成功",
            'data'=>$res
        ];
        return json($arr);
    }
     //删除收货地址
     public function delete_addr()
     {
         $aid=input('aid');
         $re=db("addr")->where("aid",$aid)->find();
         if($re){
             $del=db("addr")->where("aid",$aid)->delete();
             $arr=[
                'error_code'=>0,
                'msg'=>"删除成功",
                'data'=>[]
            ];
         }else{
            $arr=[
                'error_code'=>1,
                'msg'=>"删除失败",
                'data'=>[]
            ];
         }
         return json($arr);
     }
       //修改收货地址
       public function update_addr()
       {
           $aid=input('aid');
           $re=db("addr")->where("aid",$aid)->find();
           $arr=[
            'error_code'=>0,
            'msg'=>"获取成功",
            'data'=>$re
        ];
        return json($arr);
       }
       public function usave_addr()
       {
           $aid=input('aid');
           $data=input('post.');
           $re=db("addr")->where("aid",$aid)->find();
           if($re){
               $res=db("addr")->where("aid",$aid)->update($data);
               $arr=[
                'error_code'=>0,
                'msg'=>"修改成功",
                'data'=>[]
            ];
           }else{
            $arr=[
                'error_code'=>1,
                'msg'=>"修改失败",
                'data'=>[]
            ];
           }
           return json($arr);
       }
       /**
       * 商户入口
       *
       * @return void
       */
      public function join()
      {
          $uid=Request::instance()->header("uid");

          $re=db("shop")->where(["uid"=>$uid,"statu"=>1])->find();

          if($re){
            
            $join=db("shop_other")->field("name")->where("type",5)->find();

            $join['username']=$re['phone'];

            $join['pwd']=$re['pwd'];

            $arr=[
                'error_code'=>1,
                'msg'=>"获取成功",
                'data'=>$join
            ]; 

          }else{
            $arr=[
                'error_code'=>1,
                'msg'=>"您还不是入住商户",
                'data'=>[]
            ]; 
          }
          return json($arr);
      }
















}