<?php
/***************************
 *            invite.php
 *          -----------
 *  	邀请功能
 * 		作者 dilu     zisuw.com
 **************************/
define('IN_PHPBB', true);
$phpbb_root_path = './../../';
include($phpbb_root_path . 'extension.inc');
include($phpbb_root_path . 'common.'.$phpEx);
include($phpbb_root_path . 'mods/includes/constants.' . $phpEx);

$userdata = session_pagestart($user_ip, PAGE_PRAVILA);
init_userprefs($userdata);

$page_title = '邀请';

include($phpbb_root_path . 'includes/page_header.'.$phpEx);

$user_id = get_var('user_id',0);
$url_invite = '';

$script_name = preg_replace('/^\/?(.*?)\/?$/', '\1', trim($board_config['script_path']));
$script_name = ($script_name != '') ? $script_name . '/mods/invite/index.'.$phpEx : 'mods/invite/index.'.$phpEx; //脚本名
$server_name = trim($board_config['server_name']); //服务器名
$server_protocol = ($board_config['cookie_secure']) ? 'https://' : 'http://'; //协议
$server_port = ($board_config['server_port'] != 80) ? ':' . trim($board_config['server_port']) . '/' : '/'; //端口

$url_invite = $server_protocol . $server_name . $server_port . $script_name;

if ($user_id == 0)
{
	if ( !$userdata['session_logged_in'] )
	{
		redirect("login.$phpEx?redirect=mods/invite/invite.$phpEx", true);
	}
	else
	{ 
		$sql = "SELECT invite_num 
			FROM " . USERS_TABLE . " 
			WHERE user_id = " . $userdata['user_id'];
		if ( !$result=$db->sql_query($sql) )
		{
			message_die(GENERAL_ERROR, '查询邀请次数出差');
		}

		if ( $row=$db->sql_fetchrow($result) )
		{
			$invite_num=$row['invite_num'];
		}
	}
}
else
{
	if ( $userdata['session_logged_in'] )
	{
		message_die(GENERAL_ERROR, '您是注册会员，不能接受邀请或邀请别人<br/>');
	}
	$sql= "SELECT * 
		FROM " . INVITE_TABLE . " 
		WHERE user_id = ".$user_id." and ip = '".$user_ip."'";
	if( !$result=$db->sql_query($sql) )
	{
		message_die(GENERAL_ERROR, '查询ip失败');
	}
	if(mysql_num_rows($result)==0)
	{
		$sql = "INSERT INTO " . INVITE_TABLE . " (id, user_id, ip) 
			VALUES ('', $user_id, '$user_ip')";
		

		$db->sql_query($sql);
		
		$sql= "UPDATE " . USERS_TABLE . " 
			SET invite_num = invite_num + 1 
			WHERE user_id = " . $user_id;

		$db->sql_query($sql);
		
		$sql= "UPDATE " . USERS_TABLE . " 
			SET user_points=user_points + 10 
			WHERE user_id= " . $user_id; 
		if( !$result=$db->sql_query($sql) )
		{
			message_die(GENERAL_ERROR, '更改用户金钱出错');
		}
	}
	redirect(append_sid("index.$phpEx?redirect=mods/invite/index.$phpEx", true));
}

$template->set_filenames(array(
	'body' => 'mods/invite.tpl')
);
$template->assign_vars(array(
    'U_ROOT'        => $url_invite,
	'USERNAME' 		=> $userdata['username'],
	'USER_ID' 		=> $userdata['user_id'],
	'INVITE_NUM' 	=> $invite_num,
    'INVITE_MONEY' 	=> $invite_num*10  )
);
 $template->pparse('body'); 
include($phpbb_root_path . 'includes/page_tail.'.$phpEx);

?>