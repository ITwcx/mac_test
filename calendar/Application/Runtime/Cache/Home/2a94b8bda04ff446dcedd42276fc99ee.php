<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE html>

<html>

<head>
	<meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1,user-scalable=no">
    <meta http-equiv="Pragma" content="no-cache">
    <meta http-equiv="Expires" content="0">
    <meta http-equiv="cleartype" content="on">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>日历主页</title>

	<link rel="stylesheet" href="/calendar/Public/src/bootstrap/dist/css/bootstrap.min.css">

    <link rel="stylesheet" href="/calendar/Public/Index/css/styel.css?v=4">

    <style type="text/css">

    	body
    	{
    		text-align: center;
    		padding-top: 40px;
    	}

        .button
        {
            width: 120px;
            height: 30px;
            line-height: 30px;
            cursor: pointer;
        }

        .today
        {
        	background-color: orange;
        }

		.top-user-info
		{
			font-size: 18px;
			margin-bottom: 20px;
		}	

		.display_css
		{
			display: none;
		}

		#Logout_Btn
		{
			float: right;
			padding-right: 20px;
		}




	.dateBody > tr > td 
	{
	    width: 14.28%;
	    height: 60px;
	    text-align: center;
	    position: relative;
	    padding: 2px;
	    background-color: #fff;
		cursor: pointer;
	}

	.dateBody > tr > td
	{
	    border:1px solid #eee;
	}

	.dateBody > td > p
	{
		height: 60px;
		line-height: 60px;
	}

</style>

</head>

<body>

	<div style="height: 30px;"></div>

	<div id="Y-login-box" style="display: none;">
		<div class="top-user-info">
			<b>UserName:</b>   <span id="user-name-span"></span> 

			<a href="javascript:void(0);" id="Logout_Btn"  >Logout > </a>

		</div>

		<!-- <div class="nav-box">
			<ul>

			  <li  id="user-list-menu"   >
			  		<a href="javascript:void(0);">User List</a>
			  </li>

			  <li>
			  		<a href="javascript:void(0);">Event List</a>
			  </li>

			  <li>
			  		<a href="javascript:void(0);" id="Logout_Btn"  >Logout</a>
			  </li>
			</ul>
		</div>
 -->
	</div>


	<div  class="top-user-info" id="N-login-box"  style="display: none;">
		<a href="javascript:void(0);" id="to_login">You have not logged in yet. Login now?</a>
	</div>

	

	<hr style="border-top: 1px solid #aaa;" />

	<br />


	<div class="date">

        <div class="dateHead" style="margin-bottom: 20px;">
            <div id="pre" class="button btn btn-success" >
                <span>pre</span>
            </div>
            <span id="month" class="month"></span>
            <div id="next" class="button btn btn-success">
                <span>next</span>
            </div>

        </div>

        <div class="datebody">
            <table cellspacing="3" cellpadding="3" class="home cal-table ">
                <ul class="thead">
                    <li>Sun</li>
                    <li>Mon</li>
                    <li>Tue</li>
                    <li>Wed</li>
                    <li>Thu</li>
                    <li>Fri</li>
                    <li>Sat</li>
                </ul>
                <tbody id="tbody" class="dateBody"></tbody>
            </table>
        </div>

    </div>



	<input type="hidden" id="user_id"   value="<?php echo ($_SESSION['user_info']['user_id']); ?>"  />
	<input type="hidden" id="user_name" value="<?php echo ($_SESSION['user_info']['user_name']); ?>"  />
	<input type="hidden" id="user_role" value="<?php echo ($_SESSION['user_info']['user_role']); ?>"  />

	<input type="hidden" id="choose_date_ymd"  value=""  />

</body>

<script src="/calendar/Public/src/jquery/dist/jquery.min.js"></script>
<script src="/calendar/Public/Index/js/date.js?v=<?php echo time();?>"></script>

<script src="/calendar/Public/src/bootstrap/dist/js/bootstrap.min.js"></script>

<script type="text/javascript">
	This_URL   =  '/calendar/index.php/Home/Index/';
</script>


