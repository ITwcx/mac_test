<?php

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