<?php
/**************************************************
 *		chat.php
 *		----------
 *		copyright：phpBB Group
 *		Оптимизация под WAP: Гутник Игорь ( чел ).
 *		简体中文：爱疯的云
 *		说明：聊天室
 *************************************************/

define('IN_PHPBB', true);
$phpbb_root_path = './';
include($phpbb_root_path . 'extension.inc');
include($phpbb_root_path . 'common.'.$phpEx);
include($phpbb_root_path . 'includes/bbcode.'.$phpEx);
include($phpbb_root_path . 'includes/functions_selects.'.$phpEx);

define ('NUM_SHOUT', 20);

// 加载 session
$userdata = session_pagestart($user_ip, PAGE_SHOUTBOX_MAX);
init_userprefs($userdata);

// 禁止黑名单用户进入
$ban_information = session_userban($user_ip, $userdata['user_id']); 
if ($ban_information) 
{ 
	message_die(CRITICAL_MESSAGE, '注意：' . $ban_information); 
}

/**
if ( !$userdata['session_logged_in'] )
{
	$redirect = "chat.$phpEx";
	header('Location: ' . append_sid("login.$phpEx?redirect=$redirect", true));
}
**/

switch ($userdata['user_level'])
{
	case ADMIN : 
	case MOD :	$is_auth['auth_mod'] = 1;
	default:
			$is_auth['auth_read'] = 1;
			$is_auth['auth_view'] = 1;
			if ($userdata['user_id']==ANONYMOUS)
			{
				$is_auth['auth_delete'] = 0;
				$is_auth['auth_post'] = 0;
			} else
			{
				$is_auth['auth_delete'] = 1;
				$is_auth['auth_post'] = 1;
			}
}

if( !$is_auth['auth_read'] )
{
	message_die(GENERAL_MESSAGE, $lang['Not_Authorised']);
}

// 是否开启 bbcode
$bbcode_on = 1;
$forum_id = PAGE_SHOUTBOX_MAX;
$submit = (isset($HTTP_POST_VARS['submit']) && isset($HTTP_POST_VARS['message'])) ? 1 : 0;

if ( isset($HTTP_POST_VARS['mode']) || isset($HTTP_GET_VARS['mode']) )
{
	$mode = ( isset($HTTP_POST_VARS['mode']) ) ? $HTTP_POST_VARS['mode'] : $HTTP_GET_VARS['mode'];
}
else
{
	$mode = '';
}
if ( isset($HTTP_POST_VARS['start1']) )
{
	$start1 = abs(intval($HTTP_POST_VARS['start1']));
	$start = (($start1 - 1) * $board_config['posts_per_page']);
} else {
	$start = ( isset($HTTP_GET_VARS['start']) ) ? intval($HTTP_GET_VARS['start']) : 0;
	$start = ($start < 0) ? 0 : $start;
}

$message = (isset($HTTP_POST_VARS['message'])) ? trim($HTTP_POST_VARS['message']) : '';
$message .= (isset($HTTP_POST_VARS['smile_code'])) ? trim($HTTP_POST_VARS['smile_code']) : '';
$message = htmlspecialchars($message);

