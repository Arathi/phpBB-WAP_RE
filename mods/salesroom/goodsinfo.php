<?php
/******************************
 * 文件名称：goodsinfo.php
 * 版权所有：Arathi Software Foundation
 * 文件说明：拍卖物信息查看
 ******************************/

define('IN_PHPBB', true);
$phpbb_root_path = './../../';
include($phpbb_root_path . 'extension.inc');
include($phpbb_root_path . 'common.'.$phpEx);
$userdata = session_pagestart($user_ip, PAGE_SALESROOM);
init_userprefs($userdata);

if ( !$userdata['session_logged_in'] )
{
    redirect(append_sid("login.$phpEx?redirect=mods/salesroom/index.$phpEx", true));
    exit;
}

$salesroom_config = array();
$salesroom_config['dateformat']='Y年m月d日 h:i:s';

$gid = isset($_GET['gid']) ? $_GET['gid'] : 0;
 
$sql =  "SELECT * FROM phpbb_salesroom_goods " .
        "WHERE unix_timestamp(now()) BETWEEN start_time AND end_time AND verify_status=1 AND goods_id=$gid";

if ( !$result = $db->sql_query($sql) )
{
    message_die(GENERAL_ERROR, '数据查询失败', '', __LINE__, __FILE__, $sql);
}

if ( !$goods_info = $db->sql_fetchrow($result) )
{
    $template->assign_block_vars('message', array(
            'MESSAGE_TEXT' => "<b>该商品不存在！</b>"
        )
    );
}
else{
    $action = isset($_GET['action'])?$_GET['action']:'info'; //获取当前的动作
    $is_valid = true; //获取当前交易是否有效
    $price_now = $goods_info['min_price']; //获取该商品当前价格
    
    if ($action == 'bid')
    {
        $bid_money = isset($_POST['bid_money'])?$_POST['bid_money']:0;
        $message = '';
        //( $price_now < $bid_money ) ? "<b>出价成功！</b>" : "<b>出价失败！</b>";
        //echo $bid_money;
        if ( intval($bid_money)-intval($price_now) < $goods_info['step_money'] )
        {
            $message = "<b>出价失败：出价要高出当前价格".$goods_info['step_money']."以上！</b>";
        }
        else
        {
            $message = "<b>出价成功！</b>";
        }
        $template->assign_block_vars('message', array(
                'MESSAGE_TEXT' => $message
            )
        );
    }
    //显示当前商品信息
    $template->assign_block_vars('goods_info', array(
        'NUMBER'			=> $gid,
        'ROW_CLASS' 		=> $row_class,
        'GOODS_NAME' 		=> $goods_info['goods_name'],
        'U_GOODS_INFO' 	    => append_sid("goodsinfo.php?gid=" . $goods_info['goods_id'] . "&action=info"),
        'START_TIME' 		=> create_date($salesroom_config['dateformat'], $goods_info['start_time'], $board_config['board_timezone']),
        'END_TIME' 			=> create_date($salesroom_config['dateformat'], $goods_info['end_time'], $board_config['board_timezone']),
        'GOODS_PRICE_NOW'   => $goods_info['min_price'],
        'STEP_MONEY'        => $goods_info['step_money'],
        'GOODS_PRICE_MAX'   => $goods_info['max_price'],
        'GOODS_DESC'        => $goods_info['description']
        )
    );
    //显示出价控制台
    if ( $is_valid )
    {
        $template->assign_block_vars('goods_bid', array(
                'NUMBER' => $gid
            ) 
        );
    }
}
$page_title = '拍卖行';
include($phpbb_root_path . 'includes/page_header.'.$phpEx);
$template->set_filenames(array(
    'body' => 'mods/salesroom_body.tpl'
));

$template->assign_vars(array(

));

$template->pparse('body');
include($phpbb_root_path . 'includes/page_tail.'.$phpEx);

?>
