<?php
namespace app\api\controller;

use think\Request;

class Shop extends BaseHome
{
    public function index()
    {
        $url=parent::getUrl();

        //banner图
        $banner=db("lb")->field("url,image")->where(["fid"=>15,"status"=>1])->order(["sort asc","id desc"])->select();
        
        foreach($banner as &$v){
            $v['image']=$url.$v['image'];
        }

        $city=input("city");

        $longs=input("longs");

        $lats=input("lats");

        //附近
        $nearby=db("shop")->field("id,name,addr,manage,phone,logo,longs,lats")->where(["statu"=>1,"status"=>1,"addr"=>['like',"%".$city."%"]])->order(["sort asc","id desc"])->select();

        foreach($nearby as $kr => $vr){
            $nearby[$kr]['logo']=$url.$vr['logo'];
            $nearby[$kr]['gap']=$this->getDistance($lats,$longs,$vr['lats'],$vr['longs']);
        }

        //新入
        $news=db("shop")->field("id,name,addr,manage,phone,logo,longs,lats")->where(["statu"=>1,"status"=>1,"news"=>1,"addr"=>['like',"%".$city."%"]])->order(["sort asc","id desc"])->select();

        foreach($news as $kr => $vr){
            $news[$kr]['logo']=$url.$vr['logo'];
            $news[$kr]['gap']=$this->getDistance($lats,$longs,$vr['lats'],$vr['longs']);
        }

        //热门
        $hot=db("shop")->field("id,name,addr,manage,phone,logo,longs,lats")->where(["statu"=>1,"status"=>1,"hot"=>1,"addr"=>['like',"%".$city."%"]])->order(["sort asc","id desc"])->select();

        foreach($hot as $kr => $vr){
            $hot[$kr]['logo']=$url.$vr['logo'];
            $hot[$kr]['gap']=$this->getDistance($lats,$longs,$vr['lats'],$vr['longs']);
        }

        $arr=[
            'error_code'=>0,
            'msg'=>"获取成功",
            'data'=>[
                'banner'=>$banner,
                'nearby'=>$nearby,
                'news'=>$news,
                'hot'=>$hot,
               
            ]
        ];
        return json($arr);

    }
     /**
    * @param $lat1
    * @param $lng1
    * @param $lat2
    * @param $lng2
    * @return int
    */
    function getDistance($lat1, $lng1, $lat2, $lng2){

        //将角度转为狐度

        $radLat1=deg2rad($lat1);//deg2rad()函数将角度转换为弧度

        $radLat2=deg2rad($lat2);

        $radLng1=deg2rad($lng1);

        $radLng2=deg2rad($lng2);

        $a=$radLat1-$radLat2;

        $b=$radLng1-$radLng2;

        $s=2*asin(sqrt(pow(sin($a/2),2)+cos($radLat1)*cos($radLat2)*pow(sin($b/2),2)))*6378.137;

        $s= sprintf("%.2f",$s);
        
        return $s;

    }
    
