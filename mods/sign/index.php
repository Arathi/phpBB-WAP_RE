<?php
/************************************************
 *			index.php
 *			---------
 *		作者：(http://zisuw.com)爱疯的云[ID:888]
 *		文件说明：phpBB-WAP签到MOD
 ************************************************/

define('IN_PHPBB', true);
$phpbb_root_path = './../../';
include($phpbb_root_path . 'extension.inc');
include($phpbb_root_path . 'common.'.$phpEx);
include($phpbb_root_path . 'includes/bbcode.'.$phpEx);
include($phpbb_root_path . 'includes/functions_selects.'.$phpEx);

$userdata = session_pagestart($user_ip, PAGE_SIGN);
init_userprefs($userdata);

// 验证用户是否登录
if ( !$userdata['session_logged_in'] )
{
	redirect(append_sid("login.$phpEx?redirect=mods/sign/index.$phpEx", true));
	exit;
}

$ban_information = session_userban($user_ip, $userdata['user_id']);

if ($ban_information)
{
	$ban = '<div style="border: 1px solid #d4d6d4;background:#ffffa0;color:#494949;margin-bottom:4px;border-radius:6px;padding:5px"><span style="color: red; font-weight: bold;">注意！</span><br/>您的'.$ban_information.'</div>';
}
else
{
	$ban = '';
}

if ( isset($HTTP_POST_VARS['start1']) )
{
	$start1 = abs(intval($HTTP_POST_VARS['start1']));
	$start1 = ($start1 < 1) ? 1 : $start1;
	$start = (($start1 - 1) * $board_config['posts_per_page']);
}
else
{
	$start = ( isset($HTTP_GET_VARS['start']) ) ? intval($HTTP_GET_VARS['start']) : 0;
	$start = ($start < 0) ? 0 : $start;
}

$sql = "SELECT sign_id 
	FROM phpbb_sign";
	
if ( !$result = $db->sql_query($sql) )
{
	message_die(GENERAL_ERROR, '数据查询失败', '', __LINE__, __FILE__, $sql);
}

$total_all_sign = $db->sql_numrows($result);

$pagination = ( $total_all_sign <= 0 ) ? '' : generate_pagination("index.$phpEx?sign=all", $total_all_sign, $board_config['posts_per_page'], $start);

$sql = "SELECT s.*, u.user_id, u.username
	FROM phpbb_sign s, phpbb_users u
	WHERE u.user_id = s.sign_user_id
	ORDER BY s.sign_id DESC
	LIMIT $start, " . $board_config['posts_per_page'];

if ( !$result = $db->sql_query($sql) )
{
	message_die(GENERAL_ERROR, '数据查询失败', '', __LINE__, __FILE__, $sql);
}

$sign_rows = array();

while ($row = $db->sql_fetchrow($result))
{
	$sign_rows[] = $row;
}

$total_sign_rows = count($sign_rows);

for($i = 0; $i < $total_sign_rows; $i++)
{
	$number = $i + 1 + $start;
	$row_class = ( !($i % 2) ) ? 'row_easy' : 'row_hard';
	$template->assign_block_vars('sign_rows', array(
		'NUMBER'				=> $number,
		'ROW_CLASS' 			=> $row_class,
		'SIGN_USERNAME' 		=> $sign_rows[$i]['username'],
		'U_SIGN_VIEWPROFILE' 	=> append_sid("{$phpbb_root_path}profile.php?mode=viewprofile&u=" . $sign_rows[$i]['sign_user_id']),
		'SIGN_TIME' 			=> create_date($userdata['user_dateformat'], $sign_rows[$i]['sign_time'], $board_config['board_timezone']),
		'SIGN_TALK' 			=> smilies_pass($sign_rows[$i]['sign_talk']))
	);
}
date_default_timezone_set('PRC');
$lingchen = strtotime('today'); 
$sql = "SELECT * 
   FROM phpbb_sign 
   WHERE sign_user_id = " . $userdata['user_id'] . " 
      AND sign_time > $lingchen";

if ( !$result = $db->sql_query($sql) )
{
	message_die(GENERAL_ERROR, '数据查询失败', '', __LINE__, __FILE__, $sql);
}

if ( $db->sql_numrows($result) >= 1 )
{
	$template->assign_block_vars('switch_yes_sign', array());
}
else
{
	
	$confirm = ( isset($HTTP_POST_VARS['confirm']) ) ? true : false;
	$user_id = $userdata['user_id'];
	$time = date(time());
	$talk = ( isset($HTTP_POST_VARS['smile_code']) ) ? htmlspecialchars($HTTP_POST_VARS['smile_code']) : '';
	$talk .= ( isset($HTTP_POST_VARS['talk']) ) ? htmlspecialchars($HTTP_POST_VARS['talk']) : false;
	if ( $confirm )
	{
		if ( $user_id && $time && $talk )
		{
			$sql = "INSERT INTO phpbb_sign (sign_user_id, sign_time, sign_talk) 
				VALUES (" . str_replace("\'", "''", $user_id) . ", " . str_replace("\'", "''", $time) . ", '" . str_replace("\'", "''", $talk) . "')";
			if ( !($result = $db->sql_query($sql)) )
			{
				message_die(GENERAL_ERROR, '无法执行SQL查询', '', __LINE__, __FILE__, $sql);
			}
			$sql = "UPDATE " . USERS_TABLE . "
				SET user_points = user_points + 10
				WHERE user_id = " . $user_id;
			if ( !($result = $db->sql_query($sql)) )
			{
				message_die(GENERAL_ERROR, '无法执行SQL查询', '', __LINE__, __FILE__, $sql);
			}
			$message = '签到成功！<br /><br />点击 <a href="' . append_sid("index.$phpEx") . '">这里</a> 签到页面！<br /><br />' . sprintf('点击 %s这里%s 返回首页！', '<a href="' . append_sid("{$phpbb_root_path}index.$phpEx") . '">', '</a>');
			message_die(GENERAL_MESSAGE, $message);
		}
		else
		{
			message_die(GENERAL_MESSAGE, '请指定内容和选项！');
		}
	}
	
	$template->assign_block_vars('switch_no_sign', array());
}

$page_title = '签到';
include($phpbb_root_path . 'includes/page_header.'.$phpEx);

$template->set_filenames(array(
	'body' => 'mods/sign_body.tpl')
);

$hidden_fields = '<input type="hidden" name="userid" value="' . $userdata['user_id'] . '" /><input type="hidden" name="time" value="' . date(time()) . '" /><input type="hidden" name="confirm" value="yes" />';
$smiles_select = smiles_select();

$template->assign_vars(array(
	'SIGN_USERNAME'				=> $userdata['username'],
	'S_PROFILE_ACTION'			=> append_sid("index.$phpEx"),
	'PAGINATION'				=> $pagination,
	'SMILES_SELECT' 			=> $smiles_select,
	'S_HIDDEN_FORM_FIELDS' 		=> $hidden_fields)
);

$template->pparse('body');
include($phpbb_root_path . 'includes/page_tail.'.$phpEx);
?>