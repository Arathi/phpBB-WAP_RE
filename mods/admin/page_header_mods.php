<?php
/**************************************************
 *		page_header_mods.php
 *		-------------------
 *      Разработка: phpBB Group.
 *      Оптимизация под WAP: Гутник Игорь ( чел ).
 *          2011 год
 *		简体中文：爱疯的云
 ***************************************************/

if ( !defined('IN_PHPBB') )
{
	die("Hacking attempt");
}

define('HEADER_INC', true);

$do_gzip_compress = FALSE;
if ( $board_config['gzip_compress'] )
{
	$phpver = phpversion();

	$useragent = (isset($HTTP_SERVER_VARS['HTTP_USER_AGENT'])) ? $HTTP_SERVER_VARS['HTTP_USER_AGENT'] : getenv('HTTP_USER_AGENT');

	if ( $phpver >= '4.0.4pl1' && ( strstr($useragent,'compatible') || strstr($useragent,'Gecko') ) )
	{
		if ( extension_loaded('zlib') )
		{
			ob_start('ob_gzhandler');
		}
	}
	else if ( $phpver > '4.0' )
	{
		if ( strstr($HTTP_SERVER_VARS['HTTP_ACCEPT_ENCODING'], 'gzip') )
		{
			if ( extension_loaded('zlib') )
			{
				$do_gzip_compress = TRUE;
				ob_start();
				ob_implicit_flush(0);

				header('Content-Encoding: gzip');
			}
		}
	}
}

$template->set_filenames(array(
	'header' => 'mods/admin/mods_header.tpl')
);

$l_timezone = explode('.', $board_config['board_timezone']);
$l_timezone = (count($l_timezone) > 1 && $l_timezone[count($l_timezone)-1] != 0) ? $lang[sprintf('%.1f', $board_config['board_timezone'])] : $lang[number_format($board_config['board_timezone'])];

$template->assign_vars(array(
	'SITENAME' 			=> $board_config['sitename'],
	'PAGE_TITLE' 		=> $page_title,

	'L_ADMIN' 			=> $lang['Admin'], 
	'L_INDEX' 			=> $lang['Index'],
	'L_MODS_ADMIN'		=> $lang['Admin_Index_Left'],
	
	'U_ADMIN'			=> append_sid("{$phpbb_root_path}admin/index.$phpEx"),
	'U_INDEX' 			=> append_sid("{$phpbb_root_path}index.$phpEx"),
	'U_MODS_ADMIN'		=> append_sid("{$phpbb_root_path}mods/admin/index.$phpEx"),
	
	'T_ROOT_PATH'		=> $phpbb_root_path,

	'S_TIMEZONE' 			=> sprintf($lang['All_times'], $l_timezone),
	'S_LOGIN_ACTION' 		=> append_sid("{$phpbb_root_path}login.$phpEx"),
	'S_CURRENT_TIME' 		=> sprintf($lang['Current_time'], create_date($board_config['default_dateformat'], time(), $board_config['board_timezone'])))
);

if (!empty($HTTP_SERVER_VARS['SERVER_SOFTWARE']) && strstr($HTTP_SERVER_VARS['SERVER_SOFTWARE'], 'Apache/2'))
{
	header ('Cache-Control: no-cache, pre-check=0, post-check=0');
}
else
{
	header ('Cache-Control: private, pre-check=0, post-check=0, max-age=0');
}
header ('Expires: 0');
header ('Pragma: no-cache');

$template->pparse('header');

?>
