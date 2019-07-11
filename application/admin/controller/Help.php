<?php
namespace app\admin\controller;

class Help extends BaseAdmin
{
    public function lister()
    {
        $list=db("help")->order(["sort asc","id desc"])->paginate(20);

        $this->assign("list",$list);

        $page=$list->render();

        $this->assign("page",$page);
        
        return $this->fetch();
    }
    public function save(){
        $id=\input('id');
        $data=input("post.");
        if($id){
           $re=db('help')->where("id=$id")->find();
         
           
           $res=db('help')->where("id=$id")->update($data);
           if($res){
               $this->success("修改成功！");
           }else{
               $this->error("修改失败！");
           }
        }else{
          
            
            $re=db('help')->insert($data);
            if($re){
                $this->success("添加成功！");
            }else{
                $this->error("添加失败！");
            } 
        }
         
    }
    public function modifys(){
        $id=input("id");
        $re=db('help')->where("id=$id")->find();
        echo json_encode($re);
    }
    public function delete(){
        $id=input('id');
        $re=db("help")->where("id=$id")->find();
        if($re){
           $del=db("help")->where("id=$id")->delete();
           if($del){
               echo '1';
           }else{
               echo '2';
           }
        }else{
            echo '0';
        }
       
    }
    public function sort(){
        $data=input('post.');
        foreach ($data as $id => $sort){
            db('help')->where(array('id' => $id ))->setField('sort' , $sort);
        }
        $this->redirect('lister');
    }
    public function delete_all(){
        $id=input('id');
        $arr=explode(",", $id);
        foreach ($arr as $v){
            $re=db('help')->where("id=$v")->find();
            if($re){
                $del=db('help')->where("id=$v")->delete();
        }
        
       }
       $this->redirect('lister');
    }
    public function change(){
        $id=input('id');
        $re=db('help')->where("id=$id")->find();
        if($re){
            if($re['status'] == 0){
                $res=db('help')->where("id=$id")->setField("status",1);
            }
            if($re['status'] == 1){
                $res=db('help')->where("id=$id")->setField("status",0);
    
            }
            echo '0';
        }else{
            echo '1';
        }
    }
}