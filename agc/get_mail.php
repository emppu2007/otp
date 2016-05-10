<DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<style>
        body{background:#414141;}
        table{width:100%;margin-top:10px}
        td{text-align:left;padding:5px;border:2px dashed silver;margin: 0;background-color:white;}
</style>
</head>
<body>
<?php

require("language.php");
require("dbconnect_mysqli.php");

$lang= new lang("ru");
$uniqueid=date("U").".".rand(1, 9999);
if(preg_match("/[0-9]{1,}/",$_GET["id"]))
                {
        if($link){
                $stmt="select * from `vicidial_email_list` where email_row_id=".$_GET["id"];
                $rslt=mysqli_query($link,$stmt);
                $data=mysqli_fetch_assoc($rslt);
		$data["message"]=preg_replace("/Посилання на форму:\s*([\S]{1,})\s/",'Посилання на форму: <a href="$1">$1</a>',$data["message"]);

                $stmt="UPDATE `vicidial_email_list` SET status='DONE',user='".$_GET["user"]."',uniqueid='".$uniqueid."' where email_row_id=".$_GET["id"];
                mysqli_query($link,$stmt);
                $queue_time=date("U")-strtotime($data["email_date"]);
                if($queue_time > 30000){$queue_time=30000;}
                $stmt="insert into vicidial_closer_log(lead_id,campaign_id,call_date,start_epoch,end_epoch,status,user,comments,processed,queue_seconds,user_group,term_reason,uniqueid,queue_position) values ('".$data["lead_id"]."','".$data["group_id"]."','".date("Y-m-d h:i:s")."','".date("U")."','".(date("U")+1)."','DONE','".$_GET["user"]."','AUTO','N','".$queue_time."','".$_GET["user_group"]."','AGENT','".$uniqueid."',1)";
                mysqli_query($link,$stmt);
        }else{
                echo "ERROR DB CONNECT<br />\n";
        }
}else{
        echo "ERROR MAILID FORMAT<br />\n";
}
?>
<div style="position:absolute;top:0;left:20%;width:60%;">
<table cellspacing="3" cellpadding="0">
        <tr><td colspan="2" style="text-align:center;"><b>Почтовое сообщение от <? echo $data["email_date"] ?></b></td></tr>
</table>
<table cellspacing="3" cellpadding="0">
        <tr><td width="20%" ><b>Отправитель</b></td><td><? echo $data["email_from"] ?></td></tr>
        <tr><td><b>Получатель</b></td><td><? echo $data["email_to"] ?></td></tr>
</table>
<table cellspacing="3" cellpadding="0">
        <tr><td width="20%" ><b>Тема письма</b></td><td><? echo $data["subject"] ?></td></tr>
        <tr><td><b>Текст письма</b></td><td><? echo preg_replace("/\r\n/","<br>",$data["message"]) ?></td></tr>
</table>
</div>
</body>
</html>