if ( (isset($HTTP_POST_VARS['submit']) && isset($HTTP_POST_VARS['message'])) && !empty($message) )
{
	$current_time = time();
	$where_sql = ( $userdata['user_id'] == ANONYMOUS ) ? "shout_ip = '$user_ip'" : 'shout_user_id = ' . $userdata['user_id'];
	$sql = "SELECT MAX(shout_session_time) AS last_post_time
		FROM " . SHOUTBOX_TABLE . "
		WHERE $where_sql";
	if ( $result = $db->sql_query($sql) )
	{
		if ( $row = $db->sql_fetchrow($result) )
		{
			if ( $row['last_post_time'] > 0 && ( $current_time - $row['last_post_time'] ) < $board_config['flood_interval'] )
			{
				$error = true;
				$error_msg .= ( !empty($error_msg) ) ? '<br />' . $lang['Flood_Error'] : $lang['Flood_Error'];
			}
		}
	}

	if (isset($HTTP_POST_VARS['submit']) && !empty($message) && $is_auth['auth_post'] && !$error)
	{
		require_once($phpbb_root_path . 'includes/functions_post.'.$phpEx);
		$bbcode_uid = ( $bbcode_on ) ? make_bbcode_uid() : '';
		$message = prepare_message(trim($message), $html_on, $bbcode_on, $smilies_on, $bbcode_uid);
		$sql = "INSERT INTO " . SHOUTBOX_TABLE. " (shout_text, shout_session_time, shout_user_id, shout_ip, shout_username, shout_bbcode_uid,enable_bbcode,enable_html,enable_smilies) 
				VALUES ('$message', '".time()."', '".$userdata['user_id']."', '$user_ip', '".phpbb_clean_username($userdata['username'])."', '".$bbcode_uid."',1,0,1)";
		if (!$result = $db->sql_query($sql)) 
		{
			message_die(GENERAL_ERROR, 'Error inserting shout.', '', __LINE__, __FILE__, $sql);
		}
		else
		{
			$URL = "chat.$phpEx";
			header ("Location: $URL");
		}
		
		if ($board_config['prune_shouts'])
		{
			$sql = "DELETE FROM " . SHOUTBOX_TABLE. " WHERE shout_session_time<=".(time()-86400*$board_config['prune_shouts']);
			if (!$result = $db->sql_query($sql)) 
			{
				message_die(GENERAL_ERROR, 'Error autoprune shouts.', '', __LINE__, __FILE__, $sql);
			}
		}
	}
} 
elseif ( $is_auth['auth_mod'] && $mode=='delete')
{
		if ( isset($HTTP_GET_VARS[POST_POST_URL]) || isset($HTTP_POST_VARS[POST_POST_URL]) )
	{
		$post_id = (isset($HTTP_POST_VARS[POST_POST_URL])) ? intval($HTTP_POST_VARS[POST_POST_URL]) : intval($HTTP_GET_VARS[POST_POST_URL]);
	}
	else
	{
		message_die(GENERAL_ERROR, 'Error no shout id specifyed for delete/censor.', '', __LINE__, __FILE__);
	}
	
		$sql = "DELETE FROM " . SHOUTBOX_TABLE." 
				WHERE shout_id = $post_id";
		if (!$result = $db->sql_query($sql)) 
		{
			message_die(GENERAL_ERROR, 'Error removing shout.', '', __LINE__, __FILE__, $sql);
		}
		else
		{
			$URL = "chat.$phpEx";
			header ("Location: $URL");
		}
} 
$page_title = '聊天室';
require_once($phpbb_root_path . 'includes/functions_post.'.$phpEx);
require_once($phpbb_root_path . 'includes/page_header.'.$phpEx);

$orig_word = array();
$replacement_word = array();
obtain_word_list($orig_word, $replacement_word);

$sql = "SELECT COUNT(*) as total 
		FROM " . SHOUTBOX_TABLE; 
if ( !($result = $db->sql_query($sql)) )
{
	message_die(GENERAL_ERROR, 'Could not get shoutbox stat information', '', __LINE__, __FILE__, $sql);
}
$total_shouts = $db->sql_fetchrow($result);
$total_shouts = $total_shouts['total'];
//KasP DETECTED

