<?php
namespace app\other\controller;

class Money extends BaseAdmin
{
    public function cash()
    {

        $uid=session("uid");
        $list=db("money_log")->where("shopid",$uid)->order("mid desc")->paginate(20);
        $this->assign("list",$list);
        $page=$list->render();
        $this->assign("page",$page);

        $shop=db("shop")->where("id",$uid)->find();
        $this->assign("shop",$shop);

        $ti=db("free")->where("id",4)->find();
        $this->assign("ti",$ti);

        return $this->fetch();
    }
    public function saves()
    {
        $uid=session("uid");
        $shop=db("shop")->where("id",$uid)->find();
        $money=$shop['money'];
        $moneys=input("money");
        $ti=db("free")->where("id",4)->find();
        $fei=$ti['num'];
        if($money < $moneys){
            $this->success("账户余额不足");
        }else{
            $data['shopid']=$uid;
            $data['moneys']=$moneys;
            $data['proce']=$moneys*$fei/100;
            $data['money']=$moneys-$data['proce'];
            $data['content']=input("content");
            $data['time']=time();
            db("money_log")->insert($data);
            $res=db("shop")->where("id",$uid)->setDec("money",$moneys);
            if($res){
                $this->success("提交成功,等待审核");
            }else{
                $this->error("系统繁忙");
            }
        }
    }
}