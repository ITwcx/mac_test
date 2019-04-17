

  $(document).ready(function(){

   

  });




var localDate = {
    //接受后端传入有课的日期，格式为0124 0613
    date: []
}
    //以下是随机模拟数据
for(var j = 0; j < 30; j++) {
    var a = Math.ceil(Math.random() * 11);
    if(a < 10) {
        a = "0" + a;
    }
    var b = Math.ceil(Math.random() * 30);
    if(b < 10) {
        b = "0" + b;
    }
    var c = a.toString() + b.toString();
    localDate.date.push(c);
}

//初始化日期数据
var slidate = new Date();
var month   = slidate.getMonth();
var year    = slidate.getFullYear();
var week    = slidate.getDay();

var monthFirst = new Date(year, parseInt(month), 1).getDay(); //获取当月的1号等于星期几
var nextMonth = slidate.getMonth() + 1;
var d = new Date(year, parseInt(nextMonth), 0); //获取月
var conter = d.getDate(); //获取当前月的天数
var monthNum;
var monthCheck = (month + 1);
var date = slidate.getDate();
  
if(month<9){
    monthNum ="0"+ (month+1);
}else{
    monthNum = (month+1);
}


initall();

function initall() 
{
    dateHandler(monthFirst, d, conter, monthNum);
    checkDate(monthCheck);
}

function dateHandler(monthFirst, d, conter, monthNum) 
{
    var today = new Date();
    var today_todaydate  = today.getDate();
    var today_todayMonth = today.getMonth();
    var today_todayYear  = today.getFullYear();
   
    var u = 1;
    var blank = true;
    var $tbody = $('#tbody'),
        $month = $("#month"),
        _nullnei = '';


       // console.log(monthNum); 

    $month.append(`<p>${year}-${monthNum}</p>`);
    //遍历日历网格
    for(var i = 1; i <= 6; i++) {
        _nullnei += "<tr>";  
        for(var j = 0; j <= 6; j++)
        {

            var weekArr=["Sunday","Monday","Tuesday","Wednesday","Thursday","Friday","Saturday"];

            _nullnei += '<td  week_num="'+j+'" week_str="'+weekArr[j]+'" ></td>';

        }
        _nullnei += "</tr>";
    }
    $tbody.html(_nullnei);

    //遍历网格内容
    var $slitd = $tbody.find("td");
    for(var i = 0; i < conter; i++) 
    {
        //往网格里填充内容


        var dayNum    =  parseInt(i + 1);

        $slitd.eq(i + monthFirst).html("<p onclick='tdClickMyFunc(this)' dayNum='" + dayNum +"'  > " + parseInt(i + 1) + "</p>")

        var week_num =  $('#td'+i).attr('week_num');

        // console.log(week_num);

    }
    //给有日期的td加上id 
    var dayBlock = document.getElementsByTagName("td");
    for(var i = 0; i < dayBlock.length; i++) {
        if(dayBlock[i].textContent != "") {
            dayBlock[i].setAttribute("id", "td" + u);
            u++;
        }
    }

 
    //今天之前的日期不可选
    // if(today_todayYear > year)
    // {
    //     $('td').addClass('disabled');
    // }

    // if(today_todayYear ==  year  && today_todayMonth > month)
    // {
    //     $('td').addClass('disabled');
    // }

    // if(today_todayYear == year  &&  today_todayMonth == month)
    // {
    //     for(var i =0;i<date;i++)
    //     {
    //         $(`#td${i}`).addClass('disabled');
    //     } 
    // }



    //给今天的日期加上class
    if(today_todayYear==year)
    {
        if(today_todayMonth==month)
        {
            $(`#td${date}`).addClass("today");
        }

        if(today_todayMonth.toString().length == 1)
        {
            var today_todayMonth  =  '0' +  today_todayMonth;
        }

        var Today_YMD   =   today_todayYear + '-' + today_todayMonth+ '-' +date;

        console.log(Today_YMD);
    }
   


    //若日期不足排满每一行的tr，则删除最后一个tr
    var blankTr = document.getElementsByTagName("tr");
    var blankTd = blankTr[5].getElementsByTagName("td");
    for(var i = 0; i < blankTd.length; i++) {
        if(blankTd[i].textContent != "") {
            blank = false;
        }
    }
    if(blank == true) {
        blankTr[5].remove();
    }



}

