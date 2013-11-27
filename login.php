<?php
/*************************************************
 *		login.php
 *		-------------------
 *      Разработка: phpBB Group.
 *      Оптимизация под WAP: Гутник Игорь ( чел ).
 *          2011 год
 *		简体中文：爱疯的云
 **************************************************/

define("IN_LOGIN", true);

define('IN_PHPBB', true);
$phpbb_root_path = './';
include($phpbb_root_path . 'extension.inc');
include($phpbb_root_path . 'common.'.$phpEx);

$get_login = false;
if (isset($HTTP_GET_VARS['username']))
{
    $get_login = true;
    $HTTP_POST_VARS['method'] = $HTTP_GET_VARS['method'];
	$HTTP_POST_VARS['username'] = $HTTP_GET_VARS['username'];
	$HTTP_POST_VARS['password'] = $HTTP_GET_VARS['password'];
	$HTTP_POST_VARS['login'] = 'Enter';// 请勿翻译这个 Enter
	unset( $HTTP_POST_VARS['logout'], $HTTP_POST_VARS['autologin'] );
}

$userdata = session_pagestart($user_ip, PAGE_LOGIN);
init_userprefs($userdata);

if (!empty($HTTP_POST_VARS['sid']) || !empty($HTTP_GET_VARS['sid']))
{
	$sid = (!empty($HTTP_POST_VARS['sid'])) ? $HTTP_POST_VARS['sid'] : $HTTP_GET_VARS['sid'];
}
else
{
	$sid = '';
}

