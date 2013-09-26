<?php
/*************************************************
 *		usercp_register.php
 *		-------------------
 *      Разработка: phpBB Group.
 *      Оптимизация под WAP: Гутник Игорь ( чел ).
 *          2011 год
 *		简体中文：爱疯的云
 *		说明：注册
 *************************************************/

if ( !defined('IN_PHPBB') )
{
	die("Hacking attempt");
	exit;
}

$unhtml_specialchars_match = array('#&gt;#', '#&lt;#', '#&quot;#', '#&amp;#');
$unhtml_specialchars_replace = array('>', '<', '"', '&');

/**
 *用户必须同意协议才能进入注册页面
 **/
function show_coppa()
{
	global $userdata, $template, $lang, $phpbb_root_path, $phpEx;

	$template->set_filenames(array(
		'body' => 'agreement.tpl')
	);

	$template->assign_vars(array(
		'REGISTRATION' 		=> $lang['Registration'],
		'AGREEMENT' 		=> $lang['Reg_agreement'],
		"AGREE_OVER_13" 	=> $lang['Agree_over_13'],
		"AGREE_UNDER_13" 	=> $lang['Agree_under_13'],
		'DO_NOT_AGREE' 		=> $lang['Agree_not'],

		"U_AGREE_OVER13" 	=> append_sid("profile.$phpEx?mode=register&amp;agreed=true"),
		"U_AGREE_UNDER13" 	=> append_sid("profile.$phpEx?mode=register&amp;agreed=true&amp;coppa=true"))
	);

	$template->pparse('body');

}

function get_crypted_name($name)
{
	global $board_config;

	$code_start = $board_config['board_startdate'] % 2 ? 65: 97;
	$first = chr($code_start + $board_config['board_startdate'] % 26);
	$var = $first . md5($name . $board_config['board_startdate']);

	return $var;
}

$error = FALSE;
$error_msg = '';
$page_title = ( $mode == 'editprofile' ) ? $lang['Edit_profile'] : $lang['Register'];

if ( $mode == 'register' && !isset($HTTP_POST_VARS['agreed']) && !isset($HTTP_GET_VARS['agreed']) )
{
	include($phpbb_root_path . 'includes/page_header.'.$phpEx);

	show_coppa();

	include($phpbb_root_path . 'includes/page_tail.'.$phpEx);
}

$coppa = ( empty($HTTP_POST_VARS['coppa']) && empty($HTTP_GET_VARS['coppa']) ) ? 0 : TRUE;
$strip_var_list = array('email' => 'email', 'confirm_code' => 'confirm_code'); 
$trim_var_list = array('cur_password' => 'cur_password', 'new_password' => 'new_password', 'password_confirm' => 'password_confirm'); 

if (
	isset($HTTP_POST_VARS['submit']) ||
	$mode == 'register' )
{
	include($phpbb_root_path . 'includes/functions_validate.'.$phpEx);
	include($phpbb_root_path . 'includes/bbcode.'.$phpEx);
	include($phpbb_root_path . 'includes/functions_post.'.$phpEx);

	if ( $mode == 'editprofile' )
	{
		$user_id = intval($HTTP_POST_VARS['user_id']);
		$current_email = trim(htmlspecialchars($HTTP_POST_VARS['current_email']));
	}

	$strip_var_list = array('email' => 'email', 'confirm_code' => 'confirm_code');

	while( list($var, $param) = @each($strip_var_list) )
	{
		$param = ( $mode == 'register' ) ? get_crypted_name($param) : $param;
		if ( !empty($HTTP_POST_VARS[$param]) )
		{
			$$var = trim(htmlspecialchars($HTTP_POST_VARS[$param]));
		}
	}

	$param = ( $mode == 'register' ) ? get_crypted_name('username') : 'username';
	$username = ( !empty($HTTP_POST_VARS[$param]) ) ? phpbb_clean_username($HTTP_POST_VARS[$param]) : '';
	$gender = ( isset($HTTP_POST_VARS['gender']) ) ? intval ($HTTP_POST_VARS['gender']) : 0;

	$trim_var_list = array('cur_password' => 'cur_password', 'new_password' => 'new_password', 'password_confirm' => 'password_confirm');

	while( list($var, $param) = @each($trim_var_list) )
	{
		$param = ( $mode == 'register' ) ? get_crypted_name($param) : $param;
		if ( !empty($HTTP_POST_VARS[$param]) )
		{
			$$var = trim($HTTP_POST_VARS[$param]);
		}
	}
	$sid = (isset($HTTP_POST_VARS['sid'])) ? $HTTP_POST_VARS['sid'] : 0;
}

