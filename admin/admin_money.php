<?php
/**************************************************************
 *		admin_money.php
 *		---------------
 *   	Разработка и оптимизация под WAP: Гутник Игорь ( чел )
 *          2010 год
 *		简体中文：爱疯的云
 **************************************************************/

define('IN_PHPBB', true);

if( !empty($setmodules) )
{
	$file = basename(__FILE__);
	$module['Shop']['Shop_Config'] = $file;
	return;
}

$phpbb_root_path = "./../";
require($phpbb_root_path . 'extension.inc');
require('./pagestart.' . $phpEx);
include($phpbb_root_path . 'includes/functions_selects.'.$phpEx);

$sql = "SELECT *
	FROM " . CONFIG_TABLE;
if(!$result = $db->sql_query($sql))
{
	message_die(CRITICAL_ERROR, "Could not query config information in admin_board", "", __LINE__, __FILE__, $sql);
}
else
{
	while( $row = $db->sql_fetchrow($result) )
	{
		$config_name = $row['config_name'];
		$config_value = $row['config_value'];
		$default_config[$config_name] = isset($HTTP_POST_VARS['submit']) ? str_replace("'", "\'", $config_value) : $config_value;
		
		$new[$config_name] = ( isset($HTTP_POST_VARS[$config_name]) ) ? $HTTP_POST_VARS[$config_name] : $default_config[$config_name];

		if ($config_name == 'cookie_name')
		{
			$new['cookie_name'] = str_replace('.', '_', $new['cookie_name']);
		}

		if ($config_name == 'server_name')
		{
			$new['server_name'] = str_replace('http://', '', $new['server_name']);
		}

		if ($config_name == 'avatar_path')
		{
			$new['avatar_path'] = trim($new['avatar_path']);
			if (strstr($new['avatar_path'], "\0") || !is_dir($phpbb_root_path . $new['avatar_path']) || !is_writable($phpbb_root_path . $new['avatar_path']))
			{
				$new['avatar_path'] = $default_config['avatar_path'];
			}
		}

		if ($config_name == 'default_icq')
		{
			if (!preg_match('/^[0-9]+$/', $new['default_icq']))
			{
				$new['default_icq'] = '';
			}
		}

		if( isset($HTTP_POST_VARS['submit']) )
		{
			$sql = "UPDATE " . CONFIG_TABLE . " SET
				config_value = '" . str_replace("\'", "''", $new[$config_name]) . "'
				WHERE config_name = '$config_name'";
			if( !$db->sql_query($sql) )
			{
				message_die(GENERAL_ERROR, "Failed to update general configuration for $config_name", "", __LINE__, __FILE__, $sql);
			}
		}
	}

	if( isset($HTTP_POST_VARS['submit']) )
	{
		$message = $lang['Config_updated'] . "<br /><br />" . sprintf($lang['Click_return_config'], "<a href=\"" . append_sid("admin_money.$phpEx") . "\">", "</a>") . "<br /><br />" . sprintf($lang['Click_return_admin_index'], "<a href=\"" . append_sid("index.$phpEx?pane=left") . "\">", "</a>");

		message_die(GENERAL_MESSAGE, $message);
	}
}

$shop_yes = ($new['shop']) ? 'checked="checked"' : '';
$shop_no = (!$new['shop']) ? 'checked="checked"' : '';

$sites_yes = ($new['sites']) ? 'checked="checked"' : '';
$sites_no = (!$new['sites']) ? 'checked="checked"' : '';

$payment_yes = ($new['pay_money']) ? 'checked="checked"' : '';
$payment_no = (!$new['pay_money']) ? 'checked="checked"' : '';

$ref_yes = ($new['ref_url']) ? 'checked="checked"' : '';
$ref_no = (!$new['ref_url']) ? 'checked="checked"' : '';

$template->set_filenames(array(
	"body" => "admin/money_config_body.tpl")
);

$template->assign_vars(array(
	"L_YES"						=> $lang['Yes'],
	"L_NO"						=> $lang['No'],
	
	"L_KURS_PAYMENT"			=> $lang['Shop_Kurs_Payment'],
	"L_KURS_PAYMENT_EXPLAIN"	=> $lang['Shop_Kurs_Payment_Explain'],
	"L_OPEN_PAY_MONEY"			=> $lang['Shop_Open_Pay_money'],
	
	"S_CONFIG_ACTION" 			=> append_sid("admin_money.$phpEx"),
	"SMENA_NIKA" 				=> $new['smena_nika'],
	"SMENA_CVETA" 				=> $new['smena_cveta'],
	"SMENA_ZVANIYA" 			=> $new['smena_zvaniya'],
	"SITES_YES" 				=> $sites_yes,
	"SITES_NO" 					=> $sites_no,
	"SHOP_YES" 					=> $shop_yes,
	"SHOP_NO" 					=> $shop_no,
	"PAYMENT_YES" 				=> $payment_yes,
	"PAYMENT_NO" 				=> $payment_no,
	"REF_YES" 					=> $ref_yes,
	"REF_NO" 					=> $ref_no,
	"VERH_PAY" 					=> $new['verh_pay'],
	"NIZ_PAY" 					=> $new['niz_pay'],
	"VERH" 						=> $new['verh'],
	"NIZ" 						=> $new['niz'],
	"TIME_CLICK" 				=> $new['time_click'],
	"KURS_PAYMENT" 				=> $new['kurs_payment'],
	"RAZBLOKIROVKA_DRUGA" 		=> $new['razblokirovka_druga'],
	"POKUPKA_UCHETKI" 			=> $new['pokupka_uchetki'],
	"POKUPKA_UCHETKI_POSTS" 	=> $new['pokupka_uchetki_posts'],
	"POKUPKA_UCHETKI_NEDELI" 	=> $new['pokupka_uchetki_nedeli'])
);

$template->pparse("body");

include('./page_footer_admin.'.$phpEx);

?>