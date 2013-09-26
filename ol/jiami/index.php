<?php
$t = $q = $_POST["q"];
$type = $_POST["type"];
$arr = array("Base64加密", "Base64解密", "URL编码(raw)", "URL解码(raw)", "URL编码", "URL解码", "MD5加密(32位)", "MD5加密(16位)", "Crypt加密");
switch($type) {
case 0:
$t = base64_encode($q);
break;
case 1:
$t = base64_decode($q);
break;
case 2:
$t = rawurlencode($q);
break;
case 3:
$t = rawurldecode($q);
break;
case 4:
$t = urlencode($q);
break;
case 5:
$t = urldecode($q);
break;
case 6:
$t = md5($q);
break;
case 7:
$t = substr(md5($q), 8, 16);
break;
case 8:
$t = crypt($q);
break;
default:
$t = $q;
break;
}
?>
<html>
<head>
<title>资速论坛|在线文字加解密</title>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<style type="text/css">
<!--
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
-->
</style>
</head>
<body>
<p>
<div class="catSides"><a href='/'><font color=#ffffff>首页</font></a>|<a href='../'><font color=#ffffff>程序</font></a>|在线文字加解密</div>
<?php echo'<form name="form" action="' . $_SERVER["PHP_SELF"] . '" method="post">'; ?>
<tr bgcolor="#666666"><td>
<?php echo $type <= 3 && $type >= 0 && $type != "" ? $arr[$type] : ""; ?>
</td></tr>
<tr><td>
<textarea name="q" rows="3" cols="20"><?php echo $q; ?></textarea>
<br/>
<select name="type">
<?php
for ($i = 0; $i < count($arr); $i++)
echo '<option value="' . $i . '">' . $arr[$i] . '</option>';
?>
</select>
<input type="submit" value="提交"/>
<?php
if (isset($q))
echo '<tr><td><textarea rows="3" cols="20">' . $t . '</textarea></td></tr>';
?>
<div class="nav"><font color=#ff8a00><a href="../">返回工具</a>.<a href="/">返回首页</a></div></form>
</p><noscript>
</body>
</html></noscript>