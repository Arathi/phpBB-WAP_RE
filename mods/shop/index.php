<?php
/**************************************************************
 *		shop.php
 *		---------
 *  	Разработка и оптимизация под WAP: Гутник Игорь ( чел )
 *			2011 год
 *  	简体中文：爱疯的云
 ***************************************************************/

define('IN_PHPBB', true);
$phpbb_root_path = './../../';
include($phpbb_root_path . 'extension.inc');
include($phpbb_root_path . 'common.'.$phpEx);

// session
$userdata = session_pagestart($user_ip, PAGE_PRAVILA);
init_userprefs($userdata);

// 验证用户是否登录
if ( !$userdata['session_logged_in'] )
{
	redirect(append_sid("{$phpbb_root_path}login.$phpEx?redirect=mods/shop/index.$phpEx", true));
	exit;
}

if ( isset($HTTP_GET_VARS['forum']) && !isset($HTTP_GET_VARS['id']) )
{
	// 这里不是商店页面的标题
	$page_title = '积分的获取';
	
	include($phpbb_root_path . 'includes/page_header.'.$phpEx);

	$template->set_filenames(array(
	'body' => 'index_price_body.tpl')
	);
	// 查询论坛分类的SQL语句
	$sql = "SELECT c.cat_id, c.cat_title, c.cat_order
		FROM " . CATEGORIES_TABLE . " c 
		ORDER BY c.cat_order ASC";
	// 如果查询结果不存在则执行 message_die() 函数
	if( !($result = $db->sql_query($sql)) )
	{
		message_die(GENERAL_ERROR, 'Could not query categories list', '', __LINE__, __FILE__, $sql);
	}

	// 论坛分类的数组
	$category_rows = array();
	
	// 循环出分类
	while ($row = $db->sql_fetchrow($result))
	{
		$category_rows[] = $row;
	}
	
	$db->sql_freeresult($result);
	
	// 算出分类数
	$total_categories = count($category_rows);
	// 查询每个论坛发帖的积分
	$sql = "SELECT *
		FROM " . FORUMS_TABLE . "
		ORDER BY forum_money DESC";
		
	if ( !($result = $db->sql_query($sql)) )
	{
		message_die(GENERAL_ERROR, 'Could not query forums information', '', __LINE__, __FILE__, $sql);
	}
	
	// 论坛名称的数组
	$forum_data = array();
	
	while( $row = $db->sql_fetchrow($result) )
	{
		$forum_data[] = $row;
	}
	$db->sql_freeresult($result);
	
	$total_forums = count($forum_data);
	$is_auth_ary = array();
	$is_auth_ary = auth(AUTH_VIEW, AUTH_LIST_ALL, $userdata, $forum_data);
	$display_categories = array();

	for ($i = 0; $i < $total_forums; $i++ )
	{
		if ($is_auth_ary[$forum_data[$i]['forum_id']]['auth_view'])
		{
			$display_categories[$forum_data[$i]['cat_id']] = true;
		}
	}
	for($i = 0; $i < $total_categories; $i++)
	{
		$cat_id = $category_rows[$i]['cat_id'];

		if (isset($display_categories[$cat_id]) && $display_categories[$cat_id])
		{
			for($j = 0; $j < $total_forums; $j++)
			{
				if ( $forum_data[$j]['cat_id'] == $cat_id )
				{
					$forum_id = $forum_data[$j]['forum_id'];

					if ( $is_auth_ary[$forum_id]['auth_view'] )
					{
						$posts = $forum_data[$j]['forum_posts'];

						$template->assign_block_vars('forumrow', array(
							'FORUM_NAME' 	=> $forum_data[$j]['forum_name'],
							'MONEY' 		=> $forum_data[$j]['forum_money'],
							'U_VIEWFORUM' 	=> append_sid("{$phpbb_root_path}viewforum.$phpEx?" . POST_FORUM_URL . "=$forum_id"))
						);
					}
				}
			}
		}
	}
	
	$template->pparse('body');
	include($phpbb_root_path . 'includes/page_tail.'.$phpEx);
	exit;
}

// 取得下面 $id 的值
$id = ( isset($HTTP_GET_VARS['id']) ) ? intval($HTTP_GET_VARS['id']) : '';

// 判断是否打开商店功能
if ( !$board_config['shop'] )
{
	message_die(GENERAL_MESSAGE, $lang['Shop_Error_not_open']);
}

