<?php
include "phpqrcode.php";
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>二维码在线生成-中文二维码</title>
<meta name="keywords" content="中文二维码,二维码生成,二维码制作,手机二维码" />
<meta name="description" content="在线中文/英文二维码生成工具。" />
<style type="text/css">
body {
	background-color:#539BE3;	
	background-image: url(../images/background.jpg);
	background-repeat:no-repeat;
	background-position:center;
	background-attachment:fixed;
	color: #494949; 
	font-family: Lucida Grande, Verdana, Arial, Helvetica, sans-serif;
	font-size: 14px;
	margin: auto;
	max-width: 320px;
}
/* 导航条
--------- */
.nav {
padding:5px;
        background: #cadceb; color:#105289;
}
/* 导航条2
--------- */
.nav2 {
padding:5px;
        background: #2e7ab9; color:#105289;
}
/* 内容标题栏
------------- */
.catSides {
background: #539be3 url(../code/bg_list.gif) repeat-x top left;
margin: 0;
padding: 5px 9px 5px 9px;
color: #ffffff;
font-size: 15px;
}

  
    .l1{width:20px;height:100px;background:#F5F5F5;float:right;
    position:fixed !important; top:100px;
    position:absolute; z-index:300; top:expression(offsetParent.scrollTop+200);left:300px;}
</style>
</head>

<!--
原程序电脑版修改成手机版
zisuw.com资速论坛
-->

<body>
<div class="nav"><a href="/">首页</a>><a href="../">程序</a>>二维码制作</div>
 <div class="catSides">二维码生成|<a href=../code/help.html>帮助</a></div>

<div id="left"><form id="iform" name="iform" method="post" action=""><textarea name="content" id="content"><?php echo $_POST['content']; ?></textarea><br />
<div id="now">

<input name="go" type="submit" id="go" onclick="" value="马上生成" />
<input name="done" type="hidden" value="done" />
<br/>
<font color=#003867>输入http://网址可获取连接
<br/>
输入文字即可获取文字保存


<div id="right">
<div class="code">
处理结果:
<br/>
<?php 
if ($_POST['done']){
   if($_POST['content']){
	$c = $_POST['content'];

	$len = strlen($c);
	   if ($len <= 360){
	    $file = fopen("t.txt","r+");
	    flock($file,LOCK_EX);
	      if($file) {
	       $get_file = fgetss($file);
	       $t = $get_file+1;
	       $file2 = fopen("t.txt","w+");
	       fwrite($file2,$t);	
	       }
	    flock($file,LOCK_UN);
	    fclose($file);
	    fclose($file2);
	
	   QRcode::png($c, 'png/'.$t.'.png');	
	   $sc = urlencode($c);
	   echo '<img src="png/'.$t.'.png" /><br />'.$c; 
	   }
	   else {
	     echo '信息量过大。';
	   }	
    }
    else {
     echo '你没有输入内容。';
    }
}	
else {
  echo '二维码将会出现在这里。';
}
	?>
	 </font>
	</div>
</div>
</div>

</body>

</html>