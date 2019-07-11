<?php
namespace app\admin\controller;

use think\Request;
use Think\Db;

class Member extends BaseAdmin
{
    public function lister()
    {
        
        $key=input("key");

        if($key){

            $map["nickname"]=["like","%".$key."%"];

        }else{
            $key="";
            $map=[];
        }
         $this->assign("keywords",$key);

        
        $list=db("user")->where($map)->order("uid desc")->paginate(20,false,['query'=>request()->param()]);
        
        $this->assign("list",$list);
        $page=$list->render();
        $this->assign("page",$page);   
        return $this->fetch();
    }

    public function look()
    {
        $id=input("id");

        $res=db("user")->where(['status'=>1,'is_delete'=>0,"fid"=>$id])->select();
  
        foreach($res as $k => $v){
            $res[$k]['type']=1;
            $res[$k]['list']=db("user")->where(['status'=>1,'is_delete'=>0,"fid"=>$v['uid']])->select();
            
            if($res[$k]['list']){
                foreach($res[$k]['list'] as $ks => $vs){
                    $res[$k]['list'][$ks]['type']=2;

                  
                }
             
            }
            
        }
        
     //   $lists = array_merge_recursive($res, $list);
        $this->assign("res",$res);

     
        
        return $this->fetch();
    }

    public function out(){
        $key=input("key");

        if($key){

            $map["nickname|phone|company"]=["like","%".$key."%"];

        }else{
        
            $map=[];
        }
         
        $list=db("user")->where($map)->order("uid desc")->select();
        // var_dump($data);exit;
        vendor('PHPExcel.PHPExcel');//调用类库,路径是基于vendor文件夹的
        vendor('PHPExcel.PHPExcel.Worksheet.Drawing');
        vendor('PHPExcel.PHPExcel.Writer.Excel2007');
        $objExcel = new \PHPExcel();
        //set document Property
        $objWriter = \PHPExcel_IOFactory::createWriter($objExcel, 'Excel2007');
    
        $objActSheet = $objExcel->getActiveSheet();
        $key = ord("A");
        $letter =explode(',',"A,B,C,D");
        $arrHeader =  array("会员名","手机号码","积分","注册时间");
        //填充表头信息
        $lenth =  count($arrHeader);
        for($i = 0;$i < $lenth;$i++) {
            $objActSheet->setCellValue("$letter[$i]1","$arrHeader[$i]");
        }
        //填充表格信息
        foreach($list as $k=>$v){
            $k +=2;

            

            $objActSheet->setCellValue('A'.$k,$v['username']);
            $objActSheet->setCellValue('B'.$k, $v['phone']);    
            // 表格内容
            $objActSheet->setCellValue('C'.$k, $v['integ']);
       
            $objActSheet->setCellValue('D'.$k, \date("Y-m-d H:i:s",$v['time']));
         

    
            // 表格高度
            $objActSheet->getRowDimension($k)->setRowHeight(20);
        }
    
        $width = array(20,20,15,10,10,30,10,15,15,15);
        //设置表格的宽度
        $objActSheet->getColumnDimension('A')->setWidth(20);
        $objActSheet->getColumnDimension('B')->setWidth(20);
        $objActSheet->getColumnDimension('C')->setWidth(25);
        $objActSheet->getColumnDimension('D')->setWidth(25);
      
  
  
        $outfile = "会员列表".".xls";
    
        $userBrowser=$_SERVER['HTTP_USER_AGENT'];
        
        if(preg_match('/MSIE/i', $userBrowser)){
            $outfile=urlencode($outfile);
           
        }else{
            $outfile= iconv("utf-8","gb2312",$outfile);;
            
        }
        ob_end_clean();
        header("Content-Type: application/force-download");
        header("Content-Type: application/octet-stream");
        header("Content-Type: application/download");
        header('Content-Disposition:inline;filename="'.$outfile.'"');
        header("Content-Transfer-Encoding: binary");
        header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
        header("Pragma: no-cache");
        $objWriter->save('php://output');
    }

/**
     * 等级调整
     *
     * @return void
     */
    public function level_change(){
        $type = Request::instance()->param('type', '');
        $uid = Request::instance()->param('uid', 0);
        $user = db('user')->where('uid', $uid)->find();
        if($type == 'up'){
            if($user['level'] == 3){
                return array('status'=>-1, 'data'=>array(), 'msg'=>'已经是最高等级了');
            }
            $res = db('user')->where('uid', $uid)->setInc('level');
        }elseif($type == 'down'){
            if($user['level'] == 1){
                return array('status'=>-1, 'data'=>array(), 'msg'=>'已经是最低等级了');
            }
            $res = db('user')->where('uid', $uid)->setDec('level');
        }
        if($res){
            $level = db('user')->where('uid', $uid)->value('level');
            if($level == 1){
                $level_name = '公司';
            }elseif($level == 2){
                $level_name = '销售经理';
            }else{
                $level_name = '入驻酒店';
            }
            return array('status'=>1, 'data'=>array('level_name'=>$level_name), 'msg'=>'操作成功');
        }else{
            return array('status'=>1, 'data'=>array(), 'msg'=>'操作失败');
        }
    }