if ($mode == 'register' && ($userdata['session_logged_in'] || $username == $userdata['username']))
{
	message_die(GENERAL_MESSAGE, $lang['Username_taken'], '', __LINE__, __FILE__);
}

if ( isset($HTTP_POST_VARS['submit']) )
{
	if ($sid == '' || $sid != $userdata['session_id'])
	{
		$error = true;
		$error_msg .= ( ( isset($error_msg) ) ? '<br />' : '' ) . $lang['Session_invalid'];
	}

	$passwd_sql = '';
	if ( $mode == 'editprofile' )
	{
		if ( $user_id != $userdata['user_id'] )
		{
			$error = TRUE;
			$error_msg .= ( ( isset($error_msg) ) ? '<br />' : '' ) . $lang['Wrong_Profile'];
		}
	}
	else if ( $mode == 'register' )
	{
		if ( empty($username) || empty($new_password) || empty($password_confirm) || empty($email) || empty($gender) )
		{
			$error = TRUE;
			$error_msg .= ( ( isset($error_msg) ) ? '<br />' : '' ) . $lang['Fields_empty'];
		}
	}

	if ($board_config['enable_confirm'] && $mode == 'register')
	{
		if (empty($HTTP_POST_VARS['confirm_id']))
		{
			$error = TRUE;
			$error_msg .= ( ( isset($error_msg) ) ? '' : '' ) . $lang['Confirm_code_wrong'];
		}
		else
		{
			$confirm_id = htmlspecialchars($HTTP_POST_VARS['confirm_id']);
			if (!preg_match('/^[A-Za-z0-9]+$/', $confirm_id))
			{
				$confirm_id = '';
			}
			
			$sql = 'SELECT code 
				FROM ' . CONFIRM_TABLE . " 
				WHERE confirm_id = '$confirm_id' 
					AND session_id = '" . $userdata['session_id'] . "'";
			if (!($result = $db->sql_query($sql)))
			{
				message_die(GENERAL_ERROR, 'Could not obtain confirmation code', '', __LINE__, __FILE__, $sql);
			}

			if ($row = $db->sql_fetchrow($result))
			{
				if ($row['code'] != $confirm_code)
				{
					$error = TRUE;
					$error_msg .= ( ( isset($error_msg) ) ? '' : '' ) . $lang['Confirm_code_wrong'];
				}
				else
				{
					$sql = 'DELETE FROM ' . CONFIRM_TABLE . " 
						WHERE confirm_id = '$confirm_id' 
							AND session_id = '" . $userdata['session_id'] . "'";
					if (!$db->sql_query($sql))
					{
						message_die(GENERAL_ERROR, 'Could not delete confirmation code', '', __LINE__, __FILE__, $sql);
					}
				}
			}
			else
			{		
				$error = TRUE;
				$error_msg .= ( ( isset($error_msg) ) ? '' : '' ) . $lang['Confirm_code_wrong'];
			}
			$db->sql_freeresult($result);
		}
	}

	$passwd_sql = '';
	if ( !empty($new_password) && !empty($password_confirm) )
	{
		if ( $new_password != $password_confirm )
		{
			$error = TRUE;
			$error_msg .= ( ( isset($error_msg) ) ? '<br />' : '' ) . $lang['Password_mismatch'];
		}
		else if ( strlen($new_password) > 32 )
		{
			$error = TRUE;
			$error_msg .= ( ( isset($error_msg) ) ? '<br />' : '' ) . $lang['Password_long'];
		}
		else
		{
			if ( $mode == 'editprofile' )
			{
				$sql = "SELECT user_password
					FROM " . USERS_TABLE . "
					WHERE user_id = $user_id";
				if ( !($result = $db->sql_query($sql)) )
				{
					message_die(GENERAL_ERROR, 'Could not obtain user_password information', '', __LINE__, __FILE__, $sql);
				}

				$row = $db->sql_fetchrow($result);

				if ( $row['user_password'] != md5($cur_password) )
				{
					$error = TRUE;
					$error_msg .= ( ( isset($error_msg) ) ? '<br />' : '' ) . $lang['Current_password_mismatch'];
				}
			}

			if ( !$error )
			{
				$new_password = md5($new_password);
				$passwd_sql = "user_password = '$new_password', ";
			}
		}
	}
	else if ( ( empty($new_password) && !empty($password_confirm) ) || ( !empty($new_password) && empty($password_confirm) ) )
	{
		$error = TRUE;
		$error_msg .= ( ( isset($error_msg) ) ? '<br />' : '' ) . $lang['Password_mismatch'];
	}

	if ( $email != $userdata['user_email'] || $mode == 'register' )
	{
		$result = validate_email($email);
		if ( $result['error'] )
		{
			$email = $userdata['user_email'];

			$error = TRUE;
			$error_msg .= ( ( isset($error_msg) ) ? '<br />' : '' ) . $result['error_msg'];
		}

		if ( $mode == 'editprofile' )
		{
			$sql = "SELECT user_password
				FROM " . USERS_TABLE . "
				WHERE user_id = $user_id";
			if ( !($result = $db->sql_query($sql)) )
			{
				message_die(GENERAL_ERROR, 'Could not obtain user_password information', '', __LINE__, __FILE__, $sql);
			}

			$row = $db->sql_fetchrow($result);

			if ( $row['user_password'] != md5($cur_password) )
			{
				$email = $userdata['user_email'];

				$error = TRUE;
				$error_msg .= ( ( isset($error_msg) ) ? '<br />' : '' ) . $lang['Current_password_mismatch'];
			}
		}
	}

	$username_sql = '';
	if ( $board_config['allow_namechange'] || $mode == 'register' )
	{
		if ( empty($username) )
		{
			$error = TRUE;
		}
		else if ( $username != $userdata['username'] || $mode == 'register')
		{
			if (strtolower($username) != strtolower($userdata['username']) || $mode == 'register')
			{
				$result = validate_username($username);
				if ( $result['error'] )
				{
					$error = TRUE;
					$error_msg .= ( ( isset($error_msg) ) ? '<br />' : '' ) . $result['error_msg'];
				}
			}

			if (!$error)
			{
				$username_sql = "username = '" . str_replace("\'", "''", $username) . "', ";
			}
		}
	}

	if ( !$error )
	{

		if ( $mode == 'editprofile' )
		{
			if ( $email != $userdata['user_email'] && $board_config['require_activation'] != USER_ACTIVATION_NONE && $userdata['user_level'] != ADMIN )
			{
				$user_active = 0;

				$user_actkey = gen_rand_string(true);
				$key_len = 54 - ( strlen($server_url) );
				$key_len = ( $key_len > 6 ) ? $key_len : 6;
				$user_actkey = substr($user_actkey, 0, $key_len);

				if ( $userdata['session_logged_in'] )
				{
					session_end($userdata['session_id'], $userdata['user_id']);
				}
			}
			else
			{
				$user_active = 1;
				$user_actkey = '';
			}

			$sql = "UPDATE " . USERS_TABLE . "
				SET " . $username_sql . $passwd_sql . "user_email = '" . str_replace("\'", "''", $email) ."', user_active = $user_active, user_actkey = '" . str_replace("\'", "''", $user_actkey) . "'
				WHERE user_id = $user_id";
			if ( !($result = $db->sql_query($sql)) )
			{
				message_die(GENERAL_ERROR, 'Could not update users table', '', __LINE__, __FILE__, $sql);
			}

			if ( !empty($passwd_sql) )
			{
				session_reset_keys($user_id, $user_ip);
			}

			if ( !$user_active )
			{

				include($phpbb_root_path . 'includes/emailer.'.$phpEx);
				$emailer = new emailer($board_config['smtp_delivery']);

 				if ( $board_config['require_activation'] != USER_ACTIVATION_ADMIN )
 				{
 					$emailer->from($board_config['board_email']);
 					$emailer->replyto($board_config['board_email']);
 
 					$emailer->use_template('user_activate', stripslashes($user_lang));
 					$emailer->email_address($email);
 					$emailer->set_subject($lang['Reactivate']);
  
 					$emailer->assign_vars(array(
 						'SITENAME' 		=> $board_config['sitename'],
 						'USERNAME' 		=> preg_replace($unhtml_specialchars_match, $unhtml_specialchars_replace, substr(str_replace("\'", "'", $username), 0, 25)),
 						'EMAIL_SIG' 	=> (!empty($board_config['board_email_sig'])) ? str_replace('<br />', "\n", "-- \n" . $board_config['board_email_sig']) : '',
  
 						'U_ACTIVATE' 	=> $server_url . '?mode=activate&' . POST_USERS_URL . '=' . $user_id . '&act_key=' . $user_actkey)
 					);
 					$emailer->send();
 					$emailer->reset();
 				}
 				else if ( $board_config['require_activation'] == USER_ACTIVATION_ADMIN )
 				{
 					$sql = 'SELECT user_email, user_lang 
 						FROM ' . USERS_TABLE . '
 						WHERE user_level = ' . ADMIN;
 					
 					if ( !($result = $db->sql_query($sql)) )
 					{
 						message_die(GENERAL_ERROR, 'Could not select Administrators', '', __LINE__, __FILE__, $sql);
 					}
 					
 					while ($row = $db->sql_fetchrow($result))
 					{
 						$emailer->from($board_config['board_email']);
 						$emailer->replyto($board_config['board_email']);
 						
 						$emailer->email_address(trim($row['user_email']));
 						$emailer->use_template("admin_activate", $row['user_lang']);
 						$emailer->set_subject($lang['Reactivate']);
 
 						$emailer->assign_vars(array(
 							'USERNAME' 		=> preg_replace($unhtml_specialchars_match, $unhtml_specialchars_replace, substr(str_replace("\'", "'", $username), 0, 25)),
 							'EMAIL_SIG' 	=> str_replace('<br />', "\n", "-- \n" . $board_config['board_email_sig']),
 
 							'U_ACTIVATE' 	=> $server_url . '?mode=activate&' . POST_USERS_URL . '=' . $user_id . '&act_key=' . $user_actkey)
 						);
 						$emailer->send();
 						$emailer->reset();
 					}
 					$db->sql_freeresult($result);
 				}

				$message = $lang['Profile_updated_inactive'] . '<br /><br />' . sprintf($lang['Click_return_index'],  '<a href="' . append_sid("index.$phpEx") . '">', '</a>');
			}
			else
			{
				$message = $lang['Profile_updated'] . '<br /><br />' . sprintf($lang['Click_return_index'],  '<a href="' . append_sid("index.$phpEx") . '">', '</a>');
			}

			$template->assign_vars(array(
				"META" => '<meta http-equiv="refresh" content="0;url=' . append_sid("index.$phpEx") . '">')
			);

			message_die(GENERAL_MESSAGE, $message);
		}
		else
		{
			$sql = "SELECT MAX(user_id) AS total
				FROM " . USERS_TABLE;
			if ( !($result = $db->sql_query($sql)) )
			{
				message_die(GENERAL_ERROR, 'Could not obtain next user_id information', '', __LINE__, __FILE__, $sql);
			}

			if ( !($row = $db->sql_fetchrow($result)) )
			{
				message_die(GENERAL_ERROR, 'Could not obtain next user_id information', '', __LINE__, __FILE__, $sql);
			}
			$user_id = $row['total'] + 1;

			$board_timezone = $board_config['board_timezone'];
			$default_dateformat = $board_config['default_dateformat'];
			$default_lang = 'chinese';
			$topics_per_page = $board_config['topics_per_page'];
			$posts_per_page = $board_config['posts_per_page'];

			// 注册时执行的数据
			$sql = "INSERT INTO " . USERS_TABLE . "	(user_id, username, user_regdate, user_password, user_email, user_viewemail, user_attachsig, user_allowsmile, user_allowhtml, user_allowbbcode, user_allow_viewonline, user_notify_to_email, user_notify_to_pm, user_notify_pm, user_popup_pm, user_timezone, user_dateformat, user_lang, user_level, user_allow_pm, user_message_quote, user_topics_per_page, user_posts_per_page, user_posl_red, user_index_spisok, user_style, user_gender, user_active, user_actkey) 
				VALUES ($user_id, '" . str_replace("\'", "''", $username) . "', " . time() . ", '" . str_replace("\'", "''", $new_password) . "', '" . str_replace("\'", "''", $email) . "', 1, 0, 1, 0, 1, 1, 0, 0, 0, 1, $board_timezone, '" . str_replace("\'", "''", $default_dateformat) . "', '" . str_replace("\'", "''", $default_lang) . "', 0, 1, '" . $board_config['message_quote'] . "', '$topics_per_page', '$posts_per_page', '" . $board_config['posl_red'] . "', '" . $board_config['index_spisok'] . "', " . $board_config['default_style'] . ", '1', ";
			if ( $board_config['require_activation'] == USER_ACTIVATION_SELF || $board_config['require_activation'] == USER_ACTIVATION_ADMIN || $coppa )
			{
				$user_actkey = gen_rand_string(true);
				$key_len = 54 - (strlen($server_url));
				$key_len = ( $key_len > 6 ) ? $key_len : 6;
				$user_actkey = substr($user_actkey, 0, $key_len);
				$sql .= "0, '" . str_replace("\'", "''", $user_actkey) . "')";
			}
			else
			{
				$sql .= "1, '')";
			}

			if ( !($result = $db->sql_query($sql, BEGIN_TRANSACTION)) )
			{
				message_die(GENERAL_ERROR, 'Could not insert data into users table', '', __LINE__, __FILE__, $sql);
			}

			// START - SEND PM ON REGISTER MOD
			//
			// According to 'netclectic' we need to set the datastamp to '9999999999' in order to
			// insure the pop-up notification about a new message existing. I concur with 'netclectic'
			// and have thus made the change to his suggestion. Thanks netclectic!
			//
			mysql_query("set sql_mode=''");
			$sql = "UPDATE " . USERS_TABLE . " 
				SET user_new_privmsg = '1', user_last_privmsg = '9999999999'
				WHERE user_id = $user_id";
			if ( !($result = $db->sql_query($sql)) )
			{
				message_die(GENERAL_ERROR, 'Could not update users table', '', __LINE__, __FILE__, $sql);
			}

			$register_pm_subject = $lang['register_pm_subject'];
			$register_pm = $lang['register_pm'];
			$privmsgs_date = date("U");
			$sql = "INSERT INTO " . PRIVMSGS_TABLE . " (privmsgs_type, privmsgs_subject, privmsgs_from_userid, privmsgs_to_userid, privmsgs_date, privmsgs_enable_html, privmsgs_enable_bbcode, privmsgs_enable_smilies, privmsgs_attach_sig) VALUES ('0', '" . str_replace("\'", "''", addslashes(sprintf($register_pm_subject,$board_config['sitename']))) . "', '2', " . $user_id . ", " . $privmsgs_date . ", '0', '1', '1', '0')";
			if ( !$db->sql_query($sql) )
			{
				message_die(GENERAL_ERROR, 'Could not insert private message sent info', '', __LINE__, __FILE__, $sql);
			}

			$privmsg_sent_id = $db->sql_nextid();
			$privmsgs_text = $lang['register_pm_subject'];
			//
			$sql = "INSERT INTO " . PRIVMSGS_TEXT_TABLE . " (privmsgs_text_id, privmsgs_text) VALUES ($privmsg_sent_id, '" . str_replace("\'", "''", addslashes(sprintf($register_pm,$board_config['sitename'],$board_config['sitename']))) . "')";
			if ( !$db->sql_query($sql) )
			{
				message_die(GENERAL_ERROR, 'Could not insert private message sent text', '', __LINE__, __FILE__, $sql);
			}
			// END - SEND PM ON REGISTER MOD 
			
			$sql = "INSERT INTO " . GROUPS_TABLE . " (group_name, group_description, group_single_user, group_moderator)
				VALUES ('', 'Personal User', 1, 0)";
			if ( !($result = $db->sql_query($sql)) )
			{
				message_die(GENERAL_ERROR, 'Could not insert data into groups table', '', __LINE__, __FILE__, $sql);
			}

			$group_id = $db->sql_nextid();

			$sql = "INSERT INTO " . USER_GROUP_TABLE . " (user_id, group_id, user_pending)
				VALUES ($user_id, $group_id, 0)";
			if( !($result = $db->sql_query($sql, END_TRANSACTION)) )
			{
				message_die(GENERAL_ERROR, 'Could not insert data into user_group table', '', __LINE__, __FILE__, $sql);
			}

			if ( $coppa )
			{
				$message = $lang['COPPA'];
				$email_template = 'coppa_welcome_inactive';
			}
			else if ( $board_config['require_activation'] == USER_ACTIVATION_SELF )
			{
				$message = $lang['Account_inactive'];
				$email_template = 'user_welcome_inactive';
			}
			else if ( $board_config['require_activation'] == USER_ACTIVATION_ADMIN )
			{
				$message = $lang['Account_inactive_admin'];
				$email_template = 'admin_welcome_inactive';
			}
			else
			{
				$message = $lang['Account_added'];
				$email_template = 'user_welcome';
			}

			include($phpbb_root_path . 'includes/emailer.'.$phpEx);
			$emailer = new emailer($board_config['smtp_delivery']);

			$emailer->from($board_config['board_email']);
			$emailer->replyto($board_config['board_email']);

			$emailer->use_template($email_template, stripslashes($user_lang));
			$emailer->email_address($email);
			$emailer->set_subject(sprintf($lang['Welcome_subject'], $board_config['sitename']));

			if( $coppa )
			{
				$emailer->assign_vars(array(
					'SITENAME' 		=> $board_config['sitename'],
					'WELCOME_MSG' 	=> sprintf($lang['Welcome_subject'], $board_config['sitename']),
					'USERNAME' 		=> preg_replace($unhtml_specialchars_match, $unhtml_specialchars_replace, substr(str_replace("\'", "'", $username), 0, 25)),
					'PASSWORD' 		=> $password_confirm,
					'EMAIL_SIG' 	=> str_replace('<br />', "\n", "-- \n" . $board_config['board_email_sig']),

					'FAX_INFO' 		=> $board_config['coppa_fax'],
					'MAIL_INFO' 	=> $board_config['coppa_mail'],
					'EMAIL_ADDRESS' => $email,
					'ICQ' 			=> $icq,
					'AIM'			=> $aim,
					'YIM' 			=> $yim,
					'MSN' 			=> $msn,
					'WEB_SITE' 		=> $website,
					'FROM' 			=> $location,
					'OCC' 			=> $occupation,
					'INTERESTS' 	=> $interests,
					'SITENAME' 		=> $board_config['sitename']));
			}
			else
			{
				$emailer->assign_vars(array(
					'SITENAME' 		=> $board_config['sitename'],
					'WELCOME_MSG' 	=> sprintf($lang['Welcome_subject'], $board_config['sitename']),
					'USERNAME' 		=> preg_replace($unhtml_specialchars_match, $unhtml_specialchars_replace, substr(str_replace("\'", "'", $username), 0, 25)),
					'PASSWORD' 		=> $password_confirm,
					'EMAIL_SIG'		=> str_replace('<br />', "\n", "-- \n" . $board_config['board_email_sig']),

					'U_ACTIVATE' 	=> $server_url . '?mode=activate&' . POST_USERS_URL . '=' . $user_id . '&act_key=' . $user_actkey)
				);
			}

			$emailer->send();
			$emailer->reset();

			if ( $board_config['require_activation'] == USER_ACTIVATION_ADMIN )
			{
				$sql = "SELECT user_email, user_lang 
					FROM " . USERS_TABLE . "
					WHERE user_level = " . ADMIN;
				
				if ( !($result = $db->sql_query($sql)) )
				{
					message_die(GENERAL_ERROR, 'Could not select Administrators', '', __LINE__, __FILE__, $sql);
				}
				
				while ($row = $db->sql_fetchrow($result))
				{
					$emailer->from($board_config['board_email']);
					$emailer->replyto($board_config['board_email']);
					
					$emailer->email_address(trim($row['user_email']));
					$emailer->use_template("admin_activate", $row['user_lang']);
					$emailer->set_subject($lang['New_account_subject']);

					$emailer->assign_vars(array(
						'USERNAME' => preg_replace($unhtml_specialchars_match, $unhtml_specialchars_replace, substr(str_replace("\'", "'", $username), 0, 25)),
						'EMAIL_SIG' => str_replace('<br />', "\n", "-- \n" . $board_config['board_email_sig']),

						'U_ACTIVATE' => $server_url . '?mode=activate&' . POST_USERS_URL . '=' . $user_id . '&act_key=' . $user_actkey)
					);
					$emailer->send();
					$emailer->reset();
				}
				$db->sql_freeresult($result);
			}

			$message = $message . '<br /><br />' . sprintf($lang['Click_return_index'],  '<a href="' . append_sid("index.$phpEx") . '">', '</a>');

			message_die(GENERAL_MESSAGE, $message);
		} 
	}
}

if ( $error )
{
	$username = stripslashes($username);
	$email = stripslashes($email);
	$cur_password = '';
	$new_password = '';
	$password_confirm = '';
}
else if ( $mode == 'editprofile' )
{
	$user_id = $userdata['user_id'];
	$username = $userdata['username'];
	$email = $userdata['user_email'];
	$cur_password = '';
	$new_password = '';
	$password_confirm = '';
}

include($phpbb_root_path . 'includes/page_header.'.$phpEx);

if ( $mode == 'editprofile' )
{
	if ( $user_id != $userdata['user_id'] )
	{
		$error = TRUE;
		$error_msg = $lang['Wrong_Profile'];
	}
}
	include($phpbb_root_path . 'includes/functions_selects.'.$phpEx);

	if ( !isset($coppa) )
	{
		$coppa = FALSE;
	}

	$s_hidden_fields = '<input type="hidden" name="mode" value="' . $mode . '" /><input type="hidden" name="agreed" value="true" /><input type="hidden" name="coppa" value="' . $coppa . '" />';
	$s_hidden_fields .= '<input type="hidden" name="sid" value="' . $userdata['session_id'] . '" />';

	if( $mode == 'editprofile' )
	{
		$s_hidden_fields .= '<input type="hidden" name="user_id" value="' . $userdata['user_id'] . '" />';
		$s_hidden_fields .= '<input type="hidden" name="current_email" value="' . $userdata['user_email'] . '" />';
	}

	if ( $error )
	{
		$template->set_filenames(array(
			'reg_header' => 'error_body.tpl')
		);
		$template->assign_vars(array(
			'ERROR_MESSAGE' => $error_msg)
		);
		$template->assign_var_from_handle('ERROR_BOX', 'reg_header');
	}

	$template->set_filenames(array(
		'body' => 'profile_add_body.tpl')
	);

	if ( $mode == 'editprofile' )
	{
		$template->assign_block_vars('switch_edit_profile', array());
	}

	if ( ($mode == 'register') || ($board_config['allow_namechange']) )
	{
		$template->assign_block_vars('switch_namechange_allowed', array());
	}
	else
	{
		$template->assign_block_vars('switch_namechange_disallowed', array());
	}

	$confirm_image = '';
	if (!empty($board_config['enable_confirm']) && $mode == 'register')
	{
		$sql = 'SELECT session_id 
			FROM ' . SESSIONS_TABLE; 
		if (!($result = $db->sql_query($sql)))
		{
			message_die(GENERAL_ERROR, 'Could not select session data', '', __LINE__, __FILE__, $sql);
		}

		if ($row = $db->sql_fetchrow($result))
		{
			$confirm_sql = '';
			do
			{
				$confirm_sql .= (($confirm_sql != '') ? ', ' : '') . "'" . $row['session_id'] . "'";
			}
			while ($row = $db->sql_fetchrow($result));
		
			$sql = 'DELETE FROM ' .  CONFIRM_TABLE . " 
				WHERE session_id NOT IN ($confirm_sql)";
			if (!$db->sql_query($sql))
			{
				message_die(GENERAL_ERROR, 'Could not delete stale confirm data', '', __LINE__, __FILE__, $sql);
			}
		}
		$db->sql_freeresult($result);

		$sql = 'SELECT COUNT(session_id) AS attempts 
			FROM ' . CONFIRM_TABLE . " 
			WHERE session_id = '" . $userdata['session_id'] . "'";
		if (!($result = $db->sql_query($sql)))
		{
			message_die(GENERAL_ERROR, 'Could not obtain confirm code count', '', __LINE__, __FILE__, $sql);
		}

		if ($row = $db->sql_fetchrow($result))
		{
			if ($row['attempts'] > 3)
			{
				message_die(GENERAL_MESSAGE, $lang['Too_many_registers']);
			}
		}
		$db->sql_freeresult($result);

		$allowed_symbols = "0123456789";
		$length = 5;
			while(true){
				$code='';
				for($i=0;$i<$length;$i++){
					$code.=$allowed_symbols{mt_rand(0,strlen($allowed_symbols)-1)};
				}
				if(!preg_match('/cp|cb|ck|c6|c9|rn|rm|mm|co|do|cl|db|qp|qb|dp/', $code)) break;
			}

		$confirm_id = md5(uniqid($user_ip));

		$sql = 'INSERT INTO ' . CONFIRM_TABLE . " (confirm_id, session_id, code) 
			VALUES ('$confirm_id', '". $userdata['session_id'] . "', '$code')";
		if (!$db->sql_query($sql))
		{
			message_die(GENERAL_ERROR, 'Could not insert new confirm code information', '', __LINE__, __FILE__, $sql);
		}

		unset($code);
		
		$confirm_image = '<img src="' . append_sid("profile.$phpEx?mode=confirm&amp;id=$confirm_id") . '" alt="" title="" />';
		$s_hidden_fields .= '<input type="hidden" name="confirm_id" value="' . $confirm_id . '" />';

		$template->assign_block_vars('switch_confirm', array());
	}

	$warning = ( $mode == 'editprofile' ) ? '' : '*';
	$warning_text = ( $mode == 'editprofile' ) ? '' : $lang['Items_required'];
	switch ($gender) 
	{ 
		case 1: 
			$gender_male_checked="checked=\"checked\"";
		break; 
		case 2:
			$gender_female_checked="checked=\"checked\"";
		break; 
		default:
			$gender_no_specify_checked="checked=\"checked\""; 
	}
	foreach ( $strip_var_list as $var => $param )
	{
		$template->assign_vars(array(
			'VAR_' . strtoupper($param) => ( $mode == 'register' ) ? get_crypted_name($param) : $param
			)
		);
	}

	foreach ( $trim_var_list as $var => $param )
	{
		$template->assign_vars(array(
			'VAR_' . strtoupper($param) => ( $mode == 'register' ) ? get_crypted_name($param) : $param
			)
		);
	}

	$template->assign_vars(array(
		'VAR_USERNAME' => ( $mode == 'register' ) ? get_crypted_name('username') : 'username'
		)
	);

	$template->assign_vars(array(
		'USERNAME' 			=> isset($username) ? $username : '',
		'CUR_PASSWORD'		=> isset($cur_password) ? $cur_password : '',
		'NEW_PASSWORD' 		=> isset($new_password) ? $new_password : '',
		'PASSWORD_CONFIRM' 	=> isset($password_confirm) ? $password_confirm : '',
		'EMAIL' 			=> isset($email) ? $email : '',
		'CONFIRM_IMG' 		=> $confirm_image, 
		
		'LOCK_GENDER' 		=>($mode!='register') ? 'DISABLED':'', 
		'GENDER' 			=> $gender, 
		
		'GENDER_NO_SPECIFY_CHECKED' 	=> $gender_no_specify_checked, 
		'GENDER_MALE_CHECKED' 			=> $gender_male_checked,
		'GENDER_FEMALE_CHECKED' 		=> $gender_female_checked, 
		'L_GENDER' 						=>$lang['Gender'], 
		'L_GENDER_MALE' 				=>$lang['Male'], 
		'L_GENDER_FEMALE' 				=>$lang['Female'], 
		'L_GENDER_NOT_SPECIFY' 			=>$lang['No_gender_specify'],

		'L_CURRENT_PASSWORD' 			=> $lang['Current_password'],
		'L_NEW_PASSWORD' 				=> ( $mode == 'register' ) ? $lang['Password'] : $lang['New_password'],
		'L_CONFIRM_PASSWORD' 			=> $lang['Confirm_password'],
		'L_CONFIRM_PASSWORD_EXPLAIN' 	=> ( $mode == 'editprofile' ) ? $lang['Confirm_password_explain'] : '',
		'L_PASSWORD_IF_CHANGED' 		=> ( $mode == 'editprofile' ) ? $lang['password_if_changed'] : '',
		'L_PASSWORD_CONFIRM_IF_CHANGED' => ( $mode == 'editprofile' ) ? $lang['password_confirm_if_changed'] : '',
		'L_SUBMIT' 						=> $lang['Submit'],
		'L_PROFILE_INFO' 				=> $lang['Profile_info'],
		'L_EMAIL_ADDRESS' 				=> $lang['Email_address'],
		'L_WARNING' 					=> $warning,
		'L_WARNING_TEXT' 				=> $warning_text,

		'L_CONFIRM_CODE_IMPAIRED'	=> sprintf($lang['Confirm_code_impaired'], '<a href="mailto:' . $board_config['board_email'] . '">', '</a>'), 
		'L_CONFIRM_CODE'			=> $lang['Confirm_code'], 
		'L_CONFIRM_CODE_EXPLAIN'	=> $lang['Confirm_code_explain'], 

		'S_HIDDEN_FIELDS' 			=> $s_hidden_fields,
		'S_PROFILE_ACTION' 			=> append_sid("profile.$phpEx"))
	);

$template->pparse('body');

include($phpbb_root_path . 'includes/page_tail.'.$phpEx);

?>