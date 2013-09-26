<?php
/******************************************
 * install/remove.php
 * 移除目录
 * --------------
 * copyright: Arathi
 ******************************************/
// 调用系统的一些信息
define('IN_PHPBB', true);
$phpbb_root_path = './../';
include($phpbb_root_path . 'extension.inc');
include($phpbb_root_path . 'common.'.$phpEx);
$userdata = session_pagestart($user_ip, PAGE_REMOVEDIR);
init_userprefs($userdata);
// 网页的头部
include($phpbb_root_path . 'includes/page_header.'.$phpEx);

function random_string($length, $max=FALSE)
{
  if (is_int($max) && $max > $length)
  {
    $length = mt_rand($length, $max);
  }
  $output = '';
  
  for ($i=0; $i<$length; $i++)
  {
    $which = mt_rand(0,2);
    
    if ($which === 0)
    {
      $output .= mt_rand(0,9);
    }
    elseif ($which === 1)
    {
      $output .= chr(mt_rand(65,90));
    }
    else
    {
      $output .= chr(mt_rand(97,122));
    }
  }
  return $output;
}

if (isset($_POST['action']))
{
	$pathname = dirname(__FILE__);
	$pathdir = dirname($pathname);
	// echo "当前目录: ".$pathname."<br/>";
	// echo "当前目录的上一级: ".$pathdir."<br/>";
	$action = $_POST['action'];
	if ($action=='remove')
	{
		echo "暂时无法删除！";
	}
	else if ($action=='rename')
	{
		$newdirname=$_POST['newdirname'];
		$success = rename($pathname, "$pathdir/".$newdirname);
		if ($success)
			echo "已将安装目录改名成".$newdirname;
		else 
			echo "更改安装目录名称失败";
	}
	echo "<a href='$phpbb_root_path"."index.php'>返回首页</a>"
}
else
{
	echo "请将install目录改名<br/>";
	echo "<form action='' method='post'>";
	echo "<!--<input type='radio' name='action' value='remove' />删除<br/>-->";
	echo "<input type='radio' name='action' value='rename' checked />改名";
	$random_dir_name=random_string(mt_rand(8,32));
	echo "<input type='text' name='newdirname' value='$random_dir_name' /><br/>";
	echo "<input type='submit' name='submit' value='提交' />";
	echo "</form>";
}

include($phpbb_root_path . 'includes/page_tail.'.$phpEx);

?>
