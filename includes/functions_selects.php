<?php
/*****************************************************
 *       function_selects.php
 *       -------------------
 *      Разработка: phpBB Group.
 *      Оптимизация под WAP: Гутник Игорь ( чел ).
 *          2010 год
 *		简体中文：爱疯的云
 *		文件说明：该文件主要存放与选择的函数
 *				例如：语言、时间、风格等
 *				下拉框选择使用。。。
 ******************************************************/

/**
* 该函数用于选择地区语言
**/
function language_select($default, $select_name = "language", $dirname="language")
{
	global $phpEx, $phpbb_root_path;

	$dir = opendir($phpbb_root_path . $dirname);

	$lang = array();
	while ( $file = readdir($dir) )
	{
		if (preg_match('#^lang_#i', $file) && !is_file(@phpbb_realpath($phpbb_root_path . $dirname . '/' . $file)) && !is_link(@phpbb_realpath($phpbb_root_path . $dirname . '/' . $file)))
		{
			$filename = trim(str_replace("lang_", "", $file));
			$displayname = preg_replace("/^(.*?)_(.*)$/", "\\1 [ \\2 ]", $filename);
			$displayname = preg_replace("/\[(.*?)_(.*)\]/", "[ \\1 - \\2 ]", $displayname);
			$lang[$displayname] = $filename;
		}
	}

	closedir($dir);

	@asort($lang);
	@reset($lang);

	$lang_select = '<select name="' . $select_name . '">';
	while ( list($displayname, $filename) = @each($lang) )
	{
		$selected = ( strtolower($default) == strtolower($filename) ) ? ' selected="selected"' : '';
		$lang_select .= '<option value="' . $filename . '"' . $selected . '>' . ucwords($displayname) . '</option>';
	}
	$lang_select .= '</select>';

	return $lang_select;
}

/**
* 选择风格
**/
function style_select($default_style, $select_name = "style")
{
	global $db;

	$sql = "SELECT style_id, style_name
		FROM " . STYLES_TABLE . " 
		ORDER BY style_id ASC";
	if ( !($result = $db->sql_query($sql)) )
	{
		message_die(GENERAL_ERROR, "Couldn't query themes table", "", __LINE__, __FILE__, $sql);
	}

	$style_select = '<select name="' . $select_name . '">';
	while ( $row = $db->sql_fetchrow($result) )
	{
		$selected = ( $row['style_id'] == $default_style ) ? ' selected="selected"' : '';

		$style_select .= '<option value="' . $row['style_id'] . '"' . $selected . '>' . $row['style_name'] . '</option>';
	}
	$style_select .= "</select>";

	return $style_select;
}

/**
* 时区选择
**/
function tz_select($default, $select_name = 'timezone')
{
	global $sys_timezone, $lang;

	if ( !isset($default) )
	{
		$default == $sys_timezone;
	}
	$tz_select = '<select name="' . $select_name . '">';

	while( list($offset, $zone) = @each($lang['tz']) )
	{
		$selected = ( $offset == $default ) ? ' selected="selected"' : '';
		$tz_select .= '<option value="' . $offset . '"' . $selected . '>' . $zone . '</option>';
	}
	$tz_select .= '</select>';

	return $tz_select;
}

/**
* 日期格式选择
**/
function select_dateformat($dateformat, $name)
{
	global  $userdata;
	$select_date_format = '<select name="' . $name . '">' . "\n";
	foreach (array("Y/m/d", "Y/m/d G:i", "Y/m/d, H:i (l)", "Y/m/d, H:i (D)", "Y年m月d日", "Y年m月d日 H:i", "Y年m月d日 H:i (l)", "Y年m月d日 H:i (D)", "Y-m-d", "Y-m-d H:i", "Y-m-d H:i (l)", "Y-m-d H:i (D)") as $k => $v)
	{
		$select_date_format .= '<option value="' . $v . '"' . ($v == $dateformat ? 'selected="selected"' : '') . '>';
		$select_date_format .= create_date($v, time(), $userdata['user_timezone'], false) . '</option>' . "\n";
	}
	$select_date_format .= '</select>';
	return $select_date_format;
}

/**
* 表情选择
**/
function smiles_select()
{
	global $db;

	$sql = "SELECT code
		FROM " . SMILIES_TABLE . " 
		ORDER BY smilies_id ASC";
	if ( !($result = $db->sql_query($sql)) )
	{
		message_die(GENERAL_ERROR, '表情获取失败', '', __LINE__, __FILE__, $sql);
	}

	$smiles_select = '<select name="smile_code">';
	$smiles_select .= '<option value=""> </option>';
	while ( $row = $db->sql_fetchrow($result) )
	{
		$smiles_select .= '<option value="' . $row['code'] . '">' . $row['code'] . '</option>';
	}
	$smiles_select .= "</select>";

	return $smiles_select;
}
?>