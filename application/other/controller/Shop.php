<?php
namespace app\other\controller;

use think\Request;
use think\Db;

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

        $uid=session("uid");


        $list=db("shop")->where(["statu"=>1,"id"=>$uid])->order(["sort asc","id desc"])->paginate(20,false,['query'=>request()->param()]);

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
    public function query_address(){
        $key_words=input("addr");
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
    public function index()
    {
        $statu=input("statu");

        $map=[];

        if($statu){
            $map['statu']=['eq',$statu];
        }else{
            $map['statu']=['eq',0];
            $statu=0;
        }
        $this->assign("statu",$statu);

        $list=db("shop")->where("type",1)->where($map)->order("id asc")->paginate(20,false,["query"=>request()->param()]);

        $this->assign("list",$list);

        $page=$list->render();

        $this->assign("page",$page);

        return $this->fetch();
    }
    /**
    * 驳回申请
    *
    * @return void
    */
    public function bo()
    {
        $id=input("id");

        $re=db("shop")->where("id",$id)->find();

        if($re){
            
            $res=db("shop")->where("id",$id)->setField("statu",2);

            echo '0';

        }else{
            echo '1';
        }
    }
    /**
    * 通过申请
    *
    * @return void
    */
    public function tong()
    {
        $id=input("id");

        $re=db("shop")->where("id",$id)->find();

        $url=Request::instance()->domain();
        
        if($re){

            $sto['type']=4;
            $sto['fid']=$id;
            $sto['name']=$re['sto'];

            $data['statu']=1;
            $data['pwd']="123456";
            $images=explode(",",$re['images']);

            $content='';

            foreach($images as $v){
                $content.="<br><img src='$url.$v' /><br>";
            }
            $data['content']=$re['content'].$content;

            $user['name']=$re['username'];
            $user['phone']=$re['phone'];
            $user['fid']=$id;
            $user['type']=2;
          //  var_dump($user);exit;
            // 启动事务
            Db::startTrans();
            try{
                db("shop_other")->insert($sto);
             //   var_dump($re);exit;
                db("shop_other")->insert($user);
                db("shop")->where("id",$id)->update($data);
               
                $banner=\explode(",",$re['banner']);

                foreach($banner as $vb){
                    $b['image']=$vb;
                    $b['fid']=$id;
                    $b['type']=3;

                    db("shop_other")->insert($b);
                }
                
                // 提交事务
               Db::commit();   
                echo '0'; 
            } catch (\Exception $e) {
                // 回滚事务
                Db::rollback();

                echo '1';
            }
            

        }else{
            echo '1';
        }

    }
    public function looks()
    {
        $id=input("id");

        $re=db("shop")->where("id",$id)->find();

        $re['tname']=db("type")->where("id",$re['fid'])->find()['name'];

        $re['tnames']=db("type")->where("id",$re['tid'])->find()['name'];

        $severs=explode(",",$re['severs']);

        $res=db("shop_sever")->where("id","in",$severs)->select();
        
        $r='';
        foreach($res as $v){
            $r.=$v['sever_name'].'-';
        }
        $re['severs']=$r;


        $this->assign("re",$re);

        

        $images=explode(",",$re['images']);

        $this->assign("images",$images);

        $banner=explode(",",$re['banner']);

        $this->assign("banner",$banner);

        
        return $this->fetch();
    }








}