<?php
namespace app\admin\controller;

class Shop extends BaseAdmin
{
    public function sever()
    {
        
        $list=db("shop_sever")->order(["id asc"])->paginate(20);

        $this->assign("list",$list);

        $page=$list->render();

        $this->assign("page",$page);

        return $this->fetch();
    }
    public function save_sever(){
        $id=\input('id');
        if($id){
           $re=db('shop_sever')->where("id",$id)->find();
           if(request()->file("image")){
               $data['sever_image']=uploads("image");
               if($re['sever_image']){
                deleteImg($re['sever_image']);
               }
               
           }else{
               $data['sever_image']=$re['sever_image'];
           }
        
 
           $data['sever_name']=input('name');
          
           $res=db('shop_sever')->where("id",$id)->update($data);
           if($res){
               $this->success("修改成功！");
           }else{
               $this->error("修改失败！");
           }
        }else{
            if(request()->file("image")){
                $data['sever_image']=uploads("image");  
            }
        
            $data['sever_name']=input('name');
           
            $re=db('shop_sever')->insert($data);
            if($re){
                $this->success("添加成功！");
            }else{
                $this->error("添加失败！");
            } 
        }
         
    }
    public function modifys_sever(){
        $id=input("id");
        $re=db('shop_sever')->where("id=$id")->find();
        echo json_encode($re);
    }
    public function delete_sever()
    {
        $id=\input('id');
        $re=db("shop_sever")->where("id=$id")->find();
        if($re){
            $res=db("shop_sever")->where("id=$id")->delete();
            $this->redirect('sever');
        }else{
            $this->redirect('sever');
        }
    }
    public function lister()
    {
        $title=input("title");
        $map =[];
        if($title){
            $map['name']=["like","%".$title."%"];
        }else{
            $title='';
        }
        $this->assign("title",$title);


        $list=db("shop")->where(["statu"=>1])->order(["sort asc","id desc"])->paginate(20,false,['query'=>request()->param()]);

        $this->assign("list",$list);

        $page=$list->render();

        $this->assign("page",$page);

        return $this->fetch();
    }
    public function add()
    {

        $res=db("type")->where(["fid"=>0])->select();

        $this->assign("res",$res);

        $sever=db("shop_sever")->select();

        $this->assign("sever",$sever);

        return $this->fetch();
    }
    public function getnexts()
    {
        $sid=input("sid");
        $re=db("type")->where(["fid"=>$sid])->order(["sort asc","id desc"])->select();
     
        if($re){
           echo json_encode($re);
        }else{
            echo 0;
        }
    }
    public function save()
    {
        $data=input("post.");

        $logo=request()->file("logo");

        if($logo){
            $data['logo']=uploads("logo");
        
        }
        $wx=request()->file("wx");

        if($wx){
            $data['wx']=uploads("wx");
        
        }

        $sever=$data['severs'];
     //   var_dump($sever);exit;

        $data['severs']=implode(",",$sever);

        $re=db("shop")->insert($data);

        if($re){
            $this->success("保存成功",url("lister"));
        }else{
            $this->error("保存失败");
        }
    }
    public function modifys()
    {
        $id=input("id");

        $re=db("shop")->where(["status"=>1,"id"=>$id])->find();

        $this->assign("re",$re);

        $severs=explode(",",$re['severs']);

        $this->assign("severs",$severs);

        $res=db("type")->where(["fid"=>0])->select();

        $this->assign("res",$res);

        $sid=$re['fid'];
        
        $city=db("type")->where(["fid"=>$sid])->order(["sort asc","id desc"])->select();
        $this->assign("city",$city);

        $sever=db("shop_sever")->select();

        $this->assign("sever",$sever);

        return $this->fetch();
    }
    public function usave()
    {
        $id=input("id");
        $re=db("shop")->where("id",$id)->find();

        if($re){

            $data=input("post.");

            $logo=request()->file("logo");

            if($logo){
                $data['logo']=uploads("logo");

                deleteImg($re['logo']);
            
            }else{
                $data['logo']=$re['logo'];
                
            }
            $wx=request()->file("wx");

            if($wx){

                $data['wx']=uploads("wx");

                deleteImg($re['wx']);
            
            }else{
                $data['wx']=$re['wx'];
            }

            $sever=$data['severs'];
        

            $data['severs']=implode(",",$sever);


            $re=db("shop")->where("id",$id)->update($data);

            if($re){
                $this->success("修改成功",url("lister"));
            }else{
                $this->error("修改失败");
            }

        }else{
            $this->error("非法操作",url("lister"));
        }
    }
    public function sort(){
        $data=input('post.');
   
        foreach ($data as $id => $sort){
            db("shop")->where(array('id' => $id ))->setField('sort' , $sort);
        }
        $this->redirect('lister');
    }
    public function change()
    {
        $id=\input('id');
        $re=db("shop")->where("id=$id")->find();
        if($re){
            if($re['status'] == 1){
                $res=db("shop")->where("id=$id")->setField("status",0);
            }
           
            if($re['status'] == 0){
                $res=db("shop")->where("id=$id")->setField("status",1);
            }

            echo '0';
        }else{
            echo '1';
        }
    }
    public function changer()
    {
        $id=\input('id');
        $re=db("shop")->where("id=$id")->find();
        if($re){
            if($re['recome'] == 1){
                $res=db("shop")->where("id=$id")->setField("recome",0);
            }
           
            if($re['recome'] == 0){
                $res=db("shop")->where("id=$id")->setField("recome",1);
            }

            echo '0';
        }else{
            echo '1';
        }
    }
    public function changen()
    {
        $id=\input('id');
        $re=db("shop")->where("id=$id")->find();
        if($re){
            if($re['news'] == 1){
                $res=db("shop")->where("id=$id")->setField("news",0);
            }
           
            if($re['news'] == 0){
                $res=db("shop")->where("id=$id")->setField("news",1);
            }

            echo '0';
        }else{
            echo '1';
        }
    }
    public function changeh()
    {
        $id=\input('id');
        $re=db("shop")->where("id=$id")->find();
        if($re){
            if($re['hot'] == 1){
                $res=db("shop")->where("id=$id")->setField("hot",0);
            }
           
            if($re['hot'] == 0){
                $res=db("shop")->where("id=$id")->setField("hot",1);
            }

            echo '0';
        }else{
            echo '1';
        }
    }
    public function delete()
    {
        $id=\input('id');
        $re=db("shop")->where("id=$id")->find();
        if($re){
            $res=db("shop")->where("id=$id")->delete();
            if($res){
                echo '0';
            }else{
                echo '2';
            }
        }else{
            echo '1';
        }
    }
    public function delete_all()
    {
        $id=\input('id');
        $arr=\explode(",",$id);
        foreach($arr as $v){
            $re=db("shop")->where("id",$v)->find();
            if($re){
                $res=db("shop")->where("id",$v)->delete();
               
            }
        }
        $this->redirect('lister');
    }
    public function phone()
    {
        if(request()->isAjax()){

            $id=input("id");

            $data=input("post.");

            if($id){
                $res=db("shop_other")->where("id",$id)->update($data);
                if($res){
                    $this->success("修改成功");
                }else{
                    $this->error("修改失败");
                }
            }else{
                $data['type']=1;

                $rea=db("shop_other")->insert($data);

                if($rea){
                    $this->success("添加成功");
                }else{
                    $this->error("添加失败");
                }
            }

        }else{
       
            $id=input("id");

            $this->assign("id",$id);
            
            $list=db("shop_other")->where(["type"=>1,"fid"=>$id])->select();

            $this->assign("list",$list);
            
            return $this->fetch();
        }
 
    }
    public function modifys_phone(){
        $id=input("id");
        $re=db('shop_other')->where("id=$id")->find();
        echo json_encode($re);
    }
    public function delete_phone()
    {
        $id=\input('id');
        $re=db("shop_other")->where("id=$id")->find();
        if($re){
            $res=db("shop_other")->where("id=$id")->delete();
            if($res){
                echo '0';
            }else{
                echo '2';
            }
        }else{
            echo '1';
        }
    }
    public function people()
    {
        if(request()->isAjax()){

            $id=input("id");

            $data=input("post.");

            if($id){
                $res=db("shop_other")->where("id",$id)->update($data);
                if($res){
                    $this->success("修改成功");
                }else{
                    $this->error("修改失败");
                }
            }else{
                $data['type']=2;

                $rea=db("shop_other")->insert($data);

                if($rea){
                    $this->success("添加成功");
                }else{
                    $this->error("添加失败");
                }
            }

        }else{
       
            $id=input("id");

            $this->assign("id",$id);
            
            $list=db("shop_other")->where(["type"=>2,"fid"=>$id])->select();

            $this->assign("list",$list);
            
            return $this->fetch();
        }
 
    }
    public function banner()
    {
        if(request()->isAjax()){

            $id=input("id");

            $data=input("post.");

            if($id){

                if(request()->file("image")){
                    $data['image']=uploads("image");
                }else{
                     $this->error("请上传图片");
                }
                $res=db("shop_other")->where("id",$id)->update($data);
                if($res){
                    $this->success("修改成功");
                }else{
                    $this->error("修改失败");
                }
            }else{

                if(request()->file("image")){
                    $data['image']=uploads("image");
                }else{
                    $this->error("请上传图片");
               }

                $data['type']=3;

                $rea=db("shop_other")->insert($data);

                if($rea){
                    $this->success("添加成功");
                }else{
                    $this->error("添加失败");
                }
            }

        }else{
       
            $id=input("id");

            $this->assign("id",$id);
            
            $list=db("shop_other")->where(["type"=>3,"fid"=>$id])->select();

            $this->assign("list",$list);
            
            return $this->fetch();
        }
 
    }
    public function sto()
    {
        if(request()->isAjax()){

            $id=input("id");

            $data=input("post.");

            if($id){
                $res=db("shop_other")->where("id",$id)->update($data);
                if($res){
                    $this->success("修改成功");
                }else{
                    $this->error("修改失败");
                }
            }else{
                $data['type']=4;

                $rea=db("shop_other")->insert($data);

                if($rea){
                    $this->success("添加成功");
                }else{
                    $this->error("添加失败");
                }
            }

        }else{
       
            $id=input("id");

            $this->assign("id",$id);
            
            $list=db("shop_other")->where(["type"=>4,"fid"=>$id])->select();

            $this->assign("list",$list);
            
            return $this->fetch();
        }
 
    }









}