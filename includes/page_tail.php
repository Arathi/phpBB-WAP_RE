<?php
/*************************************************
 *		page_tail.php
 *		-------------------
 *      Разработка: phpBB Group.
 *      Оптимизация под WAP: Гутник Игорь ( чел ).
 *          2010 год
 *		简体中文：爱疯的云
 *************************************************/

if ( !defined('IN_PHPBB') )
{
	die('Hacking attempt');
}

global $do_gzip_compress;

$advertisment_foot = ($this_file == 'index.php') ? $board_config['forums_index_bottom'] : $board_config['forums_other_bottom'];

$template->set_filenames(array(
	'overall_footer' => 'overall_footer.tpl')
);

$template->assign_vars(array(
    'PHPBB_VERSION' => $board_config['version'],
	'ADVERTISMENT_FOOT' => $advertisment_foot)
);

$template->pparse('overall_footer');

$db->sql_close();

if ( $do_gzip_compress )
{
	
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