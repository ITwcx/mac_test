<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1,user-scalable=no">
    <meta http-equiv="Pragma" content="no-cache">
    <meta http-equiv="Expires" content="0">
    <meta http-equiv="cleartype" content="on">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>日历主页</title>
    <link rel="stylesheet" href="/calendar/Public/Index/css/styel.css?v=2018-06-22.2">

    <style type="text/css">
        .button
        {
            width: 120px;
            height: 30px;
            line-height: 30px;
        }
    </style>

</head>

<body>

    <div class="date">

        <div class="dateHead">
            <div id="pre" class="button" >
                <span>pre</span>
            </div>
            <span id="month" class="month"></span>
            <div id="next" class="button">
                <span>next</span>
            </div>

        </div>

        <div class="datebody">
            <table cellspacing="3" cellpadding="3" class="home">
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


    
<script>
    This_URL     =  '/calendar/index.php/Home/Index/'; 
    This_PUBLIC  =  '/calendar/Public/';
</script>

<script src="/calendar/Public/src/jquery/dist/jquery.min.js"></script>
<script src="/calendar/Public/Index/js/date.js?v=<?php echo time();?>"></script>
</body>
</html>