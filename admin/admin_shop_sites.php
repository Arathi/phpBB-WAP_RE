<?php
/*************************************************************
 *		admin_shop_sites.php
 *		-------------------
 *   	Разработка и оптимизация под WAP: Гутник Игорь ( чел )
 *          2010 год
 *		简体中文：爱疯的云
 *************************************************************/

define('IN_PHPBB', true);

if( !empty($setmodules) )
{
	$file = basename(__FILE__);
	$module['Shop']['Shop_sites'] = $file;
	return;
}

$phpbb_root_path = './../';
require($phpbb_root_path . 'extension.inc');
require('./pagestart.' . $phpEx);

$template->set_filenames(array(
	'body' => 'admin/admin_shop_sites.tpl')
);

if ( isset($HTTP_GET_VARS['delete']) )
{
	$id = intval($HTTP_GET_VARS['id']);

	$sql = "DELETE FROM ".$table_prefix."shop_sites
		WHERE id = '$id'";
	if ( !$result = $db->sql_query($sql) )
	{
		message_die(GENERAL_MESSAGE, '无法删除链接！<br /><br />点击 <a href="' . append_sid("admin_shop_sites.$phpEx") . '">这里</a> 返回上一页面！');
	}
	message_die(GENERAL_MESSAGE, '已成功删除链接！<br /><br />点击 <a href="' . append_sid("admin_shop_sites.$phpEx") . '">这里</a> 返回上一页面！');
} 
else 
{

	$sql = "SELECT * 
		FROM ".$table_prefix."shop_sites
		ORDER BY id ASC";
	if( !($result = $db->sql_query($sql)) )
	{
		message_die(GENERAL_ERROR, 'Could not query users', '', __LINE__, __FILE__, $sql);
	}

	if ( $row = $db->sql_fetchrow($result) )
	{
		$i = 0;
		do
		{
			$number = $i + 1;
			$row_class = ( !($i % 2) ) ? 'row_easy' : 'row_hard';
			$url = $row['site_url'];
			$nazvanie = $row['site_desc'];
			$time = create_date($lang['DATE_FORMAT'], $row['site_time'], $board_config['board_timezone']);
			$id = $row['id'];

			$template->assign_block_vars('memberrow', array(
				'ROW_CLASS'	=> $row_class,
				'NUMBER'	=> $number,
				'URL' 		=> $url,
				'NAZVANIE' 	=> $nazvanie,
				'TIME' 		=> $time,
				'U_DEL' 	=> append_sid("admin_shop_sites.$phpEx?delete&amp;id=$id"))
			);

			$i++;
		}
		while ( $row = $db->sql_fetchrow($result) );
		$db->sql_freeresult($result);
	} else {
		$template->assign_block_vars('no_pay', array() );
	}

	$template->pparse('body');
}
include('./page_footer_admin.'.$phpEx);

?>