    /**
     * 修改奖励金
     *
     * @return void
     */
    public function change_bonus(){
        $id = Request::instance()->param('id', 0);
        $bonus = Request::instance()->param('bonus', -1);
        if($id == 0 || $bonus == -1){
            echo '0';
            return;
        }
        $res = db('user')->where('uid', $id)->setField('bonus', $bonus);
        if($res){
            echo "1";
        }else{
            echo "0";
        }
    }

    /**
     * 修改佣金
     *
     * @return void
     */
    public function change_money(){
        $id = Request::instance()->param('id', 0);
        $money = Request::instance()->param('money', -1);
        if($id == 0 || $money == -1){
            echo '0';
            return;
        }
        // $user=db("user")->where("uid",$id)->find();
        // $old_money=$user['money'];


        // $new_money=$money-$old_money;
        // if($new_money >= 0){
        //     $data['uid']=$id;
        //     $data['money']=abs($new_money);
        //     $data['type']=1;
        //     $data['oper']=db("admin")->where("id",session('uid'))->find()['username'];
        //     $data['time']=time();
        // }else{
        //     $data['uid']=$id;
        //     $data['money']=abs($new_money);
        //     $data['type']=0;
        //     $data['oper']=db("admin")->where("id",session('uid'))->find()['username'];
        //     $data['time']=time();
        // }
        
        $res = db('user')->where('uid', $id)->setField('integ', $money);
        if($res){
          //  db("money_log")->insert($data);
            echo "1";
        }else{
            echo "0";
        }
    }

    /**
     * 股权日志
     *
     * @return void
     */
    public function money_log(){
        $id = Request::instance()->param('id', 0);
        $user = db('user')->where('uid', $id)->find();
        $list = db("money_log")->where('uid', $id)->paginate(10);
        $this->assign('list', $list);
        $this->assign('user', $user);
        return $this->fetch();
    }
    /**
    * 红包日志
    *
    * @return void
    */
    public function red_money_log(){
        $id = Request::instance()->param('id', 0);
        $user = db('user')->where('uid', $id)->find();
        $list = db("red_log")->where('uid', $id)->order("id desc")->paginate(10);
        $this->assign('list', $list);
        $this->assign('user', $user);
        return $this->fetch();
    }

    /**
     * 奖励金日志
     *
     * @return void
     */
    public function bonus_log(){
        $id = Request::instance()->param('id', 0);
        $user = db('user')->where('uid', $id)->find();
        $list = db("bonus_log")->where('u_id', $id)->paginate(10);
        $this->assign('list', $list);
        $this->assign('user', $user);
        return $this->fetch();
    }

