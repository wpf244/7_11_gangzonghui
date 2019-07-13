<?php
namespace app\admin\controller;

class Info extends BaseAdmin
{
    public function lister()
    {
        $status=input("status");

        if(empty($status)){
            $status=1;
        }

        $this->assign("status",$status);

        $list=db("info")->alias("a")->field("a.*,b.nickname")->where(["status"=>$status])->join("user b","a.uid=b.uid")->order("id desc")->paginate(20,false,["query"=>request()->param()]);

        $this->assign("list",$list);

        $page=$list->render();

        $this->assign("page",$page);

       
        return $this->fetch();
    } 
    public function delete(){
        $id=input('id');
        $re=db("info")->where("id=$id")->find();
        if($re){
           $del=db("info")->where("id=$id")->delete();
           if($del){
               echo '1';
           }else{
               echo '2';
           }
        }else{
            echo '0';
        }
       
    }
    public function looks()
    {
        $id=input("id");

        $re=db("info")->where("id",$id)->find();

        $this->assign("re",$re);

        $arr=explode(",",$re['image']);

        $this->assign("arr",$arr);
        
        return $this->fetch();
    }
    public function change(){
        $id=input('id');
        $re=db("info")->where("id=$id")->find();
        if($re){
           $del=db("info")->where("id=$id")->update(["status"=>2]);
           if($del){
               echo '1';
           }else{
               echo '2';
           }
        }else{
            echo '0';
        }
       
    }
    public function changes(){
        $id=input('id');
        $re=db("info")->where("id=$id")->find();
        if($re){
           $del=db("info")->where("id=$id")->update(["status"=>3]);
           if($del){
               echo '1';
           }else{
               echo '2';
           }
        }else{
            echo '0';
        }
       
    }
}