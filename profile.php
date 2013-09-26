<?php
/***************************************************************************
 *                                profile.php
 *                            -------------------
 *      Разработка: phpBB Group.
 *      Оптимизация под WAP: Гутник Игорь ( чел ).
 *          2010 год
 ***************************************************************************/

define('IN_PHPBB', true);
$phpbb_root_path = './';
include($phpbb_root_path . 'extension.inc');
include($phpbb_root_path . 'common.'.$phpEx);

$userdata = session_pagestart($user_ip, PAGE_PROFILE);
init_userprefs($userdata);

if (!empty($HTTP_POST_VARS['sid']) || !empty($HTTP_GET_VARS['sid']))
{
	$sid = (!empty($HTTP_POST_VARS['sid'])) ? $HTTP_POST_VARS['sid'] : $HTTP_GET_VARS['sid'];
}
else
{
	$sid = '';
}

$page_title = '个人空间';
$script_name = preg_replace('/^\/?(.*?)\/?$/', '\1', trim($board_config['script_path']));
$script_name = ( $script_name != '' ) ? $script_name . '/profile.'.$phpEx : 'profile.'.$phpEx;
$server_name = trim($board_config['server_name']);
$server_protocol = ( $board_config['cookie_secure'] ) ? 'https://' : 'http://';
$server_port = ( $board_config['server_port'] <> 80 ) ? ':' . trim($board_config['server_port']) . '/' : '/';

$server_url = $server_protocol . $server_name . $server_port . $script_name;

function gen_rand_string($hash)
{
	$chars = array( 'a', 'A', 'b', 'B', 'c', 'C', 'd', 'D', 'e', 'E', 'f', 'F', 'g', 'G', 'h', 'H', 'i', 'I', 'j', 'J',  'k', 'K', 'l', 'L', 'm', 'M', 'n', 'N', 'o', 'O', 'p', 'P', 'q', 'Q', 'r', 'R', 's', 'S', 't', 'T',  'u', 'U', 'v', 'V', 'w', 'W', 'x', 'X', 'y', 'Y', 'z', 'Z', '1', '2', '3', '4', '5', '6', '7', '8', '9', '0');
	
	$max_chars = count($chars) - 1;
	srand( (double) microtime()*1000000);
	
	$rand_str = '';
	for($i = 0; $i < 8; $i++)
	{
		$rand_str = ( $i == 0 ) ? $chars[rand(0, $max_chars)] : $rand_str . $chars[rand(0, $max_chars)];
	}

	return ( $hash ) ? md5($rand_str) : $rand_str;
}

if ( isset($HTTP_GET_VARS['mode']) || isset($HTTP_POST_VARS['mode']) )
{
	$mode = ( isset($HTTP_GET_VARS['mode']) ) ? $HTTP_GET_VARS['mode'] : $HTTP_POST_VARS['mode'];
	$mode = htmlspecialchars($mode);

	if ( $mode == 'viewprofile' )
	{
		include($phpbb_root_path . 'includes/usercp_viewprofile.'.$phpEx);
		exit;
	}
	else if ( $mode == 'viewfiles' )
	{
		include($phpbb_root_path . 'includes/usercp_viewfiles.'.$phpEx);
		exit;
	}
	else if ( $mode == 'medals' )
	{
		include($phpbb_root_path . 'includes/usercp_viewmedals.'.$phpEx);
		exit;
	}
	else if ( $mode == 'editprofile' || $mode == 'register' )
	{
		if ( !$userdata['session_logged_in'] && $mode == 'editprofile' )
		{
			redirect(append_sid("login.$phpEx?redirect=profile.$phpEx&mode=editprofile", true));
		}

		include($phpbb_root_path . 'includes/usercp_register.'.$phpEx);
		exit;
	}
	else if ( $mode == 'smsregister' )
	{
		include($phpbb_root_path . 'sms/register.'.$phpEx);
		exit;
	}
	else if ( $mode == 'selectstyle' )
	{
		if ( !$userdata['session_logged_in'] )
		{
			redirect(append_sid("login.$phpEx?redirect=profile.$phpEx&mode=selectstyle", true));
		}

		include($phpbb_root_path . 'includes/usercp_selectstyle.'.$phpEx);
		exit;
	}
	else if ( $mode == 'money' )
	{
		if ( !$userdata['session_logged_in'] )
		{
			redirect(append_sid("login.$phpEx?redirect=profile.$phpEx&mode=money", true));
		}

		include($phpbb_root_path . 'includes/usercp_money.'.$phpEx);
		exit;
	}
	else if ( $mode == 'editconfig' )
	{
		if ( !$userdata['session_logged_in'] )
		{
			redirect(append_sid("login.$phpEx?redirect=profile.$phpEx&mode=editconfig", true));
		}

		include($phpbb_root_path . 'includes/usercp_editconfig.'.$phpEx);
		exit;
	}
	else if ( $mode == 'sendicq' )
	{
		if ( !$userdata['session_logged_in'] )
		{
			redirect(append_sid("login.$phpEx", true));
		}

		include($phpbb_root_path . 'includes/usercp_sendicq.'.$phpEx);
		exit;
	}
	else if ( $mode == 'delete' )
	{
		if ( !$userdata['session_logged_in'] )
		{
			redirect(append_sid("login.$phpEx", true));
		}

		include($phpbb_root_path . 'includes/usercp_delete.'.$phpEx);
		exit;
	}
	else if ( $mode == 'editprofileinfo' )
	{
		if ( !$userdata['session_logged_in'] )
		{
			redirect(append_sid("login.$phpEx?redirect=profile.$phpEx&mode=editprofileinfo", true));
		}

		include($phpbb_root_path . 'includes/usercp_editprofileinfo.'.$phpEx);
		exit;
	}
	else if ( $mode == 'reputation' || $mode == 'given' || $mode == 'warnings' || $mode == 'expired' )
	{
		include($phpbb_root_path . 'includes/usercp_reputation.'.$phpEx);
		exit;
	}

	else if ( $mode == 'confirm' )
	{
		include($phpbb_root_path . 'includes/usercp_confirm.'.$phpEx);
		exit;
	}
	else if ( $mode == 'sendpassword' )
	{
		include($phpbb_root_path . 'includes/usercp_sendpasswd.'.$phpEx);
		exit;
	}
	else if ( $mode == 'activate' )
	{
		include($phpbb_root_path . 'includes/usercp_activate.'.$phpEx);
		exit;
	}
	else if ( $mode == 'email' )
	{
		include($phpbb_root_path . 'includes/usercp_email.'.$phpEx);
		exit;
	}
	else if ( $mode == 'clone' )
	{
		include($phpbb_root_path . 'includes/usercp_clone.'.$phpEx);
		exit;
	}
	elseif ($mode == 'lock')
	{
		include($phpbb_root_path . 'includes/usercp_lock.php');
		exit;
	}
	elseif ($mode == 'guestbook')
	{
		include($phpbb_root_path . 'includes/usercp_guestbook.php');
		exit;
	}
}

redirect(append_sid("index.$phpEx", true));

?>