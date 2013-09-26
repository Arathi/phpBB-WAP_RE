<?php
/***************************************************************************
 *			page_header_modcp.php
 *			----------
 *		copyright：phpBB Group
 *		Оптимизация под WAP: Гутник Игорь ( чел ).
 *		中文网站：http://phpbb-wap.com
 *		本程序的汉化修改作者：张云
 *		ＱＱ联系：53109774 ( 爱疯的云 ).
 *		电子邮件：zhangyunwap@qq.com
 *		最后修改时间：2011-10-01
 *		对该文件的说明：头部信息( modcp )
 ***************************************************************************/
/***************************************************************************
 *	本程序为自由软件
 *	您可依据自由软件基金会所发表的GNU通用公共授权条款规定，就本程序再为散布与／或修改
 *	无论您依据的是本授权的第二版或（您自行选择的）任一日后发行的版本
 *	本程序系基于使用目的而加以散布，然而不负任何担保责任
 *	亦无对适售性或特定目的适用性所为的默示性担保
 *	详情请参照GNU通用公共授权
 ***************************************************************************/

if ( !defined('IN_PHPBB') )
{
	die("Hacking attempt");
}

define('HEADER_INC', true);

//
// gzip_compression
//
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
	'header' => 'modcp/page_header.tpl')
);

$l_timezone = explode('.', $board_config['board_timezone']);
$l_timezone = (count($l_timezone) > 1 && $l_timezone[count($l_timezone)-1] != 0) ? $lang[sprintf('%.1f', $board_config['board_timezone'])] : $lang[number_format($board_config['board_timezone'])];

$template->assign_vars(array(
	'SITENAME' => $board_config['sitename'],
	'PAGE_TITLE' => $page_title,

	'L_MODCP' => $lang['Modcp'], 
	'L_INDEX' => $lang['Index'],
	'L_FAQ' => $lang['FAQ'],

	'U_MODCP' => append_sid('index.'.$phpEx),
	'U_INDEX' => append_sid('../index.'.$phpEx),
	'T_ROOT_PATH' => $phpbb_root_path,

	'S_TIMEZONE' => sprintf($lang['All_times'], $l_timezone),
	'S_LOGIN_ACTION' => append_sid('../login.'.$phpEx),
	'S_JUMPBOX_ACTION' => append_sid('../viewforum.'.$phpEx),
	'S_CURRENT_TIME' => sprintf($lang['Current_time'], create_date($board_config['default_dateformat'], time(), $board_config['board_timezone'])),
	'S_CONTENT_DIRECTION' => $lang['DIRECTION'],
	'S_CONTENT_ENCODING' => $lang['ENCODING'],
	'S_CONTENT_DIR_LEFT' => $lang['LEFT'],
	'S_CONTENT_DIR_RIGHT' => $lang['RIGHT'])
);


$template->pparse('header');

?>