function checkDate(prep) {

    var dateArray = [];
    var newArray = [];
    //删除不是本月的日期
    for(var i = 0; i < localDate.date.length; i++) {
        dateArray.push(localDate.date[i]);
    }
    for(var i = 0; i < dateArray.length; i++) {
        if(dateArray[i].charAt(1) != prep) {
            dateArray[i] = undefined;
        }
    }
    for(var i = 0; i < dateArray.length; i++) {
        if(dateArray[i] != undefined) {
            newArray.push(dateArray[i]);
        }
    }





    //遍历数组为有课的日期添加class
    for(var i = 0; i < newArray.length; i++) {

        //获取到的数组为XX0X的情况，比如0606
        if(newArray[i].charAt(2) == 0) {
            for(var j = 0; j < 10; j++) {
                if(newArray[i].charAt(3) == j) {
                    var checked = "#td" + j;
                     // $(checked).append(`<span class="haveClass"></span>`);
                }
            }
        } else if(newArray[i].charAt(2) == 1) {
            //获取到的数组为XX1X的情况，比如0612
            for(var j = 0; j < 10; j++) {
                if(newArray[i].charAt(3) == j) {
                    var checked = "#td1" + j;
                    // $(checked).append(`<span class="haveClass"></span>`);
                }
            }
        } else if(newArray[i].charAt(2) == 2){
            for(var j = 0; j < 10; j++) {
                if(newArray[i].charAt(3) == j) {
                    var checked = "#td2" + j;
                    // $(checked).append(`<span class="haveClass"></span>`);
                }
            }
        }else{
            for(var j = 0; j < 10; j++) {
                if(newArray[i].charAt(3) == j) {
                    var checked = "#td3" + j;
                    // $(checked).append(`<span class="haveClass"></span>`);
                }
            }
        }
    }


    for(var Im=0; Im<=31 ; Im++)
    {
        var Td_OBJ    =   $('#td'+Im);
        var week_num  =   Td_OBJ.attr('week_num');

        //周几有课插入黄点
        var is_day_class  =   $('#day_class_'+week_num).val();
    
        if(is_day_class == 'Y')
        {
            Td_OBJ.append(`<span class="haveClass"></span>`);
        }
    }


}


//上月
$("#pre").on("click", function() {
    $("tr").remove();
    $("p").remove();

    if(month>0){
        month--,nextMonth--;
    }else{
        month=11,year--;
    }

    var monthFirst = new Date(year, parseInt(month), 1).getDay();
    var d = new Date(year, parseInt(month), 0);
    var conter = d.getDate(); 
    if(month<9){
        var monthNum ="0"+ (month+1);
     }else{
         var monthNum = (month+1);
     }

    var monthCheck = nextMonth;
    dateHandler(monthFirst, d, conter, monthNum);
    checkDate(monthCheck);
});



 //下月
$("#next").on("click", function() {
    $("tr").remove();
    $("p").remove();
    //如果>12月，则重置月份，年份+1
    if(month<=10){
    month++,nextMonth++;
    }else{
        month=0;
        year++;
    }
    //dateHandler(第一天显示在哪里，月份信息，这个月有几天，几月)
    var monthFirst = new Date(year, parseInt(month), 1).getDay();
    var d = new Date(year, parseInt(month), 0);
    var conter = d.getDate(); 
    if(month<9){
        var monthNum ="0"+ (month+1);
     }else{
         var monthNum = (month+1);
     }

     var monthCheck = nextMonth;
    dateHandler(monthFirst, d, conter, monthNum);
    checkDate(monthCheck);
})

