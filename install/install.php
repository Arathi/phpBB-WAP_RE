<?php
/***************************************************
 *		install.php
 *		-------------------
 *      Разработка: phpBB Group.
 *      Оптимизация под WAP: Гутник Игорь ( чел ).
 *          2010 год
 *		中文phpBB-WAP：爱疯的云
 ***************************************************/
if ( isset($_POST['agree']) )
{
	function page_header($text, $form_action = false)
	{
		global $phpEx, $lang;

	?>
	<!DOCTYPE html PUBLIC " -//WAPFORUM//DTD XHTML Mobile 1.0//EN" "http://www.wapforum.org/DTD/xhtml-mobile10.dtd">
	<html xmlns="http://www.w3.org/1999/xhtml">
	<head>
	<meta http-equiv="Content-Type" content="application/xhtml+xml; charset=UTF-8" />
	<meta name="viewport" content="width=device-width; initial-scale=1.0; minimum-scale=1.0; maximum-scale=2.0"/>
	<meta name="apple-mobile-web-app-capable" content="yes" />
	<meta name="apple-mobile-web-app-status-bar-style" content="black" />			
	<title>安装_填写信息</title>
	<link rel="shortcut icon" href="http://phpbb-wap.com/favicon.ico" />
	<link rel="stylesheet" href="http://phpbb-wap.com/style.css" type="text/css" />	
	</head>
	<body>
	<div class="wrap">
	<div class="navbar">
	<?php echo $lang['Welcome_install'];?><br/>
	<?php echo $text; ?>
	</div>
	<div class="row1">
	<form action="<?php echo ($form_action) ? $form_action : 'install.'.$phpEx; ?>" name="install" method="post">
	<?php
	}
	function page_footer()
	{
	?>
	</form>
	</div>
	<div class="copy">Powered by <a href="http://phpbb-wap.com/">phpBB-WAP v4.0</div>
	</div>
	</body>
	</html>
	<?php
	}
	function page_common_form($hidden, $submit)
	{
	?>
	<?php echo $hidden; ?><input type="submit" value="<?php echo $submit; ?>" />
	<?php
	}
	function page_upgrade_form()
	{
		global $lang;
	?>
	<div class="row1">
	<?php echo $lang['continue_upgrade']; ?>
	</div>
	<input type="submit" name="upgrade_now" value="<?php echo $lang['upgrade_submit']; ?>" />
	<?php 
	}
	function page_error($error_title, $error)
	{
	?>
	<div class="catSides">
	<?php echo $error_title; ?>
	</div>
	<div class="row1"><?php echo $error; ?>
	</div>
	<?php
	}
	error_reporting  (E_ERROR | E_WARNING | E_PARSE);
	set_magic_quotes_runtime(0);

	if (!isset($HTTP_POST_VARS) && isset($_POST))
	{
		$HTTP_POST_VARS = $_POST;
		$HTTP_GET_VARS = $_GET;
		$HTTP_SERVER_VARS = $_SERVER;
		$HTTP_COOKIE_VARS = $_COOKIE;
		$HTTP_ENV_VARS = $_ENV;
		$HTTP_POST_FILES = $_FILES;

		if (isset($_SESSION))
		{
			$HTTP_SESSION_VARS = $_SESSION;
		}
	}

	if (!get_magic_quotes_gpc())
	{
		if (is_array($HTTP_GET_VARS))
		{
			while (list($k, $v) = each($HTTP_GET_VARS))
			{
				if (is_array($HTTP_GET_VARS[$k]))
				{
					while (list($k2, $v2) = each($HTTP_GET_VARS[$k]))
					{
						$HTTP_GET_VARS[$k][$k2] = addslashes($v2);
					}
					@reset($HTTP_GET_VARS[$k]);
				}
				else
				{
					$HTTP_GET_VARS[$k] = addslashes($v);
				}
			}
			@reset($HTTP_GET_VARS);
		}

		if (is_array($HTTP_POST_VARS))
		{
			while (list($k, $v) = each($HTTP_POST_VARS))
			{
				if (is_array($HTTP_POST_VARS[$k]))
				{
					while (list($k2, $v2) = each($HTTP_POST_VARS[$k]))
					{
						$HTTP_POST_VARS[$k][$k2] = addslashes($v2);
					}
					@reset($HTTP_POST_VARS[$k]);
				}
				else
				{
					$HTTP_POST_VARS[$k] = addslashes($v);
				}
			}
			@reset($HTTP_POST_VARS);
		}

		if (is_array($HTTP_COOKIE_VARS))
		{
			while (list($k, $v) = each($HTTP_COOKIE_VARS))
			{
				if (is_array($HTTP_COOKIE_VARS[$k]))
				{
					while (list($k2, $v2) = each($HTTP_COOKIE_VARS[$k]))
					{
						$HTTP_COOKIE_VARS[$k][$k2] = addslashes($v2);
					}
					@reset($HTTP_COOKIE_VARS[$k]);
				}
				else
				{
					$HTTP_COOKIE_VARS[$k] = addslashes($v);
				}
			}
			@reset($HTTP_COOKIE_VARS);
		}
	}

	define('IN_PHPBB', true);

	$phpbb_root_path = './../';
	include($phpbb_root_path.'extension.inc');

	$userdata = array();
	$lang = array();
	$error = false;

	include($phpbb_root_path.'includes/constants.'.$phpEx);
	include($phpbb_root_path.'includes/functions.'.$phpEx);
	include($phpbb_root_path.'includes/sessions.'.$phpEx);

	$available_dbms = array(
		'mysql4' => array(
			'LABEL'			=> 'MySQL 4.x/5.x',
			'SCHEMA'		=> 'mysql', 
			'DELIM'			=> ';', 
			'DELIM_BASIC'	=> ';',
			'COMMENTS'		=> 'remove_remarks'
		), 
		'mysql'=> array(
			'LABEL'			=> 'MySQL 3.x',
			'SCHEMA'		=> 'mysql', 
			'DELIM'			=> ';',
			'DELIM_BASIC'	=> ';',
			'COMMENTS'		=> 'remove_remarks'
		)
	);

	$confirm = (isset($HTTP_POST_VARS['confirm'])) ? true : false;
	$cancel = (isset($HTTP_POST_VARS['cancel'])) ? true : false;

	if (isset($HTTP_POST_VARS['install_step']) || isset($HTTP_GET_VARS['install_step']))
	{
		$install_step = (isset($HTTP_POST_VARS['install_step'])) ? $HTTP_POST_VARS['install_step'] : $HTTP_GET_VARS['install_step'];
	}
	else
	{
		$install_step = '';
	}

	$upgrade = '';
	$upgrade_now = (!empty($HTTP_POST_VARS['upgrade_now'])) ? $HTTP_POST_VARS['upgrade_now']:'';

	$dbms = isset($HTTP_POST_VARS['dbms']) ? $HTTP_POST_VARS['dbms'] : '';

	$dbhost = (!empty($HTTP_POST_VARS['dbhost'])) ? $HTTP_POST_VARS['dbhost'] : 'localhost';
	$dbuser = (!empty($HTTP_POST_VARS['dbuser'])) ? $HTTP_POST_VARS['dbuser'] : '';
	$dbpasswd = (!empty($HTTP_POST_VARS['dbpasswd'])) ? $HTTP_POST_VARS['dbpasswd'] : '';
	$dbname = (!empty($HTTP_POST_VARS['dbname'])) ? $HTTP_POST_VARS['dbname'] : '';
	$db_charset_utf = '1';

	$table_prefix = (!empty($HTTP_POST_VARS['prefix'])) ? $HTTP_POST_VARS['prefix'] : '';

	$admin_name = (!empty($HTTP_POST_VARS['admin_name'])) ? $HTTP_POST_VARS['admin_name'] : '';
	$admin_pass1 = (!empty($HTTP_POST_VARS['admin_pass1'])) ? $HTTP_POST_VARS['admin_pass1'] : '';
	$admin_pass2 = (!empty($HTTP_POST_VARS['admin_pass2'])) ? $HTTP_POST_VARS['admin_pass2'] : '';

	$ftp_path = (!empty($HTTP_POST_VARS['ftp_path'])) ? $HTTP_POST_VARS['ftp_path'] : '';
	$ftp_user = (!empty($HTTP_POST_VARS['ftp_user'])) ? $HTTP_POST_VARS['ftp_user'] : '';
	$ftp_pass = (!empty($HTTP_POST_VARS['ftp_pass'])) ? $HTTP_POST_VARS['ftp_pass'] : '';

	$language = 'chinese';

	$board_email = (!empty($HTTP_POST_VARS['board_email'])) ? $HTTP_POST_VARS['board_email'] : '';
	$script_path = (!empty($HTTP_POST_VARS['script_path'])) ? $HTTP_POST_VARS['script_path'] : str_replace('install', '', dirname($HTTP_SERVER_VARS['PHP_SELF']));

	if (!empty($HTTP_POST_VARS['server_name']))
	{
		$server_name = $HTTP_POST_VARS['server_name'];
	}
	else
	{
		if (!empty($HTTP_SERVER_VARS['SERVER_NAME']) || !empty($HTTP_ENV_VARS['SERVER_NAME']))
		{
			$server_name = (!empty($HTTP_SERVER_VARS['SERVER_NAME'])) ? $HTTP_SERVER_VARS['SERVER_NAME'] : $HTTP_ENV_VARS['SERVER_NAME'];
		}
		else if (!empty($HTTP_SERVER_VARS['HTTP_HOST']) || !empty($HTTP_ENV_VARS['HTTP_HOST']))
		{
			$server_name = (!empty($HTTP_SERVER_VARS['HTTP_HOST'])) ? $HTTP_SERVER_VARS['HTTP_HOST'] : $HTTP_ENV_VARS['HTTP_HOST'];
		}
		else
		{
			$server_name = '';
		}
	}

	if (!empty($HTTP_POST_VARS['server_port']))
	{
		$server_port = $HTTP_POST_VARS['server_port'];
	}
	else
	{
		if (!empty($HTTP_SERVER_VARS['SERVER_PORT']) || !empty($HTTP_ENV_VARS['SERVER_PORT']))
		{
			$server_port = (!empty($HTTP_SERVER_VARS['SERVER_PORT'])) ? $HTTP_SERVER_VARS['SERVER_PORT'] : $HTTP_ENV_VARS['SERVER_PORT'];
		}
		else
		{
			$server_port = '80';
		}
	}

	if (@file_exists(@phpbb_realpath('config.'.$phpEx)))
	{
		include($phpbb_root_path.'config.'.$phpEx);
	}

	if (defined("PHPBB_INSTALLED"))
	{
		redirect('../index.'.$phpEx);
	}

	include($phpbb_root_path.'language/lang_' . $language . '/lang_main.'.$phpEx);
	include($phpbb_root_path.'language/lang_' . $language . '/lang_admin.'.$phpEx);

	if ($upgrade == 1)
	{
		$install_step = 1;
	}

	if (!empty($HTTP_POST_VARS['send_file']) && $HTTP_POST_VARS['send_file'] == 1 && empty($HTTP_POST_VARS['upgrade_now']))
	{
		header('Content-Type: text/x-delimtext; name="config.' . $phpEx . '"');
		header('Content-disposition: attachment; filename="config.' . $phpEx . '"');

		echo stripslashes($HTTP_POST_VARS['config_data']);

		exit;
	}
	else if (!empty($HTTP_POST_VARS['send_file']) && $HTTP_POST_VARS['send_file'] == 2)
	{
		$s_hidden_fields = '<input type="hidden" name="config_data" value="' . htmlspecialchars(stripslashes($HTTP_POST_VARS['config_data'])) . '" />';
		$s_hidden_fields .= '<input type="hidden" name="ftp_file" value="1" />';

		if ($upgrade == 1)
		{
			$s_hidden_fields .= '<input type="hidden" name="upgrade" value="1" />';
		}

		page_header($lang['ftp_instructs']);

	?>
	<div class="catSides">
	<?php echo $lang['ftp_info']; ?>
	</div>
	<div class="row1">
	<?php echo $lang['ftp_path']; ?><br/>
	<input type="text" name="ftp_dir">
	</div>
	<div class="row1">
	<?php echo $lang['ftp_username']; ?><br/>
	<input type="text" name="ftp_user">
	</div>
	<div class="row1">
	<?php echo $lang['ftp_password']; ?><br/>
	<input type="password" name="ftp_pass">
	</div>
	<?php

		page_common_form($s_hidden_fields, $lang['Transfer_config']);
		page_footer();
		exit;

	}
	else if (!empty($HTTP_POST_VARS['ftp_file']))
	{
		$conn_id = @ftp_connect('localhost');
		$login_result = @ftp_login($conn_id, "$ftp_user", "$ftp_pass");

		if (!$conn_id || !$login_result)
		{
			page_header($lang['NoFTP_config']);
			$s_hidden_fields = '<input type="hidden" name="config_data" value="' . htmlspecialchars(stripslashes($HTTP_POST_VARS['config_data'])) . '" />';
			$s_hidden_fields .= '<input type="hidden" name="send_file" value="1" />';

			if ($upgrade == 1)
			{
				$s_hidden_fields .= '<input type="hidden" name="upgrade" value="1" />';
				$s_hidden_fields .= '<input type="hidden" name="dbms" value="'.$dmbs.'" />';
				$s_hidden_fields .= '<input type="hidden" name="prefix" value="'.$table_prefix.'" />';
				$s_hidden_fields .= '<input type="hidden" name="dbhost" value="'.$dbhost.'" />';
				$s_hidden_fields .= '<input type="hidden" name="dbname" value="'.$dbname.'" />';
				$s_hidden_fields .= '<input type="hidden" name="dbuser" value="'.$dbuser.'" />';
				$s_hidden_fields .= '<input type="hidden" name="dbpasswd" value="'.$dbpasswd.'" />';
				$s_hidden_fields .= '<input type="hidden" name="install_step" value="1" />';
				$s_hidden_fields .= '<input type="hidden" name="admin_pass1" value="1" />';
				$s_hidden_fields .= '<input type="hidden" name="admin_pass2" value="1" />';
				$s_hidden_fields .= '<input type="hidden" name="server_port" value="'.$server_port.'" />';
				$s_hidden_fields .= '<input type="hidden" name="server_name" value="'.$server_name.'" />';
				$s_hidden_fields .= '<input type="hidden" name="script_path" value="'.$script_path.'" />';
				$s_hidden_fields .= '<input type="hidden" name="board_email" value="'.$board_email.'" />';
				//继续提交同意条款
				$s_hidden_fields .= '<input type="hidden" name="agree" value="agree" />';
				

				page_upgrade_form();
			}
			else
			{
				page_common_form($s_hidden_fields, $lang['Download_config']);

			}

			page_footer();
			exit;
		}
		else
		{
			$tmpfname = @tempnam('/tmp', 'cfg');

			@unlink($tmpfname);

			$fp = @fopen($tmpfname, 'w');

			@fwrite($fp, stripslashes($HTTP_POST_VARS['config_data']));

			@fclose($fp);

			@ftp_chdir($conn_id, $ftp_dir);

			$res = ftp_put($conn_id, 'config.'.$phpEx, $tmpfname, FTP_ASCII);

			@ftp_quit($conn_id);

			unlink($tmpfname);

			if ($upgrade == 1)	
			{
				require('upgrade.'.$phpEx);
				exit;
			}

			$s_hidden_fields = '<input type="hidden" name="username" value="' . $admin_name . '" />';
			$s_hidden_fields .= '<input type="hidden" name="password" value="' . $admin_pass1 . '" />';
			$s_hidden_fields .= '<input type="hidden" name="redirect" value="../admin/index.'.$phpEx.'" />';
			$s_hidden_fields .= '<input type="hidden" name="submit" value="' . $lang['Login'] . '" />';

			page_header($lang['Inst_Step_2']);
			page_common_form($s_hidden_fields, $lang['Finish_Install']);
			page_footer();
			exit();
		}
	}
	else if ((empty($install_step) || $admin_pass1 != $admin_pass2 || empty($admin_pass1) || empty($dbhost)))
	{

		$instruction_text = $lang['Inst_Step_0'];

		if (!empty($install_step))
		{
			if ((($HTTP_POST_VARS['admin_pass1'] != $HTTP_POST_VARS['admin_pass2'])) ||
				(empty($HTTP_POST_VARS['admin_pass1']) || empty($dbhost)) && $HTTP_POST_VARS['cur_lang'] == $language)
			{
				$error = $lang['Password_mismatch'];
			}
		}

		$dbms_select = '<select name="dbms" onchange="if(this.form.upgrade.options[this.form.upgrade.selectedIndex].value == 1){ this.selectedIndex = 0;}">';
		while (list($dbms_name, $details) = @each($available_dbms))
		{
			$selected = ($dbms_name == $dbms) ? 'selected="selected"' : '';
			$dbms_select .= '<option value="' . $dbms_name . '">' . $details['LABEL'] . '</option>';
		}
		$dbms_select .= '</select>';
		
		$s_hidden_fields = '<input type="hidden" name="install_step" value="1" /><input type="hidden" name="cur_lang" value="' . $language . '" />';
		//继续提交同意条款
		$s_hidden_fields .= '<input type="hidden" name="agree" value="agree" />';
		page_header($instruction_text);

	?>
	<div class="catSides">
	<?php echo $lang['DB_config']; ?>
	</div>
	<div class="row1">
	<?php echo $lang['dbms']; ?>:<br/>
	<?php echo $dbms_select; ?>
	</div>
	<div class="row1">
	<?php echo $lang['DB_Host']; ?>:<br/>
	<input type="text" name="dbhost" value="<?php echo ($dbhost != '') ? $dbhost : ''; ?>" />
	</div>
	<div class="row1">
	<?php echo $lang['DB_Name']; ?>:<br/>
	<input type="text" name="dbname" value="<?php echo ($dbname != '') ? $dbname : ''; ?>" />
	</div>
	<div class="row1">
	<?php echo $lang['DB_Username']; ?>:<br/>
	<input type="text" name="dbuser" value="<?php echo ($dbuser != '') ? $dbuser : ''; ?>" />
	</div>
	<div class="row1">
	<?php echo $lang['DB_Password']; ?>:<br/>
	<input type="password" name="dbpasswd" value="<?php echo ($dbpasswd != '') ? $dbpasswd : ''; ?>" />
	</div>
	<div class="row1">
	<?php echo $lang['Table_Prefix']; ?>:<br/>
	<input type="text" name="prefix" value="<?php echo (!empty($table_prefix)) ? $table_prefix : "phpbb_"; ?>" />
	</div>
	<div class="catSides">
	<?php echo $lang['Admin_config']; ?>
	</div>
	<?php

		if ($error)
		{
	?>
	<div class="row1">
	<span style="color:red"><?php echo $error; ?></span>
	</div>
	<?php

		}
	?>
	<div class="row1">
	<?php echo $lang['Admin_email']; ?>:<br/>
	<input type="text" name="board_email" value="<?php echo ($board_email != '') ? $board_email : ''; ?>" />
	</div>
	<div class="row1">
	<?php echo $lang['Server_name']; ?>:<br/>
	<input type="text" name="server_name" value="<?php echo $server_name; ?>" />
	</div>
	<div class="row1">
	<?php echo $lang['Server_port']; ?>:<br/>
	<input type="text" name="server_port" value="<?php echo $server_port; ?>" />
	</div>
	<div class="row1">
	<?php echo $lang['Script_path']; ?>:<br/>
	<input type="text" name="script_path" value="<?php echo $script_path; ?>" />
	</div>
	<div class="row1">
	<?php echo $lang['Admin_Username']; ?>:<br/>
	<input type="text" name="admin_name" value="<?php echo ($admin_name != '') ? $admin_name : ''; ?>" />
	</div>
	<div class="row1">
	<?php echo $lang['Admin_Password']; ?>:<br/>
	<input type="password" name="admin_pass1" value="<?php echo ($admin_pass1 != '') ? $admin_pass1 : ''; ?>" />
	</div>
	<div class="row1">
	<?php echo $lang['Admin_Password_confirm']; ?>:<br/>
	<input type="password" name="admin_pass2" value="<?php echo ($admin_pass2 != '') ? $admin_pass2 : ''; ?>" />
	</div>
	<?php

		page_common_form($s_hidden_fields, $lang['Start_Install']);
		page_footer();
		exit;
	}
	else
	{
		if (isset($dbms))
		{
			$check_exts = 'mysql';
			$check_other = 'mysql';

			if (!extension_loaded($check_exts) && !extension_loaded($check_other))
			{	
				page_header($lang['Install'], '');
				page_error($lang['Installer_Error'], $lang['Install_No_Ext']);
				page_footer();
				exit;
			}

			include($phpbb_root_path.'includes/db.'.$phpEx);
		}

		$dbms_schema = 'schemas/' . $available_dbms[$dbms]['SCHEMA'] . '_schema.sql';
		$dbms_basic = 'schemas/' . $available_dbms[$dbms]['SCHEMA'] . '_basic.sql';

		$remove_remarks = $available_dbms[$dbms]['COMMENTS'];;
		$delimiter = $available_dbms[$dbms]['DELIM']; 
		$delimiter_basic = $available_dbms[$dbms]['DELIM_BASIC']; 

		if ($install_step == 1)
		{
			if ($upgrade != 1)
			{
				if ($dbms != 'msaccess')
				{
					include($phpbb_root_path.'includes/sql_parse.'.$phpEx);

					$sql_query = @fread(@fopen($dbms_schema, 'r'), @filesize($dbms_schema));
					$sql_query = preg_replace('/phpbb_/', $table_prefix, $sql_query);

					$sql_query = $remove_remarks($sql_query);
					$sql_query = split_sql_file($sql_query, $delimiter);

					$db->sql_query("ALTER DATABASE " . $dbname . " DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci");

					for ($i = 0; $i < sizeof($sql_query); $i++)
					{
						if (trim($sql_query[$i]) != '')
						{
							if (!($result = $db->sql_query($sql_query[$i])))
							{
								$error = $db->sql_error();
				
								page_header($lang['Install'], '');
								page_error($lang['Installer_Error'], $lang['Install_db_error'] . '<br />' . $error['message']);
								page_footer();
								exit;
							}
						}
					}

					$sql_query = @fread(@fopen($dbms_basic, 'r'), @filesize($dbms_basic));
					$sql_query = preg_replace('/phpbb_/', $table_prefix, $sql_query);

					$sql_query = $remove_remarks($sql_query);
					$sql_query = split_sql_file($sql_query, $delimiter_basic);

					for($i = 0; $i < sizeof($sql_query); $i++)
					{
						if (trim($sql_query[$i]) != '')
						{
							if (!($result = $db->sql_query($sql_query[$i])))
							{
								$error = $db->sql_error();

								page_header($lang['Install'], '');
								page_error($lang['Installer_Error'], $lang['Install_db_error'] . '<br />' . $error['message']);
								page_footer();
								exit;
							}
						}
					}
				}

				$error = '';

				$sql = "INSERT INTO " . $table_prefix . "config (config_name, config_value) 
					VALUES ('board_startdate', " . time() . ")";
				if (!$db->sql_query($sql))
				{
					$error .= "Could not insert board_startdate :: " . $sql . " :: " . __LINE__ . " :: " . __FILE__ . "<br /><br />";
				}

				$sql = "INSERT INTO " . $table_prefix . "config (config_name, config_value) 
					VALUES ('default_lang', '" . str_replace("\'", "''", $language) . "')";
				if (!$db->sql_query($sql))
				{
					$error .= "Could not insert default_lang :: " . $sql . " :: " . __LINE__ . " :: " . __FILE__ . "<br /><br />";
				}

				$update_config = array(
					'board_email'	=> $board_email,
					'script_path'	=> $script_path,
					'server_port'	=> $server_port,
					'server_name'	=> $server_name,
				);

				while (list($config_name, $config_value) = each($update_config))
				{
					$sql = "UPDATE " . $table_prefix . "config 
						SET config_value = '$config_value' 
						WHERE config_name = '$config_name'";
					if (!$db->sql_query($sql))
					{
						$error .= "Could not insert default_lang :: " . $sql . " :: " . __LINE__ . " :: " . __FILE__ . "<br /><br />";
					}
				}

				$admin_pass_md5 = ($confirm && $userdata['user_level'] == ADMIN) ? $admin_pass1 : md5($admin_pass1);

				$sql = "UPDATE " . $table_prefix . "users 
					SET username = '" . str_replace("\'", "''", $admin_name) . "', user_password='" . str_replace("\'", "''", $admin_pass_md5) . "', user_lang = '" . str_replace("\'", "''", $language) . "', user_email='" . str_replace("\'", "''", $board_email) . "'
					WHERE username = 'Admin'";
				if (!$db->sql_query($sql))
				{
					$error .= "Could not update admin info :: " . $sql . " :: " . __LINE__ . " :: " . __FILE__ . "<br /><br />";
				}

				$sql = "UPDATE " . $table_prefix . "users 
					SET user_regdate = " . time();
				if (!$db->sql_query($sql))
				{
					$error .= "Could not update user_regdate :: " . $sql . " :: " . __LINE__ . " :: " . __FILE__ . "<br /><br />";
				}

				if ($error != '')
				{
					page_header($lang['Install'], '');
					page_error($lang['Installer_Error'], $lang['Install_db_error'] . '<br /><br />' . $error);
					page_footer();
					exit;
				}
			}

			if (!$upgrade_now)
			{
				$config_data = '<?php'."\n\n";
				$config_data .= '$dbms = \'' . $dbms . '\';' . "\n";
				$config_data .= '$dbhost = \'' . $dbhost . '\';' . "\n";
				$config_data .= '$dbname = \'' . $dbname . '\';' . "\n";
				$config_data .= '$dbuser = \'' . $dbuser . '\';' . "\n";
				$config_data .= '$dbpasswd = \'' . $dbpasswd . '\';' . "\n";
				$config_data .= '$table_prefix = \'' . $table_prefix . '\';' . "\n";
				$config_data .= '$db_charset_utf = \'1\';' . "\n";
				$config_data .= 'define(\'PHPBB_INSTALLED\', true);'."\n\n";	
				$config_data .= '?' . '>';

				@umask(0111);
				$no_open = FALSE;

				if (!($fp = @fopen($phpbb_root_path . 'config.'.$phpEx, 'w')))
				{
					$s_hidden_fields = '<input type="hidden" name="config_data" value="' . htmlspecialchars($config_data) . '" />';

					if (@extension_loaded('ftp') && !defined('NO_FTP'))
					{
						page_header($lang['Unwriteable_config'] . '<p>' . $lang['ftp_option'] . '</p>');

	?>
	<div class="catSides">
	<?php echo $lang['ftp_choose']; ?>
	</div>
	<div class="row1">
	<?php echo $lang['Attempt_ftp']; ?><br/>
	<input type="radio" name="send_file" value="2">
	</div>
	<div class="row1">
	<?php echo $lang['Send_file']; ?><br/>
	<input type="radio" name="send_file" value="1">
	</div>
	<?php 

					}
					else
					{
						page_header($lang['Unwriteable_config']);
						$s_hidden_fields .= '<input type="hidden" name="send_file" value="1" />';
					}

					if ($upgrade == 1)
					{
						$s_hidden_fields .= '<input type="hidden" name="upgrade" value="1" />';
						$s_hidden_fields .= '<input type="hidden" name="dbms" value="'.$dbms.'" />';
						$s_hidden_fields .= '<input type="hidden" name="prefix" value="'.$table_prefix.'" />';
						$s_hidden_fields .= '<input type="hidden" name="dbhost" value="'.$dbhost.'" />';
						$s_hidden_fields .= '<input type="hidden" name="dbname" value="'.$dbname.'" />';
						$s_hidden_fields .= '<input type="hidden" name="dbuser" value="'.$dbuser.'" />';
						$s_hidden_fields .= '<input type="hidden" name="dbpasswd" value="'.$dbpasswd.'" />';
						$s_hidden_fields .= '<input type="hidden" name="install_step" value="1" />';
						$s_hidden_fields .= '<input type="hidden" name="admin_pass1" value="1" />';
						$s_hidden_fields .= '<input type="hidden" name="admin_pass2" value="1" />';
						$s_hidden_fields .= '<input type="hidden" name="server_port" value="'.$server_port.'" />';
						$s_hidden_fields .= '<input type="hidden" name="server_name" value="'.$server_name.'" />';
						$s_hidden_fields .= '<input type="hidden" name="script_path" value="'.$script_path.'" />';
						$s_hidden_fields .= '<input type="hidden" name="board_email" value="'.$board_email.'" />';

						page_upgrade_form();

					}
					else
					{
						page_common_form($s_hidden_fields, $lang['Download_config']);
					}

					page_footer();
					exit;
				}

				$result = @fputs($fp, $config_data, strlen($config_data));

				@fclose($fp);
				$upgrade_now = $lang['upgrade_submit'];
			}

			$s_hidden_fields = '<input type="hidden" name="username" value="' . $admin_name . '" />';
			$s_hidden_fields .= '<input type="hidden" name="password" value="' . $admin_pass1 . '" />';
			$s_hidden_fields .= '<input type="hidden" name="redirect" value="admin/index.'.$phpEx.'" />';
			$s_hidden_fields .= '<input type="hidden" name="login" value="true" />';
			//继续提交同意条款
			$s_hidden_fields .= '<input type="hidden" name="agree" value="agree" />';			

			page_header($lang['Inst_Step_2'], '../login.'.$phpEx);
			page_common_form($s_hidden_fields, $lang['Finish_Install']);
			page_footer();
			exit;
		}
	}
}
else
{
?>
	<!DOCTYPE html PUBLIC " -//WAPFORUM//DTD XHTML Mobile 1.0//EN" "http://www.wapforum.org/DTD/xhtml-mobile10.dtd">
	<html xmlns="http://www.w3.org/1999/xhtml">
	<head>
	<meta http-equiv="Content-Type" content="application/xhtml+xml; charset=UTF-8" />
	<meta name="viewport" content="width=device-width; initial-scale=1.0; minimum-scale=1.0; maximum-scale=2.0"/>
	<meta name="apple-mobile-web-app-capable" content="yes" />
	<meta name="apple-mobile-web-app-status-bar-style" content="black" />			
	<title>安装_协议</title>
	<link rel="shortcut icon" href="http://phpbb-wap.com/favicon.ico" />
	<link rel="stylesheet" href="http://phpbb-wap.com/style.css" type="text/css" />	
	</head>
	<body>
		<div class="wrap">
			<div class="cat" align="center"><a href="http://phpbb-wap.com"><img src="http://phpbb-wap.com/images/logo.png" /></a></div>
			<div class="row1" align="center"><h2>安装协议</h2></div>
			<div class="navbar">欢迎使用中文phpBB-WAP，phpBB-WAP是免费开源的移动终端网页程序，它可以在 GPL 协议约束的前提下，自由修改、发布。</div>
			<div class="catSides" align="center">协议</div>
			<form name="agree" action="<?php $_SERVER[PHP_SELF] ?>" method="post">
				<div class="row1">
					<textarea rows="20" style="width:99%;">1、本软件为自由软件，您可以遵守 GPL 协议的前提下自由使用！
2、本人（即 “爱疯的云” 、中文phpBB-WAP） 没有义务解答您的的问题！
3、如果您发现程序的 bug，您可以将 bug 以帖子的形式发表到 http://zisuw.com/viewforum.php?f=32 进行交流！
					</textarea>
				</div>
				<div class="row1">我 <input type="submit" name="agree" value="同意"/> 并遵守以上协议并安装！</div>
				<div class="row1">我 <a href="./">不同意</a> 以上协议！</div>
			</form>
		</div>
	</body>
	</html>
<?php } ?> 
