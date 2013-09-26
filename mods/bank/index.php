<?php
/********************************************************
 *			bank/index.php
 *			----------
 *		copyright：phpBB Group
 *		Оптимизация под WAP: Гутник Игорь ( чел ).
 *		简体中文：爱疯的云
 *******************************************************/

define('IN_PHPBB', true);
$phpbb_root_path = './../../';
include($phpbb_root_path . 'extension.inc');
include($phpbb_root_path . 'common.' . $phpEx);
include($phpbb_root_path . 'mods/includes/constants.' . $phpEx);
include($phpbb_root_path . 'language/lang_' . $board_config['default_lang'] . '/mods/lang_bank.' . $phpEx);


//
// Start session management
//
$userdata = session_pagestart($user_ip, BANK_INDEX);
init_userprefs($userdata);
//
// End session management
//

if ( $board_config['bankpayouttime'] < 1 ) { message_die(GENERAL_MESSAGE, $lang['error_payouttime_short']); }
if ( $board_config['bankopened'] == 'off' ) { message_die(GENERAL_MESSAGE, $lang['error_bank_closed']); }

$time = time();
if ( ($time - $board_config['banklastrestocked']) > $board_config['bankpayouttime'] )
{
	$sql = "UPDATE " . CONFIG_TABLE . "
		SET config_value = '$time'
		WHERE config_name = 'banklastrestocked'";
	if ( !($db->sql_query($sql)) ) { message_die(GENERAL_MESSAGE, 'Fatal Error Updating Bank Time!<br />'); }

	$interesttime = ( ($time - $board_config['banklastrestocked']) / $board_config['bankpayouttime'] );

	$sql = 'UPDATE ' . BANK_TABLE . '
		SET holding = holding + round(((holding / 100) * ' . $board_config['bankinterest'] . ') * ' . $interesttime . ')
		' . ( ( $board_config['bank_interestcut'] ) ? "WHERE holding < " . $board_config['bank_interestcut'] : "" );
	if ( !($db->sql_query($sql)) ) { message_die(GENERAL_MESSAGE, 'Error Giving Interest Out!<br />' . $sql); }

	header("Location: index.php");
}

$sql = "SELECT *
	FROM " . BANK_TABLE . "
	WHERE user_id = " .$userdata['user_id'];
if ( !$result = $db->sql_query($sql) ) 
{ 
	message_die(CRITICAL_ERROR, 'Error Getting Bank Users?<br />'); 
}
$row = $db->sql_fetchrow($result);

if ( isset($HTTP_GET_VARS['action']) || isset($HTTP_POST_VARS['action']) ) { $action = ( isset($HTTP_POST_VARS['action']) ) ? $HTTP_POST_VARS['action'] : $HTTP_GET_VARS['action']; }
else { $action = ''; }

