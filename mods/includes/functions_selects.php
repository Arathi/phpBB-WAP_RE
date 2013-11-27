<?php
if (!defined('IN_PHPBB'))
{
	die('Hacking attempt');
}

/**
* 专题
**/
function special_select($forum_id, $topic_id, $default_special)
{
	global $db, $phpEx;
	
	$sql = "SELECT special_id, special_name
		FROM " . SPECIAL_TABLE . " 
		WHERE special_forum = $forum_id";
	if ( !($result = $db->sql_query($sql)) )
	{
		message_die(GENERAL_ERROR, '读取专题信息失败！', '', __LINE__, __FILE__, $sql);
	}
	
	$special_select = '<form action="' . append_sid("{$phpbb_root_path}mods/special/select.$phpEx?" . POST_TOPIC_URL . "=$topic_id") . '" method="post" name="post">';
	$special_select .= '管理专题：';
	$special_select .= '<select name="id">';
	$special_select .= '<option value="0">不指定专题</option>';
	while ( $row = $db->sql_fetchrow($result) )
	{
		$selected = ( $row['special_id'] == $default_special ) ? ' selected="selected"' : '';
		$special_select .= '<option value="' . $row['special_id'] . '"' . $selected . '>' . $row['special_name'] . '</option>';
	}
	$special_select .= '</select>';
	$special_select .= '<input type="submit" name="submit" value="设置" />';
	$special_select .= '</form>';

	return $special_select;
}

?>