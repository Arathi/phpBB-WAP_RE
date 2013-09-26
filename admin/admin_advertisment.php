<?php
/**************************************************************
 *		admin_advertisment.php
 *		-------------------
 *   	Разработка и оптимизация под WAP: Гутник Игорь ( чел )
 *          2010 год
 *		简体中文：爱疯的云
 ***************************************************************/

define('IN_PHPBB', 1);

if( !empty($setmodules) )
{
	$file = basename(__FILE__);
	$module['General']['Advertisment'] = $file;
	return;
}

$phpbb_root_path = "./../";
require($phpbb_root_path . 'extension.inc');
require('./pagestart.' . $phpEx);
include($phpbb_root_path . 'includes/functions_selects.'.$phpEx);

$sql = "SELECT *
	FROM " . CONFIG_TABLE;
if(!$result = $db->sql_query($sql))
{
	message_die(CRITICAL_ERROR, "Could not query config information in admin_advertisment", "", __LINE__, __FILE__, $sql);
}
else
{
	while( $row = $db->sql_fetchrow($result) )
	{
		$config_name = $row['config_name'];
		$config_value = $row['config_value'];
		$default_config[$config_name] = isset($HTTP_POST_VARS['submit']) ? str_replace("'", "\'", $config_value) : $config_value;
		
		$new[$config_name] = ( isset($HTTP_POST_VARS[$config_name]) ) ? $HTTP_POST_VARS[$config_name] : $default_config[$config_name];

		if ($config_name == 'cookie_name')
		{
			$new['cookie_name'] = str_replace('.', '_', $new['cookie_name']);
		}

		if ($config_name == 'server_name')
		{
			$new['server_name'] = str_replace('http://', '', $new['server_name']);
		}

		if ($config_name == 'avatar_path')
		{
			$new['avatar_path'] = trim($new['avatar_path']);
			if (strstr($new['avatar_path'], "\0") || !is_dir($phpbb_root_path . $new['avatar_path']) || !is_writable($phpbb_root_path . $new['avatar_path']))
			{
				$new['avatar_path'] = $default_config['avatar_path'];
			}
		}

		if ($config_name == 'default_icq')
		{
			if (!preg_match('/^[0-9]+$/', $new['default_icq']))
			{
				$new['default_icq'] = '';
			}
		}

		if( isset($HTTP_POST_VARS['submit']) )
		{
			$sql = "UPDATE " . CONFIG_TABLE . " SET
				config_value = '" . str_replace("\'", "''", $new[$config_name]) . "'
				WHERE config_name = '$config_name'";
			if( !$db->sql_query($sql) )
			{
				message_die(GENERAL_ERROR, "Failed to update general configuration for $config_name", "", __LINE__, __FILE__, $sql);
			}
		}
	}

	if( isset($HTTP_POST_VARS['submit']) )
	{
		$message = "<br /><br />" . sprintf($lang['Click_return_config'], "<a href=\"" . append_sid("admin_advertisment.$phpEx") . "\">", "</a>") . "<br /><br />" . sprintf($lang['Click_return_admin_index'], "<a href=\"" . append_sid("index.$phpEx?pane=left") . "\">", "</a>");

		message_die(GENERAL_MESSAGE, $message);
	}
}

$template->set_filenames(array(
	"body" => "admin/admin_advertisment.tpl")
);

$template->assign_vars(array(
	"S_CONFIG_ACTION" 		=> append_sid("admin_advertisment.$phpEx"),
	"FORUMS_INDEX_TOP" 		=> $new['forums_index_top'],
	"FORUMS_INDEX_BUTTOM" 	=> $new['forums_index_bottom'],
	"FORUMS_OTHER_TOP" 		=> $new['forums_other_top'],
	"FORUMS_OTHER_BUTTOM" 	=> $new['forums_other_bottom'])
);

$template->pparse("body");

include('./page_footer_admin.'.$phpEx);

?>