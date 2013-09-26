<?php
/*************************************************************
 *		admin_shop_icq.php
 *		------------------
 *		Разработка и оптимизация под WAP: Гутник Игорь ( чел )
 *          2010 год
 *		简体中文：爱疯的云
 *************************************************************/

define('IN_PHPBB', true);

if( !empty($setmodules) )
{
	$file = basename(__FILE__);
	$module['Shop']['Shop_icq'] = $file;
	return;
}

$phpbb_root_path = '../';
require($phpbb_root_path . 'extension.inc');
require('./pagestart.' . $phpEx);

if ( isset($HTTP_POST_VARS['start1']) )
{
	$start1 = intval($HTTP_POST_VARS['start1']);
	$start = (($start1 - 1) * $board_config['topics_per_page']);
} 
else 
{
	$start = ( isset($HTTP_GET_VARS['start']) ) ? intval($HTTP_GET_VARS['start']) : 0;
	$start = ($start < 0) ? 0 : $start;
}

$template->set_filenames(array(
	'body' => 'admin/admin_shop_icq.tpl')
);

if ( isset($HTTP_GET_VARS['delete']) )
{
	$id = intval($HTTP_GET_VARS['id']);

	$sql = "DELETE FROM ".$table_prefix."shop_icq 
		WHERE id = '$id'";
	if ( !$result = $db->sql_query($sql) )
	{
		message_die(GENERAL_MESSAGE, '无法删除帐号！');
	}
	message_die(GENERAL_MESSAGE, '帐号已成功删除！<br /><br />点击 <a href="' . append_sid("admin_shop_icq.$phpEx") . '">这里</a> 返回上一页面</a>');

}
 elseif ( isset($HTTP_POST_VARS['add']) ) 
{

	$uin = abs(intval($HTTP_POST_VARS['uin']));
	$pass = trim(htmlspecialchars($HTTP_POST_VARS['pass']));
	$cost = abs(intval($HTTP_POST_VARS['cost']));

	$sql = "INSERT INTO ".$table_prefix."shop_icq (icq_number, icq_password, icq_cost) 
		VALUES ($uin, '" . str_replace("\'", "''", $pass) . "', $cost)";
	if ( !$result = $db->sql_query($sql) )
	{
		message_die(GENERAL_MESSAGE, '无法添加帐号！');
	}
	message_die(GENERAL_MESSAGE, '帐号已成功添加！<br /><br />点击 <a href="' . append_sid("admin_shop_icq.$phpEx") . '">这里</a> 返回上一页面！');

} 
else 
{

	$template->assign_vars(array(
		'S_ACTION' => append_sid("admin_shop_icq.$phpEx"))
	);

	$sql = "SELECT * 
		FROM ".$table_prefix."shop_icq
		ORDER BY id ASC 
		LIMIT $start, " . $board_config['topics_per_page'];
	if( !($result = $db->sql_query($sql)) )
	{
		message_die(GENERAL_ERROR, 'Could not query users', '', __LINE__, __FILE__, $sql);
	}

	if ( $row = $db->sql_fetchrow($result) )
	{
		$i = 0;
		do
		{	
			$row_class = ( !($i % 2) ) ? 'row_easy' : 'row_hard';
			$number = $i+1;
			$uin = $row['icq_number'];
			$pass = $row['icq_password'];
			$cost = $row['icq_cost'];
			$id = $row['id'];

			$template->assign_block_vars('memberrow', array(
				'ROW_CLASS'	=> $row_class,
				'NUMBER' =>$number,
				'UIN' => $uin,
				'PASS' => $pass,
				'COST' => $cost,
				'U_DEL' => append_sid("admin_shop_icq.$phpEx?delete&amp;id=$id"))
			);

			$i++;
		}
		while ( $row = $db->sql_fetchrow($result) );
		$db->sql_freeresult($result);
	} 
	else 
	{
		$template->assign_block_vars('no_pay', array() );
	}

	$sql = "SELECT count(*) AS total
		FROM ".$table_prefix."shop_icq";

	if ( !($result = $db->sql_query($sql)) )
	{
		message_die(GENERAL_ERROR, 'Error getting total users', '', __LINE__, __FILE__, $sql);
	}

	if ( $total = $db->sql_fetchrow($result) )
	{
		$total_members = $total['total'];
		$pagination = generate_pagination("admin_shop_icq.$phpEx?", $total_members, $board_config['topics_per_page'], $start);
	}
	$db->sql_freeresult($result);

	if ( $total_members > $board_config['topics_per_page'] )
	{
		$template->assign_vars(array(
			'PAGINATION' => $pagination)
		);
	}
	$template->pparse('body');
	include('./page_footer_admin.'.$phpEx);
}

?>