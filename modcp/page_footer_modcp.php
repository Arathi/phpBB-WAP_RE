<?php
/***************************************************************************
 *			page_footer_modcp.php
 *			----------
 *		copyright：phpBB Group
 *		Оптимизация под WAP: Гутник Игорь ( чел ).
 *		中文网站：http://phpbb-wap.com
 *		本程序的汉化修改作者：张云
 *		ＱＱ联系：53109774 ( 爱疯的云 ).
 *		电子邮件：zhangyunwap@qq.com
 *		最后修改时间：2011-10-01
 *		对该文件的说明：页尾信息( modcp )
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

global $do_gzip_compress;

//
// Show the overall footer.
//
$template->set_filenames(array(
	'page_footer' => 'modcp/page_footer.tpl')
);

$template->assign_vars(array(
	'PHPBB_VERSION' => ($userdata['user_level'] == ADMIN && $userdata['user_id'] != ANONYMOUS) ? '' . $board_config['version'] : '',
	'TRANSLATION_INFO' => (isset($lang['TRANSLATION_INFO'])) ? $lang['TRANSLATION_INFO'] : ((isset($lang['TRANSLATION'])) ? $lang['TRANSLATION'] : ''))
);

$template->pparse('page_footer');

//
// Close our DB connection.
//
$db->sql_close();

//
// Compress buffered output if required
// and send to browser
//
if( $do_gzip_compress )
{
	//
	// Borrowed from php.net!
	//
	$gzip_contents = ob_get_contents();
	ob_end_clean();

	$gzip_size = strlen($gzip_contents);
	$gzip_crc = crc32($gzip_contents);

	$gzip_contents = gzcompress($gzip_contents, 9);
	$gzip_contents = substr($gzip_contents, 0, strlen($gzip_contents) - 4);

	echo "\x1f\x8b\x08\x00\x00\x00\x00\x00";
	echo $gzip_contents;
	echo pack('V', $gzip_crc);
	echo pack('V', $gzip_size);
}

exit;

?>