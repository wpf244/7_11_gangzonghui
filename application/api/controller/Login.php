<?php
namespace app\api\controller;

class Login extends BaseApi
{
     /**
    * 授权登录
    *
    * @return void
    */
    public function login()
    {
        $code=input('code');

        $payment=db("payment")->where("id",1)->find();
        $appid = $payment['appid'];
        $secret = $payment['appsecret'];
        $url="https://api.weixin.qq.com/sns/jscode2session?appid=".$appid."&secret=".$secret."&js_code=".$code."&grant_type=authorization_code";
        $results=json_decode(file_get_contents($url),true);
        
        $openid=$results['openid'];
        if(!$openid){
            $arr=[
                'error_code'=>1,
                'msg'=>'openID获取失败',
                'data'=>''
            ];
        }else{
            
            $data['openid']=$openid;
            $data['nickname']=\input('nickname');
            $data['image']=\input('image');
         
            $ret=db('user')->where("openid",$openid)->find();
            if($ret['openid']){
   
                db("user")->where("openid",$openid)->update($data);

                    $arr=[
                        'error_code'=>0,
                        'msg'=>'授权成功',
                        'data'=>[
                            'uid'=>$ret['uid'],
                        ]
                    ];
            }else{
                $data['time']=\time();
                $rea=db('user')->insert($data);
                $uid=db('user')->getLastInsID();
                if($rea){

                    $arr=[
                        'error_code'=>0,
                        'msg'=>'授权成功',
                        'data'=>[
                            'uid'=>$uid,
                        ]
                    ];
    
                }else{
                    $arr=[
                        'error_code'=>2,
                        'msg'=>'授权失败',
                        'data'=>''
                    ];
                }
               
            }
        }
        echo \json_encode($arr);
    }
}