if ( empty($action) )
{
	$template->set_filenames(array(
		'body' => 'mods/bank_body.tpl')
	);

	if ( !isset($row['holding']) && $userdata['user_id'] > 0 )
	{
		$template->assign_block_vars('no_account', array(
			'L_NO_ACCOUNT' => $lang['no_owned_account'],
			'U_OPEN_ACCOUNT' => sprintf($lang['click_open_account'], '<a href="' . append_sid("index.$phpEx?action=createaccount") . '" title="Open an Account!">', '</a>')
		));
	}
	elseif ( $userdata['user_id'] > 0 )
	{
		$template->assign_block_vars('has_account', array());
	}

	$sql = "SELECT sum(holding) as total_holding, count(user_id) as total_users
		FROM " . BANK_TABLE . "
		WHERE id > 0";
	if ( !($result = $db->sql_query($sql)) ) { message_die(GENERAL_MESSAGE, 'Fatal Error Getting Total Users!<br />' . $sql); }
	$b_row = $db->sql_fetchrow($result);

	$bankholdings = ( $b_row['total_holding'] ) ? $b_row['total_holding'] : 0;
	$bankusers = $b_row['total_users'];

	$withdrawtotal = ( $row['fees'] == 'on' ) ? $row['holding'] - (round($row['holding'] / 100 * $board_config['bankfees'])) : $row['holding'];

	if ( $row['fees'] == 'on' && $lang['withdraw_rate'] )
	{
		$template->assign_block_vars('switch_withdraw_fees', array());
	}
	if ( $board_config['bank_minwithdraw'] )
	{
		$template->assign_block_vars('switch_min_with', array());
	}
	if ( $board_config['bank_mindeposit'] )
	{
		$template->assign_block_vars('switch_min_depo', array());
	}

	$banklocation = ' -> <a href="'.append_sid("index.".$phpEx).'" class="nav">'.$board_config['bankname'].'</a>';
	$title = $board_config['bankname'];
	$account = (!is_numeric($row['holding']) ) ? $lang['open_account'] : $lang['deposit_withdraw'];
	$page_title = $board_config['bankname'];

	$template->assign_vars(array(
		'BANKLOCATION' => $banklocation,
		'L_BANK_TITLE' => $title,
		'L_BANK_ACCOUNT_TITLE' => $account,

		'BANK_OPENED' => create_date($board_config['default_dateformat'], $board_config['bankopened'], $board_config['board_timezone']),
		'BANK_HOLDINGS' => $bankholdings,
		'BANK_ACCOUNTS' => $bankusers,
		'BANK_FEES' => $board_config['bankfees'],
		'BANK_INTEREST' => $board_config['bankinterest'],
		'BANK_MIN_WITH' => $board_config['bank_minwithdraw'],
		'BANK_MIN_DEPO' => $board_config['bank_mindeposit'],

		'USER_BALANCE' => $row['holding'],
		'USER_GOLD' => $userdata['user_points'],
		'USER_WITHDRAW' => $withdrawtotal,

		'L_OPEN_SINCE' => $lang['bank_openedsince'],
		'L_HOLDING' => $lang['holding'],
		'L_TOTAL_ACCS' => $lang['total_accounts'],
		'L_WITHDRAW_RATE' => $lang['withdraw_rate'],
		'L_INTEREST_RATE' => $lang['interest_rate'],
		'L_USER_BALANCE' => $lang['bank_balance'],
		'L_POINTS' => $board_config['points_name'],
		'L_BANK_INFO' => $lang['bank_info'],
		'L_DEPOSIT' => $lang['button_deposit'],
		'L_WITHDRAW' => $lang['button_withdraw'],
		'L_ACTIONS' => $lang['bank_actions'],
		'L_MIN_DEPO' => $lang['min_depo'],
		'L_MIN_WITH' => $lang['min_with'],

		'U_WITHDRAW' => append_sid("index.$phpEx?action=withdraw"),
		'U_DEPOSIT' => append_sid("index.$phpEx?action=deposit")
	));
	$template->assign_block_vars('', array());
}

// Start of create account page
elseif ( $action == 'createaccount' )
{
	if ( !$userdata['session_logged_in'] )
	{
		$redirect = "index.$phpEx";
		$redirect .= ( isset($user_id) ) ? '&action=' . $action : '';
		header('Location: ' . append_sid("login.$phpEx?redirect=$redirect", true));
	}
	$template->set_filenames(array(
		'body' => 'mods/bank_body.tpl')
	);

	if ( is_numeric($row['holding']) )
	{
		message_die(GENERAL_MESSAGE, $lang['yes_account'] . '!<br /><br />点击 <a href="' . sppend_sid("index.$phpEx") . '">这里</a> 返回上一页面！');
	}
	else
	{
		$sql = "INSERT INTO " . BANK_TABLE . "
			(user_id, opentime, fees)
			VALUES('{$userdata['user_id']}', '" . time() . "', 'on')";
		if ( !($db->sql_query($sql)) ) { message_die(GENERAL_MESSAGE, 'Fatal Error Adding User Account!<br />'); }

		message_die(GENERAL_MESSAGE, $lang['welcome_bank'] . $board_config['bankname'] . '！<br /><br />' . $lang['start_balance'] . '<br /><br />' . $lang['your_account'] . '<br /><br />点击 <a href="' . append_sid("index.$phpEx") . '">这里</a> 上一页面');
	}
}
	
