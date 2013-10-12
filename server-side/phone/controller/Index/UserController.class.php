<?php

class UserController extends Controller {
    
    public function indexAction(){
        
    }

    public function dologinAction(){
        header("Access-Control-Allow-Origin:*");
        error_reporting(E_ALL ^ E_NOTICE);
        include_once(ROOT_PATH .'includes/lib_clips.php');
        include_once(ROOT_PATH . 'includes/lib_transaction.php');
        
        //angular.js 对php post的bug
        $json = file_get_contents("php://input"); 
        $_POST = json_decode($json,true);
        
        $username = isset($_GET['username']) ? trim($_GET['username']) : '';
        $password = isset($_GET['password']) ? trim($_GET['password']) : '';

        $user =& init_users();
       // var_dump($user);
        if ($user->login($username, $password,isset($_POST['remember'])))
        {
            update_user_info();
            //recalculate_price();

            //$password = $user->compile_password(array('password'=>$password));
            //echo $password;exit;
            $sql = "SELECT user_id,user_name,email,user_money,pay_points,rank_points,last_login
                    FROM ecs_users
                    WHERE user_name = '$username'";
            //echo $sql;exit;
            $user = $GLOBALS['db']->getRow($sql); 
            $user['last_login'] = date("Y-m-d H:i:s",$user['last_login']);     
            
            $orders = get_user_orders($user['user_id'], 100, 0);
            $user['orders'] = $orders;
            //print_r($user);
            echo json_encode(array("code"=>200,'data'=>$user,'msg'=>'登录成功'));
        }
        else
        {
            //$_SESSION['login_fail'] ++ ;
            echo json_encode(array("code"=>400,'msg'=>'用户名或密码错误'));
           
        }
    }

    public function collectAction(){
        $_SESSION['user_id'] = $_GET['user_id'];
        $goods_id = $_GET['goods_id'];
        /* 检查是否已经存在于用户的收藏夹 */
        $sql = "SELECT COUNT(*) FROM " .$GLOBALS['ecs']->table('collect_goods') .
            " WHERE user_id='$_SESSION[user_id]' AND goods_id = '$goods_id'";
        if ($GLOBALS['db']->GetOne($sql) > 0)
        {
            $result['code'] = 400;
            $result['msg'] = $GLOBALS['_LANG']['collect_existed'];
            die(json_encode($result));

        }
        else
        {
            $time = gmtime();
            $sql = "INSERT INTO " .$GLOBALS['ecs']->table('collect_goods'). " (user_id, goods_id, add_time)" .
                    "VALUES ('$_SESSION[user_id]', '$goods_id', '$time')";

            if ($GLOBALS['db']->query($sql) === false)
            {
                $result['code'] = 400;
                $result['msg'] = $GLOBALS['db']->errorMsg();
                die(json_encode($result));
            }
            else
            {
                $result['code'] = 200;
                $result['msg'] = $GLOBALS['_LANG']['collect_success'];
                die(json_encode($result));
            }
        }
    }

    public function collectionAction(){
        include_once(ROOT_PATH . 'includes/lib_clips.php');
        $user_id = $_GET['user_id'];
        $c = get_collection_goods($user_id, 100, 0);    //user_id size page
        $c = array_values($c);
        if(count($c) != 0 ){
            $d = array('code'=>200,'msg'=>'获取收藏成功','data'=>$c);
            echo json_encode($d);
        }else{
            $d = array('code'=>400,'msg'=>'没有收藏',);
            echo json_encode($d);
        }
    }

    public function delete_collectionAction(){
        include_once(ROOT_PATH . 'includes/lib_clips.php');
        $collection_id = isset($_GET['collection_id']) ? intval($_GET['collection_id']) : 0;

        if ($collection_id > 0)
        {
            $GLOBALS['db']->query('DELETE FROM ' .$GLOBALS['ecs']->table('collect_goods'). " WHERE rec_id='$collection_id' " );
        }

        echo json_encode(array('code'=> 200,'msg'=>'删除成功！'));
    }

}