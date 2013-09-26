<?php
/******************************
 *        index.php
 *		------------
 *		版权所有：爱疯的云
 *		文件说明：猜拳游戏
 ******************************/
 
// 调用系统的一些信息
define('IN_PHPBB', true);
$phpbb_root_path = './../../';
include($phpbb_root_path . 'extension.inc');
include($phpbb_root_path . 'common.'.$phpEx);

$userdata = session_pagestart($user_ip, PAGE_MORA);
init_userprefs($userdata);


// 验证用户是否登录
if ( !$userdata['session_logged_in'] )
{
	redirect(append_sid("login.$phpEx?redirect=mods/mora/index.$phpEx", true));
	exit;
}

// 检测是否是整数。。。。
if ( isset($HTTP_GET_VARS['mora_id']) )
{
	$mora_id = intval($HTTP_GET_VARS['mora_id']);
}

// 网页标题
$page_title = '猜拳游戏';
// 网页的头部
include($phpbb_root_path . 'includes/page_header.'.$phpEx);

if ( $userdata['user_points'] < 10 )
{
	message_die(GENERAL_MESSAGE, '您的用户积分必须大于10');
}

if ( !empty($mora_id) && $mora_id <= 3 && $mora_id >= 1)
{	
    $sys = rand(1,3);// 计算出随机值，1表示石头，2表示剪刀，3表示布
	$user = $mora_id;// 用户出拳
	
	if ( $sys == 1 )// 系统出石头
	{
		switch ($user)
		{
			case 1:	
				$info = '电脑出石头，你出石头，平局！';
			break;
			case 2:
				$info = '电脑出石头，你出剪刀，你输了！';
				$sql = "UPDATE " . USERS_TABLE . " SET user_points = user_points - 10 WHERE user_id = " . $userdata['user_id'];
			break;
			case 3:
				$info = '电脑出石头，你出布，你赢了！';
				$sql = "UPDATE " . USERS_TABLE . " SET user_points = user_points + 10 WHERE user_id = " . $userdata['user_id'];
			break;
			default:
				$info = '我要耍赖...怎么样？不服打我啊！';
		}
	}
	elseif ( $sys == 2 )//剪刀
	{
		switch ($user)
		{
			case 1:	
				$info = '电脑出剪刀，你出石头，你赢了！';
				$sql = "UPDATE " . USERS_TABLE . " SET user_points = user_points + 10 WHERE user_id = " . $userdata['user_id'];
			break;
			case 2:
				$info = '电脑出剪刀，你出剪刀，平局！';
			break;
			case 3:
				$info = '电脑出剪刀，你出布，你输了！';
				$sql = "UPDATE " . USERS_TABLE . " SET user_points = user_points - 10 WHERE user_id = " . $userdata['user_id'];
			break;
			default:
				$info = '我要耍赖...怎么样？不服打我啊！';
		}
	}
	elseif ( $sys == 3 )// 包布
	{
		switch ($user)
		{
			case 1:	
				$info = '电脑出布，你出石头，你输了';
				$sql = "UPDATE " . USERS_TABLE . " SET user_points = user_points - 10 WHERE user_id = " . $userdata['user_id'];
			break;
			case 2:
				$info = '电脑出布，你出剪刀，你赢了';
				$sql = "UPDATE " . USERS_TABLE . " SET user_points = user_points + 10 WHERE user_id = " . $userdata['user_id'];
			break;
			case 3:
				$info = '电脑出布，你出布，平局！';
			break;
			default:
				$info = '我要耍赖...怎么样？不服打我啊！';
		}
	}
	else // 意外
	{
		message_die(GENERAL_MESSAGE, '我要耍赖...怎么样？不服打我啊！');
	}
}
else // 意外
{
	$info = '游戏规则：<br />您可以选择出石头、剪刀或者布，赢了获得 10 积分，输了则要扣 10 积分！';
}

// SQL 检测
if (!$db->sql_query($sql))
{
	message_die(GENERAL_ERROR, '无法更新用户表', '', __LINE__, __FILE__, $sql);
}

// 调用模版
$template->set_filenames(array(
	'body' => 'mods/mora_body.tpl')
);

// 调用模版语言
$template->assign_vars(array(
	'INFO' => $info,
	'L_MORA' => '猜拳'));

$template->pparse('body');

include($phpbb_root_path . 'includes/page_tail.'.$phpEx);