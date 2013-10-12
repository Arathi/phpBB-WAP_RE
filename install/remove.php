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

if (isset($_POST['action']))
{
	$pathname = dirname(__FILE__);
	$pathdir = dirname($pathname);
	$action = isset($_POST['action'])?$_POST['action']:$_GET['action'];
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
		$newdirname=$_POST['newdirname'];
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
	echo "请将install目录<a href=''>改名</a>或<a href=''>删除</a><br/>";
	echo "<form action='' method='post'>";
	echo "<input type='radio' name='action' value='remove' />删除<br/>";
	echo "<input type='radio' name='action' value='rename' checked />改名为";
	$random_dir_name = random_string(mt_rand(8,32));
	echo "<input type='text' name='newdirname' value='$random_dir_name' /><br/>";
	echo "<input type='submit' name='submit' value='提交' />";
	echo "</form>";
}

include($phpbb_root_path . 'includes/page_tail.'.$phpEx);

?>
