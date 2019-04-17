<?php
namespace Home\Controller;
use Think\Controller;
class IndexController extends Controller 
{

	//主页
    public function index()
    {
    	// $str  =  '3:56 PM';

    	// $ex_one_Arr   =   explode(':', $str);
    	// $ex_two_Arr   =   explode(' ', $ex_one_Arr[1]);

   		// $hour    =  $ex_one_Arr[0];
    	// $minute  =  $ex_two_Arr[0];
    	// $ampm    =  $ex_two_Arr[1];

    	// echo '<pre>';


    	// var_dump($hour);
    	// var_dump($minute);
    	// var_dump($ampm);

    	// exit;




        $this->display('index');
    }

    //执行登录
    public function do_login()
    {	

    	$login_name    =   I('post.login_name');
    	$login_pwd     =   md5(I('post.login_pwd'));

    	$user_info     =   M('t_user')
    					   ->where("user_name='{$login_name}' AND user_pwd='{$login_pwd}' ")
    					   ->find();

    	if($user_info)
    	{
    		$_SESSION['user_info']  =   $user_info;

    		$output_J   =  array(

    			'code' => '1',
    			'msg'  =>  'Login successfully！',
    			'user_info' => $user_info
    		);

    	}
    	else
    	{

    		$output_J   =  array(

    			'code' => '901',
    			'msg' => 'Username or password incorrect'
    		);
    	}

    	exit(json_encode($output_J));
  
    }

    // 退出登录
    public function logout()
    {
    	unset($_SESSION);
    	session_destroy();
    }

    // 执行注册
    public function do_register()
    {	

    	$MOD_user         =    M('t_user');

    	$register_name    =   I('post.register_name');
    	$register_pwd     =   md5(I('post.register_pwd'));
    	$register_email   =   I('post.register_email');


    	$check_name_repeat   =  $MOD_user
    							->where("user_name = '{$register_name}' ")
    							->find();

    	if($check_name_repeat)
    	{
    		$output_J   =  array(

    			'code' => '902',
    			'msg' => 'Name already exists.'
    		);

	    	exit(json_encode($output_J));
    	}


    	$add_Arr    =   array(

    		'user_name'  =>  $register_name,
    		'user_pwd'   =>  $register_pwd,
    		'user_email' =>  $register_email,
    		'ctime'      =>  date('Y-m-d H:i:s')

    	);

    	$add_Res       =   $MOD_user->add($add_Arr);

    	if($add_Res)
    	{
    		$user_info   =   $MOD_user
    						 ->where('user_id='.$add_Res)
    						 ->find();

    		$_SESSION['user_info']  =   $user_info;

    		$output_J   =  array(

    			'code' => '1',
    			'msg'  =>  'Register successfully！',
    			'user_info' => $user_info
    		);

    	}
    	else
    	{

    		$output_J   =  array(

    			'code' => '901',
    			'msg' => 'Registration failed.'
    		);
    	}

    	exit(json_encode($output_J));
  
    }

    // 新增事件
    public function do_add_event()
    {	
    	$MOD_event        =    M('t_event');


    	$add_event_time   =    I('post.add_event_time');

    	
    	$ex_one_Arr   =   explode(':', $add_event_time);
    	$ex_two_Arr   =   explode(' ', $ex_one_Arr[1]);

   		$hour         =   $ex_one_Arr[0]; //时
    	$minute       =   $ex_two_Arr[0]; //分
    	$ampm         =   $ex_two_Arr[1]; //AM or PM


    	$add_Arr   =   array();

    	$add_Arr['event_title']       =   I('post.event_title');
    	$add_Arr['event_details']     =   I('post.event_details');
    	$add_Arr['choose_date_ymd']   =   I('post.choose_date_ymd');
    	$add_Arr['user_id']   		  =   I('post.user_id');

    	$add_Arr['event_time']        =   $add_event_time;
    	$add_Arr['hour']    		  =   $hour;
    	$add_Arr['minute']    		  =   $minute;
    	$add_Arr['ampm']    		  =   $ampm;

 		$add_Arr['ctime']  			  =   date('Y-m-d H:i:s');

    	$add_Res           			  =   $MOD_event->add($add_Arr);


    	if($add_Res)
    	{
    		$add_Arr['event_id']   =  $add_Res;

    		$output_J   =  array(

    			'code' =>  1,
    			'msg'  =>  'Add success！',
    			'event_info' => $add_Arr
    		);

    	}
    	else
    	{

    		$output_J   =  array(

    			'code' => 901,
    			'msg' => 'add failed.'
    		);
    	}

    	exit(json_encode($output_J));
  
    }	

    //查找当日事件
    public function select_event()
    {	
    	$MOD_event          =   M('t_event');

    	$user_id     		=   I('post.user_id');
    	$choose_date_ymd    =   I('post.choose_date_ymd');


    	$event_list         =   $MOD_event
    							->where("user_id={$user_id} AND choose_date_ymd = '{$choose_date_ymd}' ")
    							->order('ampm ASC,hour ASC, minute ASC ')
    							->select();


    	exit(json_encode($event_list));

    }	

    // 执行删除事件
    public function do_delete_event()
    {	
    	$MOD_event          =   M('t_event');

    	$event_id     		=   I('post.event_id');

    	$delete_Res         =   $MOD_event
    							->where('event_id='.$event_id)
    							->delete();

    	if($delete_Res)
    	{
    		$output_J   =  array(

    			'code' =>  1,
    			'msg'  =>  'Delete success！'
    		);

    	}
    	else
    	{

    		$output_J   =  array(

    			'code' => 901,
    			'msg' => 'Delete failed.'
    		);
    	}

    	exit(json_encode($output_J));
    }	


    //执行修改事件
    public function do_save_event()
    {	
    	$MOD_event         =   M('t_event');


    	$edit_event_time   =   I('post.edit_event_time');

    	$ex_one_Arr        =   explode(':', $edit_event_time);
    	$ex_two_Arr        =   explode(' ', $ex_one_Arr[1]);

   		$hour              =   $ex_one_Arr[0]; //时
    	$minute            =   $ex_two_Arr[0]; //分
    	$ampm              =   $ex_two_Arr[1]; //AM or PM


    	$edit_event_id     		=   I('post.edit_event_id');
    	$edit_event_title     	=   I('post.edit_event_title');
    	$edit_event_details     =   I('post.edit_event_details');

    	$save_Arr      =     array(

    		'event_title'    => $edit_event_title,
    		'event_details'  => $edit_event_details,
    		'utime'          => date('Y-m-d H:i:s'),
    		'event_time'     => $edit_event_time,
    		'hour'           => $hour,
    		'minute'         => $minute,
    		'ampm'           => $ampm

    	);

    	$save_Res         =   $MOD_event
    							->where('event_id='.$edit_event_id)
    							->save($save_Arr);

    	if($save_Res)
    	{
    		$output_J   =  array(

    			'code' =>  1,
    			'msg'  =>  'Update success！'
    		);

    	}
    	else
    	{

    		$output_J   =  array(

    			'code' => 901,
    			'msg' => 'Update failed.'
    		);
    	}

    	exit(json_encode($output_J));
    }	

    
    



}