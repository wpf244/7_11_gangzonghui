<?php
namespace app\admin\controller;

class Goods extends BaseAdmin
{
    public function type()
    {
        $res=db("type")->where(["fid"=>0])->order(["sort asc","id asc"])->select();

        $this->assign("res",$res);

        $list=db('type')->where("fid=0")->order(["sort asc","id asc"])->select();
        foreach($list as $k => $v){

            $list[$k]['list1']=db("type")->where(["fid"=>$v['id']])->order(["sort asc","id asc"])->select();
  
        }
        $this->assign("list",$list);

        return $this->fetch();
    }
    public function save_type(){
        $id=\input('id');
        $data=input("post.");
        if($id){

           $re=db('type')->where("id",$id)->find();
           if(request()->file("image")){
               $data['image']=uploads("image");
               if($re['image']){
                deleteImg($re['image']);
               }
               
           }else{
               $data['image']=$re['image'];
           }
        
          
           $res=db('type')->where("id",$id)->update($data);
           if($res){
               $this->success("修改成功！");
           }else{
               $this->error("修改失败！");
           }
        }else{
            if(request()->file("image")){
                $data['image']=uploads("image");  
            }
        
           
            $re=db('type')->insert($data);
            if($re){
                $this->success("添加成功！");
            }else{
                $this->error("添加失败！");
            } 
        }
         
    }
    public function change_type(){
        $id=input("id");

        $re=db("type")->where("id",$id)->find();

        if($re){
            if($re['status'] == 0){
                $res=db("type")->where("id",$id)->setField("status",1);
            }
            if($re['status'] == 1){
                $res=db("type")->where("id",$id)->setField("status",0);
            }
            if($res){
                echo '1';
            }else{
                echo '0';
            }
        }else{
            echo '2';
        }  
    }
    public function modifys_type(){
        $id=input("id");
        $re=db('type')->where("id=$id")->find();
        echo json_encode($re);
    }
    public function delete_type()
    {
        $id=\input('id');
        $re=db("type")->where("id=$id")->find();
        if($re){
            $res=db("type")->where("id=$id")->delete();
            $this->redirect('type');
        }else{
            $this->redirect('type');
        }
    }
    public function sort_type(){
        $data=input('post.');
   
        foreach ($data as $id => $sort){
            db("type")->where(array('id' => $id ))->setField('sort' , $sort);
        }
        $this->redirect('type');
    }
}