    /**
     * 奖励金提现
     *
     * @return void
     */
    public function balance(){
        $wx_account = Request::instance()->param('wx_account', '');
        $wx_nickname = Request::instance()->param('wx_nickname', '');
        $status = Request::instance()->param('status', '-1');
        $start = Request::instance()->param('start', '');
        $end = Request::instance()->param('end', '');
        $map = [];
        if($status != -1){
            $map['status'] = $status;
        }
        if($wx_account != ''){
            $map['wx_account'] = array('like', '%'.$wx_account.'%');
        }
        if($wx_nickname != ''){
            $map['wx_nickname'] = array('like', '%'.$wx_nickname.'%');
        }
        if($start != '' && $end != ''){
            $map['c.time'] = array(array('egt',strtotime($start)),array('elt',strtotime($end.' 23:55:55')),'AND');
        }elseif($start == '' && $end != ''){
            $map['c.time'] = array('elt',strtotime($end.' 23:55:55'));
        }elseif($start != '' && $end == ''){
            $map['c.time'] = array('egt',strtotime($start));
        }
        $list = db("bonus_withdrow")->alias('c')
        ->field('u.nickname, u.image, c.id, c.uid, c.money, c.wx_nickname, c.wx_account, c.status, c.time')
        ->join('user u','u.uid=c.uid')->where($map)->order('c.time desc')->paginate(10,false,['query'=>request()->param()]);

        $this->assign('wx_account', $wx_account);
        $this->assign('wx_nickname', $wx_nickname);
        $this->assign('status', $status);
        $this->assign('start', $start);
        $this->assign('end', $end);
        $this->assign('list', $list);
        return $this->fetch('balance');
    }

    /**
     * 奖励金状态
     *
     * @return void
     */
    public function balance_status(){
        $id = Request::instance()->param('id');
        $ftype = Request::instance()->param('ftype');
        db("bonus_withdrow")->where('id', $id)->setField('status', $ftype);
        $user = db("bonus_withdrow")->where('id', $id)->find();
        if($ftype == 3){
            //驳回，返回余额
            db("user")->where('uid', $user['uid'])->setInc('bonus', $user['money']);
        }
    }

    /**
    * 删除
    *
    * @return void
    */
    public function delete()
    {
        $id=input('id');
        $re=db("user")->where("uid=$id")->find();
        if($re){
            
            $del=db("user")->where("uid=$id")->delete();
            if($del){
                
                echo '0';
            }else{
                echo '1';
            }
        }else{
            echo '2';
        }
    }
    /**
    * 升级申请列表
    *
    * @return void
    */
    public function apply()
    {
        $status=input("status");
       
        $key=input("key");

        if($key || $status){
           if($key){
            $map["name|u_phone"]=["like","%".$key."%"];
           }
           if($status){
            $map['u_status']=['eq',$status];
           }

           

        }else{
            $key="";
            $status=0;
            $map=[];
        }
        $map['type']=['eq',0];
        $this->assign("keywords",$key);
        $this->assign("status",$status);

      //  var_dump($status);

        $list=db("user_apply")->alias("a")->field("a.*,b.nickname")->where($map)->join("user b","a.u_id=b.uid")->order("id desc")->paginate(20,false,['query'=>request()->param()]);
        $this->assign("list",$list);

        $page=$list->render();
        $this->assign("page",$page);

        

        return $this->fetch();
    }

    public function outx(){
        $status=input("status");
       
        

        $key=input("key");

        if($key || $status){

            if($key){
                $map["name|u_phone"]=["like","%".$key."%"];
               }
               if($status){
                $map['u_status']=['eq',$status];
               }

        }else{
            $key="";
            $status=0;
            $map=[];
        }
        $map['type']=['eq',0];
         
        $list=db("user_apply")->alias("a")->field("a.*,b.nickname")->where($map)->join("user b","a.u_id=b.uid")->order("id desc")->select();
        // var_dump($data);exit;
        vendor('PHPExcel.PHPExcel');//调用类库,路径是基于vendor文件夹的
        vendor('PHPExcel.PHPExcel.Worksheet.Drawing');
        vendor('PHPExcel.PHPExcel.Writer.Excel2007');
        $objExcel = new \PHPExcel();
        //set document Property
        $objWriter = \PHPExcel_IOFactory::createWriter($objExcel, 'Excel2007');
    
        $objActSheet = $objExcel->getActiveSheet();
        $key = ord("A");
        $letter =explode(',',"A,B,C,D");
        $arrHeader =  array("会员名","手机号码","酒店名称","申请时间");
        //填充表头信息
        $lenth =  count($arrHeader);
        for($i = 0;$i < $lenth;$i++) {
            $objActSheet->setCellValue("$letter[$i]1","$arrHeader[$i]");
        }
        //填充表格信息
        foreach($list as $k=>$v){
            $k +=2;


            $objActSheet->setCellValue('A'.$k,$v['nickname']);
            $objActSheet->setCellValue('B'.$k, $v['u_phone']);    
            // 表格内容
            $objActSheet->setCellValue('C'.$k, $v['name']);
         
            $objActSheet->setCellValue('D'.$k, \date("Y-m-d H:i:s",$v['u_time']));
         

    
            // 表格高度
            $objActSheet->getRowDimension($k)->setRowHeight(20);
        }
    
        $width = array(20,20,15,10,10,30,10,15,15,15);
        //设置表格的宽度
        $objActSheet->getColumnDimension('A')->setWidth(20);
        $objActSheet->getColumnDimension('B')->setWidth(20);
        $objActSheet->getColumnDimension('C')->setWidth(25);
        $objActSheet->getColumnDimension('D')->setWidth(25);
     
  
  
        $outfile = "销售经理".".xls";
    
        $userBrowser=$_SERVER['HTTP_USER_AGENT'];
        
        if(preg_match('/MSIE/i', $userBrowser)){
            $outfile=urlencode($outfile);
           
        }else{
            $outfile= iconv("utf-8","gb2312",$outfile);;
            
        }
        ob_end_clean();
        header("Content-Type: application/force-download");
        header("Content-Type: application/octet-stream");
        header("Content-Type: application/download");
        header('Content-Disposition:inline;filename="'.$outfile.'"');
        header("Content-Transfer-Encoding: binary");
        header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
        header("Pragma: no-cache");
        $objWriter->save('php://output');
    }