$template->set_filenames(array(
	'body' => 'chat_body.tpl')
);

	$pagination = ( $highlight_match ) ? generate_pagination("chat.$phpEx?", $total_shouts, $board_config['posts_per_page'], $start) : generate_pagination("chat.$phpEx?", $total_shouts, $board_config['posts_per_page'], $start);
	
	if(isset($_GET['id']))
	{
		$for = intval(abs($_GET['id']));
		
		$sql = "SELECT shout_username 
			FROM " . SHOUTBOX_TABLE . "
			WHERE shout_id = $for";
			
		$result = $db->sql_query($sql);
		if (!$result)
		{
			message_die(GENERAL_ERROR, '无法取得结果！');
		}
		$for_him = $db->sql_fetchrow($result);
		if(!empty($for_him['shout_username']))
		{
			$for_you = $for_him['shout_username'].',';
		}
		else
		{
			$for_you = '';
		}
	}
	
	$sql = "SELECT s.*, u.* FROM " . SHOUTBOX_TABLE . " s, " . USERS_TABLE . " u
		WHERE s.shout_user_id = u.user_id 
		ORDER BY s.shout_session_time DESC 
		LIMIT $start, " . $board_config['posts_per_page'];
	if ( !($result = $db->sql_query($sql)) )
	{
		message_die(GENERAL_ERROR, 'Could not get shoutbox information', '', __LINE__, __FILE__, $sql);
	}
		while ($shout_row = $db->sql_fetchrow($result))
		{
			$i++;
			$user_id = $shout_row['shout_user_id'];
			//$shout_username = ( $user_id == ANONYMOUS ) ? (( $shout_row['shout_username'] == '' ) ? $lang['Guest'] : $shout_row['shout_username'] ) : '<a href="#">' . $shout_row['username'] . '</a>';
			$shout_username = ( $user_id == ANONYMOUS ) ? (( $shout_row['shout_username'] == '' ) ? $lang['Guest'] : $shout_row['shout_username'] ) : '<a href="' . append_sid("profile.$phpEx?mode=viewprofile&amp;" . 'u=' . $shout_row['user_id']) . '">' . $shout_row['username'] . '</a>';
			$shout = (! $shout_row['shout_active']) ? $shout_row['shout_text'] : $lang['Shout_censor'].(($is_auth['auth_mod']) ? '<br/><hr/><br/>'.$shout_row['shout_text'] : '');
			$message = make_clickable($message);
			$shout = smilies_pass($shout);
			$shout = bbencode_second_pass($shout,$shout_row['shout_bbcode_uid']);
			$shout = str_replace("\n", "\n<br />\n", $shout);

		if ( $is_auth['auth_mod'] && $is_auth['auth_delete'])
	{
		$temp_url = append_sid("chat.$phpEx?mode=delete&amp;" . POST_POST_URL . "=" . $shout_row['shout_id']);
		$delshout = '<a href="' . $temp_url . '">' . $lang['Delete'] . '</a>';
	}
	
	if( $userdata['user_on_off'] == 1)
	{
		if ($shout_row['user_session_time'] >= (time()-$board_config['online_time']))
		{
			if ($shout_row['user_allow_viewonline'])
			{
				$online_status = '<span' . $online_color . '>' . $lang['Online'] . '</span>';
			}
			else if ( $is_auth['auth_mod'] || $userdata['user_id'] == $poster_id )
			{
				$online_status = '<span' . $hidden_color . '>' . $lang['Hidden'] . '</span>';
			}
			else
			{
				$online_status = '<span' . $offline_color . '>' . $lang['Offline'] . '</span>';
			}
		}
		else
		{
			$online_status = '<span' . $offline_color . '>' . $lang['Offline'] . '</span>';
		}
	}
	else 
	{
		$online_status = '';
	}
	
		$template->assign_block_vars('shoutrow', array(
			'SHOUT' 				=> $shout,
			'TIME' 					=> create_date($board_config['default_dateformat'], $shout_row['shout_session_time'], $board_config['board_timezone']),
			'SHOUT_USERNAME' 		=> $shout_username,
			'U_VIEW_USER_PROFILE' 	=> $user_profile,
		    'POSTER_ONLINE_STATUS' 	=> $online_status,
			'DELETE' 				=> $delshout, 
			'U_SHOUT_ID' 			=> $shout_row['shout_id'])
		);
	}
		
		$update = append_sid("chat.$phpEx?up" . time());
		$smiles_select = smiles_select();
		
		$template->assign_vars(array( 
			'U_LOGIN'		=> append_sid("login.$phpEx?redirect=chat.$phpEx"),
			'SMILES_SELECT'	=> $smiles_select,
			'PAGINATION' 	=> $pagination,
			'U_SHOUTBOX' 	=> append_sid("chat.$phpEx?"),
			'OTVET' 		=> $for_you,
			'UPDATE' 		=> $update)
		);

	if( $error_msg != '' )
	{
		$template->set_filenames(array(
			'reg_header' 	=> 'error_body.tpl')
		);
		$template->assign_vars(array(
			'ERROR_MESSAGE' => $error_msg)
		);
		$template->assign_var_from_handle('ERROR_BOX', 'reg_header');
	}

$template->pparse('body');  
include($phpbb_root_path . 'includes/page_tail.'.$phpEx);

?>