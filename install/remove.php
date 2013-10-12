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
include($phpbb_root_path . 'includes/page_header.' . $phpEx);

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

function total_delete($arg) 
{
    if (file_exists($arg)) 
    {
        @chmod($arg,0777);
        if (is_dir($arg)) 
        {
            $handle = opendir($arg);
            while($aux = readdir($handle)) 
            {
                if ($aux != "." && $aux != "..")
                    total_delete($arg."/".$aux);
            }
            @closedir($handle);
            rmdir($arg);
        }
        else unlink($arg);
    }
}

$action = "";
if ( isset($_POST['action']) ) $action = $_POST['action'];
else if ( isset($_GET['action']) ) $action = $_GET['action'];
else unset($action);

if (isset($action))
{
	$pathname = dirname(__FILE__);
	$pathdir = dirname($pathname);
	$newdirname=isset($_POST['newdirname']) ? $_POST['newdirname'] : "";
	if ($action=='remove')
	{
		total_delete($pathname);
        if ( file_exists($pathname) == false )
        {
            echo "安装目录删除成功";
        }
        else
        {
            echo "安装目录删除失败，请尝试改名或手动删除！";
        }
	}
	else if ($action=='rename')
	{
        if ($newdirname=="") $newdirname = random_string(mt_rand(8,32));
		$success = rename($pathname, "$pathdir/".$newdirname);
		if ($success)
        {
			echo "已将安装目录改名成".$newdirname;
        }
		else
        {
			echo "更改安装目录名称失败";
        }
	}
	echo "<br/><a href='$phpbb_root_path"."index.php'>返回首页</a>";
}
else
{
	$random_dir_name = random_string(mt_rand(8,32));
	echo "请将install目录<a href='./remove.php?action=rename&newdirname=" . $random_dir_name . "'>改名</a>或<a href='./remove.php?action=remove'>删除</a><br/>";
	echo "<form action='' method='post'>";
	echo "<input type='radio' name='action' value='remove' />删除<br/>";
	echo "<input type='radio' name='action' value='rename' checked />改名为";
	echo "<input type='text' name='newdirname' value='$random_dir_name' /><br/>";
	echo "<input type='submit' name='submit' value='提交' />";
	echo "</form>";
}

include($phpbb_root_path . 'includes/page_tail.'.$phpEx);

?>