// Start of deposit page
elseif ( $action == 'deposit' )
{
	if ( isset($HTTP_GET_VARS['deposit']) || isset($HTTP_POST_VARS['deposit']) ) { $deposit = ( isset($HTTP_POST_VARS['deposit']) ) ? intval($HTTP_POST_VARS['deposit']) : intval($HTTP_GET_VARS['deposit']); }
	else { $deposit = ''; }

	if ( !$userdata['session_logged_in'] )
	{
		$redirect = "index.$phpEx";
		$redirect .= ( isset($action) ) ? '&action=' . $action : '';
		$redirect .= ( isset($deposit) ) ? '&deposit=' . $deposit : '';
		header('Location: ' . append_sid("login.$phpEx?redirect=$redirect", true));
	}

	if ( $deposit < $board_config['bank_mindeposit'] ) { message_die(GENERAL_MESSAGE, sprintf($lang['deposit_small_amount'], $board_config['bank_mindeposit'], $board_config['points_name'])); }
	elseif ( $deposit < 1 ) { message_die(GENERAL_MESSAGE, $lang['error_deposit']); }
	elseif ( $deposit > $userdata['user_points'] ) { message_die(GENERAL_MESSAGE, $lang['error_not_enough_deposit'].'!'); }

	$sql = "UPDATE " . USERS_TABLE . "
		SET user_points = (user_points - $deposit)
		WHERE user_id = '{$userdata['user_id']}'";
	if ( !($db->sql_query($sql)) ) { message_die(GENERAL_MESSAGE, 'Fatal Updating User Points!<br />'); }

	$sql = "UPDATE " . BANK_TABLE . "
		SET holding = (holding + $deposit),
			totaldeposit = (totaldeposit + $deposit)
		WHERE user_id = '{$userdata['user_id']}'";
	if ( !($db->sql_query($sql)) ) { message_die(GENERAL_MESSAGE, 'Fatal Updating User Points!<br />'); }

	message_die(GENERAL_MESSAGE, $lang['have_deposit'] . $deposit . $board_config['points_name'] . $lang['to_account'] . '<br /><br />' . $lang['new_balance'] . ($row['holding'] + $deposit) . '<br /><br />' . $lang['leave_with'] . ($userdata['user_points'] - $deposit) . $board_config['points_name'] . $lang['on_hand'] . '<br /><br />点击 <a href="' . append_sid("index.$phpEx") . '">这里</a> 返回上一页面');
}

// Begin withdraw page
elseif ( $action == 'withdraw' )
{
	if ( isset($HTTP_GET_VARS['withdraw']) || isset($HTTP_POST_VARS['withdraw']) ) { $withdraw = ( isset($HTTP_POST_VARS['withdraw']) ) ? intval($HTTP_POST_VARS['withdraw']) : intval($HTTP_GET_VARS['withdraw']); }
	else { $withdraw = ''; }

	if ( !$userdata['session_logged_in'] )
	{
		$redirect = "index.$phpEx";
		$redirect .= ( isset($action) ) ? '&action=' . $action : '';
		$redirect .= ( isset($withdraw) ) ? '&withdraw=' . $withdraw : '';
		header('Location: ' . append_sid("login.$phpEx?redirect=$redirect", true));
	}

	if ( $withdraw < $board_config['bank_minwithdraw'] ) { message_die(GENERAL_MESSAGE, sprintf($lang['withdraw_small_amount'], $board_config['bank_minwithdraw'], $board_config['points_name'])); }
	elseif ( $withdraw < 1 ) { message_die(GENERAL_MESSAGE, $lang['error_withdraw']); }
	if ( $row['fees'] == 'on' )
	{
		$withdrawtotal = round((($withdraw / 100) * $board_config['bankfees']));
		if ( $withdrawtotal == 0 ) { $withdrawtotal = 1; }
	}
	else 
	{
		$withdrawtotal = 0;
	}
	$withdrawtotal = $withdrawtotal + $withdraw;

	if ( $row['holding'] < $withdrawtotal ) { message_die(GENERAL_MESSAGE, $lang['error_not_enough_withdraw']); }

	$sql = "UPDATE " . USERS_TABLE . "
		SET user_points = (user_points + $withdraw)
		WHERE user_id = '{$userdata['user_id']}'";
	if ( !($db->sql_query($sql)) ) { message_die(GENERAL_MESSAGE, 'Fatal Updating User Points!<br />'); }

	$sql = "UPDATE " . BANK_TABLE . "
		SET holding = (holding - $withdrawtotal),
			totalwithdrew = (totalwithdrew + $withdraw)
		WHERE user_id = '{$userdata['user_id']}'";
	if ( !($db->sql_query($sql)) ) { message_die(GENERAL_MESSAGE, 'Fatal Updating User Points!<br />'); }

	message_die(GENERAL_MESSAGE, $lang['have_withdraw'] . ' ' . $withdraw . ' ' . $board_config['points_name'] . ' ' . $lang['from_account'] . ' . <br />' . $lang['new_balance'] . ' ' . ($row['holding'] - $withdrawtotal) . '.<br />' . $lang['now_have'] . ' ' . ($userdata['user_points'] + $withdraw) . ' ' . $board_config['points_name'] . ' ' . $lang['on_hand'] . '.<br /><br />点击 <a href="' . append_sid("index.$phpEx") . '">这里</a> 返回上一页面');
}
else 
{
	redirect("index.$phpEx");
}

//
// Start output of page
//
include($phpbb_root_path . 'includes/page_header.' . $phpEx);

//
// Generate the page
//
$template->pparse('body');

include($phpbb_root_path . 'includes/page_tail.' . $phpEx);

?>