    /**
    * 商家入驻 信息列表
    *
    * @return void
    */
    public function lister()
    {
        //产品分类
        $type=db("type")->field("id,name")->where(["fid"=>0,"status"=>1])->order(["sort asc","id asc"])->select();

        foreach($type as &$vs){
            $vs['next']=db("type")->field("id,name")->where("fid",$vs['id'])->select();
        }

        //配套设施

        $sever=db("shop_sever")->order("id asc")->select();

        $url=parent::getUrl();

        foreach($sever as &$v){
            $v['sever_image']=$url.$v['sever_image'];
        }

        $arr=[
            'error_code'=>0,
            'msg'=>"获取成功",
            'data'=>[
                'type'=>$type,
                'sever'=>$sever,
               
            ]
        ];
        return json($arr);
    }
    /**
    * 获取二级分类
    *
    * @return void
    */
    public function get_type()
    {
        $id=input("id");

        $res=db("type")->field("id,name")->where(["fid"=>$id,"status"=>1])->order(["sort asc","id asc"])->select();

        $arr=[
            'error_code'=>0,
            'msg'=>"获取成功",
            'data'=>$res
        ];
        return json($arr);
    }
    /**
    * 商家入驻
    *
    * @return void
    */
    public function shop()
    {
        $uid=Request::instance()->header("uid");

        $url=parent::getUrl();

        $re=db("shop")->where(["uid"=>$uid])->find();

        if($re){
            if($re['statu'] == 1){
                $arr=[
                    'error_code'=>2,
                    'msg'=>"您已经是入驻商家了",
                    'data'=>[]
                ];
            }
            if($re['statu'] == 0){

                $re['severs']=explode(",",$re['severs']);

                $re['logo']=$url.$re['logo'];

                $re['wx']=$url.$re['wx'];

                $re['busi']=$url.$re['busi'];

                $time=explode("-",$re['time']);

                $re['start_time']=$time[0];

                $re['end_time']=$time[1];

                

                $images=\explode(",",$re['images']);

                foreach($images as $v){
                    $imagess[]=$url.$v;
                }
                $re['images']=$imagess;

                $banner=\explode(",",$re['banner']);

                foreach($banner as $v){
                    $banners[]=$url.$v;
                }
                $re['banner']=$banners;

                $arr=[
                    'error_code'=>3,
                    'msg'=>"申请中",
                    'data'=>$re
                ];
            }
            if($re['statu'] == 3){

                $re['severs']=explode(",",$re['severs']);

                $re['logo']=$url.$re['logo'];

                $re['wx']=$url.$re['wx'];

                $re['busi']=$url.$re['busi'];

                $time=explode("-",$re['time']);

                $re['start_time']=$time[0];

                $re['end_time']=$time[1];

                $images=\explode(",",$re['images']);

                foreach($images as $v){
                    $imagess[]=$url.$v;
                }
                $re['images']=$imagess;

                $banner=\explode(",",$re['banner']);

                foreach($banner as $v){
                    $banners[]=$url.$v;
                }
                $re['banner']=$banners;

                $arr=[
                    'error_code'=>4,
                    'msg'=>"申请已被驳回",
                    'data'=>$re
                ];
            }

        }else{
            $arr=[
                'error_code'=>0,
                'msg'=>"没有申请信息",
                'data'=>[]
            ];
        }
        return json($arr);
    }
    /**
    * 提交
    *
    * @return void
    */
    public function save()
    {
        $uid=Request::instance()->header("uid");

        $res=db("shop")->where("uid",$uid)->find();

        if(empty($res)){

            $code=input("code");
            $phone=input("phone");

            $re=db("sms_code")->where(['phone'=>$phone,'code'=>$code])->find();

        if($re){
            $time=$re['time'];

            $times=time();

            $c_time=($times-$time);

            if($c_time < 300){

                db("sms_code")->where("id",$re['id'])->delete();

    
                $data=input("post.");

                $data['severs']=implode(",",$data['severs']);

                $data['images']=implode(",",$data['images']);

                $data['banner']=implode(",",$data['banner']);

                $data['times']=time();

                $data['statu']=0;

                $data['type']=1;

                $data['uid']=$uid;

                unset($data['code']);

                $rea=db("shop")->insert($data);

                if($rea){
                    $arr=[
                        'error_code'=>0,
                        'msg'=>"申请成功,请等待审核",
                        'data'=>[]
                    ];
                }else{
                    $arr=[
                        'error_code'=>4,
                        'msg'=>"申请失败,请稍后再试",
                        'data'=>[]
                    ];
                }
            }else{

                $arr=[
                    'error_code'=>3,
                    'msg'=>"验证码已失效",
                    'data'=>[]
                ];

            }

        }else{
            $arr=[
                'error_code'=>2,
                'msg'=>"验证码错误",
                'data'=>[]
            ];
         }
        }else{
            $arr=[
                'error_code'=>1,
                'msg'=>"请勿重复提交",
                'data'=>[]
            ];
        }
        return json($arr);
    }

    /**
    * 修改提交
    *
    * @return void
    */
    public function usave()
    {
        $uid=Request::instance()->header("uid");

        $res=db("shop")->where("uid",$uid)->find();

        if($res){

            $code=input("code");
            $phone=input("phone");

            $re=db("sms_code")->where(['phone'=>$phone,'code'=>$code])->find();

        if($re){
            $time=$re['time'];

            $times=time();

            $c_time=($times-$time);

            if($c_time < 300){

                db("sms_code")->where("id",$re['id'])->delete();

                $data=input("post.");

               $data['severs']=implode(",",$data['severs']);

                if($data['images']){

                    $data['images']=implode(",",$data['images']);

                }else{
                    $data['images']=$res['images'];
                }

                if($data['banner']){

                    $data['banner']=implode(",",$data['banner']);

                }else{
                    $data['banner']=$res['banner'];
                }

                if(!$data['logo']){
                    $data['logo']=$res['logo'];
    
                }

                if(!$data['wx']){

                    $data['wx']=$res['wx'];

                }

                if(!$data['busi']){

                    $data['busi']=$res['busi'];

                }

                
                $data['times']=time();

                $data['statu']=0;

                $data['type']=1;

                $data['uid']=$uid;

                unset($data['code']);

                $rea=db("shop")->where("id",$res['id'])->update($data);

                if($rea){
                    $arr=[
                        'error_code'=>0,
                        'msg'=>"申请成功,请等待审核",
                        'data'=>[]
                    ];
                }else{
                    $arr=[
                        'error_code'=>4,
                        'msg'=>"申请失败,请稍后再试",
                        'data'=>[]
                    ];
                }
            }else{

                $arr=[
                    'error_code'=>3,
                    'msg'=>"验证码已失效",
                    'data'=>[]
                ];

            }

        }else{
            $arr=[
                'error_code'=>2,
                'msg'=>"验证码错误",
                'data'=>[]
            ];
         }
        }else{
            $arr=[
                'error_code'=>1,
                'msg'=>"还没有提交,如何修改",
                'data'=>[]
            ];
        }
        return json($arr);
    }

     /**
    * 获取验证码
    *
    * @return void
    */
    public function getcode(){
        
        $phone=input("phone");
        $code =mt_rand(100000,999999);       
        $data['phone']=$phone;
        $data['code']=$code;
        $data['time']=time();
        $re=\db("sms_code")->where("phone",$phone)->find();
        if($re){
            $del=db("sms_code")->where("phone",$phone)->delete();
        }
        $rea=db("sms_code")->insert($data);
        Post($phone,$code);
        if($rea){
            $arr=[
                'error_code'=>0,
                'msg'=>'发送成功',
                'data'=>''
            ];
        }else{
            $arr=[
                'error_code'=>1,
                'msg'=>'发送失败',
                'data'=>''
            ];
        }
           
        
        return  json($arr);
    }
}