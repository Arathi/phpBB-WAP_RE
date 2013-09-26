<?php
/**************************************************************
 *		usercp_money.php
 *		-------------------
 *   	Разработка и оптимизация под WAP: Гутник Игорь ( чел ).
 *          2011 год
 *		简体中文：爱疯的云
 *		说明：关于用户 money 的处理文件
 **************************************************************/

if ( !defined('IN_PHPBB') )
{
	die('Hacking attempt');
	exit;
}

// 检查商店是否开启
if ( !$board_config['shop'] ) 
{ 
	message_die(GENERAL_MESSAGE, $lang['Shop_Error_not_open']);
}

if ( isset($HTTP_POST_VARS['start1']) )
{
	$start1 = abs(intval($HTTP_POST_VARS['start1']));
	$start1 = ($start1 < 1) ? 1 : $start1;
	$start = (($start1 - 1) * $board_config['topics_per_page']);
}
else
{
	$start = ( isset($HTTP_GET_VARS['start']) ) ? intval($HTTP_GET_VARS['start']) : 0;
	$start = ($start < 0) ? 0 : $start;
}

// 积分的名称
$point_name = $attach_config['point_name'];

// 用户名修改
if (isset($HTTP_GET_VARS['nick']))
{
	// 检查用户积分是否大于修改昵称的积分
	if ( $userdata['user_points'] < $board_config['smena_nika'] )
	{
		message_die(GENERAL_MESSAGE, sprintf($lang['Shop_Error_Not_Enough_Points'], $point_name));
	}
	// 检查是否提交和用户名是否为空
	if ( isset($HTTP_POST_VARS['submit']) && !empty($HTTP_POST_VARS['username']) )
	{
		include($phpbb_root_path . 'includes/functions_validate.'.$phpEx);// 包含验证函数
		$username = phpbb_clean_username($HTTP_POST_VARS['username']);// 修正用户名
		$result = validate_username($username);// 验证用户名
		if ( $result['error'] )
		{
			message_die(GENERAL_MESSAGE, $result['error_msg']);
		}
		// 执行 MySQL 更新语句
		$sql = "UPDATE " . USERS_TABLE . "
			SET username = '" . str_replace("\'", "''", $username) . "'
			WHERE user_id = " . $userdata['user_id'];
			
		if ( !($result = $db->sql_query($sql)) )
		{
			message_die(GENERAL_ERROR, 'Could not update users table', '', __LINE__, __FILE__, $sql);
		}

		$sql = "UPDATE " . USERS_TABLE . "
			SET user_points = user_points - " . $board_config['smena_nika'] . "
			WHERE user_id = " . $userdata['user_id'];
			
		if ( !($result = $db->sql_query($sql)) )
		{
			message_die(GENERAL_ERROR, 'Could not update users table', '', __LINE__, __FILE__, $sql);
		}
		// 算出用户目前积分
		$ostatok = $userdata['user_points'] - $board_config['smena_nika'];

		message_die(GENERAL_MESSAGE, '用户名更改成功！<br />系统已从您的虚拟账户中收取 ' . $board_config['smena_nika'] . $point_name . '<br />您目前的虚拟帐户剩余 ' . $ostatok . $point_name);
	}
	else
	{
		$page_title = $lang['Shop_Change_Username'];
		include($phpbb_root_path . 'includes/page_header.'.$phpEx);

		$template->set_filenames(array(
			'body' => 'mods/shop_action.tpl')
		);

		$template->assign_vars(array(
			'L_SHOP_ACTION' 	=> $lang['Shop_Change_Username'],
			'SHOP_ACTION' 		=> $lang['Shop_Enter_Username'],
			'SHOP_ACTION_DB' 	=> 'username',
			'SHOP_ACTION_MAX' 	=> '25',
			'SHOP_MONEY' 		=> $board_config['smena_nika'],
			'SHOP_POINT_NAME' 	=> $point_name,
			'S_SHOP_ACTION' 	=> append_sid("profile.$phpEx?mode=money&amp;nick"))
		);

		$template->pparse('body');

		include($phpbb_root_path . 'includes/page_tail.'.$phpEx);
	}
}
// 购买道具
elseif (isset($HTTP_GET_VARS['daoju']))
{
	// 检查用户积分是否大于购买道具的积分
	if ( ( $userdata['user_points'] > $board_config['stick_price'] ) ||( $userdata['user_points'] > $board_config['highlight_price'] ) )
	{
			// 检查是否提交和用户名是否为空
		if ( isset($HTTP_POST_VARS['submit']) && !empty($HTTP_POST_VARS['buy_num']) ){
			$daoju_type = ( isset($HTTP_POST_VARS['type']) ) ? $HTTP_POST_VARS['type'] : '0';
			$buy_num = ( isset($HTTP_POST_VARS['buy_num']) ) ? $HTTP_POST_VARS['buy_num'] : '0';
//			if(eregi("^[0-9]*$",$buy_num)){
//				message_die(GENERAL_ERROR, '输入不合法,请输入数字');

				buy_daoju($daoju_type,$buy_num,$userdata['user_id']);
				message_die(GENERAL_ERROR, '购买成功<br/>');
		}
	}
	include($phpbb_root_path . 'includes/page_header.'.$phpEx);
		$page_title = $lang['buy_daoju'];
		$template->set_filenames(array(
			'body' => 'mods/shop_daoju.tpl')
		);
		$price="道具价格:<br/>置顶卡 (".$board_config['stick_price'].$point_name."/张)<br/>高亮卡 (".$board_config['highlight_price'].$point_name."/张)<br/>抢楼卡 (".$board_config['qianglou_price'].$point_name."/张)";

		$template->assign_vars(array(
			'L_SHOP_ACTION' 	=> $lang['buy_daoju'],
			'SHOP_ACTION' 		=> $lang['enter_buy_num'],
			'SHOP_ACTION_DB' 	=> 'buy_num',
			'SHOP_ACTION_MAX' 	=> '25',
			'SHOP_MONEY' 		=> $board_config['stick_price'],
			'SHOP_POINT_NAME' 	=> $point_name,
			'SHOP_DAOJU_TYPE'   => $lang['daoju_type'],
			'S_SHOP_ACTION' 	=> append_sid("profile.$phpEx?mode=money&amp;daoju"),
			'PRICE'             => $price,
			'USER_POINTS'       => $userdata['user_points'])
		);

		$template->pparse('body');

		include($phpbb_root_path . 'includes/page_tail.'.$phpEx);
	}

