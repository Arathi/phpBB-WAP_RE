<?php
/***************************************************************************
 *                          admin_album_clearcache.php
 *                             -------------------
 *   Разработка: (C) 2003 Smartor
 *   Модификация: Гутник Игорь ( чел )
 ***************************************************************************/

define('IN_PHPBB', true);

if( !empty($setmodules) )
{
	$filename = basename(__FILE__);
	$module['Photo_Album']['Clear_Cache'] = $filename;
	return;
}

$phpbb_root_path = '../';
require($phpbb_root_path . 'extension.inc');
require('./pagestart.' . $phpEx);
require($phpbb_root_path . 'language/lang_' . $board_config['default_lang'] . '/lang_main_album.' . $phpEx);
require($phpbb_root_path . 'language/lang_' . $board_config['default_lang'] . '/lang_admin_album.' . $phpEx);


if( !isset($HTTP_POST_VARS['confirm']) )
{
	$template->set_filenames(array(
		'body' => 'confirm_body.tpl')
	);

	$template->assign_vars(array(
		'MESSAGE_TITLE' => $lang['Confirm'],

		'MESSAGE_TEXT' => $lang['Album_clear_cache_confirm'],

		'L_NO' => $lang['No'],
		'L_YES' => $lang['Yes'],

		'S_CONFIRM_ACTION' => append_sid("admin_album_clearcache.$phpEx"),
		)
	);

	$template->pparse('body');

	include('./page_footer_admin.'.$phpEx);
}
else
{
	$cache_dir = @opendir('../' . ALBUM_CACHE_PATH);

	while( $cache_file = @readdir($cache_dir) )
	{
		if( preg_match('/(\.gif$|\.png$|\.jpg|\.jpeg)$/is', $cache_file) )
		{
			@unlink('../' . ALBUM_CACHE_PATH . $cache_file);
		}
	}

	@closedir($cache_dir);

	message_die(GENERAL_MESSAGE, $lang['Thumbnail_cache_cleared_successfully']);
}

?>