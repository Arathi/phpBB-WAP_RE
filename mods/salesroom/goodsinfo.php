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

$auctioneer = 0;
$sql = "SELECT user_id,auctioneer,fakename,selected FROM ".$table_prefix."salesroom_auctioneer WHERE user_id=".$userdata['user_id']." AND selected=1";
if ( !$result = $db->sql_query($sql) )
{
    message_die(GENERAL_ERROR, '数据查询失败', '', __LINE__, __FILE__, $sql);
}
if ( !$auctioneer_info = $db->sql_fetchrow($result) )
{
    $message = $userdata['username']."，您尚未<a href='sign.php'>选取</a>拍卖人号牌！";
    $template->assign_block_vars('message', array(
            'MESSAGE_TEXT' => $message
        )
    );
}
else
{
    $auctioneer = $auctioneer_info['auctioneer'];
    $message = "您好，".$auctioneer."号买家"." ".$auctioneer_info['fakename'];
    $template->assign_block_vars('message', array(
            'MESSAGE_TEXT' => $message
        )
    );
}

$salesroom_config = array();
$salesroom_config['dateformat']='Y年m月d日 h:i:s';

$gid = is_numeric($_GET['gid']) ? $_GET['gid'] : 0;
//if ($gid == '') $gid = 0;

$sql =  "SELECT * FROM ".$table_prefix."salesroom_goods WHERE verify_status=1 AND goods_id=$gid"; //unix_timestamp(now()) BETWEEN start_time AND end_time AND 

if ( !$result = $db->sql_query($sql) )
{
    message_die(GENERAL_ERROR, '数据查询失败', '', __LINE__, __FILE__, $sql);
}

$action = isset($_GET['action'])?$_GET['action']:'info'; //获取当前的动作

if ( $gid==0 || ( !$goods_info = $db->sql_fetchrow($result) ) )
{
    $template->assign_block_vars('message', array(
            'MESSAGE_TEXT' => "<b>该商品不存在！</b>"
        )
    );
}
else{
    $is_valid = true; //获取当前交易是否有效
    $price_now = $goods_info['min_price']; //获取该商品当前价格
    $sql_goods_price  = "SELECT bid_id,auctioneer,goods_id,money,bid_time,comment FROM ".$table_prefix."salesroom_bid " .
                        "WHERE goods_id=".$gid." ORDER BY money DESC";
    $bid_list = array();
    $goods_pre_owner = ""; //底价
    $goods_pre_owner_id = 0;
    if ( !$result_bid = $db->sql_query($sql_goods_price) )
    {
        message_die(GENERAL_ERROR, '数据查询失败', '', __LINE__, __FILE__, $sql);
    }
    while ( $goods_bid = $db->sql_fetchrow($result_bid) )
    {
        $bid_list[] = $goods_bid;
    }
    if ( count($bid_list)>0 )
    {
        $goods_pre_owner_id = $bid_list[0]['auctioneer'];
        $price_now = $bid_list[0]['money'];
    }
    
    if ($action == 'bid')
    {
        $bid_money = isset($_POST['bid_money'])?$_POST['bid_money']:0;
        $delta_min = ($goods_pre_owner_id>0) ? intval($goods_info['step_money']) : 0;
        $message = '';
        if (intval($bid_money) > $userdata['user_points'])
        {
            $message = "<b>出价失败：当前余额(".$userdata['user_points'].")不足以支付您所出的价位(".$bid_money.")！</b>";
        }
        else if ( intval($bid_money)-intval($price_now) < $delta_min )
        {
            $message = "<b>出价失败：出价要高出当前价格".$goods_info['step_money']."以上！</b>";
        }
        else
        {
            $sql_insert_bid = "INSERT INTO ".$table_prefix."salesroom_bid(auctioneer,goods_id,money,bid_time) ".
                                "VALUES( $auctioneer, $gid, $bid_money, unix_timestamp(now()) )";
            if(!$result_insert_bid = $db->sql_query($sql_insert_bid))
            {
                message_die(GENERAL_ERROR, '插入出价信息失败！', '', __LINE__, __FILE__, $sql_insert_bid);
            }
            message_die(GENERAL_MESSAGE,"出价成功！<br/><br/>".
                                        "返回<a href='goodsinfo.php?gid=$gid'>商品信息</a><br/>".
                                        "返回<a href='index.php'>商品列表</a>");
            //$price_now = $bid_money;
        }
        $template->assign_block_vars('message', array(
                'MESSAGE_TEXT' => $message
            )
        );
    }
    if ( $goods_pre_owner_id>0 )
    {
        //TODO 补上买家信息页面
        $goods_pre_owner = "<a href='auctioneer.php?mode=select&aid=$goods_pre_owner_id'>".$goods_pre_owner_id."号买家</a>";
    }
    else
    {
        $goods_pre_owner = "底价";
    }
    //显示当前商品信息
    $template->assign_block_vars('goods_info', array(
        'NUMBER'			=> $gid,
        'ROW_CLASS' 		=> $row_class,
        'GOODS_NAME' 		=> $goods_info['goods_name'],
        'U_GOODS_INFO' 	    => append_sid("goodsinfo.php?gid=" . $goods_info['goods_id'] . "&action=info"),
        'START_TIME' 		=> create_date($salesroom_config['dateformat'], $goods_info['start_time'], $board_config['board_timezone']),
        'END_TIME' 			=> create_date($salesroom_config['dateformat'], $goods_info['end_time'], $board_config['board_timezone']),
        'GOODS_PRICE_NOW'   => $price_now . " (".$goods_pre_owner.")",
        'STEP_MONEY'        => $goods_info['step_money'],
        'GOODS_PRICE_MAX'   => $goods_info['max_price'],
        'GOODS_DESC'        => $goods_info['description']
        )
    );
    //显示出价控制台
    if ( $auctioneer!=0 && $is_valid )
    {
        $template->assign_block_vars('goods_bid', array(
                'MONEY_NOW' => $userdata['user_points'],
                'NUMBER' => $gid
            ) 
        );
    }
    //显示出价记录
    $size = count($bid_list);
    if ($size>5) $size=5;
    if ($size>0)
    {
        $template->assign_block_vars('bid_log_head', array());
    }
    for ($i=0; $i<$size; $i++)
    {
        $row_class = ( !($i % 2) ) ? 'row_easy' : 'row_hard';
        $template->assign_block_vars('bid_log', array(
            'ROW_CLASS'    => $row_class,
            'BID_ID'       => $bid_list[$i]['bid_id'],
            'U_AUCTIONEER' => "<a href='auctioneer.php?mode=select&aid=".$bid_list[$i]['auctioneer']."'>".$bid_list[$i]['auctioneer']."号买家</a>",
            'BID_TIME'     => create_date($salesroom_config['dateformat'], $bid_list[$i]['bid_time'], $board_config['board_timezone']),
            'BID_MONEY'    => $bid_list[$i]['money']
            )
        );
    }
}

$page_title = '拍卖行';
include($phpbb_root_path . 'includes/page_header.'.$phpEx);
$template->set_filenames(array(
    'body' => 'mods/salesroom_body.tpl'
));
$template->assign_vars( array() );
$template->pparse('body');
include($phpbb_root_path . 'includes/page_tail.'.$phpEx);
?>
