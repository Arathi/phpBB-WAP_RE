<?php
/*******************************************************
 *			admin_bank.php
 *			----------
 *		copyright：phpBB Group
 *		Оптимизация под WAP: Гутник Игорь ( чел ).
 *		中文网站：http://phpbb-wap.com
 *******************************************************/

define('IN_PHPBB', 1);

if( !empty($setmodules) )
{
	$file = basename(__FILE__);
	$module['游戏插件']['虚拟银行'] = "$file";
	return;
}

//
// Let's set the root dir for phpBB
//
$phpbb_root_path = './../../';
require($phpbb_root_path . 'extension.inc');
require('pagestart.' . $phpEx);
include($phpbb_root_path . 'language/lang_' . $board_config['default_lang'] . '/mods/lang_bank.' . $phpEx);

//
//check for userlevel
//
if( !$userdata['session_logged_in'] )
{
	header('Location: ' . append_sid("login.$phpEx?redirect=admin/admin_bank.$phpEx", true));
}

if( $userdata['user_level'] != ADMIN )
{
	message_die(GENERAL_MESSAGE, $lang['Not_Authorised']);
}
//end check

//bank pages
if ( isset($HTTP_GET_VARS['action']) || isset($HTTP_POST_VARS['action']) ) { $action = ( isset($HTTP_POST_VARS['action']) ) ? $HTTP_POST_VARS['action'] : $HTTP_GET_VARS['action']; }
else { $action = ''; }

