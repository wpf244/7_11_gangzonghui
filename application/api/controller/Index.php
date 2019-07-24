<?php
namespace app\api\controller;

use think\Request;

class Index extends BaseApi
{
    public function index()
    {
        $url=parent::getUrl();
        //轮播图
        $banner=db("lb")->field("url,image")->where(["fid"=>1,"status"=>1])->order(["sort asc","id desc"])->select();
        
        foreach($banner as $kb => $vb){
            $banner[$kb]['image']=$url.$vb['image'];
        }

        //分类图标
        $type=db("type")->field("id,name,image")->where(["status"=>1,"fid"=>0])->order(["sort asc","id asc"])->select();

        foreach($type as $kt => $vt){
            $type[$kt]['image']=$url.$vt['image'];
        }

        //头条
        $sto=db("lb")->field("id,name")->where(["fid"=>2,"status"=>1])->order(["sort asc","id desc"])->select();

        //推荐企业
        $city=input("city");

        $longs=input("longs");

        $lats=input("lats");

        $recome=db("shop")->field("id,name,addr,manage,phone,logo,longs,lats")->where(["statu"=>1,"status"=>1,"recome"=>1,"addr"=>['like',"%".$city."%"]])->order(["sort asc","id desc"])->select();

        foreach($recome as $kr => $vr){
            $recome[$kr]['logo']=$url.$vr['logo'];
            $recome[$kr]['gap']=$this->getDistance($lats,$longs,$vr['lats'],$vr['longs']);
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
                'type'=>$type,
                'sto'=>$sto,
                'recome'=>$recome,
                'news'=>$news,
                'hot'=>$hot,
            ]
        ];
        return json($arr);

    }
    /**
    * 首页分类
    *
    * @return void
    */
    public function type()
    {
        $id=input("id");

        $url=parent::getUrl();

        $list=db("type")->field("id,name,image")->where(["status"=>1,"fid"=>$id])->order(["sort asc","id asc"])->select();

        foreach($list as $k => $v){
            $list[$k]['image']=$url.$v['image'];
        }

        //轮播图
        $banner=db("lb")->field("url,image")->where(["fid"=>3,"status"=>1])->order(["sort asc","id desc"])->select();
        
        foreach($banner as $kb => $vb){
            $banner[$kb]['image']=$url.$vb['image'];
        }

        $arr=[
            'error_code'=>0,
            'msg'=>"获取成功",
            'data'=>[
                'banner'=>$banner,
                'list'=>$list,
                
            ]
        ];
        return json($arr);

    }
    /**
    * 首页分类--商家列表
    *
    * @return void
    */
    public function lister()
    {
        $city=input("city");
 
        $url=parent::getUrl();

        $id=input("id");

        $longs=input("longs");

        $lats=input("lats");

        $res=db("shop")->field("id,name,addr,manage,phone,logo,lats,longs")->where(["statu"=>1,"status"=>1,"tid"=>$id,"addr"=>['like',"%".$city."%"]])->order(["sort asc","id desc"])->select();

        foreach($res as $kr => $vr){
            $res[$kr]['logo']=$url.$vr['logo'];
            $res[$kr]['gap']=$this->getDistance($lats,$longs,$vr['lats'],$vr['longs']);
        }
        $arr=[
            'error_code'=>0,
            'msg'=>"获取成功",
            'data'=>$res
        ];
        return json($arr);
    }
    /**
    * 热门搜索
    *
    * @return void
    */
    public function hot_search()
    {
        $res=db("lb")->field("name")->where(["fid"=>4,"status"=>1])->order(["sort asc","id desc"])->select();

        $arr=[
            'error_code'=>0,
            'msg'=>"获取成功",
            'data'=>$res
        ];
           
        return json($arr);
    }
    /**
    * 搜索列表
    *
    * @return void
    */
    public function search()
    {
        $url=parent::getUrl();
        
        $keywords=input("keywords");

        $longs=input("longs");

        $lats=input("lats");

        $res=db("shop")->field("id,name,addr,manage,phone,logo,longs,lats")->where(["statu"=>1,"status"=>1,"name|addr|manage"=>['like',"%".$keywords."%"]])->order(["sort asc","id desc"])->select();

        foreach($res as $kr => $vr){
            $res[$kr]['logo']=$url.$vr['logo'];
            $res[$kr]['gap']=$this->getDistance($lats,$longs,$vr['lats'],$vr['longs']);
        }
        $arr=[
            'error_code'=>0,
            'msg'=>"获取成功",
            'data'=>$res
        ];
        return json($arr);
    }
    /**
    * 商家详情
    *
    * @return void
    */
    public function detail()
    {
        $id=input("id");

        $url=parent::getUrl();

        $uid=Request::instance()->header("uid");

        $re=db("shop")->field("id,name,manage,logo,score,share,browse,addr,time,severs,content,wx,image")->where(["id"=>$id,"statu"=>1,"status"=>1])->find();

        if($re){

            $re['logo']=$url.$re['logo'];
            $re['wx']=$url.$re['wx'];
            $re['image']=$url.$re['image'];

            if($uid){
                $re['collect']=0;
            }else{
                //是否收藏
                $collect=db("collect")->where(["uid"=>$uid,"sid"=>$id])->find();

                if($collect){
                    $re['collect']=1;
                }else{
                    $re['collect']=0;
                }
            }

             

            //设施
            $severs=$re['severs'];

            $arr=\explode(",",$severs);

            $severs=db("shop_sever")->where("id","in",$arr)->select();

            foreach($severs as $k => $v){
                $severs[$k]['sever_image']=$url.$v['sever_image'];
            }
            $re['severs']=$severs;

            //轮播
            $banner=db("shop_other")->field("image")->where(["fid"=>$id,"type"=>3])->select();

            foreach($banner as $k => $v){
                $banner[$k]['image']=$url.$v['image'];
            }
            $re['banner']=$banner;

            //头条
            $sto=db("shop_other")->field("name")->where(["fid"=>$id,"type"=>4])->select();
            
            $re['sto']=$sto;

            //电话

            $tel=db("shop_other")->field("phone")->where(["fid"=>$id,"type"=>1])->select();
            
            $re['tel']=$tel;

            //联系人
            $people=db("shop_other")->field("name,phone,phones")->where(["fid"=>$id,"type"=>2])->select();
            
            $re['people']=$people;

           

            //增加浏览量
            db("shop")->where("id",$id)->setInc("browse",1);


            $arr=[
                'error_code'=>0,
                'msg'=>"获取成功",
                'data'=>$re
            ];

        }else{
            $arr=[
                'error_code'=>1,
                'msg'=>"暂无数据",
                'data'=>[]
            ];
        }
        return json($arr);
    }
    /**
    * 商家电话
    *
    * @return void
    */
    public function phone()
    {
        $id=input("id");

        $phone=db("shop_other")->field("name,phone")->where(["fid"=>$id,"type"=>1])->select();

        $people=db("shop_other")->field("name,phone,phones")->where(["fid"=>$id,"type"=>2])->select();

        if($phone || $people){
          //  $arrs=array();
            if($phone){
               foreach($phone as $kp => $vp){
                   $phone[$kp]['name']="电话";
                   $phone[$kp]['phone']=$vp['phone'];
               }
               
            }
            if($people){
               
                foreach($people as  $v){
                    
                    if($v['phone']){
                        $tels['name']=$v['name'];
                        $tels['phone']=$v['phone'];
                        $phone[]=$tels;
                    }
                    if($v['phones']){
                        $tels['name']=$v['name'];
                        $tels['phone']=$v['phones'];
                        $phone[]=$tels;
                    }
                                  
                }
              
            }
          //  $arrs[]=$phone;
            $arr=[
                'error_code'=>0,
                'msg'=>"获取成功",
                'data'=>$phone
            ];
        }else{
            $arr=[
                'error_code'=>1,
                'msg'=>"暂无数据",
                'data'=>[]
            ];
        }

       return json($arr);
    }
    public function save_assess()
    {
        $uid=Request::instance()->header("uid");

        if($uid){
            $data=input("post.");
            $data['uid']=$uid;
            $data['time']=time();
            $data['type']=1;
            
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
        }else{
            $arr=[
                'error_code'=>501,
                'msg'=>"请先登录",
                'data'=>''
            ];
        }

        
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

        $res=db("assess")->alias("a")->where(["sid"=>$id,"status"=>1,"type"=>1])->field("num,content,nickname,image")->join("user b","a.uid=b.uid")->order("id desc")->select();

        $arr=[
            'error_code'=>0,
            'msg'=>"获取成功",
            'data'=>$res
        ];

        return json($arr);
    }
    public function collect()
    {
        $uid=Request::instance()->header("uid");

        if($uid){

            $id=input("id");

            $re=db("collect")->where(["uid"=>$uid,"sid"=>$id])->find();
    
            if($re){
                $res=db("collect")->where("id",$re['id'])->delete();
            }else{
                $data['uid']=$uid;
                $data['sid']=$id;
    
                $res=db("collect")->insert($data);
            }
            if($res){
                $arr=[
                    'error_code'=>0,
                    'msg'=>"操作成功",
                    'data'=>[]
                ];
            }else{
                $arr=[
                    'error_code'=>1,
                    'msg'=>"操作成功",
                    'data'=>[]
                ];
            }

        }else{
            $arrs=[
                'error_code'=>501,
                'msg'=>"请先登录",
                'data'=>''
            ];
        }

      
        return json($arr);
    }
    /**
    * 导航
    *
    * @return void
    */
    public function nav()
    {
        $id=input("id");

        $re=db("shop")->field("longs,lats")->where("id",$id)->find();

        if($re){

            $arr=[
                'error_code'=>0,
                'msg'=>"获取成功",
                'data'=>$re
            ];  
        }else{
            $arr=[
                'error_code'=>1,
                'msg'=>"商户不存在",
                'data'=>[]
            ]; 
        }
        return json($arr);
    }



    /**
    * 根据地址获取经纬度
    *
    * @return void
    */
    public function get_addr($addr){
        $key_words=$addr;
        $header[] = 'Referer: http://lbs.qq.com/webservice_v1/guide-suggestion.html';
        $header[] = 'User-Agent: Mozilla/5.0 (Macintosh; Intel Mac OS X 10_13_3) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/66.0.3359.139 Safari/537.36';
        $url ="http://apis.map.qq.com/ws/place/v1/suggestion/?&region=&key=OB4BZ-D4W3U-B7VVO-4PJWW-6TKDJ-WPB77&keyword=".$key_words; 
 
        $ch = curl_init();
        //设置选项，包括URL
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch,CURLOPT_HTTPHEADER,$header);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_HEADER, 0);
 
        //执行并获取HTML文档内容
        $output = curl_exec($ch);
        
        curl_close($ch);
        
        $result = json_decode($output,true);

        if(!empty($result['data'][0])){
            return $result['data'][0]['location'];
        }else{
            echo '0';
        }
        
      
       

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

}