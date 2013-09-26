<?php
/*******************************************
 *		album_common.php
 *		-------------------
 *   	Разработка: (C) 2003 Smartor
 *   	Модификация: Гутник Игорь ( чел )
 *******************************************/

if ( !defined('IN_PHPBB') )
{
	die('Hacking attempt');
}

$language = $board_config['default_lang'];

if ( !file_exists($phpbb_root_path . 'language/lang_' . $language . '/lang_main_album.'.$phpEx) )
{
	$language = 'english';
}

include($phpbb_root_path . 'language/lang_' . $language . '/lang_main_album.' . $phpEx);

$sql = "SELECT *
		FROM ". ALBUM_CONFIG_TABLE;
if(!$result = $db->sql_query($sql))
{
	message_die(GENERAL_ERROR, "Could not query Album config information", "", __LINE__, __FILE__, $sql);
}
while( $row = $db->sql_fetchrow($result) )
{
	$album_config_name = $row['config_name'];
	$album_config_value = $row['config_value'];
	$album_config[$album_config_name] = $album_config_value;
}

$template->assign_vars(array(
	'ALBUM_VERSION' => '2' . $album_config['album_version']
	)
);

include($album_root_path . 'album_functions.' . $phpEx);

?>