// 更改用户名颜色
elseif (isset($HTTP_GET_VARS['color'])) 
{
	if ( $userdata['user_points'] < $board_config['smena_cveta'] )
	{
		message_die(GENERAL_MESSAGE, sprintf($lang['Shop_Error_Not_Enough_Points'], $point_name));
	}
	if ( isset($HTTP_POST_VARS['submit']) && !empty($HTTP_POST_VARS['color']) )
	{
		$color = trim(htmlspecialchars($HTTP_POST_VARS['color']));
		if ( !ereg("^[A-Za-z0-9#]+$", $color) )// 匹配颜色代码
		{
			message_die(GENERAL_MESSAGE, $lang['Shop_Error_Enter_Char_invalid']);
		}

		$sql = "UPDATE " . USERS_TABLE . "
			SET user_nic_color = '" . str_replace("\'", "''", $color) . "'
			WHERE user_id = " . $userdata['user_id'];
			
		if ( !($result = $db->sql_query($sql)) )
		{
			message_die(GENERAL_ERROR, 'Could not update users table', '', __LINE__, __FILE__, $sql);
		}

		$sql = "UPDATE " . USERS_TABLE . "
			SET user_points = user_points - " . $board_config['smena_cveta'] . "
			WHERE user_id = " . $userdata['user_id'];
			
		if ( !($result = $db->sql_query($sql)) )
		{
			message_die(GENERAL_ERROR, 'Could not update users table', '', __LINE__, __FILE__, $sql);
		}
		$ostatok = $userdata['user_points'] - $board_config['smena_cveta'];

		message_die(GENERAL_MESSAGE, '用户名颜色更改成功！<br />系统已从您的虚拟账户中收取 ' . $board_config['smena_cveta'] . $point_name . '<br />您目前的虚拟账户剩余 ' . $ostatok . $point_name);
	}
	else
	{
		$page_title = $lang['Shop_Change_Username_Color'];
		include($phpbb_root_path . 'includes/page_header.'.$phpEx);

		$template->set_filenames(array(
			'body' => 'mods/shop_action.tpl')
		);

		$template->assign_vars(array(
			'L_SHOP_ACTION' 	=> $lang['Shop_Change_Username_Color'],
			'SHOP_ACTION' 		=> $lang['Shop_Enter_Color'],
			'SHOP_ACTION_DB' 	=> 'color',
			'SHOP_ACTION_MAX' 	=> '25',
			'SHOP_MONEY' 		=> $board_config['smena_cveta'],
			'SHOP_POINT_NAME' 	=> $point_name,
			'S_SHOP_ACTION' 	=> append_sid("profile.$phpEx?mode=money&amp;color"))
		);

		$template->pparse('body');

		include($phpbb_root_path . 'includes/page_tail.'.$phpEx);
	}
} 
// 更改等级
elseif (isset($HTTP_GET_VARS['rank'])) 
{
	if ( $userdata['user_points'] < $board_config['smena_zvaniya'] )
	{
		message_die(GENERAL_MESSAGE, sprintf($lang['Shop_Error_Not_Enough_Points'], $point_name));
	}
	if ( isset($HTTP_POST_VARS['submit']) )
	{
		$rank = trim(htmlspecialchars($HTTP_POST_VARS['rank']));// 把字符串转换为实体，然后去除

		$sql = "UPDATE " . USERS_TABLE . "
			SET user_zvanie = '" . str_replace("\'", "''", $rank) . "'
			WHERE user_id = " . $userdata['user_id'];
		if ( !($result = $db->sql_query($sql)) )
		{
			message_die(GENERAL_ERROR, 'Could not update users table', '', __LINE__, __FILE__, $sql);
		}

		$sql = "UPDATE " . USERS_TABLE . "
			SET user_points = user_points - " . $board_config['smena_zvaniya'] . "
			WHERE user_id = " . $userdata['user_id'];
		if ( !($result = $db->sql_query($sql)) )
		{
			message_die(GENERAL_ERROR, 'Could not update users table', '', __LINE__, __FILE__, $sql);
		}
		$ostatok = $userdata['user_points'] - $board_config['smena_zvaniya'];

		message_die(GENERAL_MESSAGE, '等级更改成功！<br />系统已从您的虚拟账户中收取 ' . $board_config['smena_zvaniya'] . $point_name . '<br />您目前的虚拟账户剩余 ' . $ostatok . $point_name);
	}
	else
	{
		$page_title = $lang['Shop_Change_Rank'];
		include($phpbb_root_path . 'includes/page_header.'.$phpEx);

		$template->set_filenames(array(
			'body' => 'mods/shop_action.tpl')
		);

		$template->assign_vars(array(
			'L_SHOP_ACTION' 	=> $lang['Shop_Change_Rank'],
			'SHOP_ACTION' 		=> $lang['Shop_Enter_Rank'],
			'SHOP_ACTION_DB' 	=> 'rank',
			'SHOP_ACTION_MAX' 	=> '50',
			'SHOP_MONEY' 		=> $board_config['smena_zvaniya'],
			'SHOP_POINT_NAME' 	=> $point_name,
			'S_SHOP_ACTION' 	=> append_sid("profile.$phpEx?mode=money&amp;rank"))
		);

		$template->pparse('body');

		include($phpbb_root_path . 'includes/page_tail.'.$phpEx);
	}
}
// 购买本站账号
elseif (isset($HTTP_GET_VARS['acc'])) 
{
	if ( $userdata['user_points'] < $board_config['pokupka_uchetki'] )
	{
		message_die(GENERAL_MESSAGE, sprintf($lang['Shop_Error_Not_Enough_Points'], $point_name));
	}
	if ( isset($HTTP_POST_VARS['submit']) && !empty($HTTP_POST_VARS['useracc']) )
	{
		$useracc = phpbb_clean_username($HTTP_POST_VARS['useracc']);
		$nedeli = time() - ($board_config['pokupka_uchetki_nedeli'] * 604800);// 604800 = 1周

		$sql = "SELECT * 
			FROM " . USERS_TABLE . " 
			WHERE username LIKE '" . str_replace("\'", "''", $useracc) . "' AND user_id <> " . ANONYMOUS . " AND user_level = " . USER . "
			AND user_posts <= " . $board_config['pokupka_uchetki_posts'] . " AND user_regdate <= $nedeli";
			
		if ( !($result = $db->sql_query($sql)) )
		{
			message_die(GENERAL_ERROR, 'Could not obtain search results', '', __LINE__, __FILE__, $sql);
		}
		
		if ( $row = $db->sql_fetchrow($result) )
		{
			$arr = array("0","1","2","3","4","5","6","7","8","9","A","B","C","D","E","F","G","H","I","J","K","L","M","N","O","P","Q","R","S","T","U","V","W","X","Y","Z","a","b","c","d","e","f","g","h","i","j","k","l","m","n","o","p","q","r","s","t","u","v","w","x","y","z");
			$new_password = $arr[rand(0,61)].$arr[rand(0,61)].$arr[rand(0,61)].$arr[rand(0,61)].$arr[rand(0,61)].$arr[rand(0,61)].$arr[rand(0,61)];
			$new_password_md = md5($new_password);

			$sql = "UPDATE " . USERS_TABLE . "
				SET user_password = '$new_password_md', user_active = 1, user_email = 'sale" . time() . "@account.forum'
				WHERE user_id = " . $row['user_id'];
				
			if ( !($result = $db->sql_query($sql)) )
			{
				message_die(GENERAL_ERROR, 'Could not update users table', '', __LINE__, __FILE__, $sql);
			}
		}
		else 
		{
			message_die(GENERAL_MESSAGE, '无法购买账号 ' . $useracc . ' ！可能因素：<br />- 该账号的帖子数量和注册日期没有符合您购买的标准<br />- 您购买的是管理员的帐号<br />- 该帐号不存在（在这种情况下，如果你喜欢 ' . $useracc . ' 这个用户名，您可以 <a href="'.append_sid("shop.$phpEx?id=1").'">' . $lang['Shop_Change_Username'] . '</a>）');
		}

		$sql = "UPDATE " . USERS_TABLE . "
			SET user_points = user_points - " . $board_config['pokupka_uchetki'] . "
			WHERE user_id = " . $userdata['user_id'];
		if ( !($result = $db->sql_query($sql)) )
		{
			message_die(GENERAL_ERROR, 'Could not update users table', '', __LINE__, __FILE__, $sql);
		}
		$ostatok = $userdata['user_points'] - $board_config['pokupka_uchetki'];

		message_die(GENERAL_MESSAGE, '恭喜您！帐号 <b>' . $useracc . '</b> 已被您成功购买！<br />新密码：' . $new_password . '<br />为了避免和原注册者之间的冲突，系统已把原来帐号的 E-mail 地址更改为一个不存在的 E-mail 地址<br />系统已从您的虚拟账户收取 ' . $board_config['pokupka_uchetki'] . $point_name . '<br />您的虚拟账户剩余 ' . $ostatok . $point_name);
	}
	else
	{
		$page_title = $lang['Shop_Buy_Account'];
		include($phpbb_root_path . 'includes/page_header.'.$phpEx);

		$template->set_filenames(array(
			'body' => 'mods/shop_action.tpl')
		);

		$template->assign_vars(array(
			'L_SHOP_ACTION' 	=> $lang['Shop_Buy_Account'],
			'SHOP_ACTION' 		=> $lang['Shop_Enter_Buy_Username'],
			'SHOP_ACTION_DB' 	=> 'useracc',
			'SHOP_ACTION_MAX' 	=> '25',
			'SHOP_MONEY' 		=> $board_config['pokupka_uchetki'],
			'SHOP_POINT_NAME' 	=> $point_name,
			'S_SHOP_ACTION' 	=> append_sid("profile.$phpEx?mode=money&amp;acc"))
		);

		$template->pparse('body');

		include($phpbb_root_path . 'includes/page_tail.'.$phpEx);
	}
} 
// 解除封禁
elseif (isset($HTTP_GET_VARS['ban'])) 
{
	if ( $userdata['user_points'] < $board_config['razblokirovka_druga'] )
	{
		message_die(GENERAL_MESSAGE, sprintf($lang['Shop_Error_Not_Enough_Points'], $point_name));
	}
	if ( isset($HTTP_POST_VARS['submit']) && !empty($HTTP_POST_VARS['userban']) )
	{
		$userban = phpbb_clean_username($HTTP_POST_VARS['userban']);

		$sql = "SELECT * 
			FROM " . USERS_TABLE . " 
			WHERE username LIKE '" . str_replace("\'", "''", $userban) . "' AND user_id <> " . ANONYMOUS . " 
			AND user_id <> " . $userdata['user_id'] . " AND user_level = " . USER . " AND user_warnings > 0";
		if ( !($result = $db->sql_query($sql)) )
		{
			message_die(GENERAL_ERROR, 'Could not obtain users results', '', __LINE__, __FILE__, $sql);
		}
		if ( $row = $db->sql_fetchrow($result) )
		{
			$sql = "SELECT id, user_id, expire, modification 
				FROM " . REPUTATION_TABLE . "
				WHERE user_id = " . $row['user_id'] . " AND modification = 4";
			if( !($resultat = $db->sql_query($sql)) )
			{
				message_die(GENERAL_ERROR, 'Could not query users', '', __LINE__, __FILE__, $sql);
			}
			if ( $roww = $db->sql_fetchrow($resultat) )
			{
				$expire_money = ceil((($roww['expire'] - time()) / 60) / 60) * $board_config['razblokirovka_druga'];
				if ( $expire_money > $userdata['user_points'] )
				{
					message_die(GENERAL_MESSAGE, '对不起，您没有足够的' . $point_name . '为 ' . $userban . ' 解除黑名单！');
				}
				db_query('DELETE FROM {REPUTATION_TABLE} WHERE id = %d', $roww['id']);
				db_query('DELETE FROM {REPUTATION_TEXT_TABLE} WHERE id = %d', $roww['id']);
				db_query('DELETE FROM {BANLIST_TABLE} WHERE ban_userid = %d', $row['user_id']);
				db_query('UPDATE {USERS_TABLE} SET user_warnings = user_warnings - 1 WHERE user_id = %d', $row['user_id']);
				db_query('UPDATE {USERS_TABLE} SET user_allow_pm = 1 WHERE user_id = %d', $row['user_id']);
			} 
			else 
			{
				message_die(GENERAL_MESSAGE, '您的朋友没有被列为黑名单！');
			}
		}
		else
		{
			message_die(GENERAL_MESSAGE, '您不能为自己接触黑名单或用户不存在！');
		}

		$sql = "UPDATE " . USERS_TABLE . "
			SET user_points = user_points - $expire_money
			WHERE user_id = " . $userdata['user_id'];
		if ( !($result = $db->sql_query($sql)) )
		{
			message_die(GENERAL_ERROR, 'Could not update users table', '', __LINE__, __FILE__, $sql);
		}
		$ostatok = $userdata['user_points'] - $expire_money;

		message_die(GENERAL_MESSAGE, '用户 ' . $userban . ' 的黑名单已成功解除！<br />系统已从您的虚拟账户中收取 ' . $expire_money . $point_name . '<br />您目前的虚拟账户剩余 ' . $ostatok . $point_name);
	}
	else
	{
		$page_title = $lang['Shop_Remove_Ban'];
		include($phpbb_root_path . 'includes/page_header.'.$phpEx);

		$template->set_filenames(array(
			'body' => 'mods/shop_action.tpl')
		);

		$template->assign_vars(array(
			'L_SHOP_ACTION' 	=> $lang['Shop_Remove_Ban'],
			'SHOP_ACTION' 		=> $lang['Shop_Enter_Username'],
			'SHOP_ACTION_DB' 	=> 'userban',
			'SHOP_ACTION_MAX' 	=> '25',
			'SHOP_MONEY' 		=> '（每小时需要 ' . $board_config['razblokirovka_druga'] . '）',
			'SHOP_POINT_NAME' 	=> $point_name,
			'S_SHOP_ACTION' 	=> append_sid("profile.$phpEx?mode=money&amp;ban"))
		);

		$template->pparse('body');

		include($phpbb_root_path . 'includes/page_tail.'.$phpEx);
	}
}
// 购买ICQ 
elseif (isset($HTTP_GET_VARS['icq'])) 
{

	if ( $userdata['user_points'] > 0 )
	{
	if ( isset($HTTP_GET_VARS['icq']) && !empty($HTTP_GET_VARS['id']) )
	{
		$uin = abs(intval($HTTP_GET_VARS['id']));

		$sql = "SELECT * 
			FROM ".$table_prefix."shop_icq
			WHERE id = $uin";
		if( !($result = $db->sql_query($sql)) )
		{
			message_die(GENERAL_ERROR, 'Could not query icq', '', __LINE__, __FILE__, $sql);
		}
		if ( $row = $db->sql_fetchrow($result) )
		{
			if ( $userdata['user_points'] < $row['icq_cost'] )
			{
				message_die(GENERAL_MESSAGE, sprintf($lang['Shop_Error_Not_Enough_Points'], $point_name));
			}

			$cost = $row['icq_cost'];
			$pass = $row['icq_password'];
			$number = $row['icq_number'];

			$sql = "DELETE FROM ".$table_prefix."shop_icq 
				WHERE id = $uin";
				
			if ( !$result = $db->sql_query($sql) )
			{
				message_die(GENERAL_MESSAGE, 'Could not delete ICQ id！');
			}

			$sql = "UPDATE " . USERS_TABLE . "
				SET user_points = user_points - " . $cost . "
				WHERE user_id = " . $userdata['user_id'];
			if ( !($result = $db->sql_query($sql)) )
			{
				message_die(GENERAL_ERROR, 'Could not update users table', '', __LINE__, __FILE__, $sql);
			}
			$ostatok = $userdata['user_points'] - $cost;

			message_die(GENERAL_MESSAGE, '谢谢谢您的购买！<br />帐号：<b>' . $number . '</b><br />密码：<input type="text" value="' . $pass . '" /><br />系统已从您的虚拟账户中收取 ' . $cost . $point_name . '<br />您的虚拟账户剩余 ' . $ostatok . $point_name);
		} 
		else 
		{
			message_die(GENERAL_MESSAGE, '该号码不存在！');
		}
	}
	else
	{
		$page_title = $lang['Shop_Buy_ICQ'];
		include($phpbb_root_path . 'includes/page_header.'.$phpEx);

		$template->set_filenames(array(
			'body' => 'mods/shop_action_icq.tpl')
		);

		$sql = "SELECT * 
			FROM ".$table_prefix."shop_icq
			ORDER BY id ASC LIMIT $start, " . $board_config['topics_per_page'];
		if( !($result = $db->sql_query($sql)) )
		{
			message_die(GENERAL_ERROR, 'Could not query users');
		}

		if ( $row = $db->sql_fetchrow($result) )
		{
			$i = 0;
			do
			{
				$uin = $row['icq_number'];
				$cost = $row['icq_cost'];
				$id = $row['id'];

				$template->assign_block_vars('memberrow', array(
					'UIN' 	=> $uin,
					'COST' 	=> $cost,
					'U_PAY' => append_sid("profile.$phpEx?mode=money&amp;icq&amp;id=$id"))
				);

				$i++;
			}
			while ( $row = $db->sql_fetchrow($result) );
			$db->sql_freeresult($result);
		} else {
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
			$pagination = generate_pagination("profile.$phpEx?mode=money&amp;icq", $total_members, $board_config['topics_per_page'], $start);
		}
		$db->sql_freeresult($result);

		if ( $total_members > $board_config['topics_per_page'] )
		{
			$template->assign_vars(array(
				'PAGINATION' => $pagination)
			);
		}
		$template->pparse('body');

		include($phpbb_root_path . 'includes/page_tail.'.$phpEx);
	}
	} else {
		message_die(GENERAL_MESSAGE, sprintf($lang['Shop_Error_Not_Enough_Points'], $point_name));
	}
} 
// 投放广告
elseif (isset($HTTP_GET_VARS['sites'])) 
{
	if ( !$board_config['sites'] )
	{
		message_die(GENERAL_MESSAGE, $lang['Shop_Error_not_open']);
	}
	if ( ($userdata['user_points'] > $board_config['verh_pay']) || ($userdata['user_points'] > $board_config['niz_pay']) )
	{
		$sql = "SELECT count(*) AS total FROM ".$table_prefix."shop_sites WHERE site_order = 0";
		$result = $db->sql_query($sql);
		$total = $db->sql_fetchrow($result);
		$total_verh = $total['total'];
		$sql = "SELECT count(*) AS total FROM ".$table_prefix."shop_sites WHERE site_order = 1";
		$result = $db->sql_query($sql);
		$total = $db->sql_fetchrow($result);
		$total_niz = $total['total'];
		if ( ($board_config['verh'] == $total_verh) && ($board_config['niz'] == $total_niz) )
		{
			message_die(GENERAL_MESSAGE, '目前的广告位已占满！');
		}

		if ( isset($HTTP_POST_VARS['submit']) && !empty($HTTP_POST_VARS['url']) && !empty($HTTP_POST_VARS['desc']) && !empty($HTTP_POST_VARS['time']) )
		{
			$time = intval($HTTP_POST_VARS['time']);
			$url = trim(htmlspecialchars($HTTP_POST_VARS['url']));
			$desc = trim(htmlspecialchars($HTTP_POST_VARS['desc']));
			$order = ( isset($HTTP_POST_VARS['order']) ) ? ( ($HTTP_POST_VARS['order']) ? 1 : 0 ) : 1;

			if ( preg_match('#\?#', $url) && $board_config['ref_url'] )
			{
				message_die(GENERAL_MESSAGE, '搞不懂什么东西来着！');
			}
			if ( !$order )
			{
				if ( $board_config['verh'] == $total_verh )
				{
					message_die(GENERAL_MESSAGE, '顶部广告位已占满！');
				}
				$stoimost = $board_config['verh_pay'] * $time;

				if ( $stoimost > $userdata['user_points'] )
				{
					message_die(GENERAL_MESSAGE, sprintf($lang['Shop_Error_Not_Enough_Points'], $point_name));
				}
				$vremya_sek = $time * 604800;
				$vremya_okonch = time() + $vremya_sek;

				$sql = "INSERT INTO ".$table_prefix."shop_sites (site_url, site_desc, site_time, site_order) VALUES ('" . str_replace("\'", "''", $url) . "', '" . str_replace("\'", "''", $desc) . "', $vremya_okonch, 0)";
				if ( !$result = $db->sql_query($sql) )
				{
					message_die(GENERAL_MESSAGE, 'Could not insert shop_site info', '', __LINE__, __FILE__, $sql);
				}

				$sql = "UPDATE " . USERS_TABLE . "
					SET user_points = user_points - " . $stoimost . "
					WHERE user_id = " . $userdata['user_id'];
				if ( !($result = $db->sql_query($sql)) )
				{
					message_die(GENERAL_ERROR, 'Could not update users table', '', __LINE__, __FILE__, $sql);
				}
				$ostatok = $userdata['user_points'] - $stoimost;

				message_die(GENERAL_MESSAGE, '广告已成功投放！<br />系统已从您的虚拟账户中收取 ' . $stoimost . $point_name . '<br />目前您的虚拟账户剩余 ' . $ostatok . $point_name);
			} 
			else 
			{
				if ( $board_config['niz'] == $total_niz )
				{
					message_die(GENERAL_MESSAGE, '底部位置已满！');
				}
				$stoimost = $board_config['niz_pay'] * $time;

				if ( $stoimost > $userdata['user_points'] )
				{
					message_die(GENERAL_MESSAGE, sprintf($lang['Shop_Error_Not_Enough_Points'], $point_name));
				}
				$vremya_sek = $time * 604800;
				$vremya_okonch = time() + $vremya_sek;

				$sql = "INSERT INTO ".$table_prefix."shop_sites (site_url, site_desc, site_time, site_order) VALUES ('" . str_replace("\'", "''", $url) . "', '" . str_replace("\'", "''", $desc) . "', $vremya_okonch, 1)";
				if ( !$result = $db->sql_query($sql) )
				{
					message_die(GENERAL_MESSAGE, 'Could not insert shop_site info', '', __LINE__, __FILE__, $sql);
				}

				$sql = "UPDATE " . USERS_TABLE . "
					SET user_points = user_points - " . $stoimost . "
					WHERE user_id = " . $userdata['user_id'];
				if ( !($result = $db->sql_query($sql)) )
				{
					message_die(GENERAL_ERROR, 'Could not update users table', '', __LINE__, __FILE__, $sql);
				}
				$ostatok = $userdata['user_points'] - $stoimost;

				message_die(GENERAL_MESSAGE, '广告已成功投放！<br />系统已从您的虚拟账户中收取 ' . $stoimost . $point_name . '<br />目前您的虚拟账户剩余 ' . $ostatok . $point_name);
			}
		}
		else
		{
			$page_title = $lang['Shop_Put_In_Ad'];
			include($phpbb_root_path . 'includes/page_header.'.$phpEx);

			$template->set_filenames(array(
				'body' => 'mods/shop_action_sites.tpl')
			);
			$template->assign_vars(array(
				'S_SHOP_ACTION' => append_sid("profile.$phpEx?mode=money&amp;sites"))
			);
			$template->pparse('body');

			include($phpbb_root_path . 'includes/page_tail.'.$phpEx);
		}
	} 
	else 
	{
		message_die(GENERAL_MESSAGE, sprintf($lang['Shop_Error_Not_Enough_Points'], $point_name));
	}
} 
// 赚取积分
elseif (isset($HTTP_GET_VARS['url'])) 
{
	if ( isset($HTTP_GET_VARS['url']) && !empty($HTTP_GET_VARS['id']) )
	{
		$uin = abs(intval($HTTP_GET_VARS['id']));

		$sql = "SELECT * 
			FROM ".$table_prefix."shop_url
			WHERE id = $uin";
		if( !($result = $db->sql_query($sql)) )
		{
			message_die(GENERAL_ERROR, 'Could not query icq', '', __LINE__, __FILE__, $sql);
		}
		if ( $row = $db->sql_fetchrow($result) )
		{
			$cost = $row['url_cost'];
			$url = $row['url'];
			if ( ($userdata['time_last_click'] + $board_config['time_click']) < time() )
			{
				$sql = "UPDATE " . USERS_TABLE . "
					SET user_points = user_points + " . $cost . ", time_last_click = '" . time() . "' 
					WHERE user_id = " . $userdata['user_id'];
				if ( !($result = $db->sql_query($sql)) )
				{
					message_die(GENERAL_ERROR, 'Could not update users table', '', __LINE__, __FILE__, $sql);
				}

				header('Location: ' . $url);
				exit;
			} else {
				$ost = time() - ($userdata['time_last_click'] + $board_config['time_click']);
				message_die(GENERAL_MESSAGE, '请勿重复点击，距离第二次点击还剩 ' . $ost . ' 秒！');
			}
		} else {
			message_die(GENERAL_MESSAGE, 'Такой ссылки не существует');
		}
	}
	else
	{
		$page_title = $lang['Shop_Earn_points'];
		include($phpbb_root_path . 'includes/page_header.'.$phpEx);

		$template->set_filenames(array(
			'body' => 'mods/shop_action_url.tpl')
		);

		$sql = "SELECT * 
			FROM ".$table_prefix."shop_url
			ORDER BY id ASC LIMIT $start, " . $board_config['topics_per_page'];
		if( !($result = $db->sql_query($sql)) )
		{
			message_die(GENERAL_ERROR, 'Could not query users', '', __LINE__, __FILE__, $sql);
		}

		if ( $row = $db->sql_fetchrow($result) )
		{
			$i = 0;
			do
			{
				$cost = $row['url_cost'];
				$nazv = $row['nazvanie'];
				$id = $row['id'];

				$template->assign_block_vars('memberrow', array(
					'NAZVANIE' 			=> $nazv,
					'COST' 				=> $cost,
					'SHOP_POINT_NAME' 	=> $point_name,
					'U_PAY' 			=> append_sid("profile.$phpEx?mode=money&amp;url&amp;id=$id"))
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
			FROM ".$table_prefix."shop_url";

		if ( !($result = $db->sql_query($sql)) )
		{
			message_die(GENERAL_ERROR, 'Error getting total users', '', __LINE__, __FILE__, $sql);
		}

		if ( $total = $db->sql_fetchrow($result) )
		{
			$total_members = $total['total'];
			$pagination = generate_pagination("profile.$phpEx?mode=money&amp;url", $total_members, $board_config['topics_per_page'], $start);
		}
		$db->sql_freeresult($result);

		if ( $total_members > $board_config['topics_per_page'] )
		{
			$template->assign_vars(array(
				'PAGINATION' => $pagination)
			);
		}
		$template->pparse('body');

		include($phpbb_root_path . 'includes/page_tail.'.$phpEx);
	}
} 
// Конвертировать в рубли ，与简体中文无关
elseif (isset($HTTP_GET_VARS['exch'])) 
{
	if ( !$board_config['pay_money'] )
	{
		message_die(GENERAL_MESSAGE, $lang['Shop_Error_not_open']);
	}
	$HTTP_POST_VARS['exch'] = str_replace('-','+',$HTTP_POST_VARS['exch']);
	if ($userdata['user_points'] < $HTTP_POST_VARS['exch'])
	{
		message_die(GENERAL_MESSAGE, 'Попытка обмана не удалась');
	}
	if ( isset($HTTP_POST_VARS['submit']) && !empty($HTTP_POST_VARS['exch']) )
	{
		$userexch = abs(intval($HTTP_POST_VARS['exch']));
		$payexch = $userexch * $board_config['kurs_payment'];

		$sql = "UPDATE " . USERS_TABLE . "
			SET user_money_earned = user_money_earned + $payexch
			WHERE user_id = " . $userdata['user_id'];
		if ( !($result = $db->sql_query($sql)) )
		{
			message_die(GENERAL_ERROR, 'Could not update users table', '', __LINE__, __FILE__, $sql);
		}

		$sql = "UPDATE " . CONFIG_TABLE . "
			SET config_value = config_value + $payexch
			WHERE config_name = 'money_earned'";
		if ( !($result = $db->sql_query($sql)) )
		{
			message_die(GENERAL_ERROR, 'Could not update config table', '', __LINE__, __FILE__, $sql);
		}

		$sql = "UPDATE " . USERS_TABLE . "
			SET user_points = user_points - $userexch
			WHERE user_id = " . $userdata['user_id'];
		if ( !($result = $db->sql_query($sql)) )
		{
			message_die(GENERAL_ERROR, 'Could not update users table', '', __LINE__, __FILE__, $sql);
		}
		$ostatok = $userdata['user_points'] - $userexch;
		$ostatok_rub = $userdata['user_money_earned'] + $payexch;

		message_die(GENERAL_MESSAGE, sprintf($lang['Shop_error_message_die_1'], $payexch, $ostatok_rub, $userexch, $ostatok));
	}
	else
	{
		$page_title = $lang['Shop_Pay_Money'];
		include($phpbb_root_path . 'includes/page_header.'.$phpEx);

		$template->set_filenames(array(
			'body' => 'mods/shop_action.tpl')
		);

		$template->assign_vars(array(
			'L_SHOP_ACTION' 	=> $lang['Shop_Pay_Money'],
			'SHOP_ACTION' 		=> sprintf($lang['Shop_Pay_Money_Action'], $userdata['user_points']),
			'SHOP_ACTION_DB' 	=> 'exch',
			'SHOP_ACTION_MAX' 	=> '100',
			'SHOP_MONEY' 		=> $lang['Shop_Pay_Money_money'],
			'S_SHOP_ACTION' 	=> append_sid("profile.$phpEx?mode=money&amp;exch"))
		);

		$template->pparse('body');

		include($phpbb_root_path . 'includes/page_tail.'.$phpEx);
	}
} 
else 
{

	if ( empty($HTTP_GET_VARS[POST_USERS_URL]) || $HTTP_GET_VARS[POST_USERS_URL] == ANONYMOUS )
	{
		message_die(GENERAL_MESSAGE, $lang['No_user_id_specified']);
	}

	$user = intval($HTTP_GET_VARS[POST_USERS_URL]);

	if ( $user == $userdata['user_id'] )
	{
		message_die(GENERAL_MESSAGE, '您不能转给您自己！');
	}

	$sql = "SELECT username 
		FROM " . USERS_TABLE . " 
		WHERE user_id = '$user'";
	if ( !$result = $db->sql_query($sql) )
	{
		message_die(GENERAL_ERROR, 'Could not obtain user information for sendpassword', '', __LINE__, __FILE__, $sql);
	}
	if ( !$row = $db->sql_fetchrow($result) )
	{
		message_die(GENERAL_MESSAGE, $lang['No_user_id_specified']);
	}
	$username = $row['username'];

	if ( isset($HTTP_POST_VARS['submit']) && !empty($HTTP_POST_VARS['money_send']) )
	{
		$money = intval($HTTP_POST_VARS['money_send']);
		if ( $userdata['user_level'] != ADMIN )
		{
			$money = abs($money);
		}

		if ( $money > $userdata['user_points'] && $userdata['user_level'] != ADMIN )
		{
			message_die(GENERAL_MESSAGE, '您没有足够的' . $point_name);
		}

		$sql = "UPDATE " . USERS_TABLE . "
			SET user_points = user_points + $money
			WHERE user_id = $user";
		if ( !($result = $db->sql_query($sql)) )
		{
			message_die(GENERAL_ERROR, 'Could not update users table', '', __LINE__, __FILE__, $sql);
		}

		if ( $userdata['user_level'] != ADMIN )
		{
			$sql = "UPDATE " . USERS_TABLE . "
				SET user_points = user_points - $money
				WHERE user_id = " . $userdata['user_id'];
			if ( !($result = $db->sql_query($sql)) )
			{
				message_die(GENERAL_ERROR, 'Could not update users table', '', __LINE__, __FILE__, $sql);
			}
		}

		if ( $userdata['user_level'] == ADMIN )
		{
			message_die(GENERAL_MESSAGE, '用户账户 ' . $username . ' 有 ' . $money . $point_name);
		} 
		else 
		{
			message_die(GENERAL_MESSAGE, '已转给' . $username . $money . $point_name);
		}
	}
	else
	{
		$page_title = $lang['Profile_Send_Money'];
		include($phpbb_root_path . 'includes/page_header.'.$phpEx);

		$template->set_filenames(array(
			'body' => 'profile_send_money.tpl')
		);

		if ( isset($HTTP_POST_VARS['submit']) && empty($HTTP_POST_VARS['money_send']) )
		{
			$template->set_filenames(array(
				'reg_header' => 'error_body.tpl')
			);
			$template->assign_vars(array(
				'ERROR_MESSAGE' => $lang['Error_Profile_Send_Money'])
			);
			$template->assign_var_from_handle('ERROR_BOX', 'reg_header');
		}

		$template->assign_vars(array(
			'USERNAME' 			=> $username,
			'U_USER_PROFILE'	=> append_sid("{$phpbb_root_path}profile.$phpEx?mode=viewprofile&amp;u=" . $user),
			'USER_MONEY' 		=> $userdata['user_points'],
			'SHOP_POINT_NAME' 	=> $point_name,
			'L_SUBMIT' 			=> $lang['Submit'],
			'S_POST_ACTION'		=> append_sid("profile.$phpEx?mode=money&amp;u=$user"))
		);

		$template->pparse('body');

		include($phpbb_root_path . 'includes/page_tail.'.$phpEx);
	}

}

?>