    /**
    * 通过审核
    *
    * @return void
    */
    public function change()
    {
        $id=input("id");
        $re=db("user_apply")->where("id",$id)->find();
        if($re){ 
           if($re['u_status'] == 0){
            $uid=$re['u_id'];
            $level=$re['u_level'];
            // 启动事务
                Db::startTrans();
                try{
                   db("user_apply")->where("id",$id)->setField("u_status",1);
                   db("user")->where("uid",$uid)->setField("level",$level);
                    // 提交事务
                    Db::commit();   
                   
                } catch (\Exception $e) {
                    // 回滚事务
                    Db::rollback();
                    $this->redirect('apply');
                }
           
                $this->redirect('apply');
            
           }else{
            $this->redirect('apply');
           }
        }else{ 
            $this->redirect('apply');
        }
    }
    /**
    * 驳回
    *
    * @return void
    */
    public function bo()
    {
        $id=input("id");
        $re=db("user_apply")->where("id",$id)->find();
        if($re){ 
           if($re['u_status'] == 0){
          
            db("user_apply")->where("id",$id)->setField("u_status",2);
             
            $this->redirect('apply');
            
           }else{
            $this->redirect('apply');
           }
        }else{ 
            $this->redirect('apply');
        }
    }
    /**
    * 入住酒店申请列表
    *
    * @return void
    */
    public function hotel_apply()
    {
        $status=input("status");
       
        $key=input("key");

        if($key || $status){
           if($key){
            $map["username|idcode|a.company|name|addr|genre|u_phone"]=["like","%".$key."%"];
           }
           if($status){
            $map['u_status']=['eq',$status];
           }

           

        }else{
            $key="";
            $status=0;
            $map=[];
        }
        $map['type']=['eq',1];
        $this->assign("keywords",$key);
        $this->assign("status",$status);

     //   $map['type']=['eq',1];
        $list=db("user_apply")->alias("a")->field("a.*,b.nickname")->where($map)->join("user b","a.u_id=b.uid")->order("id desc")->paginate(10);
        $this->assign("list",$list);

        $page=$list->render();
        $this->assign("page",$page);

       // $this->assign("status",$status);

        return $this->fetch();
    }

