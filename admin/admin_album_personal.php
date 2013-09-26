<?php
/****************************************
 *		admin_album_personal.php
 *		-------------------
 *   	Разработка: (C) 2003 Smartor
 *   	Модификация: Гутник Игорь ( чел )
 *		简体中文：爱疯的云
 ****************************************/

define('IN_PHPBB', true);

if( !empty($setmodules) )
{
	$filename = basename(__FILE__);
	$module['Photo_Album']['Personal_Galleries'] = $filename;
	return;
}

$phpbb_root_path = '../';
require($phpbb_root_path . 'extension.inc');
require('./pagestart.' . $phpEx);
require($phpbb_root_path . 'language/lang_' . $board_config['default_lang'] . '/lang_main_album.' . $phpEx);
require($phpbb_root_path . 'language/lang_' . $board_config['default_lang'] . '/lang_admin_album.' . $phpEx);

if( !isset($HTTP_POST_VARS['submit']) )
{
	$template->set_filenames(array(
		'body' => 'admin/album_personal_body.tpl')
	);

	$sql = "SELECT group_id, group_name
			FROM " . GROUPS_TABLE . "
			WHERE group_single_user <> " . TRUE ."
			ORDER BY group_name ASC";
	if ( !($result = $db->sql_query($sql)) )
	{
		message_die(GENERAL_ERROR, "Couldn't get group list", "", __LINE__, __FILE__, $sql);
	}

	while( $row = $db->sql_fetchrow($result) )
	{
		$groupdata[] = $row;
	}

	$sql = "SELECT *
			FROM ". ALBUM_CONFIG_TABLE ."
			WHERE config_name = 'personal_gallery_private'";
	if ( !($result = $db->sql_query($sql)) )
	{
		message_die(GENERAL_ERROR, "Couldn't get Album info", "", __LINE__, __FILE__, $sql);
	}
	$row = $db->sql_fetchrow($result);
	$private_groups = explode(',', $row['config_value']);

	for($i = 0; $i < count($groupdata); $i++)
	{
		$template->assign_block_vars('grouprow', array(
			'GROUP_ID' => $groupdata[$i]['group_id'],
			'GROUP_NAME' => $groupdata[$i]['group_name'],
			'PRIVATE_CHECKED' => (in_array($groupdata[$i]['group_id'], $private_groups)) ? 'checked="checked"' : ''
			)
		);
	}

	$template->assign_vars(array(
		'L_ALBUM_PERSONAL_TITLE' => $lang['Album_personal_gallery_title'],
		'L_ALBUM_PERSONAL_EXPLAIN' => $lang['Album_personal_gallery_explain'],
		'L_SUBMIT' => $lang['Submit'],
		'L_RESET' => $lang['Reset'],
		'L_GROUP_CONTROL' => $lang['Auth_Control_Group'],
		'L_GROUPS' => $lang['Usergroups'],
		'L_PRIVATE_ACCESS' => $lang['Private_access'],
		'S_ALBUM_ACTION' => append_sid('admin_album_personal.'.$phpEx)
		)
	);

	$template->pparse('body');

	include('./page_footer_admin.'.$phpEx);
}
else
{
	$private_groups = @implode(',', $HTTP_POST_VARS['private']);

	$sql = "UPDATE ". ALBUM_CONFIG_TABLE ."
			SET config_value = '$private_groups'
			WHERE config_name = 'personal_gallery_private'";
	if ( !($result = $db->sql_query($sql)) )
	{
		message_die(GENERAL_ERROR, 'Could not update Album config table', '', __LINE__, __FILE__, $sql);
	}

	$message = $lang['Album_personal_successfully'] . '<br /><br />' . sprintf($lang['Click_return_album_personal'], '<a href="' . append_sid("admin_album_personal.$phpEx") . '">', '</a>') . '<br /><br />' . sprintf($lang['Click_return_admin_index'], '<a href="' . append_sid("index.$phpEx?pane=right") . '">', '</a>');

	message_die(GENERAL_MESSAGE, $message);
}

?>