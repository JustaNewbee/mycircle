<?php
/**
 * Created by PhpStorm.
 * User: Micosoft
 * Date: 2017/12/30 0030
 * Time: 17:22
 */
namespace Home\Controller;
use Think\Controller;
class CircleController extends Controller{
    public $default = '/mycircle/Public/img/akari.jpg';
    public function index(){
        $category = $_GET['category'];
        if(isset($category)){
            $this->assign('total',M('circle')->where("circle_class = $category")->count('circle_id'));
            $this->assign('child_class',json_encode(M('class')->where("parent_id = $category")->select()));
        }else {
            $this->assign('total', M('circle')->count('circle_id'));
        }
        $this->assign("history_circle",$_COOKIE['history_circle']);
        $this->display();
    }
    public function circle_display(){
        $category = $_GET['category'];
        $child = $_GET['child'];
        $circle = M('circle');
        $need = $_POST['need'];
        $current = ($_POST['current']-1)*$need;
        if(!is_null($category)){
            if(!is_null($child)){
                $result = $circle -> query("SELECT circle_id,circle_name,circle_people_num,circle_article_num,circle_intro,class_name,circle_avatar FROM my_circle INNER JOIN my_class ON my_circle.circle_class = my_class.class_id WHERE circle_class = '$category' and checked = 'pass' and class_id = '$child' LIMIT $current,$need");
            }else {
                $result = $circle -> query("SELECT circle_id,circle_name,circle_people_num,circle_article_num,circle_intro,class_name,circle_avatar FROM my_circle INNER JOIN my_class ON my_circle.circle_class = my_class.class_id WHERE circle_class = $category and checked = 'pass' LIMIT $current,$need");
            }
        }else{
            $result = $circle -> query("SELECT circle_id,circle_name,circle_people_num,circle_article_num,circle_intro,class_name,circle_avatar FROM my_circle INNER JOIN my_class ON my_circle.circle_class = my_class.class_id WHERE checked = 'pass' LIMIT $current,$need");
        }
        $this->ajaxReturn($result);
    }
    public function get_circle_class(){
        $select = $_POST['select'];
        $circle = M('class');
        if($select){
            $result = $circle->where("parent_id='$select'")->getfield("class_id,class_name",true);
            $this->ajaxReturn($result);
        }else{
            $result = $circle->where("parent_id=0")->getfield("class_id,class_name",true);
            $this->ajaxReturn($result);
        }
    }
    public function create_circle(){
        $circle = M('circle');
        $circle_name = $_POST['circle_name'];
        $circle_intro = $_POST['circle_intro'];
        $circle_class = $_POST['circle_class'];
        if(isset($_POST['circle_avatar'])){
            $circle_avatar = $_POST['circle_avatar'];
        }else{
            $circle_avatar = $this->default;
        }
        $insert_data['circle_name'] = $circle_name;
        $insert_data['circle_intro'] = $circle_intro;
        $insert_data['circle_class'] = $circle_class;
        $insert_data['circle_avatar'] = $circle_avatar;
        $insert_data['circle_creator'] = session('uid');
        $circle->data($insert_data)->add();
    }
    public function my_circle(){
        $flag = true;
        $circle = M('circle');
        if(isset($_GET['id'])) {
            $id = $_GET['id'];
            $data = $circle->where("circle_id = $id")->find();
            if($data['checked']=='unpass') {
                $this->error('页面不存在','../Circle',3);
                return ;
            }
            $this->assign('total',M('post')->where("p_cid = $id")->count());
            $class = M('class')->find($data['circle_class']);
            $this->assign("name",$data['circle_name']);
            $this->assign("intro",$data["circle_intro"]);
            $this->assign("people",$data["circle_people_num"]);
            $this->assign("article",$data["circle_article_num"]);
            $this->assign("id",$_GET['id']);
            $this->assign("class",$class['class_name']);
            $this->assign("category",$class['class_id']);
            $this->assign("avatar",$data['circle_avatar']);
            if(is_null($data['notice'])){
                $this->assign("notice","暂无公告");
            }
            else $this->assign("notice",$data['notice']);
            $creator_id = $data['circle_creator'];
            $creator = M('data')->join("my_user on my_user.uid = my_data.d_uid")->where("d_uid = $creator_id")->find();
            $this->assign("creator",$creator['username']);
            $this->assign("creator_avatar",$creator['d_avatar']);
            if(!$_COOKIE['history_circle']){
                $arr = array();
            }else {
                $arr = $_COOKIE['history_circle'];
                $arr = json_decode($arr);
                for($i = 0; $i<count($arr);$i++){
                    if($arr[$i]->id==$_GET['id']){
                        $flag = false;
                    }
                }
            }
            $history = array('id'=>$_GET['id'],'avatar'=>$data['circle_avatar'],'name'=>$data['circle_name'],'time'=>localtime());
            if($flag) array_push($arr,$history);
            if(count($arr)>=6) {
                array_shift($arr);
            }
            $arr = json_encode($arr,JSON_UNESCAPED_UNICODE);
            setcookie('history_circle',$arr,time()+3600*24*365*3);
        }
        $this->display();
    }
//    加入兴趣圈操作
    public function join(){
        if($this->redirect_login()){
            return;
        }
        $join = M('relation');
        $uid = session("uid");
        $data['r_cid'] = $_GET['circle_id'];
        $data['r_uid'] = $uid;
        $join->add($data);
        $this->people_count($_GET['circle_id'],'join');
    }
    public function quit(){
        $quit = M('relation');
        $uid = session('uid');
        $cid = $_POST['circle_id'];
        $quit->where("r_uid='$uid' and r_cid=$cid")->delete();
        $this->people_count($cid,'quit');
    }
    public function people_count($cid,$action){
        $circle = M('circle');
        if($action=='join'){
            $circle->where("circle_id='$cid'")->setInc('circle_people_num');
        }
        if($action=='quit'){
            $circle->where("circle_id='$cid'")->setDec('circle_people_num');
        }
    }
    public function user_list(){
        $user = M('user');
        $uid = session("uid");
        if(isset($uid)){
            $join = $user -> query("SELECT circle_id,circle_name,circle_avatar FROM my_user 
            JOIN my_relation on my_relation.r_uid=my_user.uid 
            JOIN my_circle on my_relation.r_cid=my_circle.circle_id
            where uid = '$uid' ORDER BY circle_id DESC LIMIT 4");
            $article = $user -> query("SELECT title ,article_id FROM my_user 
                              JOIN my_write ON my_user.uid = my_write.w_uid 
                              JOIN my_article ON my_article.article_id = my_write.w_aid
                              WHERE uid = '$uid' ORDER BY publish_date DESC LIMIT 5");
            $result = array($join,$article);
            $this->ajaxReturn($result);
        }
    }
    public function join_status(){
        $relation = M('relation');
        $uid = session('uid');
        $cid = $_POST['cid'];
        if($relation->where("r_uid = '$uid' and r_cid = '$cid'")->find()){
            $this->ajaxReturn(true);
        }else{
            $this->ajaxReturn(false);
        }
    }
    public function redirect_login(){
        if(session("uid")){
            return false;
        }
        $this->ajaxReturn(true);
        return true;
    }
    public function write(){
        if($this->redirect_login()){
            return;
        }
        $this->ajaxReturn(false);
    }
    public function upload($savepath=null){
        $upload = new \Think\Upload();// 实例化上传类
        $upload->maxSize = 0;
        $upload->rootPath = '..';
        $upload->savePath = '/upload/circle/';
        if(!is_null($savepath)){
            $upload->savePath = $savepath;
        }
        $upload->saveName = array('uniqid','');
        $upload->exts     = array('jpg', 'gif', 'png', 'jpeg');
        $upload->autoSub  = true;
        $upload->subName  = array('date','Ymd');
        // 上传单个文件
        $info   =   $upload->uploadOne($_FILES['photo']);
        if(!$info) {
            // 上传错误返回错误信息
            $data['head'] = false;
            $data['content'] = $upload->getError();
        }else{
            // 上传成功返回文件路径
            $data['head'] = true;
            $data['content'] = $info['savepath'].$info['savename'];
        }
        $this->ajaxReturn($data);
    }
    public function recommendPost(){
        $article = M('article');
        $cid = $_POST['circle_id'];
        $rec = $article ->join("my_post on my_post.p_aid = my_article.article_id")->where("p_cid = $cid")->field('article_id,title,pageview,comment')->limit(5)->select();
        $this->ajaxReturn($rec);
    }
    public function getTopicCircleList(){
        $circle = M('circle');
        $rank = $circle -> field('circle_id,circle_name,circle_people_num,circle_article_num,circle_avatar')->select();
        $rank = array_chunk($this->quick_sort($rank),5);
        $this->ajaxReturn($rank[0]);
    }
    public function quick_sort($arr) {
        //先判断是否需要继续进行
        $length = count($arr);
        if($length <= 1) {
            return $arr;
        }
        //如果没有返回，说明数组内的元素个数 多余1个，需要排序
        //选择第一个元素
        $base_num = $arr[0];
        $base = $arr[0]['circle_people_num'] + $arr[0]['circle_people_num']*2;
        //遍历 除了标尺外的所有元素，按照大小关系放入两个数组内
        //初始化两个数组
        $left_array = array();
        $right_array = array();
        for($i=1; $i<$length; $i++) {
            $current = $arr[$i]['circle_people_num'] + $arr[$i]['circle_people_num']*2;
            if($base < $current) {
                //放入左边数组
                $left_array[] = $arr[$i];
            } else {
                //放入右边
                $right_array[] = $arr[$i];
            }
        }
        //递归
        $left_array = $this->quick_sort($left_array);
        $right_array = $this->quick_sort($right_array);
        //合并
        return array_merge($left_array, array($base_num), $right_array);
    }
}