    public function outr(){
        $status=input("status");
       
        $key=input("key");

        if($key || $status){
           if($key){
            $map["username|idcode|name|addr|genre|u_phone"]=["like","%".$key."%"];
           }
           if($status){
            $map['u_status']=['eq',$status];
           }

           

        }else{
            $key="";
            $status=0;
            $map=[];
        }
        $map['type']=['eq',1];
         
        $list=db("user_apply")->alias("a")->field("a.*,b.nickname")->where($map)->join("user b","a.u_id=b.uid")->order("id desc")->select();
        // var_dump($data);exit;
        vendor('PHPExcel.PHPExcel');//调用类库,路径是基于vendor文件夹的
        vendor('PHPExcel.PHPExcel.Worksheet.Drawing');
        vendor('PHPExcel.PHPExcel.Writer.Excel2007');
        $objExcel = new \PHPExcel();
        //set document Property
        $objWriter = \PHPExcel_IOFactory::createWriter($objExcel, 'Excel2007');
    
        $objActSheet = $objExcel->getActiveSheet();
        $key = ord("A");
        $letter =explode(',',"A,B,C,D,E,F,G,H");
        $arrHeader =  array("会员名","法人姓名","身份证号","酒店名称","详细地址","酒店类型","联系方式","申请时间");
        //填充表头信息
        $lenth =  count($arrHeader);
        for($i = 0;$i < $lenth;$i++) {
            $objActSheet->setCellValue("$letter[$i]1","$arrHeader[$i]");
        }
        //填充表格信息
        foreach($list as $k=>$v){
            $k +=2;


            $objActSheet->setCellValue('A'.$k,$v['nickname']);
            $objActSheet->setCellValue('B'.$k, $v['username']);    
            // 表格内容
            $objActSheet->setCellValue('C'.$k, $v['idcode']);
        
            $objActSheet->setCellValue('D'.$k, $v['name']);
            $objActSheet->setCellValue('E'.$k, $v['addr']);
            $objActSheet->setCellValue('F'.$k, $v['genre']);
            $objActSheet->setCellValue('G'.$k, $v['u_phone']);
         
            $objActSheet->setCellValue('H'.$k, \date("Y-m-d H:i:s",$v['u_time']));
         

    
            // 表格高度
            $objActSheet->getRowDimension($k)->setRowHeight(20);
        }
    
        $width = array(20,20,15,10,10,30,10,15,15,15);
        //设置表格的宽度
        $objActSheet->getColumnDimension('A')->setWidth(20);
        $objActSheet->getColumnDimension('B')->setWidth(20);
        $objActSheet->getColumnDimension('C')->setWidth(25);
        $objActSheet->getColumnDimension('D')->setWidth(25);
        $objActSheet->getColumnDimension('E')->setWidth(25);
        $objActSheet->getColumnDimension('F')->setWidth(25);
        $objActSheet->getColumnDimension('G')->setWidth(25);
        $objActSheet->getColumnDimension('H')->setWidth(25);
     
     
  
  
        $outfile = "入驻酒店".".xls";
    
        $userBrowser=$_SERVER['HTTP_USER_AGENT'];
        
        if(preg_match('/MSIE/i', $userBrowser)){
            $outfile=urlencode($outfile);
           
        }else{
            $outfile= iconv("utf-8","gb2312",$outfile);;
            
        }
        ob_end_clean();
        header("Content-Type: application/force-download");
        header("Content-Type: application/octet-stream");
        header("Content-Type: application/download");
        header('Content-Disposition:inline;filename="'.$outfile.'"');
        header("Content-Transfer-Encoding: binary");
        header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
        header("Pragma: no-cache");
        $objWriter->save('php://output');
    }
    /**
    * 通过审核
    *
    * @return void
    */
    public function changes()
    {
        $id=input("id");
        $re=db("user_apply")->where("id",$id)->find();
        if($re){ 
           if($re['u_status'] == 0){
            $uid=$re['u_id'];
            $level=$re['u_level'];
            // 启动事务
                Db::startTrans();
                try{
                   db("user_apply")->where("id",$id)->setField("u_status",1);
                   db("user")->where("uid",$uid)->setField("level",$level);
                    // 提交事务
                    Db::commit();   
                   
                } catch (\Exception $e) {
                    // 回滚事务
                    Db::rollback();
                    $this->redirect('hotel_apply');
                }
           
                $this->redirect('hotel_apply');
            
           }else{
            $this->redirect('hotel_apply');
           }
        }else{ 
            $this->redirect('hotel_apply');
        }
    }
     /**
    * 驳回
    *
    * @return void
    */
    public function bos()
    {
        $id=input("id");
        $re=db("user_apply")->where("id",$id)->find();
        if($re){ 
           if($re['u_status'] == 0){
               $data['u_status']=2;
               $data['rebut']=input("rebut");
          
            db("user_apply")->where("id",$id)->update($data);
             
            echo '1';
            
           }else{
            echo '0';
           }
        }else{ 
            echo '2';
        }
    }
   
   

















 
}