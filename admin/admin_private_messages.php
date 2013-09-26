<?php
/**************************************************************
 *		admin_private_messages.php
 *		-------------------
 *  	Разработка и оптимизация под WAP: Гутник Игорь ( чел )
 *			2010 год
 *		简体中文：爱疯的云
***************************************************************/

define('IN_PHPBB', true);
if( !empty($setmodules) )
{
	$filename = basename(__FILE__);
	$module['Users']['Private_Messages'] = $filename;
	
	return;
}
$no_page_header = TRUE;
$phpbb_root_path = './../';
require($phpbb_root_path . 'extension.inc');
require('./pagestart.' . $phpEx);
include($phpbb_root_path . 'includes/bbcode.'.$phpEx);
include('./page_header_admin.'.$phpEx);

if ( isset($HTTP_POST_VARS['start1']) )
{
$start1 = abs(intval($HTTP_POST_VARS['start1']));
$start = (($start1 - 1) * $board_config['topics_per_page']);
} else {
$start = ( isset($HTTP_GET_VARS['start']) ) ? intval($HTTP_GET_VARS['start']) : 0;
$start = ($start < 0) ? 0 : $start;
}
$users_per_page = 25;

$template->set_filenames(array(
	'body' => 'admin/admin_priv_mess.tpl')
);

$sql = "SELECT COUNT(privmsgs_id) as total FROM " . PRIVMSGS_TABLE;
if ( !($result = $db->sql_query($sql)))
{
	message_die(GENERAL_ERROR, 'Could not query private message information', '', __LINE__, __FILE__, $sql);
}
$pm_count = $db->sql_fetchrow($result);
$pagination ='';

if ($pm_count['total'] > $board_config['posts_per_page'])
{
	$pagination = generate_pagination("admin_private_messages.$phpEx?", $pm_count['total'], $board_config['posts_per_page'], $start);
}

$template->assign_vars(array(
	'L_PRIVMSG' 	=> $lang['Private_Message'],
	'L_PRIVMSGS' 	=> $lang['Private_Messages'],
	'L_FROM' 		=> $lang['From'],
	'L_TO' 			=> $lang['To'],
	'L_DATE' 		=> $lang['Date'],
	'L_IP' 			=> $lang['IP_Address'],
	'L_SUBJ' 		=> $lang['Subject'],
	'PAGINATION' 	=> $pagination
	)
);

$sql = "SELECT u.username AS username_1, u.user_id AS user_id_1, u2.username AS username_2, u2.user_id AS user_id_2, pm.privmsgs_type, pm.privmsgs_subject, pm.privmsgs_date, pm.privmsgs_ip, pmt.privmsgs_bbcode_uid, pmt.privmsgs_text
		FROM " . PRIVMSGS_TABLE . " pm, " . PRIVMSGS_TEXT_TABLE . " pmt, " . USERS_TABLE . " u, " . USERS_TABLE . " u2 
		WHERE pmt.privmsgs_text_id = pm.privmsgs_id
			AND u.user_id = pm.privmsgs_from_userid 
			AND u2.user_id = pm.privmsgs_to_userid
		ORDER BY pm.privmsgs_date DESC
		LIMIT " . $start . ", " . $board_config['posts_per_page'];
if ( !($result = $db->sql_query($sql)))
{
	message_die(GENERAL_ERROR, 'Could not query private message information', '', __LINE__, __FILE__, $sql);
}
while ($pm_text = $db->sql_fetchrow($result))
{
	if ($pm_text['privmsgs_type'] == PRIVMSGS_READ_MAIL)
	{
		$privmsgs_type = $lang['Read_message'];
	}
	elseif ($pm_text['privmsgs_type'] == PRIVMSGS_NEW_MAIL)
	{
		$privmsgs_type = $lang['Unread_message'];
	}
	elseif ($pm_text['privmsgs_type'] == PRIVMSGS_SENT_MAIL)
	{
		$privmsgs_type = $lang['Sent'];
	}
	elseif ($pm_text['privmsgs_type'] == PRIVMSGS_SAVED_IN_MAIL)
	{
		$privmsgs_type = $lang['Saved'];
	}
	elseif ($pm_text['privmsgs_type'] == PRIVMSGS_SAVED_OUT_MAIL)
	{
		$privmsgs_type = $lang['Saved'];
	}
	elseif ($pm_text['privmsgs_type'] == PRIVMSGS_UNREAD_MAIL)
	{
		$privmsgs_type = $lang['Unread_message'];
	}
	else
	{
		$privmsgs_type = '';
	}

	$number = $i + 1 + $start;
	$row_class = ( !($i % 2) ) ? 'row_easy' : 'row_hard';
	
	$template->assign_block_vars('pmrow', array(
		'L_MUNBER'	=> $number,
		'ROW_CLASS'	=> $row_class,
		'FROM_URL' 	=> append_sid($phpbb_root_path . "profile.$phpEx?mode=viewprofile&amp;" . POST_USERS_URL . "=" . $pm_text['user_id_1']),
		'TO_URL' 	=> append_sid($phpbb_root_path . "profile.$phpEx?mode=viewprofile&amp;" . POST_USERS_URL . "=" . $pm_text['user_id_2']),
		'FROM' 		=> $pm_text['username_1'],
		'TO' 		=> $pm_text['username_2'],
		'DATE' 		=> create_date($board_config['default_dateformat'], $pm_text['privmsgs_date'], $board_config['board_timezone']),
		'IP' 		=> decode_ip($pm_text['privmsgs_ip']),
		'SUBJ' 		=>  $pm_text['privmsgs_subject'],
		'TYPE' 		=>  $privmsgs_type,
		'MESSAGE' 	=> bbencode_second_pass($pm_text['privmsgs_text'], $pm_text['privmsgs_bbcode_uid']))
	);
	$i++;
}

$template->pparse('body');
include('./page_footer_admin.'.$phpEx);
?>