if( isset($HTTP_POST_VARS['login']) || isset($HTTP_GET_VARS['login']) || isset($HTTP_POST_VARS['logout']) || isset($HTTP_GET_VARS['logout']) )
{
	if( ( isset($HTTP_POST_VARS['login']) || isset($HTTP_GET_VARS['login']) ) && (!$userdata['session_logged_in'] || isset($HTTP_POST_VARS['admin'])) )
	{
		$username = isset($HTTP_POST_VARS['username']) ? phpbb_clean_username($HTTP_POST_VARS['username']) : '';
		$uid = isset($HTTP_POST_VARS['username']) ? intval($HTTP_POST_VARS['username']) : '';
		$password = isset($HTTP_POST_VARS['password']) ? $HTTP_POST_VARS['password'] : '';
		$password_select = md5($password); 
		$method = isset($HTTP_POST_VARS['method']) ? $HTTP_POST_VARS['method'] : '';
        if ( $get_login == true && $method == '' ) $method = 'username'; 
		if ( $method == 'username' )
		{
			$sql = "SELECT user_id, username, user_password, user_active, user_level, user_login_tries, user_last_login_try, user_lastvisit
				FROM " . USERS_TABLE . "
				WHERE username = '" . str_replace("\\'", "''", $username) . "'"; 
		}
		elseif ( $method == 'uid' )
		{
			$sql = "SELECT user_id, username, user_password, user_active, user_level, user_login_tries, user_last_login_try, user_lastvisit
				FROM " . USERS_TABLE . "
				WHERE user_id = '" . str_replace("\\'", "''", $uid) . "'";
		}
		elseif ( $method == 'email' )
		{
			$sql = "SELECT user_id, username, user_password, user_active, user_level, user_login_tries, user_last_login_try, user_lastvisit
				FROM " . USERS_TABLE . "
				WHERE user_email = '" . str_replace("\\'", "''", $username) . "'";
		}
		
		if ( !($result = $db->sql_query($sql)) )
		{
			message_die(GENERAL_ERROR, 'Error in obtaining userdata', '', __LINE__, __FILE__, $sql);
		}

		if( $row = $db->sql_fetchrow($result) )
		{
			if( $row['user_level'] != ADMIN && $board_config['board_disable'] )
			{
				redirect(append_sid("index.$phpEx", true));
			}
			else
			{
				if ($row['user_last_login_try'] && $board_config['login_reset_time'] && $row['user_last_login_try'] < (time() - ($board_config['login_reset_time'] * 60)))
				{
					$db->sql_query('UPDATE ' . USERS_TABLE . ' SET user_login_tries = 0, user_last_login_try = 0 WHERE user_id = ' . $row['user_id']);
					$row['user_last_login_try'] = $row['user_login_tries'] = 0;
				}

				if ($row['user_last_login_try'] && $board_config['login_reset_time'] && $board_config['max_login_attempts'] && 
					$row['user_last_login_try'] >= (time() - ($board_config['login_reset_time'] * 60)) && $row['user_login_tries'] >= $board_config['max_login_attempts'] && $userdata['user_level'] != ADMIN)
				{
					message_die(GENERAL_MESSAGE, sprintf($lang['Login_attempts_exceeded'], $board_config['max_login_attempts'], $board_config['login_reset_time']));
				}

				if( md5($password) == $row['user_password'] && $row['user_active'] )
				{
					$sql = "SELECT user_regdate
						FROM " . USERS_TABLE . "
						WHERE user_id = ".$row['user_id'];
					if ( !($result = $db->sql_query($sql)) )
					{
						message_die(GENERAL_ERROR, 'Error in obtaining userdata', '', __LINE__, __FILE__, $sql);
					}
					if( $rowreg = $db->sql_fetchrow($result) )
					{
						$min_login_regdate = $board_config['min_login_regdate'] * 60;
						$time_activation = $rowreg['user_regdate'] + $min_login_regdate;
						$now_time = time();
						if ( $time_activation > $now_time )
						{
							$data_aktivacii = $time_activation - $now_time;
							$data_aktivacii_min = ceil($data_aktivacii / 60);
							message_die(GENERAL_MESSAGE, '当你注册成功那一刻起，您有<u> '.$board_config['min_login_regdate'].' </u>分钟的时间阅读<u> <a href="' . append_sid("rules.php") . '">规则</a> </u>，此期间您是不能登录的，还剩<u> '.$data_aktivacii_min.' </u>分钟');
						}
					}

					$autologin = ( isset($HTTP_POST_VARS['autologin']) ) ? TRUE : 0;

					$admin = (isset($HTTP_POST_VARS['admin'])) ? 1 : 0;
					$session_id = session_begin($row['user_id'], $user_ip, PAGE_INDEX, FALSE, $autologin, $admin);

					$db->sql_query('UPDATE ' . USERS_TABLE . ' SET user_login_tries = 0, user_last_login_try = 0 WHERE user_id = ' . $row['user_id']);

					if( $session_id )
					{
						$posl_visit = create_date($board_config['default_dateformat'], $row['user_lastvisit'], $board_config['board_timezone']);
						$url = ( !empty($HTTP_POST_VARS['redirect']) ) ? str_replace('&amp;', '&', htmlspecialchars($HTTP_POST_VARS['redirect'])) : "index.$phpEx";

						$template->assign_vars(array(
							'META' => '<meta http-equiv="refresh" content="3;url=' . append_sid($url, true) . '">')
						);
                        
                        //获取浏览器信息
                        include_once($phpbb_root_path . 'includes/browser.'.$phpEx);
                        $browser = new Browser();
                        $browser_type = $browser->getBrowser();
                        $browser_version = $browser->getVersion();
                        $browser_string = $browser_type . " " . $browser_version;
                        //拼接欢迎信息
                        $login_infomation = '欢迎您：' . $row['username'] . '！<br />';
                        if ( $row['user_lastvisit'] != 0 )
                            $login_infomation .='上次访问：' . $posl_visit . '<br />';
                        $login_infomation .='浏览器：' . $browser_string . '<br />';
                        $login_infomation .='IP地址：' . $client_ip . '<br />';
                        $login_infomation .='<a href="' . append_sid($url, true) . '">&lt;--快速进入</a><br/>';
				 		message_die(GENERAL_MESSAGE, $login_infomation);
					}
					else
					{
						message_die(CRITICAL_ERROR, "Couldn't start session : login", "", __LINE__, __FILE__);
					}
				}
				else
				{
					if ($row['user_id'] != ANONYMOUS)
					{
						$sql = 'UPDATE ' . USERS_TABLE . '
							SET user_login_tries = user_login_tries + 1, user_last_login_try = ' . time() . '
							WHERE user_id = ' . $row['user_id'];
						$db->sql_query($sql);
					}
					
					$redirect = ( !empty($HTTP_POST_VARS['redirect']) ) ? str_replace('&amp;', '&', htmlspecialchars($HTTP_POST_VARS['redirect'])) : '';
					$redirect = str_replace('?', '&', $redirect);

					if (strstr(urldecode($redirect), "\n") || strstr(urldecode($redirect), "\r") || strstr(urldecode($redirect), ';url'))
					{
						message_die(GENERAL_ERROR, 'Tried to redirect to potentially insecure url.');
					}

					$template->assign_vars(array(
						'META' => "<meta http-equiv=\"refresh\" content=\"3;url=login.$phpEx?redirect=$redirect\">")
					);

					$message = $lang['Error_login'] . '<br /><br />' . sprintf($lang['Click_return_login'], "<a href=\"login.$phpEx?redirect=$redirect\">", '</a>') . '<br /><br />' .  sprintf($lang['Click_return_index'], '<a href="' . append_sid("index.$phpEx") . '">', '</a>');

					message_die(GENERAL_MESSAGE, $message);
				}
			}
		}
		else
		{
			$redirect = ( !empty($HTTP_POST_VARS['redirect']) ) ? str_replace('&amp;', '&', htmlspecialchars($HTTP_POST_VARS['redirect'])) : "";
			$redirect = str_replace("?", "&", $redirect);

			if (strstr(urldecode($redirect), "\n") || strstr(urldecode($redirect), "\r") || strstr(urldecode($redirect), ';url'))
			{
				message_die(GENERAL_ERROR, 'Tried to redirect to potentially insecure url.');
			}

			$template->assign_vars(array(
				'META' => "<meta http-equiv=\"refresh\" content=\"3;url=login.$phpEx?redirect=$redirect\">")
			);

			$message = $lang['Error_login'] . '<br /><br />' . sprintf($lang['Click_return_login'], "<a href=\"login.$phpEx?redirect=$redirect\">", '</a>') . '<br /><br />' .  sprintf($lang['Click_return_index'], '<a href="' . append_sid("index.$phpEx") . '">', '</a>');

			message_die(GENERAL_MESSAGE, $message);
		}
	}
	else if( ( isset($HTTP_GET_VARS['logout']) || isset($HTTP_POST_VARS['logout']) ) && $userdata['session_logged_in'] )
	{
		if ($sid == '' || $sid != $userdata['session_id'])
		{
			message_die(GENERAL_ERROR, 'Invalid_session');
		}

		if( $userdata['session_logged_in'] )
		{
			session_end($userdata['session_id'], $userdata['user_id']);
		}

		if (!empty($HTTP_POST_VARS['redirect']) || !empty($HTTP_GET_VARS['redirect']))
		{
			$url = (!empty($HTTP_POST_VARS['redirect'])) ? htmlspecialchars($HTTP_POST_VARS['redirect']) : htmlspecialchars($HTTP_GET_VARS['redirect']);
			$url = str_replace('&amp;', '&', $url);
			redirect(append_sid($url, true));
		}
		else
		{
		$template->assign_vars(array(
			'META' => "<meta http-equiv=\"refresh\" content=\"2;url=index.php\">")
		);
		message_die(GENERAL_MESSAGE, '欢迎下次访问！<br/><a href="index.php">&lt;--快速退出</a><br/>');
		}
	}
	else
	{
		$url = ( !empty($HTTP_POST_VARS['redirect']) ) ? str_replace('&amp;', '&', htmlspecialchars($HTTP_POST_VARS['redirect'])) : "index.$phpEx";
		$template->assign_vars(array(
			'META' => '<meta http-equiv="refresh" content="3;url=' . append_sid($url, true) . '">')
		);

		message_die(GENERAL_MESSAGE, '请点击确认！<br/><a href="' . append_sid($url, true) . '">确认--&gt;</a><br/>');
	}
}
else
{

	if( !$userdata['session_logged_in'] || (isset($HTTP_GET_VARS['admin']) && $userdata['session_logged_in'] && $userdata['user_level'] == ADMIN || (isset($HTTP_GET_VARS['admin']) && $userdata['session_logged_in'] && $userdata['user_level'] == MODCP )))
	{
		$page_title = $lang['Login'];
		include($phpbb_root_path . 'includes/page_header.'.$phpEx);

		$template->set_filenames(array(
			'body' => 'login_body.tpl')
		);

		$forward_page = '';

		if( isset($HTTP_POST_VARS['redirect']) || isset($HTTP_GET_VARS['redirect']) )
		{
			$forward_to = $HTTP_SERVER_VARS['QUERY_STRING'];

			if( preg_match("/^redirect=([a-z0-9\.#\/\?&=\+\-_]+)/si", $forward_to, $forward_matches) )
			{
				$forward_to = ( !empty($forward_matches[3]) ) ? $forward_matches[3] : $forward_matches[1];
				$forward_match = explode('&', $forward_to);

				if(count($forward_match) > 1)
				{
					for($i = 1; $i < count($forward_match); $i++)
					{
						if( !ereg("sid=", $forward_match[$i]) )
						{
							if( $forward_page != '' )
							{
								$forward_page .= '&';
							}
							$forward_page .= $forward_match[$i];
						}
					}
					$forward_page = $forward_match[0] . '?' . $forward_page;
				}
				else
				{
					$forward_page = $forward_match[0];
				}
			}
		}

		$username = ( $userdata['user_id'] != ANONYMOUS ) ? $userdata['username'] : '';

		$s_hidden_fields = '<input type="hidden" name="redirect" value="' . $forward_page . '" />';
		$s_hidden_fields .= (isset($HTTP_GET_VARS['admin'])) ? '<input type="hidden" name="admin" value="1" />' : '';

		$template->assign_vars(array(
			'USERNAME' 			=> $username,

			'L_ENTER_PASSWORD' 	=> (isset($HTTP_GET_VARS['admin'])) ? $lang['Admin_reauthenticate'] : $lang['Enter_password'],
			'L_SEND_PASSWORD' 	=> $lang['Forgotten_password'],

			'U_SEND_PASSWORD' 	=> append_sid("profile.$phpEx?mode=sendpassword"),
			'U_AUTOLOGIN' 		=> append_sid("rules.$phpEx?mode=faq&amp;act=autologin"),

			'S_HIDDEN_FIELDS' 	=> $s_hidden_fields)
		);

		$template->pparse('body');

		include($phpbb_root_path . 'includes/page_tail.'.$phpEx);
	}
	else
	{
		redirect(append_sid("index.$phpEx", true));
	}

}

?>