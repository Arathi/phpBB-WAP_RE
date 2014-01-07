<?php
/******************************
 * 文件名称：sign.php
 * 版权所有：Arathi Software Foundation
 * 文件说明：拍卖号牌管理
 ******************************/

define('IN_PHPBB', true);
$phpbb_root_path = './../../';
include($phpbb_root_path . 'extension.inc');
include($phpbb_root_path . 'common.'.$phpEx);
$userdata = session_pagestart($user_ip, PAGE_SALESROOM);
init_userprefs($userdata);

if ( !$userdata['session_logged_in'] )
{
    redirect(append_sid("login.$phpEx?redirect=mods/salesroom/sign.$phpEx", true));
    exit;
}

$new_fakename = isset($_POST['fakename'])?$_POST['fakename']:"";
if ( trim($new_fakename)!="" ) //isset($new_fakename) && 
{
    //注册新的号牌
    $new_auctioneer = register_auctioneer( $userdata['user_id'], $new_fakename );
    if ( $new_auctioneer > 0 )
    {
        message_die(GENERAL_MESSAGE, '号牌注册成功！<br/><br/>返回<a href="sign.php">拍卖号牌管理</a>');
    }
    else
    {
        message_die(GENERAL_ERROR, '号牌注册失败！<br/><br/>返回<a href="sign.php">拍卖号牌管理</a>');
    }
}

$mode = isset($_GET['mode'])?$_GET['mode']:"";
$auctioneer_id = isset($_GET['aid'])?$_GET['aid']:"";
if ( $mode=="select" && is_numeric($auctioneer_id) && $auctioneer_id>0 )
{
    //$mode='select';
    switch_auctioneer($userdata['user_id'], $auctioneer_id);
}

//获取当前用户的所有拍卖人号牌信息
$sql =  "SELECT * FROM ".$table_prefix."salesroom_auctioneer " .
        "WHERE user_id = ".$userdata['user_id'];
if ( !$result = $db->sql_query($sql) )
{
    message_die(GENERAL_ERROR, '数据查询失败', '', __LINE__, __FILE__, $sql);
}
$auctioneer_rows = array();
while ($row = $db->sql_fetchrow($result))
{
    $auctioneer_rows[] = $row;
}

$size = count($auctioneer_rows);
if ($size>0)
{
    $template->assign_block_vars( 'switch_has_signed', array() );
    for ($i=0; $i<$size; $i++)
    {
        $row_class = ( !($i % 2) ) ? 'row_easy' : 'row_hard';
        $u_select = ($auctioneer_rows[$i]['selected']!=1)?"<a href='sign.php?mode=select&aid=".$auctioneer_rows[$i]['auctioneer']."'>选取</a>":"已选";
        $template->assign_block_vars('auctioneer_info', array(
            'AUCTIONEER' => $auctioneer_rows[$i]['auctioneer'],
            'FAKENAME'   => $auctioneer_rows[$i]['fakename'],
            'ROW_CLASS'  => $row_class,
            'U_SELECT'   => $u_select
            )
        );
    }
}
else
{
    $template->assign_block_vars( 'switch_no_signed', array() );
}

//申请新的拍卖号牌
if ($size<10) //拍卖号牌数量限制
{
    $template->assign_block_vars( 'auctioneer_register', array(
            'DEFAULT_FAKENAME' => $userdata['username']
        )
    );
}

$page_title = '拍卖行';
include($phpbb_root_path . 'includes/page_header.'.$phpEx);
$template->set_filenames(array(
    'body' => 'mods/salesroom_body.tpl'
));
$template->assign_vars( array() );
$template->pparse('body');
include($phpbb_root_path . 'includes/page_tail.'.$phpEx);

function register_auctioneer($user_id, $fakename="")
{
    //检查当前持有号牌数量
    global $db,$table_prefix;
    $sql = "SELECT user_id FROM ".$table_prefix."salesroom_auctioneer WHERE auctioneer=".$auctioneer_id;
    //开始注册，重复三次
    mt_srand( time() );
    for ($i=0; $i<3; $i++)
    {
        $auctioneer_id = mt_rand(1,999);
        $sql = "SELECT user_id FROM ".$table_prefix."salesroom_auctioneer WHERE auctioneer=".$auctioneer_id;
        if ( !$result = $db->sql_query($sql) )
        {
            message_die(GENERAL_ERROR, '数据查询失败', '', __LINE__, __FILE__, $sql);
        }
        $auctioneer_info = null;
        if ($row = $db->sql_fetchrow($result))
        {
            $auctioneer_info = $row;
        }
        if ( $auctioneer_info==null )
        {
            //记录不存在，插入新值
            $sql_new_auctioneer = "INSERT INTO ".$table_prefix."salesroom_auctioneer(user_id,auctioneer,fakename,selected) "
                                . "VALUES(".$user_id.", $auctioneer_id, '".$fakename."', 0)";
            if ( !$result = $db->sql_query($sql_new_auctioneer) )
            {
                message_die(GENERAL_ERROR, '数据插入失败', '', __LINE__, __FILE__, $sql_new_auctioneer);
            }
            return $auctioneer_id;
        }
        else if (is_numeric($auctioneer_info['user_id'])==false || $auctioneer_info['user_id']<=0 )
        {
            //值不为一个合法的ID，更新user_id
            $sql_new_auctioneer = "UPDATE ".$table_prefix."salesroom_auctioneer ".
                                  "SET user_id=".$user_id;
            if ( !$result = $db->sql_query($sql_new_auctioneer) )
            {
                message_die(GENERAL_ERROR, '数据更新失败', '', __LINE__, __FILE__, $sql_new_auctioneer);
            }
            return $auctioneer_id;
        }
        else
        {
            //该ID已被占用
            continue;
        }
    }
    return 0;
}

function switch_auctioneer($user_id, $auctioneer_id)
{
    global $db,$table_prefix;
    //把该用户所有号牌的selected都设置为0
    $sql = "UPDATE ".$table_prefix."salesroom_auctioneer SET selected=0 WHERE user_id=".$user_id;
    if ( !$result = $db->sql_query($sql) )
    {
        message_die(GENERAL_ERROR, '数据更新失败', '', __LINE__, __FILE__, $sql);
    }
    //把指定号牌的selected设置为1
    $sql = "UPDATE ".$table_prefix."salesroom_auctioneer SET selected=1 WHERE user_id=" . $user_id . " AND auctioneer=" . $auctioneer_id;
    if ( !$result = $db->sql_query($sql) )
    {
        message_die(GENERAL_ERROR, '数据更新失败', '', __LINE__, __FILE__, $sql);
    }
}

?>
