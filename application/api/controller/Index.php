<?php
namespace app\api\controller;

class Index extends BaseHome
{
    public function index()
    {
        $url=parent::getUrl();
        //轮播图
        $banner=db("lb")->field("url,image")->where(["fid"=>1,"status"=>1])->order(["sort asc","id desc"])->select();
        
        foreach($banner as $kb => $vb){
            $banner[$kb]['image']=$url.$vb['image'];
        }

        $arr=[
            'error_code'=>0,
            'msg'=>"获取成功",
            'data'=>[
                'banner'=>$banner,
            ]
        ];
        return json($arr);

    }
}