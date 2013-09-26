<?php

if ( !defined('IN_PHPBB') )
{
	die("Hacking attempt");
}

define('LOTTERY_TABLE', $table_prefix.'lottery');
define('LOTTERY_HISTORY_TABLE', $table_prefix.'lottery_history');
define('INVITE_TABLE', $table_prefix.'invite');
define('BANK_TABLE', $table_prefix.'bank');

?>