<style type="text/css">
	.btn { display: inline-block; *display: inline; *zoom: 1; padding: 4px 10px 4px; margin-bottom: 0; font-size: 13px; line-height: 18px; color: #333333; text-align: center;text-shadow: 0 1px 1px rgba(255, 255, 255, 0.75); vertical-align: middle; background-color: #f5f5f5; background-image: -moz-linear-gradient(top, #ffffff, #e6e6e6); background-image: -ms-linear-gradient(top, #ffffff, #e6e6e6); background-image: -webkit-gradient(linear, 0 0, 0 100%, from(#ffffff), to(#e6e6e6)); background-image: -webkit-linear-gradient(top, #ffffff, #e6e6e6); background-image: -o-linear-gradient(top, #ffffff, #e6e6e6); background-image: linear-gradient(top, #ffffff, #e6e6e6); background-repeat: repeat-x; filter: progid:dximagetransform.microsoft.gradient(startColorstr=#ffffff, endColorstr=#e6e6e6, GradientType=0); border-color: #e6e6e6 #e6e6e6 #e6e6e6; border-color: rgba(0, 0, 0, 0.1) rgba(0, 0, 0, 0.1) rgba(0, 0, 0, 0.25); border: 1px solid #e6e6e6; -webkit-border-radius: 4px; -moz-border-radius: 4px; border-radius: 4px; -webkit-box-shadow: inset 0 1px 0 rgba(255, 255, 255, 0.2), 0 1px 2px rgba(0, 0, 0, 0.05); -moz-box-shadow: inset 0 1px 0 rgba(255, 255, 255, 0.2), 0 1px 2px rgba(0, 0, 0, 0.05); box-shadow: inset 0 1px 0 rgba(255, 255, 255, 0.2), 0 1px 2px rgba(0, 0, 0, 0.05); cursor: pointer; *margin-left: .3em; }
.btn:hover, .btn:active, .btn.active, .btn.disabled, .btn[disabled] { background-color: #e6e6e6; }
.btn-large { padding: 9px 14px; font-size: 15px; line-height: normal; -webkit-border-radius: 5px; -moz-border-radius: 5px; border-radius: 5px; }
.btn:hover { color: #333333; text-decoration: none; background-color: #e6e6e6; background-position: 0 -15px; -webkit-transition: background-position 0.1s linear; -moz-transition: background-position 0.1s linear; -ms-transition: background-position 0.1s linear; -o-transition: background-position 0.1s linear; transition: background-position 0.1s linear; }
.btn-primary, .btn-primary:hover { text-shadow: 0 -1px 0 rgba(0, 0, 0, 0.25); color: #ffffff; }
.btn-primary.active { color: rgba(255, 255, 255, 0.75); }
.btn-primary { background-color: #4a77d4; background-image: -moz-linear-gradient(top, #6eb6de, #4a77d4); background-image: -ms-linear-gradient(top, #6eb6de, #4a77d4); background-image: -webkit-gradient(linear, 0 0, 0 100%, from(#6eb6de), to(#4a77d4)); background-image: -webkit-linear-gradient(top, #6eb6de, #4a77d4); background-image: -o-linear-gradient(top, #6eb6de, #4a77d4); background-image: linear-gradient(top, #6eb6de, #4a77d4); background-repeat: repeat-x; filter: progid:dximagetransform.microsoft.gradient(startColorstr=#6eb6de, endColorstr=#4a77d4, GradientType=0);  border: 1px solid #3762bc; text-shadow: 1px 1px 1px rgba(0,0,0,0.4); box-shadow: inset 0 1px 0 rgba(255, 255, 255, 0.2), 0 1px 2px rgba(0, 0, 0, 0.5); }
.btn-primary:hover, .btn-primary:active, .btn-primary.active, .btn-primary.disabled, .btn-primary[disabled] { filter: none; background-color: #4a77d4; }
.btn-block { width: 100%; display:block; }

* { -webkit-box-sizing:border-box; -moz-box-sizing:border-box; -ms-box-sizing:border-box; -o-box-sizing:border-box; box-sizing:border-box; }

html { width: 100%; height:100%; overflow:hidden; }

#login_modal { 
	width: 100%;
	height:100%;
	font-family: 'Open Sans', sans-serif;
	/*background: #092756;*/
/*	background: -moz-radial-gradient(0% 100%, ellipse cover, rgba(104,128,138,.4) 10%,rgba(138,114,76,0) 40%),-moz-linear-gradient(top,  rgba(57,173,219,.25) 0%, rgba(42,60,87,.4) 100%), -moz-linear-gradient(-45deg,  #670d10 0%, #092756 100%);
	background: -webkit-radial-gradient(0% 100%, ellipse cover, rgba(104,128,138,.4) 10%,rgba(138,114,76,0) 40%), -webkit-linear-gradient(top,  rgba(57,173,219,.25) 0%,rgba(42,60,87,.4) 100%), -webkit-linear-gradient(-45deg,  #670d10 0%,#092756 100%);
	background: -o-radial-gradient(0% 100%, ellipse cover, rgba(104,128,138,.4) 10%,rgba(138,114,76,0) 40%), -o-linear-gradient(top,  rgba(57,173,219,.25) 0%,rgba(42,60,87,.4) 100%), -o-linear-gradient(-45deg,  #670d10 0%,#092756 100%);
	background: -ms-radial-gradient(0% 100%, ellipse cover, rgba(104,128,138,.4) 10%,rgba(138,114,76,0) 40%), -ms-linear-gradient(top,  rgba(57,173,219,.25) 0%,rgba(42,60,87,.4) 100%), -ms-linear-gradient(-45deg,  #670d10 0%,#092756 100%);
	background: -webkit-radial-gradient(0% 100%, ellipse cover, rgba(104,128,138,.4) 10%,rgba(138,114,76,0) 40%), linear-gradient(to bottom,  rgba(57,173,219,.25) 0%,rgba(42,60,87,.4) 100%), linear-gradient(135deg,  #670d10 0%,#092756 100%);
	filter: progid:DXImageTransform.Microsoft.gradient( startColorstr='#3E1D6D', endColorstr='#092756',GradientType=1 );*/
}
.login { 
	position: absolute;
	top: 42%;
	left: 50%;
	margin: -150px 0 0 -150px;
	width:300px;
	min-height: 400px;
}
.login h1 { color: #fff; text-shadow: 0 0 10px rgba(0,0,0,0.3); letter-spacing:1px; text-align:center; }

input { 
	width: 100%; 
	margin-bottom: 10px; 
	background: rgba(0,0,0,0.3);
	border: none;
	outline: none;
	padding: 10px;
	font-size: 13px;
	color: #fff;
	text-shadow: 1px 1px 1px rgba(0,0,0,0.3);
	border: 1px solid rgba(0,0,0,0.3);
	border-radius: 4px;
	box-shadow: inset 0 -5px 45px rgba(100,100,100,0.2), 0 1px 1px rgba(255,255,255,0.2);
	-webkit-transition: box-shadow .5s ease;
	-moz-transition: box-shadow .5s ease;
	-o-transition: box-shadow .5s ease;
	-ms-transition: box-shadow .5s ease;
	transition: box-shadow .5s ease;
}
input:focus { box-shadow: inset 0 -5px 45px rgba(100,100,100,0.4), 0 1px 1px rgba(255,255,255,0.2); }

	.login_modal_content 
	{
		min-height: 500px;

		background: #092756;

		background: -moz-radial-gradient(0% 100%, ellipse cover, rgba(104,128,138,.4) 10%,rgba(138,114,76,0) 40%),-moz-linear-gradient(top,  rgba(57,173,219,.25) 0%, rgba(42,60,87,.4) 100%), -moz-linear-gradient(-45deg,  #670d10 0%, #092756 100%);
		background: -webkit-radial-gradient(0% 100%, ellipse cover, rgba(104,128,138,.4) 10%,rgba(138,114,76,0) 40%), -webkit-linear-gradient(top,  rgba(57,173,219,.25) 0%,rgba(42,60,87,.4) 100%), -webkit-linear-gradient(-45deg,  #670d10 0%,#092756 100%);
		background: -o-radial-gradient(0% 100%, ellipse cover, rgba(104,128,138,.4) 10%,rgba(138,114,76,0) 40%), -o-linear-gradient(top,  rgba(57,173,219,.25) 0%,rgba(42,60,87,.4) 100%), -o-linear-gradient(-45deg,  #670d10 0%,#092756 100%);
		background: -ms-radial-gradient(0% 100%, ellipse cover, rgba(104,128,138,.4) 10%,rgba(138,114,76,0) 40%), -ms-linear-gradient(top,  rgba(57,173,219,.25) 0%,rgba(42,60,87,.4) 100%), -ms-linear-gradient(-45deg,  #670d10 0%,#092756 100%);
		background: -webkit-radial-gradient(0% 100%, ellipse cover, rgba(104,128,138,.4) 10%,rgba(138,114,76,0) 40%), linear-gradient(to bottom,  rgba(57,173,219,.25) 0%,rgba(42,60,87,.4) 100%), linear-gradient(135deg,  #670d10 0%,#092756 100%);
		filter: progid:DXImageTransform.Microsoft.gradient( startColorstr='#3E1D6D', endColorstr='#092756',GradientType=1 );
	}

</style>

<!-- 登录弹框开始 -->
<div id="login_modal" class="modal fade bs-example-modal-lg" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
	<div class="modal-dialog modal-lg LG "  style="width: 90%;">

		<div class="modal-content login_modal_content">	
				
			<div class="login">

				<h1 style="margin-bottom: 50px;">Login</h1>

				<input type="text"     id="login_name"   placeholder="Name"       style="margin-bottom: 20px;" />
				<input type="password" id="login_pwd"    placeholder="Password"   style="margin-bottom: 50px;" />


				<button  id="do_Login_Btn" class="btn btn-primary btn-block btn-large" >Login</button>


				<div style="margin-top: 80px;">
					<a href="javascript:void(0);"  id="to_register" style="color:#fff;">No account to register?</a>
				</div>	

			</div>

			<!-- <div id="cancel_down_btn" type="button" class="btn TK_dis_btn" data-dismiss="modal">关闭弹框</div> -->

		</div>

	</div>
</div>
<!-- 登录弹框结束 -->


<!-- 注册弹框开始 -->
<div id="register_modal" class="modal fade bs-example-modal-lg" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
	<div class="modal-dialog modal-lg LG "  style="width: 90%;">

		<div class="modal-content login_modal_content">	
				
			<div class="login">

				<h1 style="margin-bottom: 50px;">Register</h1>

				<input type="text"     id="register_name"   placeholder="Name"       style="margin-bottom: 20px;" />

				<input type="password" id="register_pwd"    placeholder="Password"   style="margin-bottom: 20px;" />

				<input type="text"     id="register_email"  placeholder="Email"      style="margin-bottom: 50px;" />


				<button  id="do_Register_Btn" class="btn btn-primary btn-block btn-large" >Register</button>



			</div>

			<!-- <div id="cancel_down_btn" type="button" class="btn TK_dis_btn" data-dismiss="modal">关闭弹框</div> -->

		</div>

	</div>
</div>
<!-- 注册弹框结束 -->


<script type="text/javascript">

	//执行注册
	$('#do_Register_Btn').click(function(){

		var register_name  =  $('#register_name').val();
		var register_pwd   =  $('#register_pwd').val();		
		var register_email =  $('#register_email').val();		

		// console.log('login_name ===' + login_name);
		// console.log('login_pwd ===' + login_pwd);

		if(register_name.trim().length == 0)
		{
			alert('Name cannot be empty.');
			return false;
		}

		if(register_pwd.trim().length == 0)
		{
			alert('Password cannot be empty.');
			return false;
		}	

		if(register_email.trim().length == 0)
		{
			alert('Email cannot be empty.');
			return false;
		}		

		var EMAIL_REG  =  /^[A-Za-z\d]+([-_.][A-Za-z\d]+)*@([A-Za-z\d]+[-.])+[A-Za-z\d]{2,4}$/; 

	    if(!EMAIL_REG.test(register_email)) 
	   	{
			alert('Email format is incorrect.');
			return false;
	   	}

		$.post('/calendar/index.php/Home/Index/do_register',
			{
				register_name:register_name,
				register_pwd:register_pwd,
				register_email:register_email
			},
			function(p_Data){

				if(p_Data == '[]' || p_Data == '')
				{
					return false;
				}

				var p_Arr   =   JSON.parse(p_Data);

				if(p_Arr['code'] == 1)
				{

					alert(p_Arr['msg']);

					$('#register_modal').modal('hide');

					$('#user-name-span').html(p_Arr['user_info']['user_name']);
					$('#N-login-box').hide();
					$('#Y-login-box').show();


					$('#user_id').val(p_Arr['user_info']['user_id']);
					$('#user_name').val(p_Arr['user_info']['user_name']);
					$('#user_role').val(p_Arr['user_info']['user_role']);


					if(p_Arr['user_info']['user_role'] == 1)
					{
						$('#user-list-menu').show();
					}
					else
					{
						$('#user-list-menu').hide();
					}

				}	
				else
				{
					alert(p_Arr['msg']);

					$('#register_name').val('');
					$('#register_pwd').val('');		
					$('#register_email').val('');	

					return false;
				}

		});


	});



	// 执行登录
	$('#do_Login_Btn').click(function(){

		var login_name  =  $('#login_name').val();
		var login_pwd   =  $('#login_pwd').val();		

		// console.log('login_name ===' + login_name);
		// console.log('login_pwd ===' + login_pwd);

		if(login_name.trim().length == 0)
		{
			alert('Name cannot be empty.');
			return false;
		}

		if(login_pwd.trim().length == 0)
		{
			alert('Password cannot be empty.');
			return false;
		}		

		$.post('/calendar/index.php/Home/Index/do_login',
			{
				login_name:login_name,
				login_pwd:login_pwd
			},
			function(p_Data){

				if(p_Data == '[]' || p_Data == '')
				{
					return false;
				}

				var p_Arr   =   JSON.parse(p_Data);

				if(p_Arr['code'] == 1)
				{

					alert(p_Arr['msg']);

					$('#login_modal').modal('hide');

					$('#user-name-span').html(p_Arr['user_info']['user_name']);
					$('#N-login-box').hide();
					$('#Y-login-box').show();

					$('#user_id').val(p_Arr['user_info']['user_id']);
					$('#user_name').val(p_Arr['user_info']['user_name']);
					$('#user_role').val(p_Arr['user_info']['user_role']);


					if(p_Arr['user_info']['user_role'] == 1)
					{
						$('#user-list-menu').show();
					}
					else
					{
						$('#user-list-menu').hide();
					}


				}	
				else
				{
					alert(p_Arr['msg']);

					$('#login_name').val('');
					$('#login_pwd').val('');

					return false;
				}

		});

	});


	// 去登录
	$('#to_login').click(function(){

		$('#login_modal').modal('show');

	});

	// 去注册
	$('#to_register').click(function(){

		$('#login_modal').modal('hide');
		$('#register_modal').modal('show');


	});

</script>

<link href="/calendar/Public/src/bootstrap_time/css/bootstrap-datetimepicker.css" rel="stylesheet">
<link href="/calendar/Public/src/bootstrap_time/css/font-awesome.min.css" rel="stylesheet">
<!-- <link href="/calendar/Public/src/bootstrap_time/css/bootstrap.min.css"     rel="stylesheet" type="text/css" media="screen" /> -->

<link href="/calendar/Public/src/bootstrap_time/css/animate.min.css" rel="stylesheet">
<link href="/calendar/Public/src/bootstrap_time/css/prettify-1.0.css" rel="stylesheet">
<link href="/calendar/Public/src/bootstrap_time/css/base.css" rel="stylesheet">
<link href="/calendar/Public/src/bootstrap_time/css/default.css"  rel="stylesheet" type="text/css" >

<!-- <script src="/calendar/Public/src/bootstrap_time/js/bootstrap.min.js"   type="text/javascript" ></script> -->
<script src="/calendar/Public/src/bootstrap_time/js/moment-with-locales.js"></script>
<script src="/calendar/Public/src/bootstrap_time/js/bootstrap-datetimepicker.js"></script>



<style>
	.bootstrap-datetimepicker-widget
	{

	}
	.event_modal_content
	{
		min-height: 300px;

		background-color: #eee;
	}
	.Event_table
	{
		width: 96%;
		margin: 0 auto;
		min-height: 50px;
		text-align: center;
		line-height: 35px;
		margin-bottom: 60px;
	}
	.form-control
	{
		margin-bottom: 0px;
		height: 40px;
		color:black;
	}
	.Event_table thead th
	{
		text-align: center;
	}

	.Event_table thead tr th:nth-child(1)
	{
		width: 20%;
	}

	.Event_table thead tr th:nth-child(2)
	{
		width: 20%;
	}

	.Event_table thead tr th:nth-child(3)
	{
		width: 50%;
	}

	.Event_table thead tr th:nth-child(4)
	{
		width: 5%;
	}

	.Event_table thead tr th:nth-child(5)
	{
		width: 5%;
	}


	.Event_table .list-tbody tr:hover
	{
		background-color: green;
		color:#fff;
	}
	.glyphicon
	{
		font-size: 25px;
		cursor: pointer;
		margin-top: 10px;
	}

	.glyphicon-edit
	{
		color:orange;
	}

	.glyphicon-remove
	{
		color:red;
	}	

	.add_event_css
	{
		font-size: 25px;
		margin-bottom: 40px;
	}

	.add_tbody input
	{
		background-color: #fff;
	}

	.top-date-div
	{
		width: 100%;
		height: 45px;
		border-bottom:8px solid green;
	}

	.Table_title
	{
		font-size: 20px;
		font-weight: bold;
		margin: 20px;
	}

	.glyphicon-plus
	{
		color:green;
	}

	.save_event_Btn
	{
		font-size: 20px;
		width: 20%;
		height: 40px;
		line-height: 27px;
		border:1px solid green;
		color:green;
	}

	.choose_td
	{
		border: 2px solid green;
	}


</style>


<!-- 事件列表弹框开始 -->
<div id="event_modal" class="modal fade bs-example-modal-lg" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
	<div class="modal-dialog modal-lg LG "  style="width: 90%;">

		<div class="modal-content event_modal_content">	
				
			<div class="event_Box">

				<div class="top-date-div">
					<h3 >Date：<span class='TK_Date'></span> </h3>
				</div>

				<div class="Table_title">Event List</div>

				<table  class="Event_table" border="1">
					
					<thead>
						<tr>
							<th>Time</th>
							<th>Title</th>
							<th>Details</th>
							<th>Edit</th>
							<th>Delete</th>
						</tr>
					</thead>

					<tbody class="list-tbody">


					</tbody>

				
					
				</table>


				<div class="Table_title">Add Event</div>

				
				<table  class="Event_table" border="1">
					
					<thead>
						<tr>
							<th>Time</th>
							<th>Title</th>
							<th>Details</th>
							<th>Add</th>
						</tr>
					</thead>

					<tbody class="add-tbody">

						
						<tr>


							<td>
								 <div class="">
								    <div class="">
								        <div class="">
								            <input type='text' class="form-control" id="add_event_time"  placeholder="Click Select Time" />
								        </div>
								        <script type="text/javascript">
								            $(function () {
								                $('#add_event_time').datetimepicker({locale: 'en',format: 'LT'});
								            });
								        </script>
								    </div>
								</div>

							</td>

							<td>

								<input type="text" class="form-control" id="event_title" placeholder="title..."  />

							</td>
							<td>

								<input type="text" class="form-control" id="event_details"  placeholder="details..." />

							</td>
							<td>
								<span class="glyphicon glyphicon-plus" id="add_event_Btn" ></span>
							</td>
							
						</tr>
						


					</tbody>

				
					
				</table>




			</div>

		</div>

	</div>
</div>
<!-- 事件列表弹框结束 -->


<!-- 修改事件弹框开始 -->
<div id="save_event_modal" class="modal fade bs-example-modal-lg" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
	<div class="modal-dialog modal-lg LG "  style="width: 90%;">

		<div class="modal-content event_modal_content">	
				
			<div class="event_Box">

				<div class="top-date-div">
					<h3 >Date：<span class='TK_Date'></span> </h3>
				</div>

				<div class="Table_title" style="margin-top: 40px;"  >Edit Event</div>

				
				<table  class="Event_table Edit_Table" border="1" style="margin-bottom: 30px;">
					
					<thead>
						<tr>
							<th>Time</th>
							<th>Title</th>
							<th>Details</th>
						</tr>
					</thead>

					<tbody class="edit-tbody">

						<input type="hidden" id="edit_event_id"    />
						
						<tr>
							<td>
								<div class="">
								    <div class="">
								        <div class="">
								            <input type='text' class="form-control" id="edit_event_time"  placeholder="Click Select Time"  />
								        </div>
								        <script type="text/javascript">
								            $(function () {
								                $('#edit_event_time').datetimepicker({locale: 'en',format: 'LT'});
								            });
								        </script>
								    </div>
								</div>
							</td>
							<td>

								<input type="text" class="form-control" id="edit_event_title" placeholder="title..."  />

							</td>
							<td>

								<input type="text" class="form-control" id="edit_event_details"  placeholder="details..." />

							</td>
							
							
						</tr>
						


					</tbody>

				
					
				</table>


				<div style="margin-bottom: 40px;">

					<span class="btn btn-success save_event_Btn" id="save_event_Btn" >save</span>
					
				</div>



			</div>

		</div>

	</div>
</div>
<!-- 修改事件弹框结束 -->



<script type="text/javascript">

	//执行修改
	$('#save_event_Btn').click(function(){

		var edit_event_id         =   $('#edit_event_id').val();
		var edit_event_title      =   $('#edit_event_title').val();
		var edit_event_details    =   $('#edit_event_details').val();
		var edit_event_time       =   $('#edit_event_time').val();

		if(edit_event_time == '')
		{
			alert('Please choose the time first.');
			return false;
		}

		if(edit_event_title.trim().length == 0)
		{
			alert('Title cannot be empty.');
			return false;
		}



		// console.log('edit_event_id ==' + edit_event_id);
		// console.log('edit_event_title ==' + edit_event_title);
		// console.log('edit_event_details ==' + edit_event_details);

		$.post(This_URL + 'do_save_event',
			{
				edit_event_id:edit_event_id,
				edit_event_title:edit_event_title,
				edit_event_details:edit_event_details,
				edit_event_time:edit_event_time
			},
			function(p_Data){

				if(p_Data == '[]' || p_Data == '')
				{
					return false;
				}

				var p_Arr   =   JSON.parse(p_Data);

				if(p_Arr['code'] == 1)
				{
					alert(p_Arr['msg']);
					
					$('#save_event_modal').modal('hide');
				}	
				else
				{
					alert(p_Arr['msg']);
					return false;
				}

		});	


	});

	//点击修改事件
	function edit_event_func(obj)
	{
		var This_OBJ   =   $(obj);

		var event_id  		 =   This_OBJ.attr('event_id');
		var event_title      =   This_OBJ.attr('event_title');
		var event_details    =   This_OBJ.attr('event_details');
		var event_time       =   This_OBJ.attr('event_time');

		$('#edit_event_time').val(event_time);

		if(event_id == 'undefined')
		{
			alert('Please select the event to delete.');
			return false;
		}

		// var confirm_edit  =   confirm('Confirm Edit?');

		$('#edit_event_id').val(event_id);
		$('#edit_event_title').val(event_title);
		$('#edit_event_details').val(event_details);



		$('#event_modal').modal('hide');
		$('#save_event_modal').modal('show');
	}


	//删除事件
	function delete_event_func(obj)
	{
		var This_OBJ   =   $(obj);

		var event_id   =   This_OBJ.attr('event_id');

		if(event_id == 'undefined')
		{
			alert('Please select the event to delete.');
			return false;
		}

		var confirm_delete  =   confirm('Confirm Delete?');

		if(confirm_delete   ==  true)
		{
			$.post(This_URL + 'do_delete_event',
				{
					event_id:event_id
				},
				function(p_Data){

					if(p_Data == '[]' || p_Data == '')
					{
						return false;
					}

					var p_Arr   =   JSON.parse(p_Data);

					if(p_Arr['code'] == 1)
					{
						This_OBJ.parents('tr').remove();
					}	
					else
					{
						alert(p_Arr['msg']);
						return false;
					}

			});	


		}
	}



	//执行添加事件
	$('#add_event_Btn').click(function(){

		var event_title      =  $('#event_title').val();
		var event_details    =  $('#event_details').val();
		var choose_date_ymd  =  $('#choose_date_ymd').val();
		var user_id  		 =  $('#user_id').val();
		var add_event_time   =  $('#add_event_time').val();


		// console.log('event_title  === ' + event_title);
		// console.log('event_details  === ' + event_details);
		// console.log('choose_date_ymd  === ' + choose_date_ymd);

		if(user_id == '')
		{
			alert('You have not logged in yet.');
			return false;
		}

		if(add_event_time == '')
		{
			alert('Please choose the time first.');
			return false;
		}

		if(choose_date_ymd == '')
		{
			alert('Please choose the date first.');
			return false;
		}

		if(event_title.trim().length == 0)
		{
			alert('Title cannot be empty.');
			return false;
		}

		$.post(This_URL + 'do_add_event',
			{
				event_title:event_title,
				event_details:event_details,
				choose_date_ymd:choose_date_ymd,
				user_id:user_id,
				add_event_time:add_event_time
			},
			function(p_Data){

				if(p_Data == '[]' || p_Data == '')
				{
					return false;
				}

				var p_Arr   =   JSON.parse(p_Data);

				if(p_Arr['code'] == 1)
				{
					// alert(p_Arr['msg']);

					$('#event_title').val('');
					$('#event_details').val('');

					var event_id       =   p_Arr['event_info']['event_id'];
					var event_title    =   p_Arr['event_info']['event_title'];
					var event_details  =   p_Arr['event_info']['event_details'];
					var event_time     =   p_Arr['event_info']['event_time'];

					var new_event_HTML  =   `<tr>
												<td>` + event_time + `</td>
												<td>` + event_title + `</td>
												<td>` + event_details + `</td>
												<td>
													<span 
															onclick="edit_event_func(this)"
															event_id="`+ event_id +`"  
															event_title="`+ event_title +`"  
															event_details="`+ event_details +`"  
															event_time="`+ event_time +`"  
															class="glyphicon glyphicon-edit"
													></span>
												</td>
												<td>
													<span 
															onclick="delete_event_func(this)"
															event_id="`+ event_id +`"  
															class="glyphicon glyphicon-remove"
													></span>
												</td>
											</tr>`;


					$('.list-tbody').append(new_event_HTML);

					console.log();

				}	
				else
				{
					alert(p_Arr['msg']);


					return false;
				}

		});


	});


	// td 点击事件
	function tdClickMyFunc(obj)	
	{
		var This_OBJ    =   $(obj);

		$('td').removeClass(' choose_td');
		This_OBJ.closest('td').addClass(' choose_td');


		var ymNum    =    $('#month > p').html(); //年月
		var dayNum   =    This_OBJ.attr('dayNum'); //日

		var choose_date_ymd    =   ymNum + '-' + dayNum;


		console.log(choose_date_ymd);

		$('#choose_date_ymd').val(choose_date_ymd);
		$('.TK_Date').html(choose_date_ymd);

		$('.list-tbody').children().remove();


		var user_id     =   $('#user_id').val();

		if(user_id == '')
		{
			var confirm_login  =   confirm('You have not logged in yet. Login now?');

			if(confirm_login == true)
			{
				$('#login_name').val('');
				$('#login_pwd').val('');	

				$('#login_modal').modal('show');
			}		
		}
		else
		{

			// 发送ajax查询当天的事件
			$.post(This_URL + 'select_event',
				{
					choose_date_ymd:choose_date_ymd,
					user_id:user_id
				},
				function(p_Data){

					if(p_Data == '[]' || p_Data == '')
					{
						return false;
					}

					console.log(p_Data);

					var p_Arr   =   JSON.parse(p_Data);

					for(var Im=0; Im<p_Arr.length; Im++)
					{

						var  event_id  		=   p_Arr[Im]['event_id'];
						var  event_time     =   p_Arr[Im]['event_time'];
						var  event_title    =   p_Arr[Im]['event_title'];
						var  event_details  =   p_Arr[Im]['event_details'];

						var new_event_HTML  =   `<tr>
													<td>` + event_time    +`</td>
													<td>` + event_title   + `</td>
													<td>` + event_details + `</td>
													<td>
														<span 
																onclick="edit_event_func(this)"
																event_id="`+ event_id +`"  
																event_time="`+ event_time +` "  
																event_title="`+ event_title +`"  
																event_details="`+ event_details +`"  
																class="glyphicon glyphicon-edit"
														></span>
													</td>
													<td>
														<span 
																onclick="delete_event_func(this)"
																event_id="`+ event_id +`"  
																class="glyphicon glyphicon-remove"
														></span>
													</td>


												</tr>`;


						$('.list-tbody').append(new_event_HTML);
					}



			});


			$('#event_modal').modal('show');

		}


	}

</script>




<script type="text/javascript">

	$(document).ready(function(){

		var user_name  =  $('#user_name').val();
		var user_role  =  $('#user_role').val();

		if(user_name == '')
		{
			$('#N-login-box').show();
		}
		else
		{
			$('#user-name-span').html(user_name);
			$('#Y-login-box').show();
		}

		if(user_role == 1)
		{
			$('#user-list-menu').show();
		}
		else
		{
			$('#user-list-menu').hide();
		}

	});


	$('#Logout_Btn').click(function(){

		var confirm_logout  =   confirm('Confirm logout?');

		if(confirm_logout == true)
		{
			$.post(This_URL + 'logout');

			$('#N-login-box').show();
			$('#Y-login-box').hide();	

			$('#user_id').val('');
			$('#user_name').val('');
			$('#user_role').val('');

		}
		
	});

</script>


</html>