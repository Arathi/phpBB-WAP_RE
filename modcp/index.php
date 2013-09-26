<?php
/**************************************************
 *		(mod) index.php
 *		-------------------
 *		版权所有：爱疯的云
 *		说明：MOD的后台索引页
 ***************************************************/

define('IN_PHPBB', true);

$phpbb_root_path = "./../";
require($phpbb_root_path . 'extension.inc');
require('./pagestart.' . $phpEx);

$dir = @opendir(".");
$setmodules = true;
while( $file = @readdir($dir) )
{
	if( preg_match("/^admin_.*?\." . $phpEx . "$/", $file) )
	{
		include('./' . $file);
	}
}

@closedir($dir);

unset($setmodules);

include('page_header_modcp.'.$phpEx);

$template->set_filenames(array(
	"body" => "modcp/index_body.tpl")
);

if ( !isset($modcp_module) || empty($modcp_module) )
{
	message_die(GENERAL_MESSAGE, 'error');
}

ksort($modcp_module);

while( list($cat, $action_array) = each($modcp_module) )
{
	$cat = ( !empty($lang[$cat]) ) ? $lang[$cat] : preg_replace("/_/", " ", $cat);

	$template->assign_block_vars("catrow", array(
		"ADMIN_CATEGORY" => $cat)
	);

	ksort($action_array);

	$row_count = 0;
	while( list($action, $file)	= each($action_array) )
	{

		$row_color = '';
		$row_class = ( !($row_count%2) ) ? 'row_easy' : 'row_hard';
		$action = ( !empty($lang[$action]) ) ? $lang[$action] : preg_replace("/_/", " ", $action);

		$template->assign_block_vars("catrow.modulerow", array(
			"ROW_COLOR" 	=> "#" . $row_color,
			"ROW_CLASS" 	=> $row_class, 

			"ADMIN_MODULE" => $action,
			"U_ADMIN_MODULE" => append_sid($file))
		);
		$row_count++;
	}
}

$template->pparse("body");

include('page_footer_modcp.'.$phpEx);

?>