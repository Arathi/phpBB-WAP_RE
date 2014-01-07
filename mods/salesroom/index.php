<?php
/******************************
 * 文件名称：index.php
 * 版权所有：Arathi Software Foundation
 * 文件说明：拍卖行首页
 ******************************/

define('IN_PHPBB', true);
$phpbb_root_path = './../../';
include($phpbb_root_path . 'extension.inc');
include($phpbb_root_path . 'common.'.$phpEx);
$userdata = session_pagestart($user_ip, PAGE_SALESROOM);
init_userprefs($userdata);

// 验证用户是否登录
if ( !$userdata['session_logged_in'] )
{
    redirect(append_sid("login.$phpEx?redirect=mods/salesroom/index.$phpEx", true));
    exit;
}

$sql =  "SELECT goods_id, goods_name, end_time FROM ".$table_prefix."salesroom_goods " .
        "WHERE unix_timestamp(now()) BETWEEN start_time AND end_time AND verify_status=1 ";

if ( !$result = $db->sql_query($sql) )
{
	message_die(GENERAL_ERROR, '数据查询失败', '', __LINE__, __FILE__, $sql);
}

$goods_rows = array();

while ($row = $db->sql_fetchrow($result))
{
	$goods_rows[] = $row;
}

$total_goods_rows = count($goods_rows);

if ($total_goods_rows > 0)
{
    $template->assign_block_vars( 'switch_has_goods', array() );
    for($i = 0; $i < $total_goods_rows; $i++)
    {
        $number = $i + 1 + $start;
        $row_class = ( !($i % 2) ) ? 'row_easy' : 'row_hard';
        $template->assign_block_vars('goods_rows', array(
            'NUMBER'				=> $goods_rows[$i]['goods_id'],
            'ROW_CLASS' 			=> $row_class,
            'GOODS_NAME' 		=> $goods_rows[$i]['goods_name'],
            'U_GOODS_INFO' 	=> append_sid("goodsinfo.php?gid=" . $goods_rows[$i]['goods_id']),
            'END_TIME' 			=> create_date($userdata['user_dateformat'], $goods_rows[$i]['end_time'], $board_config['board_timezone']),
            )
        );
    }
}
else
{
    $template->assign_block_vars( 'switch_no_goods', array() );
}

// 页面头部
$page_title = '拍卖行';
include($phpbb_root_path . 'includes/page_header.'.$phpEx);

// 调用模版
$template->set_filenames(array(
    'body' => 'mods/salesroom_body.tpl'
));

// 调用模版语言
$template->assign_vars(array(
    
));

$template->pparse('body');
include($phpbb_root_path . 'includes/page_tail.'.$phpEx);

?>