if ( empty($action) )
{
	$template->set_filenames(array(
		'body' => 'mods/admin/bank_config_body.tpl')
	);

	$sql = "SELECT sum(holding) as holdings, sum(totaldeposit) as total_deposits, sum(totalwithdrew) as total_withdraws, count(*) as total_users
		FROM " . BANK_TABLE;
	if ( !($result = $db->sql_query($sql)) ) 
	{ 
		message_die(GENERAL_MESSAGE, 'Fatal Error Getting Total Users!'); 
	}
	$row = $db->sql_fetchrow($result);

	$bank_on_select = ( $board_config['bankopened'] != off ) ? 'SELECTED' : '';
	$bank_off_select = ( $board_config['bankopened'] != off ) ? '' : 'SELECTED';


	$template->assign_vars(array(
		'S_CONFIG_ACTION' => append_sid('admin_bank.' . $phpEx),

		'L_BANK_NAME' => $lang['bank_name'],
		'L_INTEREST_RATE' => $lang['interest_rate'],
		'L_WITHDRAW_RATE' => $lang['withdraw_rate'],
		'L_PAY_TIME' => $lang['interest_pay_time'],
		'L_STATUS' => $lang['bank_status'],
		'L_HOLDING' => $lang['holding'],
		'L_WITHDRAWN' => $lang['total_withdrawn'],
		'L_DEPOSITED' => $lang['total_deposited'],
		'L_ACCOUNTS' => $lang['total_accounts'],
		'L_UPDATE' => $lang['button_update'],
		'L_SECONDS' => $lang['seconds'],
		'L_FIND' => $lang['button_find'],
		'L_EDIT_ACCOUNT' => $lang['button_edit_account'],
		'L_EDIT_ACCOUNTS' => $lang['edit_account'],
		'L_TABLE_TITLE' => $lang['bank_modify'],
		'L_DISABLE_INTEREST' => $lang['disable_interest'],
		'L_ZERO_FOR_NONE' => $lang['zero_for_none'],
		'L_POINTS' => $board_config['points_name'],
		'L_MIN_DEPO' => $lang['min_depo'],
		'L_MIN_WITH' => $lang['min_with'],

		'BANK_ACCOUNTS' => ( $row['total_users'] ) ? $row['total_users'] : '0',
		'BANK_DEPOSITS' => ( $row['total_deposits'] ) ? $row['total_deposits'] : '0',
		'BANK_WITHDRAWS' => ( $row['total_withdraws'] ) ? $row['total_withdraws'] : '0',
		'BANK_HOLDING' => ( $row['holdings'] ) ? $row['holdings'] : '0', 
		'BANK_DISABLE_INTEREST' => $board_config['bank_interestcut'],
		'BANK_PAY_TIME' => $board_config['bankpayouttime'],
		'BANK_FEES' => $board_config['bankfees'],
		'BANK_INTEREST' => $board_config['bankinterest'],
		'BANK_NAME' => $board_config['bankname'],
		'BANK_MIN_DEPO' => $board_config['bank_mindeposit'],
		'BANK_MIN_WITH' => $board_config['bank_minwithdraw'],

		'SELECT_STATUS_ON' => $bank_on_select,
		'SELECT_STATUS_OFF' => $bank_off_select,

		'BANKTITLE' => $lang['bank_settings'],
		'BANKEXPLAIN' => $lang['bank_settings_explain']
	));
	
}
elseif ( $action == 'update_config')
{
	if ( isset($HTTP_GET_VARS['name']) || isset($HTTP_POST_VARS['name']) ) { $name = ( isset($HTTP_POST_VARS['name']) ) ? $HTTP_POST_VARS['name'] : $HTTP_GET_VARS['name']; }
	else { $name = ''; }
	if ( isset($HTTP_GET_VARS['interestrate']) || isset($HTTP_POST_VARS['interestrate']) ) { $interestrate = ( isset($HTTP_POST_VARS['interestrate']) ) ? intval($HTTP_POST_VARS['interestrate']) : intval($HTTP_GET_VARS['interestrate']); }
	else { $interestrate = ''; }
	if ( isset($HTTP_GET_VARS['withdrawfee']) || isset($HTTP_POST_VARS['withdrawfee']) ) { $withdrawfee = ( isset($HTTP_POST_VARS['withdrawfee']) ) ? intval($HTTP_POST_VARS['withdrawfee']) : intval($HTTP_GET_VARS['withdrawfee']); }
	else { $withdrawfee = ''; }
	if ( isset($HTTP_GET_VARS['paymenttime']) || isset($HTTP_POST_VARS['paymenttime']) ) { $paymenttime = ( isset($HTTP_POST_VARS['paymenttime']) ) ? intval($HTTP_POST_VARS['paymenttime']) : intval($HTTP_GET_VARS['paymenttime']); }
	else { $paymenttime = ''; }
	if ( isset($HTTP_GET_VARS['disableinterest']) || isset($HTTP_POST_VARS['disableinterest']) ) { $disableinterest = ( isset($HTTP_POST_VARS['disableinterest']) ) ? intval($HTTP_POST_VARS['disableinterest']) : intval($HTTP_GET_VARS['disableinterest']); }
	else { $disableinterest = ''; }
	if ( isset($HTTP_GET_VARS['min_depo']) || isset($HTTP_POST_VARS['min_depo']) ) { $min_depo = ( isset($HTTP_POST_VARS['min_depo']) ) ? intval($HTTP_POST_VARS['min_depo']) : intval($HTTP_GET_VARS['min_depo']); }
	else { $min_depo = ''; }
	if ( isset($HTTP_GET_VARS['min_with']) || isset($HTTP_POST_VARS['min_with']) ) { $min_with = ( isset($HTTP_POST_VARS['min_with']) ) ? intval($HTTP_POST_VARS['min_with']) : intval($HTTP_GET_VARS['min_with']); }
	else { $min_with = ''; }

	if ( isset($HTTP_GET_VARS['status']) || isset($HTTP_POST_VARS['status']) ) { $status = ( isset($HTTP_POST_VARS['status']) ) ? $HTTP_POST_VARS['status'] : $HTTP_GET_VARS['status']; }
	else { $status = ''; }


	$sql = array();

	if ( stripslashes($name) != $board_config['bankname'] )
	{
		$sql[] = "UPDATE ". CONFIG_TABLE . "
			SET config_value = '$name'
			WHERE config_name = 'bankname'";
	}
	if ( $interestrate != $board_config['bankinterest'] )
	{
		$sql[] = "UPDATE ". CONFIG_TABLE . "
			SET config_value = '$interestrate'
			WHERE config_name = 'bankinterest'";
	}
	if ( $withdrawfee != $board_config['bankfees'] )
	{
		$sql[] = "UPDATE ". CONFIG_TABLE . "
			SET config_value = '$withdrawfee'
			WHERE config_name = 'bankfees'"; 
	}
	if ( $paymenttime != $board_config['bankpayouttime'] )
	{
		$sql[] = "UPDATE ". CONFIG_TABLE . "
			SET config_value = '$paymenttime'
			WHERE config_name = 'bankpayouttime'";
	}
	if ( $min_with != $board_config['bank_minwithdraw'] )
	{
		$sql[] = "UPDATE ". CONFIG_TABLE . "
			SET config_value = '$min_with'
			WHERE config_name = 'bank_minwithdraw'";
	}
	if ( $min_depo != $board_config['bank_mindeposit'] )
	{
		$sql[] = "UPDATE ". CONFIG_TABLE . "
			SET config_value = '$min_depo'
			WHERE config_name = 'bank_mindeposit'";
	}
	if ( $disableinterest != $board_config['bank_interestcut'] )
	{
		$sql[] = "UPDATE ". CONFIG_TABLE . "
			SET config_value = '$disableinterest'
			WHERE config_name = 'bank_interestcut'";
	}
	if ( $status != $board_config['bankopened'] && ( $status == 'off' || $status == 'on' ) )
	{
		$sql[] = "UPDATE ". CONFIG_TABLE . "
			SET config_value = '" . ( ( $status == 'off' ) ? 'off' : time() ) . "'
			WHERE config_name = 'bankopened'";
	}

	$sql_count = count($sql);
	for ( $i = 0; $i < $sql_count; $i++ ) 
	{ 
		if ( !($db->sql_query($sql[$i])) ) { message_die(GENERAL_MESSAGE, 'Fatal Error Updating Bank Information!'); } 
	}
	message_die(GENERAL_MESSAGE, $lang['bank_updated'].'!<br /><br />'.sprintf($lang['click_return_bank_admin'], '<a href="' . append_sid("admin_bank.$phpEx") . '">', '</a>').'<br /><br />'.sprintf($lang['Click_return_admin_index'], '<a href="' . append_sid("index.$phpEx?pane=right") .'">', '</a>').'<br /><br />');
}
elseif ( $action == 'edit_account' )
{
	if ( isset($HTTP_GET_VARS['username']) || isset($HTTP_POST_VARS['username']) ) { $username = ( isset($HTTP_POST_VARS['username']) ) ? $HTTP_POST_VARS['username'] : $HTTP_GET_VARS['username']; }
	else { $username = ''; }

	$template->set_filenames(array(
		'body' => 'mods/admin/bank_edit_user.tpl')
	);

	//check username & get account information
	$user_row = get_userdata($username);

	$sql = "SELECT *
		FROM " . BANK_TABLE . "
		WHERE user_id = '{$user_row['user_id']}'";
	if ( !($result = $db->sql_query($sql)) ) { message_die(GENERAL_MESSAGE, 'Fatal Error Getting User Item!'); }
	if ( !($db->sql_numrows($result)) )
	{
		message_die(GENERAL_MESSAGE, $lang['no_account'].'!<br /><br />'.sprintf($lang['click_return_bank_admin'], '<a href="' . append_sid("admin_bank.$phpEx") . '">', '</a>').'<br /><br />'.sprintf($lang['Click_return_admin_index'], '<a href="' . append_sid("index.$phpEx?pane=right") .'">', '</a>').'<br /><br />');
	}
	else
	{
		$row = $db->sql_fetchrow($result);
	}

	$fees_on_select = ( $row['fees'] == 'on' ) ? 'SELECTED' : '';
	$fees_off_select = ( $row['fees'] == 'on' ) ? '' : 'SELECTED';

	$template->assign_vars(array(
		'S_CONFIG_ACTION' => append_sid('admin_bank.' . $phpEx),

		'USER_ID' => $user_row['user_id'],
		'USER_HOLDING' => $row['holding'],
		'USER_WITHDRAWN' => $row['totalwithdrew'],
		'USER_DEPOSITED' => $row['totaldeposit'],
		
		'U_ADMIN_BANK'	=> append_sid('admin_bank.' . $phpEx),

		'SELECT_FEES_ON' => $fees_on_select,
		'SELECT_FEES_OFF' => $fees_off_select,

		'L_TABLE_TITLE' => $lang['bank_modify'],
		'L_BALANCE' => $lang['bank_balance'],
		'L_DEPOSITED' => $lang['total_deposited'],
		'L_WITHDRAWN' => $lang['total_withdrawn'],
		'L_FEES' => $lang['withdraw_fees'],
		'L_UPDATE' => $lang['button_update'],
		'L_CLOSE' => $lang['button_close'],

		'BANKTITLE' => $lang['bank_settings'],
		'BANKEXPLAIN' => $lang['bank_settings_explain'])
	);
}
elseif ( $action == 'update_account' )
{
	if ( isset($HTTP_GET_VARS['user_id']) || isset($HTTP_POST_VARS['user_id']) ) { $user_id = ( isset($HTTP_POST_VARS['user_id']) ) ? intval($HTTP_POST_VARS['user_id']) : intval($HTTP_GET_VARS['user_id']); }
	else { $user_id = ''; }
	if ( isset($HTTP_GET_VARS['holding']) || isset($HTTP_POST_VARS['holding']) ) { $holding = ( isset($HTTP_POST_VARS['holding']) ) ? intval($HTTP_POST_VARS['holding']) : intval($HTTP_GET_VARS['holding']); }
	else { $holding = ''; }
	if ( isset($HTTP_GET_VARS['withdrawn']) || isset($HTTP_POST_VARS['withdrawn']) ) { $withdrawn = ( isset($HTTP_POST_VARS['withdrawn']) ) ? intval($HTTP_POST_VARS['withdrawn']) : intval($HTTP_GET_VARS['withdrawn']); }
	else { $withdrawn = ''; }
	if ( isset($HTTP_GET_VARS['deposited']) || isset($HTTP_POST_VARS['deposited']) ) { $deposited = ( isset($HTTP_POST_VARS['deposited']) ) ? intval($HTTP_POST_VARS['deposited']) : intval($HTTP_GET_VARS['deposited']); }
	else { $deposited = ''; }
	if ( isset($HTTP_GET_VARS['fees']) || isset($HTTP_POST_VARS['fees']) ) { $fees = ( isset($HTTP_POST_VARS['fees']) ) ? $HTTP_POST_VARS['fees'] : $HTTP_GET_VARS['fees']; }
	else { $fees = ''; }

	$sql = "SELECT *
		FROM " . BANK_TABLE . "
		WHERE user_id = '$user_id'";
	if ( !($result = $db->sql_query($sql)) ) { message_die(GENERAL_MESSAGE, 'Fatal Error: '.mysql_error()); }
	if ( !($db->sql_numrows($result)) ) { message_die(GENERAL_MESSAGE, $lang['no_account'].'!<br /><br />'.sprintf($lang['click_return_bank_admin'], '<a href="' . append_sid("admin_bank.$phpEx") . '">', '</a>').'<br /><br />'.sprintf($lang['Click_return_admin_index'], '<a href="' . append_sid("index.$phpEx?pane=right") .'">', '</a>').'<br /><br />'); }
	elseif ( !($row = $db->sql_fetchrow($result)) ) { message_die(GENERAL_MESSAGE, 'Fatal Error: '.mysql_error()); }

	$holding = ( $holding < 0 ) ? $row['holding'] : $holding;
	$withdrawn = ( $withdrawn < 0 ) ? $row['totalwithdrew'] : $withdrawn;
	$deposited = ( $deposited < 0 ) ? $row['totaldeposited'] : $deposited;
	$fees = ( $fees != 'on' && $fees != 'off' ) ? $row['fees'] : $fees;

	$sql = "UPDATE " . BANK_TABLE . "
		SET holding = '$holding',
			totalwithdrew = '$withdrawn',
			totaldeposit = '$deposited', 
			fees = '$fees'
		WHERE user_id = '$user_id'";
	if ( !($db->sql_query($sql)) ) { message_die(GENERAL_MESSAGE, 'Fatal Error: '.mysql_error()); }

	message_die(GENERAL_MESSAGE, $lang['user_updated'].'!<br /><br />'.sprintf($lang['click_return_bank_admin'], '<a href="' . append_sid("admin_bank.$phpEx") . '">', '</a>').'<br /><br />'.sprintf($lang['Click_return_admin_index'], '<a href="' . append_sid("index.$phpEx?pane=right") .'">', '</a>').'<br /><br />');
}
else
{
	message_die(GENERAL_MESSAGE, "Invalid Command!"); 
}


//
// Generate the page
//
$template->pparse('body');

include('page_footer_mods.' . $phpEx);

?>
