<?php
/***************************************************************************
 *                               lottery.php
 *                            -------------------
 *                    官方网站：http://phpbb-wap.com
 *                    程序作者：爱疯的云
 *                    ＱＱ联系：53109774
 *                    电子邮件：zhangyunwap@qq.com
 *                    最后修改：2011-08-01
 *                    文件说明：彩票游戏
 *                               
 ***************************************************************************/

define('IN_PHPBB', true);
$phpbb_root_path = './../../';
include($phpbb_root_path . 'extension.inc');
include($phpbb_root_path . 'mods/includes/function_duration.' . $phpEx);
include($phpbb_root_path . 'common.' . $phpEx);
include($phpbb_root_path . 'mods/includes/constants.' . $phpEx);
include($phpbb_root_path . 'language/lang_chinese/mods/lang_lottery.' . $phpEx);

$userdata = session_pagestart($user_ip, PAGE_LOTERY);
init_userprefs($userdata);

if ( isset($HTTP_GET_VARS['action']) || isset($HTTP_POST_VARS['action']) ) 
{
	$action = ( isset($HTTP_POST_VARS['action']) ) ? $HTTP_POST_VARS['action'] : $HTTP_GET_VARS['action']; 
}
else 
{
	$action = ''; 
}

if ( !($board_config['lottery_status']) ) 
{
	message_die(GENERAL_MESSAGE, $lang['lottery_disabled']); 
}

$sql = "SELECT *
	FROM " . LOTTERY_TABLE . "
	WHERE user_id = {$userdata['user_id']}";

if ( !($result = $db->sql_query($sql)) )
{
	message_die(GENERAL_ERROR, sprintf($lang['lottery_error_selecting'], 'lottery'), '', __LINE__, __FILE__, $sql);
}
$sql_count = $db->sql_numrows($result);
$tickbuy = ( ($board_config['lottery_ticktype'] == 'single') && $sql_count ) ? 0 : 1;