$point_name = $attach_config['point_name'];// 积分名称
$back_shop_index = append_sid("{$phpbb_root_path}mods/shop/index.$phpEx");// 商店首页
$back_index = append_sid("{$phpbb_root_path}index.$phpEx");// 论坛首页


switch( $id )
{
	case '1':
		$tovar = $lang['Shop_Change_Username'];// 更改用户名
		$opisanie = sprintf($lang['Shop_Change_Username_Example'], $board_config['smena_nika'], $point_name);
		if ( $userdata['user_points'] >= $board_config['smena_nika'] )
		{
			$opisanie .= sprintf($lang['Shop_Change_Username_Example1'], $userdata['user_points'], $point_name);
			$opisanie .= '<a href="' . append_sid("{$phpbb_root_path}profile.$phpEx?mode=money&amp;nick") . '">' . $lang['Yes']  . '</a>|<a href="' . $back_shop_index . '">' . $lang['No'] . '</a>';
		} 
		else 
		{
			$opisanie .= sprintf($lang['Shop_Change_Username_Example3'], $userdata['user_points'], $point_name);
			$opisanie .= '<a href="' . $back_shop_index . '">' . $lang['Shop_Back'] . '</a>|<a href="' . $back_index . '">' . $lang['Shop_Back_Index'] . '</a>';
		}
	break;
	
	case '2':
		$tovar = $lang['Shop_Change_Username_Color'];// 更改用户名颜色
		$opisanie = sprintf($lang['Shop_Change_Username_Color_Example'], $board_config['smena_cveta'], $point_name);
		if ( $userdata['user_points'] >= $board_config['smena_cveta'] )
		{
			$opisanie .= sprintf($lang['Shop_Change_Username_Color_Example2'], $userdata['user_points'], $point_name);
			$opisanie .= '<a href="' . append_sid("{$phpbb_root_path}profile.$phpEx?mode=money&amp;color") . '">' . $lang['Yes'] . '</a>|<a href="' . $back_shop_index . '">' . $lang['No'] . '</a>';
		}
		else
		{
			$opisanie .= sprintf($lang['Shop_Change_Username_Color_Example3'], $userdata['user_points'], $point_name);
			$opisanie .= '<a href="' . $back_shop_index . '">' . $lang['Shop_Back'] . '</a>|<a href="' . $back_index . '">' . $lang['Shop_Back_Index'] . '</a>';
		}
	break;
	
	case '3':
		$tovar = $lang['Shop_Change_Rank']; //更改个人等级
		$opisanie = sprintf($lang['Shop_Change_Rank_Example'], $board_config['smena_zvaniya'], $point_name);
		if ( $userdata['user_points'] >= $board_config['smena_zvaniya'] )
		{
			$opisanie .= sprintf($lang['Shop_Change_Username_Color_Example2'], $userdata['user_points'], $point_name);
			$opisanie .= '<a href="' . append_sid("{$phpbb_root_path}profile.$phpEx?mode=money&amp;rank") . '">' . $lang['Yes'] . '</a>|<a href="' . $back_shop_index . '">' . $lang['No'] . '</a>';
		}
		else
		{
			$opisanie .= sprintf($lang['Shop_Change_Username_Color_Example3'], $userdata['user_points'], $point_name);
			$opisanie .= '<a href="' . $back_shop_index . '">' . $lang['Shop_Back'] . '</a>|<a href="' . $back_index . '">' . $lang['Shop_Back_Index'] . '</a>';
		}
	break;
	
	case '4':
		$tovar = $lang['Shop_Buy_Account']; //购买帐号
		$opisanie = sprintf($lang['Shop_Buy_Account_Example'], $point_name, $board_config['pokupka_uchetki_posts'], $board_config['pokupka_uchetki_nedeli'], $board_config['pokupka_uchetki'], $point_name);
		if ( $userdata['user_points'] >= $board_config['pokupka_uchetki'] )
		{
			$opisanie .= sprintf($lang['Shop_Buy_Account_Example2'], $userdata['user_points'], $point_name);
			$opisanie .= '<a href="' . append_sid("{$phpbb_root_path}profile.$phpEx?mode=money&amp;acc") . '">' . $lang['Yes'] . '</a>|<a href="' . $back_shop_index . '">' . $lang['No'] . '</a>';
		}
		else
		{
			$opisanie .= sprintf($lang['Shop_Buy_Account_Example3'], $userdata['user_points'], $point_name);
			$opisanie .= '<a href="' . $back_shop_index . '">' . $lang['Shop_Back'] . '</a>|<a href="' . $back_index . '">' . $lang['Shop_Back_Index'] . '</a>';
		}
	break;
	
	case '5':
		$tovar = $lang['Shop_Remove_Ban'];// 解除黑名单
		$opisanie = sprintf($lang['Shop_Remove_Ban_Example'], $point_name, $board_config['razblokirovka_druga'], $point_name);
		if ( $userdata['user_points'] >= $board_config['razblokirovka_druga'] )
		{
			$opisanie .= sprintf($lang['Shop_Remove_Ban_Example2'], $userdata['user_points'], $point_name);
			$opisanie .= '<a href="' . append_sid("{$phpbb_root_path}profile.$phpEx?mode=money&amp;ban") . '">' . $lang['Yes'] . '</a>|<a href="' . $back_shop_index . '">' . $lang['No'] . '</a>';
		}
		else
		{
			$opisanie .= sprintf($lang['Shop_Remove_Ban_Example3'], $userdata['user_points'], $point_name);
			$opisanie .= '<a href="' . $back_shop_index . '">' . $lang['Shop_Back'] . '</a>|<a href="' . $back_index . '">' . $lang['Shop_Back_Index'] . '</a>';
		}
	break;
	
	case '6':
		$tovar = $lang['Shop_Buy_ICQ']; // ICQ
		$opisanie = sprintf($lang['Shop_Buy_ICQ_Example'], $point_name, $point_name);
		if ( $userdata['user_points'] < 1 )
		{
			$opisanie .= sprintf($lang['Shop_Buy_ICQ_Example2'], $point_name);
			$opisanie .= '<a href="' . $back_shop_index . '">' . $lang['Shop_Back'] . '</a>|<a href="' . $back_index . '">' . $lang['Shop_Back_Index'] . '</a>';
		}
		else
		{
			$opisanie .= sprintf($lang['Shop_Buy_ICQ_Example3'], $userdata['user_points'], $point_name);
			$opisanie .= '<a href="' . append_sid("{$phpbb_root_path}profile.$phpEx?mode=money&amp;icq") . '">' . $lang['Yes'] . '</a>|<a href="' . $back_shop_index . '">' . $lang['No'] . '</a>';
		}
	break;
	
	case '7':
		$tovar = $lang['Shop_Earn_points'];// 赚取积分
		$opisanie = sprintf($lang['Shop_Earn_points_Example'], $point_name, $userdata['user_points'], $point_name);
		$opisanie .= '<a href="' . append_sid("{$phpbb_root_path}profile.$phpEx?mode=money&amp;url") . '">' . $lang['Yes'] . '</a>|<a href="' . $back_shop_index . '">' . $lang['No'] . '</a>';
	break;
	
	case '8':
		$tovar = $lang['Shop_Put_In_Ad'];// 投放广告
		$opisanie = sprintf($lang['Shop_Put_In_Ad_Example'], $point_name);
		if ( $userdata['user_points'] < 1 )
		{
			$opisanie .= sprintf($lang['Shop_Put_In_Ad_Example2'], $userdata['user_points'], $point_name, $point_name);
			$opisanie .= '<a href="' . $back_shop_index . '">' . $lang['Shop_Back'] . '</a>|<a href="' . $back_index . '">' . $lang['Shop_Back_Index'] . '</a>';
		}
		else
		{
			$opisanie .= sprintf($lang['Shop_Put_In_Ad_Example3'], $userdata['user_points'], $point_name, $board_config['verh_pay'], $point_name, $board_config['niz_pay'], $point_name);
			$opisanie .= '<a href="' . append_sid("{$phpbb_root_path}profile.$phpEx?mode=money&amp;sites") . '">' . $lang['Yes'] . '</a>|<a href="' . $back_shop_index . '">' . $lang['No'] . '</a>';
		}
	break;
	
	case '9':
		if ( !$board_config['pay_money'] )// Конвертировать в рубли
		{
			message_die(GENERAL_MESSAGE, $lang['Shop_Error_pay_Money_not_open']);
		}
		$tovar = $lang['Shop_Pay_Money'];
		$opisanie = sprintf($lang['Shop_Pay_Money_Example'], $board_config['kurs_payment']);
		if ( !empty($userdata['user_purse']) )
		{
			$opisanie .= sprintf($lang['Shop_Pay_Money_Example1'], $userdata['user_points']);
			$opisanie .= '<a href="' . append_sid("{$phpbb_root_path}profile.$phpEx?mode=money&amp;exch") . '">' . $lang['Shop_Pay_Money_Exch'] . '</a>|<a href="' . $back_shop_index . '">' . $lang['Shop_Pay_Money_To_Shop'] . '</a>';
		}
		elseif ( empty($userdata['user_purse']) ) 
		{
			$opisanie .= $lang['Shop_Pay_Money_Example2'];
			$opisanie .= '<a href="' . append_sid("{$phpbb_root_path}profile.$phpEx?mode=editprofileinfo") . '">' . $lang['Shop_Pay_Money_Edit_Profileinfo'] . '</a>|<a href="' . $back_shop_index . '">' . $lang['Shop_Pay_Money_To_Shop'] . '</a>';
		}
		else
		{
			$opisanie .= sprintf($lang['Shop_Pay_Money_Example3'], $userdata['user_points']);
			$opisanie .= '<a href="' . $back_shop_index . '">' . $lang['Shop_Pay_Money_To_Shop'] . '</a>|<a href="' . $back_index . '">' . $lang['Shop_Pay_Money_To_Index'] . '</a>';
		}
	break;
		case '10':
		$tovar = $lang['buy_daoju'];
		if(($board_config['stick_price']<$userdata['user_points'])||($$board_config['highlight_price']<$userdata['user_points'])){
			$opisanie .= sprintf($lang['buy_daoju'],
			$userdata['user_points']);
			$opisanie .= '<a href="' . append_sid("{$phpbb_root_path}profile.$phpEx?mode=money&amp;daoju") . '"><br/>' . $lang['Yes']  . '</a>|<a href="' . $back_shop_index . '">' . $lang['No'] . '</a>';
		}else{
			$opisanie .="对不起，你的".$points_name."不足";
		}

		break;

	default:
		$tovar = $lang['Welcome_To_Shop'];
		
		$opisanie = sprintf($lang['Welcome_To_Shop_Example'], $point_name, $point_name);
		$opisanie .= '<div class="catSides">' . $lang['Shop_Virtual_Service_List'] . '</div>';
		$opisanie .= '- <a href="'.append_sid("{$phpbb_root_path}mods/shop/index.$phpEx?id=1").'">' . $lang['Shop_Change_Username'] . '</a><br />';
		$opisanie .= '- <a href="'.append_sid("{$phpbb_root_path}mods/shop/index.$phpEx?id=2").'">' . $lang['Shop_Change_Username_Color'] . '</a><br />';
		$opisanie .= '- <a href="'.append_sid("{$phpbb_root_path}mods/shop/index.$phpEx?id=3").'">' . $lang['Shop_Change_Rank'] . '</a><br />';
		$opisanie .= '- <a href="'.append_sid("{$phpbb_root_path}mods/shop/index.$phpEx?id=4").'">' . $lang['Shop_Buy_Account'] . '</a><br />';
		$opisanie .= '- <a href="'.append_sid("{$phpbb_root_path}mods/shop/index.$phpEx?id=6").'">' . $lang['Shop_Buy_ICQ'] . '</a><br />';
		$opisanie .= '- <a href="'.append_sid("{$phpbb_root_path}mods/shop/index.$phpEx?id=7").'">' . $lang['Shop_Earn_points'] . '</a><br />';
		$opisanie .= '- <a href="'.append_sid("{$phpbb_root_path}mods/shop/index.$phpEx?id=5").'">' . $lang['Shop_Remove_Ban'] . '</a><br />';
		
		if ( $board_config['sites'] )
		{
			$opisanie .= '- <a href="'.append_sid("{$phpbb_root_path}mods/shop/index.$phpEx?id=8").'">' . $lang['Shop_Put_In_Ad'] . '</a><br/>';
		}
		// 与简体中文版无关
		if ( $board_config['pay_money'] )
		{
			$opisanie .= '<br/>- <a href="'.append_sid("{$phpbb_root_path}mods/shop/index.$phpEx?id=9").'">' . $lang['Shop_Pay_Money'] . '</a>';

		}
		$opisanie .= '- <a href="'.append_sid("{$phpbb_root_path}mods/shop/index.$phpEx?id=10").'">' . $lang['buy_daoju'] . '</a><br />';
	break;
}

// 虚拟商店标题
$page_title = $lang['Shop'];

include($phpbb_root_path . 'includes/page_header.'.$phpEx);

$template->set_filenames(array(
	'body' => 'mods/shop.tpl')
);

$template->assign_vars(array(
	'TOVAR' 	=> $tovar,
	'OPISANIE' 	=> $opisanie)
);

$template->pparse('body');

include($phpbb_root_path . 'includes/page_tail.'.$phpEx);

?>