$timeleft = ( ( $board_config['lottery_start'] + $board_config['lottery_length'] ) - time() );
$thetime = time();
if ( $timeleft < 1 )
{
	$sql = "SELECT *
		FROM " . LOTTERY_TABLE;

	if ( !($result = $db->sql_query($sql)) )
	{
		message_die(GENERAL_ERROR, sprintf($lang['lottery_error_selecting'], 'lottery'), '', __LINE__, __FILE__, $sql);
	}

	if ( $sql_count = $db->sql_numrows($result) )
	{
		$pool = (($sql_count * $board_config['lottery_cost']) + $board_config['lottery_base']);

		list($usec, $sec) = explode(' ', microtime());
		$seed = (float) $sec + ((float) $usec * 100000);
		srand($seed);

		$randnum = (rand(1, $sql_count)-1);
		for ($i = 0; $i < $sql_count; $i++) 
		{
			if (!( $row = $db->sql_fetchrow($result) ))
			{
				message_die(GENERAL_ERROR, sprintf($lang['lottery_error_selecting'], 'lottery'), '', __LINE__, __FILE__, $sql);
			}

			if ( $i == $randnum )
			{
				$row2 = get_userdata($row['user_id']);
				break;
			}
		}

		if ( defined('SHOP_TABLE') )
		{
			$item_array = explode(';', $board_config['lottery_win_items']);
			$add_items = array();
			for ($i = 0; $i < count($item_array); $i++)
			{
				$item_array[$i] = trim($item_array[$i]);

				if ( $item_array[$i] == 'random' )
				{
					$shop_sql = ( !empty($board_config['lottery_random_shop']) ) ? "AND shop = '" . str_replace("'", "''", $board_config['lottery_random_shop']) . "'" : '';

					$sql = "SELECT *
						FROM " . SHOP_ITEMS_TABLE . "
						WHERE cost > " . (int)$board_config['lottery_item_mcost'] . "
							AND cost < " . (int)$board_config['lottery_item_xcost'] . "
							" . $shop_sql . "
						ORDER BY RAND()
							LIMIT 0, 1";
					if ( !($result = $db->sql_query($sql)) )
					{
						message_die(GENERAL_ERROR, sprintf($lang['lottery_error_selecting'], 'shop items'), '', __LINE__, __FILE__, $sql);
					}

					if ( $db->sql_numrows($result) )
					{
						if (!( $item_row = $db->sql_fetchrow($result) ))
						{
							message_die(GENERAL_ERROR, sprintf($lang['lottery_error_selecting'], 'shop items'), '', __LINE__, __FILE__, $sql);
						}

						if ( defined('USER_ITEMS_TABLE') )
						{
							$decay_time = ( $row['decay'] ) ? ( time() + $row['decay'] ) : 0;

							$sql = "INSERT INTO " . USER_ITEMS_TABLE . "
								(user_id, item_id, item_name, item_s_desc, item_l_desc, die_time)
								VALUES({$row2['user_id']}, {$item_row['id']}, '" . str_replace("'", "''", $item_row['name']) . "', '" . str_replace("'", "''", $item_row['sdesc']) . "', '" . str_replace("'", "''", $item_row['ldesc']) . "', $decay_time)";
							if ( !($db->sql_query($sql)) )
							{
								message_die(GENERAL_MESSAGE, 'Fatal Error: Unable to add item to user for random');
							}
						}
						else
						{
							$add_items[] = $item_row['name'];
						}
					}
				}
				elseif ( !empty($item_array[$i]) )
				{
					$sql = "SELECT *
						FROM " . SHOP_ITEMS_TABLE . "
						WHERE name = '" . str_replace("'", "''", $item_array[$i]) . "'";

					if ( !($result = $db->sql_query($sql)) )
					{
						message_die(GENERAL_ERROR, sprintf($lang['lottery_error_selecting'], 'shop items'), '', __LINE__, __FILE__, $sql);
					}

					if ( $db->sql_numrows($result) )
					{
						if (!( $item_row = $db->sql_fetchrow($result) ))
						{
							message_die(GENERAL_ERROR, sprintf($lang['lottery_error_selecting'], 'shop items'), '', __LINE__, __FILE__, $sql);
						}

						if ( defined('USER_ITEMS_TABLE') )
						{
							$decay_time = ( $row['decay'] ) ? (time() + $row['decay']) : 0;

							$sql = "INSERT INTO " . USER_ITEMS_TABLE . "
								(user_id, item_id, item_name, item_s_desc, item_l_desc, die_time)
								VALUES({$row2['user_id']}, {$item_row['id']}, '" . str_replace("'", "''", $item_row['name']) . "', '" . str_replace("'", "''", $item_row['sdesc']) . "', '" . str_replace("'", "''", $item_row['ldesc']) . "', $decay_time)";
							if ( !($db->sql_query($sql)) )
							{
								message_die(GENERAL_MESSAGE, 'Fatal Error: Unable to add normal item');
							}
						}
						else
						{
							$add_items[] = $item_row['name'];
						}
					}
				}

			}
			if ( count($add_items) > 0 && !defined('USER_ITEMS_TABLE') )
			{
				$new_items = str_replace("'", "''", $row2['user_items'] . 'Я' . implode('ЮЯ', $add_items) . 'Ю');

				$sql = "UPDATE " . USERS_TABLE . "
					SET user_items = '$new_items'
					WHERE user_id = {$row2['user_id']}";
				if ( !($db->sql_query($sql)) )
				{
					message_die(GENERAL_ERROR, sprintf($lang['lottery_error_updating'], 'users'), '', __LINE__, __FILE__, $sql);
				}
			}
		}

		if ( DEFINED('CASH_TABLE') )
		{
			$cash_sql_where = ( !empty($board_config['lottery_currency']) ) ? "WHERE cash_name = '{$board_config['lottery_currency']}'" : '';

			$sql = "SELECT `cash_dbfield`, `cash_name`
				FROM " . CASH_TABLE . "
				" . $cash_sql_where;
			if ( !($result = $db->sql_query($sql)) )
			{
				message_die(GENERAL_ERROR, sprintf($lang['lottery_error_selecting'], 'cash'), '', __LINE__, __FILE__, $sql);
			}
			if ( $db->sql_numrows($result) )
			{
				if (!( $cash_row = $db->sql_fetchrow($result) ))
				{
					message_die(GENERAL_ERROR, sprintf($lang['lottery_error_selecting'], 'cash'), '', __LINE__, __FILE__, $sql);
				}

				$currency_db = $cash_row['cash_dbfield'];
				$currency_name = $cash_row['cash_name'];
			}
		}
		else		
		{
			$currency_db = 'user_points';
			$currency_name = $board_config['points_name'];
		}
function cash_pm(&$targetdata,$privmsg_subject,&$message)
{
	global $db, $board_config, $lang, $userdata, $phpbb_root_path, $phpEx, $html_entities_match, $html_entities_replace;

	include($phpbb_root_path . 'includes/bbcode.'.$phpEx);
	include($phpbb_root_path . 'includes/functions_post.'.$phpEx);

	if ( !$board_config['allow_html'] )
	{
		$html_on = 0;
	}
	else
	{
		$html_on = $userdata['user_allowhtml'];
	}

	$bbcode_on = TRUE;

	if ( !$board_config['allow_smilies'] )
	{
		$smilies_on = 0;
	}
	else
	{
		$smilies_on = $userdata['user_allowsmile'];
	}

	$attach_sig = $userdata['user_attachsig'];

	$sql = "SELECT MAX(privmsgs_date) AS last_post_time
		FROM " . PRIVMSGS_TABLE . "
		WHERE privmsgs_from_userid = " . $userdata['user_id'];
	if ( $result = $db->sql_query($sql) )
	{
		$db_row = $db->sql_fetchrow($result);

		$last_post_time = $db_row['last_post_time'];
		$current_time = time();

		if ( ( $current_time - $last_post_time ) < $board_config['flood_interval'])
		{
			message_die(GENERAL_MESSAGE, $lang['Flood_Error']);
		}
	}

	$msg_time = time();
	$bbcode_uid = make_bbcode_uid();

	$privmsg_message = prepare_message($message, $html_on, $bbcode_on, $smilies_on, $bbcode_uid);

	$sql = "SELECT COUNT(privmsgs_id) AS inbox_items, MIN(privmsgs_date) AS oldest_post_time 
		FROM " . PRIVMSGS_TABLE . " 
		WHERE ( privmsgs_type = " . PRIVMSGS_NEW_MAIL . " 
				OR privmsgs_type = " . PRIVMSGS_READ_MAIL . "  
				OR privmsgs_type = " . PRIVMSGS_UNREAD_MAIL . " ) 
			AND privmsgs_to_userid = " . $targetdata['user_id'];
	if ( !($result = $db->sql_query($sql)) )
	{
		message_die(GENERAL_MESSAGE, $lang['No_such_user']);
	}

	$sql_priority = ( SQL_LAYER == 'mysql' ) ? 'LOW_PRIORITY' : '';

	if ( $inbox_info = $db->sql_fetchrow($result) )
	{
		if ( $inbox_info['inbox_items'] >= $board_config['max_inbox_privmsgs'] )
		{
			$sql = "SELECT privmsgs_id FROM " . PRIVMSGS_TABLE . " 
				WHERE ( privmsgs_type = " . PRIVMSGS_NEW_MAIL . " 
						OR privmsgs_type = " . PRIVMSGS_READ_MAIL . " 
						OR privmsgs_type = " . PRIVMSGS_UNREAD_MAIL . "  ) 
					AND privmsgs_date = " . $inbox_info['oldest_post_time'] . " 
					AND privmsgs_to_userid = " . $targetdata['user_id'];
			if ( !$result = $db->sql_query($sql) )
			{
				message_die(GENERAL_ERROR, 'Could not find oldest privmsgs (inbox)', '', __LINE__, __FILE__, $sql);
			}
			$old_privmsgs_id = $db->sql_fetchrow($result);
			$old_privmsgs_id = $old_privmsgs_id['privmsgs_id'];
		
			$sql = "DELETE $sql_priority FROM " . PRIVMSGS_TABLE . " 
				WHERE privmsgs_id = $old_privmsgs_id";
			if ( !$db->sql_query($sql) )
			{
				message_die(GENERAL_ERROR, 'Could not delete oldest privmsgs (inbox)'.$sql, '', __LINE__, __FILE__, $sql);
			}

			$sql = "DELETE $sql_priority FROM " . PRIVMSGS_TEXT_TABLE . " 
				WHERE privmsgs_text_id = $old_privmsgs_id";
			if ( !$db->sql_query($sql) )
			{
				message_die(GENERAL_ERROR, 'Could not delete oldest privmsgs text (inbox)', '', __LINE__, __FILE__, $sql);
			}
		}
	}

	$sql_info = "INSERT INTO " . PRIVMSGS_TABLE . " (privmsgs_type, privmsgs_subject, privmsgs_from_userid, privmsgs_to_userid, privmsgs_date, privmsgs_ip, privmsgs_enable_html, privmsgs_enable_bbcode, privmsgs_enable_smilies, privmsgs_attach_sig)
		VALUES (" . PRIVMSGS_NEW_MAIL . ", '" . str_replace("\'", "''", $privmsg_subject) . "', " . $userdata['user_id'] . ", " . $targetdata['user_id'] . ", $msg_time, '$user_ip', $html_on, $bbcode_on, $smilies_on, $attach_sig)";
	if ( !($result = $db->sql_query($sql_info, BEGIN_TRANSACTION)) )
	{
		message_die(GENERAL_ERROR, "Could not insert/update private message sent info.", "", __LINE__, __FILE__, $sql_info);
	}
	$privmsg_sent_id = $db->sql_nextid();

	$sql = "INSERT INTO " . PRIVMSGS_TEXT_TABLE . " (privmsgs_text_id, privmsgs_bbcode_uid, privmsgs_text)
		VALUES ($privmsg_sent_id, '" . $bbcode_uid . "', '" . str_replace("\'", "''", $privmsg_message) . "')";
	if ( !$db->sql_query($sql, END_TRANSACTION) )
	{
		message_die(GENERAL_ERROR, "Could not insert/update private message sent text.", "", __LINE__, __FILE__, $sql_info);
	}

	$sql = "UPDATE " . USERS_TABLE . "
		SET user_new_privmsg = user_new_privmsg + 1, user_last_privmsg = " . time() . "  
		WHERE user_id = " . $targetdata['user_id']; 
	if ( !$status = $db->sql_query($sql) )
	{
		message_die(GENERAL_ERROR, 'Could not update private message new/read status for user', '', __LINE__, __FILE__, $sql);
	}

	if ( $targetdata['user_notify_pm'] && !empty($targetdata['user_email']) && $targetdata['user_active'] && $a = 1)
	{
		$script_name = preg_replace('/^\/?(.*?)\/?$/', "\\1", trim($board_config['script_path']));
		$script_name = ( $script_name != '' ) ? $script_name . '/privmsg.'.$phpEx : 'privmsg.'.$phpEx;
		$server_name = trim($board_config['server_name']);
		$server_protocol = ( $board_config['cookie_secure'] ) ? 'https://' : 'http://';
		$server_port = ( $board_config['server_port'] <> 80 ) ? ':' . trim($board_config['server_port']) . '/' : '/';

		include($phpbb_root_path . 'includes/emailer.'.$phpEx);
		$emailer = new emailer($board_config['smtp_delivery']);
			
		$emailer->from($board_config['board_email']);
		$emailer->replyto($board_config['board_email']);

		$emailer->use_template('privmsg_notify', $targetdata['user_lang']);
		$emailer->email_address($targetdata['user_email']);
		$emailer->set_subject($lang['Notification_subject']);
			
		$emailer->assign_vars(array(
			'USERNAME' => $to_username, 
			'SITENAME' => $board_config['sitename'],
			'EMAIL_SIG' => (!empty($board_config['board_email_sig'])) ? str_replace('<br />', "\n", "-- \n" . $board_config['board_email_sig']) : '', 

			'U_INBOX' => $server_protocol . $server_name . $server_port . $script_name . '?folder=inbox')
		);

		$emailer->send();
		$emailer->reset();
	}
}

		$sql = "UPDATE " . USERS_TABLE . "
			SET `" . $currency_db . "` = " . $currency_db . " + $pool
			WHERE user_id = {$row2['user_id']}";
		if ( !($db->sql_query($sql)) )
		{
			message_die(GENERAL_ERROR, sprintf($lang['lottery_error_updating'], 'users'), '', __LINE__, __FILE__, $sql);
		}

		$sql = "INSERT INTO " . LOTTERY_HISTORY_TABLE . "
			(user_id, amount, currency, time)
			VALUES ({$row2['user_id']}, $pool, '" . str_replace("'", "''", $currency_name) . "', $thetime)";
		if ( !($db->sql_query($sql)) )
		{
			message_die(GENERAL_ERROR, sprintf($lang['lottery_error_inserting'], 'lottery history'), '', __LINE__, __FILE__, $sql);
		}

   		$privmsg_subject = $lang['lottery_pm_title'] . 'fdsa'; 
   		$message = sprintf($lang['lottery_pm_message'], $pool); 

   		$temp_data = $userdata; 
		$userdata = get_userdata(2); 
		cash_pm($row2,$privmsg_subject,$message); 
		$userdata = $temp_data;
	}

	$sql = "DELETE FROM " . LOTTERY_TABLE;
	if ( !($db->sql_query($sql)) )
	{
		message_die(GENERAL_ERROR, sprintf($lang['lottery_error_deleting'], 'lottery'), '', __LINE__, __FILE__, $sql);
	}
	if ($board_config['lottery_reset'])
	{ 
		$sql = "UPDATE " . CONFIG_TABLE . "
			SET config_value = '" . ($thetime - 30) . "'
			WHERE config_name = 'lottery_start'";
		if ( !($db->sql_query($sql)) )
		{
			message_die(GENERAL_ERROR, sprintf($lang['lottery_error_updating'], 'config'), '', __LINE__, __FILE__, $sql);
		}
	}
	else
	{
		$sql = "UPDATE " . CONFIG_TABLE . "
			SET config_value = '0'
			WHERE config_name = 'lottery_status'";
		if ( !($db->sql_query($sql)) )
		{
			message_die(GENERAL_ERROR, sprintf($lang['lottery_error_updating'], 'config'), '', __LINE__, __FILE__, $sql);
		}
		$sql = "UPDATE " . CONFIG_TABLE . "
			SET config_value = '0'
			WHERE config_name = 'lottery_start'";
		if ( !($db->sql_query($sql)) )
		{
			message_die(GENERAL_ERROR, sprintf($lang['lottery_error_updating'], 'config'), '', __LINE__, __FILE__, $sql);
		}
	}

	redirect("index.$phpEx");
}

if ( empty($action) )
{
	$template->set_filenames(array(
		'body' => 'mods/lottery_index_body.tpl')
	);	

	$duration = duration($timeleft);

	$title = $board_config['lottery_name'] . $lang['lottery_information']; 

	if ( $tickbuy || $board_config['lottery_history'] )
	{
		if ( $tickbuy )
		{
			if ( $board_config['lottery_mb'] )
			{
				$template->assign_block_vars('switch_tickets_multi', array());
			}
			else
			{
				$template->assign_block_vars('switch_tickets_single', array());
			}
		}
		if ( $board_config['lottery_history'] )
		{
			$template->assign_block_vars('switch_view_history', array());

			$sql = "SELECT *
				FROM " . LOTTERY_HISTORY_TABLE . "
				WHERE user_id = {$userdata['user_id']}";

			if ( !($result = $db->sql_query($sql)) )
			{
				message_die(GENERAL_ERROR, sprintf($lang['lottery_error_selecting'], 'lottery'), '', __LINE__, __FILE__, $sql);
			}
			if ( $db->sql_numrows($result) )
			{
				$template->assign_block_vars('switch_view_personal', array());
			}
		}
		$template->assign_block_vars('switch_are_actions', array());
	}

	if ( $board_config['lottery_show_entries'] )
	{
		$template->assign_block_vars('switch_full_display', array());

		$sql = "SELECT *
			FROM " . LOTTERY_TABLE;

		if ( !($result = $db->sql_query($sql)) )
		{
			message_die(GENERAL_ERROR, sprintf($lang['lottery_error_selecting'], 'lottery'), '', __LINE__, __FILE__, $sql);
		}
		$total_entries = $db->sql_numrows($result);
		$total_pool = ($board_config['lottery_cost'] * $total_entries) + $board_config['lottery_base'];
	}

	if ( defined('CASH_TABLE') )
	{
		$currency_name = $board_config['lottery_currency'];
	}
	else
	{
		$currency_name = $board_config['points_name'];
	}

	if ( $board_config['lottery_items'] && !empty($board_config['lottery_win_items']) )
	{
		$lottery_items = str_replace(';', ', ', $board_config['lottery_win_items']);
		$template->assign_block_vars('switch_items', array());
	}

	$sql = "SELECT t1.*, t2.username
		FROM " . LOTTERY_HISTORY_TABLE . " t1, " . USERS_TABLE . " t2
		WHERE t2.user_id = t1.user_id
		ORDER BY time DESC";

	if ( !($result = $db->sql_query($sql)) )
	{
		message_die(GENERAL_ERROR, sprintf($lang['lottery_error_selecting'], 'lottery history'), '', __LINE__, __FILE__, $sql);
	}

	if ( $db->sql_numrows($result) )
	{
		if (!( $row = $db->sql_fetchrow($result) ))
		{
			message_die(GENERAL_ERROR, sprintf($lang['lottery_error_selecting'], 'lottery'), '', __LINE__, __FILE__, $sql);
		}
		$template->assign_block_vars('switch_last_winner', array(
			'WINNER_NAME' => $row['username']));
	}

	$page_title = $board_config['lottery_name'];

	$template->assign_vars(array(
		'TICKETS_OWNED' => $sql_count,
		'L_PRIZE_BASE' => $board_config['lottery_base'],
		'L_TICKET_COST' => $board_config['lottery_cost'],
		'L_TOTAL_PRIZE' => $total_pool,
		'L_CURRENT_ENTRIES' => $total_entries,
		'L_ITEM_PRIZE' => $lottery_items,

		'S_CONFIG_ACTION' => append_sid('index.'.$phpEx),

		'L_CURRENCY' => $currency_name,
		'L_DURATION' => $duration,
		'L_NAME' => $board_config['lottery_name'],
		'L_INFO_TITLE' => $board_config['lottery_name'] . ' ' . $lang['lottery_information'],
		'L_ACTIONS_TITLE' => $lang['lottery_actions'],
		'L_TICKET_OWNED' => $lang['lottery_tickets_owned'],
		'L_TICKETS_COST' => $lang['lottery_ticket_cost'],
		'L_BASE_POOL' => $lang['lottery_base_pool'],
		'L_CURRENT_POOL' => $lang['lottery_current_entries'],
		'L_TOTAL_POOL' => $lang['lottery_total_pool'],
		'L_ITEM_DRAW' => $lang['lottery_item_draw'],
		'L_TIME_DRAW' => $lang['lottery_time_draw'],
		'L_LAST_WINNER' => $lang['lottery_last_winner'],

		'I_BUY_TICKET' => $lang['lottery_buy_ticket'],
		'I_BUY_TICKETS' => $lang['lottery_buy_tickets'],
		'I_VIEW_HISTORY' => $lang['lottery_view_history'],
		'I_VIEW_PHISTORY' => $lang['lottery_view_phistory']
	));
	$template->assign_block_vars('', array());
}
elseif ( $action == 'options' )
{
	if ( !$userdata['session_logged_in'] )
	{
		$redirect = "index.$phpEx";
		redirect("login.$phpEx?redirect=$redirect");
	}

	if ( isset($HTTP_GET_VARS['amount']) || isset($HTTP_POST_VARS['amount']) ) { $amount = ( isset($HTTP_POST_VARS['amount']) ) ? intval($HTTP_POST_VARS['amount']) : intval($HTTP_GET_VARS['amount']); }
	else { $amount = ''; }
	if ( isset($HTTP_GET_VARS['view_history']) || isset($HTTP_POST_VARS['view_history']) ) { $view_history = ( isset($HTTP_POST_VARS['view_history']) ) ? htmlspecialchars($HTTP_POST_VARS['view_history']) : htmlspecialchars($HTTP_GET_VARS['view_history']); }
	else { $view_history = ''; }
	if ( isset($HTTP_GET_VARS['view_personal']) || isset($HTTP_POST_VARS['view_personal']) ) { $view_personal = ( isset($HTTP_POST_VARS['view_personal']) ) ? htmlspecialchars($HTTP_POST_VARS['view_personal']) : htmlspecialchars($HTTP_GET_VARS['view_personal']); }
	else { $view_personal = ''; }
	if ( isset($HTTP_GET_VARS['start']) || isset($HTTP_POST_VARS['start']) ) { $start = ( isset($HTTP_POST_VARS['start']) ) ? intval($HTTP_POST_VARS['start']) : intval($HTTP_GET_VARS['start']); }
	else { $start = 0; }

	if ( isset($HTTP_GET_VARS['buy_ticket']) || isset($HTTP_POST_VARS['buy_ticket']) ) { $buy_ticket = ( isset($HTTP_POST_VARS['buy_ticket']) ) ? $HTTP_POST_VARS['buy_ticket'] : $HTTP_GET_VARS['buy_ticket']; }
	else { $buy_ticket = ''; }
	if ( isset($HTTP_GET_VARS['buy_tickets']) || isset($HTTP_POST_VARS['buy_tickets']) ) { $buy_tickets = ( isset($HTTP_POST_VARS['buy_tickets']) ) ? $HTTP_POST_VARS['buy_tickets'] : $HTTP_GET_VARS['buy_tickets']; }
	else { $buy_tickets = ''; }

	if ( !empty($buy_ticket) || !empty($buy_tickets) )
	{

		if ( !($tickbuy) )
		{
			message_die(GENERAL_MESSAGE, $lang['lottery_too_many_tickets']);
		}
		$amount = ( ($amount < 1) || ($amount > 9999) ) ? 1 : $amount;

		if ( $board_config['lottery_ticktype'] != 'multiple' || !($board_config['lottery_mb']) )
		{
			$amount = 1;
		}
		else
		{
			if ( $amount > $board_config['lottery_mb_amount'] ) { $amount = $board_config['lottery_mb_amount']; }
		}

		$sql = "SELECT count(*) as total_tickets
			FROM " . LOTTERY_TABLE . "
			WHERE user_id = {$userdata['user_id']}";
		if ( !($result = $db->sql_query($sql)) )
		{
			message_die(GENERAL_ERROR, sprintf($lang['lottery_error_selecting'], 'lottery'), '', __LINE__, __FILE__, $sql);
		}
		if (!( $row = $db->sql_fetchrow($result) ))
		{
			message_die(GENERAL_ERROR, sprintf($lang['lottery_error_selecting'], 'lottery'), '', __LINE__, __FILE__, $sql);
		}
		if ( ( $row['total_tickets'] + $amount ) > $board_config['lottery_mb_amount'] )
		{
			$amount = ( $board_config['lottery_mb_amount'] - $row['total_tickets'] );
			if ( $amount < 1 ) { message_die(GENERAL_MESSAGE, $lang['lottery_max_tickets']); }
		}

		$ticket_cost = ( $board_config['lottery_cost'] * $amount );

		if ( DEFINED('CASH_TABLE') )
		{
			$cash_sql_where = ( !empty($board_config['lottery_currency']) ) ? "WHERE cash_name = '{$board_config['lottery_currency']}'" : '';

			$sql = "SELECT `cash_dbfield`
				FROM " . CASH_TABLE . "
				" . $cash_sql_where;
			if ( !($result = $db->sql_query($sql)) )
			{
				message_die(GENERAL_ERROR, sprintf($lang['lottery_error_selecting'], 'cash'), '', __LINE__, __FILE__, $sql);
			}
			if ( $db->sql_numrows($result) )
			{
				if (!( $row = $db->sql_fetchrow($result) ))
				{
					message_die(GENERAL_ERROR, sprintf($lang['lottery_error_selecting'], 'cash'), '', __LINE__, __FILE__, $sql);
				}

				if ( ($userdata[$row['cash_dbfield']] - $ticket_cost) < 0 )
				{
				$msg = ( $amount == '1' ) ? $lang['lottery_purchased_ticket'] : sprintf($lang['lottery_purchased_tickets'], $amount);
				message_die(GENERAL_MESSAGE, $lang['lottery_purchased_ne'] . $row['cash_name'] . $msg);
				}

				$sql = "UPDATE " . USERS_TABLE . "
					SET " . $row['cash_dbfield'] . " = " . $row['cash_dbfield'] . " - $ticket_cost
					WHERE user_id='{$userdata['user_id']}'";
				if ( !($db->sql_query($sql)) )
				{
					message_die(GENERAL_ERROR, sprintf($lang['lottery_error_updating'], 'users'), '', __LINE__, __FILE__, $sql);
				}
				$cash_done = 1;
			}
		}

		if ( !($cash_done) )		
		{
			if ( ($userdata['user_points'] - $ticket_cost) < 0 )
			{
				$msg = ( $amount == '1' ) ? $lang['lottery_purchased_ticket'] : sprintf($lang['lottery_purchased_tickets'], $amount);
				message_die(GENERAL_MESSAGE, $lang['lottery_purchased_ne'] . $board_config['points_name'] . $msg);
			}

			$sql = "UPDATE " . USERS_TABLE . "
				SET user_points = user_points - $ticket_cost
				WHERE user_id='{$userdata['user_id']}'";
			if ( !($db->sql_query($sql)) )
			{
				message_die(GENERAL_ERROR, sprintf($lang['lottery_error_updating'], 'users'), '', __LINE__, __FILE__, $sql);
			}
		}

		$sql = "INSERT INTO " . LOTTERY_TABLE . "
			(user_id)
			VALUES ('{$userdata['user_id']}')";
		for ($i = 0; $i < $amount; $i++)
		{
			if ( !($db->sql_query($sql)) )
			{
				message_die(GENERAL_ERROR, sprintf($lang['lottery_error_inserting'], 'lottery'), '', __LINE__, __FILE__, $sql);
			}
		}
		$msg = ( $amount < 2 ) ? sprintf($lang['lottery_ticket_bought'], $board_config['lottery_name']) : sprintf($lang['lottery_tickets_bought'], $amount, $board_config['lottery_name']);
		message_die(GENERAL_MESSAGE, $msg);
	}
	elseif ( !empty($view_history) || ( !empty($view_personal) ) )
	{

		$template->set_filenames(array(
			'body' => 'mods/lottery_history_body.tpl')
		);

		if ( !($board_config['lottery_history']) ) { message_die(GENERAL_MESSAGE, $lang['lottery_history_disabled']); }

		if ( !empty($view_personal) )
		{
			$sql = "SELECT t1.*, t2.username
				FROM " . LOTTERY_HISTORY_TABLE . " t1, " . USERS_TABLE . " t2
				WHERE t1.user_id = {$userdata['user_id']}
					AND t2.user_id = t1.user_id
				ORDER BY time DESC
				LIMIT $start, {$board_config['topics_per_page']}";

			$page_sql = "SELECT count(*) AS total
				FROM " . LOTTERY_HISTORY_TABLE . "
				WHERE user_id = {$userdata['user_id']}";
		}
		elseif ( !empty($view_history) )
		{
			$sql = "SELECT t1.*, t2.username
				FROM " . LOTTERY_HISTORY_TABLE . " t1, " . USERS_TABLE . " t2
				WHERE t2.user_id = t1.user_id
				ORDER BY time DESC
				LIMIT $start, {$board_config['topics_per_page']}";

			$page_sql = "SELECT count(*) AS total
				FROM " . LOTTERY_HISTORY_TABLE;
		}
		else { message_die(GENERAL_MESSAGE, $lang['lottery_no_history_type']); }

		if ( !($result = $db->sql_query($sql)) )
		{
			message_die(GENERAL_ERROR, sprintf($lang['lottery_error_selecting'], 'lottery history'), '', __LINE__, __FILE__, $sql);
		}
		$sql_count = $db->sql_numrows($result);

		if ( !($sql_count) )
		{
			$template->assign_block_vars('switch_no_history', array(
				'MESSAGE' => $lang['lottery_no_history']));
		}
		else
		{
			for ($i = 0; $i < $sql_count; $i++)
			{
				if (!( $row = $db->sql_fetchrow($result) ))
				{
					message_die(GENERAL_ERROR, sprintf($lang['lottery_error_selecting'], 'lottery history'), '', __LINE__, __FILE__, $sql);
				}
				$row_class = ( $i % 2 ) ? 'row1' : 'row2';

				$template->assign_block_vars('listrow', array(
					'ROW_CLASS' 		=> $row_class,
					'HISTORY_NUM' 		=> $i + 1 + $start,
					'HISTORY_WINNER' 	=> $row['username'],
					'HISTORY_AMOUNT' 	=> $row['amount'],
					'HISTORY_CURRENCY' 	=> $row['currency'],
					'HISTORY_TIME' 		=> create_date($board_config['default_dateformat'], $row['time'], $board_config['board_timezone']))
				);
			}

			$template->assign_block_vars('switch_title_info', array());
		}

		if ( !($result = $db->sql_query($page_sql)) )
		{
			message_die(GENERAL_ERROR, sprintf($lang['lottery_error_selecting'], 'lottery history'), '', __LINE__, __FILE__, $sql);
		}
		$sql_count = $db->sql_numrows($result);

		if ( ($total = $db->sql_fetchrow($result)) && ($sql_count > 0) )
		{
			$total_history = $total['total'];

			if ( $total_history > $board_config['topics_per_page'] )
			{
				$pagination = generate_pagination("index.$phpEx?action=options&amp;view_history=$view_history&amp;view_personal=$view_personal", $total_history, $board_config['topics_per_page'], $start). '&nbsp;';
			}
			else
			{
				$pagination = '&nbsp;';
			}
		}

		$page_title = $board_config['lottery_name'];

		$next_location = ' -> <a href="' . append_sid("index.$phpEx") . '" class="nav">' . $board_config['lottery_name'] . '</a> -> <a href="' . append_sid("index.$phpEx") . '" class="nav">' . $board_config['lottery_name'] . ' ' . $lang['lottery_history'] . '</a>';

		$template->assign_vars(array(
			'L_HISTORY' => $lang['lottery_current_history'],
			'L_ID' => $lang['lottery_ID'],
			'L_WINNER' => $lang['lottery_winner'],
			'L_AMOUNT_WON' => $lang['lottery_amount_won'],
			'L_TIME_WON' => $lang['lottery_time_won'],
			'L_TOTAL_HISTORY' => sprintf($lang['lottery_total_history'], $total_history),

			'LOCATION' => $next_location,
			'PAGINATION' => $pagination,
			'PAGE_NUMBER' => sprintf($lang['Page_of'], ( floor( $start / $board_config['topics_per_page'] ) + 1 ), ceil( $total_history / $board_config['topics_per_page'] )), 
			'L_GOTO_PAGE' => $lang['Goto_page']
		));
		$template->assign_block_vars('', array());
	}
	else
	{
		redirect("index.$phpEx");
	}
}
else 
{
	message_die(GENERAL_MESSAGE, $lang['lottery_invalid_command']);
}

include($phpbb_root_path . 'includes/page_header.' . $phpEx);

$template->pparse('body');

include($phpbb_root_path . 'includes/page_tail.' . $phpEx);

?>