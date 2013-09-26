<?php
/*****************************************
 *		rules.php
 *		-------------------
 *		Разработка: Гутник Игорь ( чел )
 *		Идея: Тимаков Сергей ( M0rbid )
 *			2011 год
 *		简体中文：爱疯的云
 ****************************************/

define('IN_PHPBB', true);
$phpbb_root_path = './';
include($phpbb_root_path . 'extension.inc');
include($phpbb_root_path . 'common.'.$phpEx);

$forum_id = get_var('f', 0);
$privmsg = (!$forum_id) ? true : false;

$userdata = session_pagestart($user_ip, PAGE_PRAVILA);
init_userprefs($userdata);

if ( isset($HTTP_GET_VARS['mode']) )
{
	$mode = htmlspecialchars($HTTP_GET_VARS['mode']);
}
else
{
	$mode = '';
}

if ( isset($HTTP_GET_VARS['act']) )
{
	$action = htmlspecialchars($HTTP_GET_VARS['act']);
}
else
{
	$action = '';
}

$rule_cat_id = ( isset($HTTP_GET_VARS['crid']) ) ? abs(intval($HTTP_GET_VARS['crid'])) : '';

$is_rules_auth = ( $userdata['user_level'] == ADMIN || $userdata['user_level'] == MODCP ) ? true : false;

switch( $mode )
{
	case 'addcat':
	case 'editcat':
		if( $is_rules_auth )
		{
			$subj = htmlspecialchars(trim($HTTP_POST_VARS['subject']));

			if($rule_cat_id)
			{
				$sql = "SELECT * FROM " . RULES_CAT_TABLE . " WHERE cat_r_id = $rule_cat_id";
				if( !($result = $db->sql_query($sql)) )
				{
					message_die(GENERAL_ERROR, "Could not obtain rules information.", '', __LINE__, __FILE__, $sql);
				}
				$crid = $db->sql_fetchrow($result);
				$cridn = $crid['cat_r_name'];
				$crid = $crid['cat_r_id'];
			}
			else
			{
				$crid = '';
			}

			if( $crid != '' && $subj != '' )
			{
				$sql = "UPDATE " . RULES_CAT_TABLE . " SET cat_r_name = '" . str_replace("\'", "''", $subj) . "'
            				WHERE cat_r_id = $crid";
				if(!($result=$db->sql_query($sql)))
				{
					message_die(GENERAL_ERROR, 'Error rules cat table!','',__LINE__,__FILE__,$sql);
				}
             			redirect(append_sid("rules.$phpEx", 1));
			}
			elseif( $crid == '' && $subj != '' )
			{
				$sql = "SELECT MAX(cat_r_id) AS max_id FROM " . RULES_CAT_TABLE;
				if(!($result=$db->sql_query($sql)))
				{
					message_die(GENERAL_ERROR, 'Error rules cat table!', '',__LINE__,__FILE__, $sql);
				}
				$result = $db->sql_fetchrow($result);
				$new_id = $result['max_id'] + 1;
				$sql = "INSERT INTO " . RULES_CAT_TABLE . " (cat_r_id, cat_r_name) VALUES ($new_id, '" . str_replace("\'", "''", $subj) . "')";
				if(!($result=$db->sql_query($sql)))
				{
					message_die(GENERAL_ERROR, 'Error rules cat table!', '',__LINE__,__FILE__, $sql);
				}

             			redirect(append_sid("rules.$phpEx", 1));
            		}
            		else
			{
				if ( $mode == 'editcat' )
				{
					$page_title = $lang['Edit_cat_rules'];
					$s_rules_action = append_sid("rules.$phpEx?mode=editcat&amp;crid=$crid");
					$cat_name = $cridn;
				}
				else
				{
					$page_title = $lang['New_cat_rules'];
					$s_rules_action = append_sid("rules.$phpEx?mode=addcat");
					$cat_name = '';
				}
				include($phpbb_root_path . 'includes/page_header.'.$phpEx);

				$template->set_filenames(array(
					'body' => 'rules_cat.tpl')
				);

				$template->assign_vars(array(
					'CAT_NAME' => $cat_name,
					'S_RULES_ACTION' => $s_rules_action)
				);

				$template->pparse('body');

				include($phpbb_root_path . 'includes/page_tail.'.$phpEx);
			}
		}
		else
		{
			redirect(append_sid("rules.$phpEx", 1));
		}
	break;
	case 'addrule':
	case 'editrule':
		if( $is_rules_auth )
		{
			$rule_id = abs(intval($HTTP_GET_VARS['r']));
			$subj = htmlspecialchars(trim($HTTP_POST_VARS['subject']));
			$name = htmlspecialchars(trim($HTTP_POST_VARS['name']));
			$moder = ( $HTTP_POST_VARS['moder'] == 1 ) ? 1 : 0;

			if ( $rule_id )
			{
				$sql = "SELECT * FROM " . RULES_TABLE . " WHERE rule_id = $rule_id";
				if( !($result = $db->sql_query($sql)) )
				{
					message_die(GENERAL_ERROR, "Could not obtain rules information.", '', __LINE__, __FILE__, $sql);
				}
				$r = $db->sql_fetchrow($result);
				$rn = $r['rule_name'];
				$rs = $r['rule_subj'];
				$cr = $r['rule_cat_id'];
				$rm = $r['rule_moder'];
				$rule_base_id = $r['rule_id'];
			}

			$rule_cat_id = ( $cr ) ? $cr : $rule_cat_id;

			if($rule_cat_id)
			{
				$sql = "SELECT * FROM " . RULES_CAT_TABLE . " WHERE cat_r_id = $rule_cat_id";
				if( !($result = $db->sql_query($sql)) )
				{
					message_die(GENERAL_ERROR, "Could not obtain rules information.", '', __LINE__, __FILE__, $sql);
				}
				$cid = $db->sql_fetchrow($result);
				$cid_name = $cid['cat_r_name'];
				$cid = $cid['cat_r_id'];
			}
			else
			{
				$cid = '';
			}

			if( $cid != '' && $rule_base_id == '' && $subj != '' && $name != '' )
			{
				$sql = "SELECT MAX(rule_id) AS max_id FROM " . RULES_TABLE;
				if(!($result = $db->sql_query($sql)))
				{
					message_die(GENERAL_ERROR, 'Error rules table!', '', __LINE__, __FILE__, $sql);
				}
				$result = $db->sql_fetchrow($result);
				$new_id = $result['max_id'] + 1;

				$sql = "INSERT INTO " . RULES_TABLE . " (rule_id, rule_cat_id, rule_name, rule_subj, rule_moder)
						VALUES ($new_id, $cid, '" . str_replace("\'", "''", $name) . "', '" . str_replace("\'", "''", $subj) . "', '" . $moder . "')";
				if(!($result=$db->sql_query($sql)))
				{
					message_die(GENERAL_ERROR, 'Error rules table!','',__LINE__,__FILE__,$sql);
				}
				redirect(append_sid("rules.$phpEx?crid=$cid", 1));
			}
			elseif ( $cid != '' && $rule_base_id && $subj != '' && $name != '' )
			{
				$sql = "UPDATE " . RULES_TABLE . "
					SET rule_name = '" . str_replace("\'", "''", $name) . "', rule_subj = '" . str_replace("\'", "''", $subj) . "', rule_moder = '" . $moder . "'
					WHERE rule_id = $rule_base_id";
				if(!($result=$db->sql_query($sql)))
				{
					message_die(GENERAL_ERROR, 'db error !!!','',__LINE__,__FILE__,$sql);
				}
				redirect(append_sid("rules.$phpEx?crid=$cid", 1));
			}
			elseif ( $cid != '' )
			{
				if ( $mode == 'editrule' )
				{
					$page_title = $lang['Edit_rules'];
					$s_rules_action = append_sid("rules.$phpEx?mode=editrule&amp;r=$rule_base_id");
					$name = $rn;
					$text = $rs;
					$moder = ( $rm == 0 ) ? '' : ' checked="checked"';
				}
				else
				{
					$page_title = $lang['New_rules'];
					$s_rules_action = append_sid("rules.$phpEx?mode=addrule&amp;crid=$cid");
					$moder = ' checked="checked"';
					$name = '';
					$text = '';
				}
				$cat_url = '<a href="' . append_sid("rules.$phpEx?crid=$cid") . '">' . $cid_name . '</a>';

				include($phpbb_root_path . 'includes/page_header.'.$phpEx);

				$template->set_filenames(array(
					'body' => 'rules_edit.tpl')
				);

				$template->assign_vars(array(
					'NAME' => $name,
					'TEXT' => $text,
					'MODER' => $moder,
					'CAT_URL' => $cat_url,
					'S_RULES_ACTION' => $s_rules_action)
				);

				$template->pparse('body');

				include($phpbb_root_path . 'includes/page_tail.'.$phpEx);
			}
			else
			{
				redirect(append_sid("rules.$phpEx", 1));
			}
		}
		else
		{
			redirect(append_sid("rules.$phpEx", 1));
		}
	break;
	case 'delcat':
	case 'delrule':
		$rule_id = abs(intval($HTTP_GET_VARS['r']));
		$crid = abs(intval($HTTP_GET_VARS['crid']));
		if( $is_rules_auth )
		{
			if ( $rule_id && $mode == 'delrule' )
			{
				$sql = "SELECT * FROM " . RULES_TABLE . " WHERE rule_id = $rule_id";
				if( !($result = $db->sql_query($sql)) )
				{
					message_die(GENERAL_ERROR, "Could not obtain rules information.", '', __LINE__, __FILE__, $sql);
				}
				$r = $db->sql_fetchrow($result);
				$rn = $r['rule_name'];
				$cr = $r['rule_cat_id'];
				$rule_base_id = $r['rule_id'];
				$page_title = $lang['Delete_rules'];
			}
			elseif ( $crid && $mode == 'delcat' )
			{
				$sql = "SELECT * FROM " . RULES_CAT_TABLE . " WHERE cat_r_id = $crid";
				if( !($result = $db->sql_query($sql)) )
				{
					message_die(GENERAL_ERROR, "Could not obtain rules cat information.", '', __LINE__, __FILE__, $sql);
				}
				$crid = $db->sql_fetchrow($result);
				$cridn = $crid['cat_r_name'];
				$crid_base = $crid['cat_r_id'];
				$page_title = $lang['Delete_cat_rules'];
			}
			else
			{
				redirect(append_sid("rules.$phpEx", 1));
			}

			if ( isset($HTTP_POST_VARS['cancel']) )
			{
				redirect(append_sid("rules.$phpEx", 1));
			}

			$confirm = ( $HTTP_POST_VARS['confirm'] ) ? TRUE : 0;
			if(!$confirm)
			{
				include($phpbb_root_path . 'includes/page_header.'.$phpEx);
				$template->set_filenames(array(
					'confirm' => 'confirm_body.tpl')
				);

				$lang_confirm_rule = '请确认是否删除规则：' . $rn;
				$lang_confirm_cat = '请确认是否删除规则分类：' . $cridn;
				$s_confirm = ( $mode == 'delrule' ) ? append_sid("rules.$phpEx?mode=delrule&amp;r=$rule_base_id") : append_sid("rules.$phpEx?mode=delcat&amp;crid=$crid_base");

				$template->assign_vars(array(
					'MESSAGE_TITLE' => $lang['Confirm'],
					'MESSAGE_TEXT' => ( $mode == 'delrule' ) ? $lang_confirm_rule : $lang_confirm_cat,
					'L_YES' => $lang['Yes'],
					'L_NO' => $lang['No'],
					'S_CONFIRM_ACTION' => $s_confirm)
				);

				$template->pparse('confirm');

				include($phpbb_root_path . 'includes/page_tail.'.$phpEx);
				exit;
			}

			if ( $rule_base_id && $mode == 'delrule' )
			{
				$sql = "DELETE FROM " . RULES_TABLE . "
					WHERE rule_id = $rule_base_id";
				if(!($result = $db->sql_query($sql)))
				{
					message_die(GENERAL_ERROR, 'Error delete rule', '', __LINE__, __FILE__, $sql);
				}
				redirect(append_sid("rules.$phpEx?crid=$cr", 1));
			}
			elseif ( $crid_base && $mode == 'delcat' )
			{
				$sql = "DELETE FROM " . RULES_CAT_TABLE . "
					WHERE cat_r_id = $crid_base";
				if(!($result = $db->sql_query($sql)))
				{
					message_die(GENERAL_ERROR, 'Error delete rules cat', '', __LINE__, __FILE__, $sql);
				}
				$sql = "DELETE FROM " . RULES_TABLE . "
					WHERE rule_cat_id = $crid_base";
				if(!($result = $db->sql_query($sql)))
				{
					message_die(GENERAL_ERROR, 'Error delete rules', '', __LINE__, __FILE__, $sql);
				}
				redirect(append_sid("rules.$phpEx", 1));
			}
			else
			{
				redirect(append_sid("rules.$phpEx", 1));
			}
		}
		else
		{
			redirect(append_sid("rules.$phpEx", 1));
		}
	break;
	case 'attach':
		if ($privmsg)
		{
			$auth['auth_attachments'] = ($userdata['user_level'] != ADMIN) ? intval($attach_config['allow_pm_attach']) : true;
			$auth['auth_view'] = true;
			$_max_filesize = $attach_config['max_filesize_pm'];
		}
		else
		{
			$auth = auth(AUTH_ALL, $forum_id, $userdata);
			$_max_filesize = $attach_config['max_filesize'];
		}

		if (!($auth['auth_attachments'] && $auth['auth_view']))
		{
			message_die(GENERAL_ERROR, 'You are not allowed to call this file (ID:2)');
		}

		$page_title = $lang['Attach_rules_title'];
		include($phpbb_root_path . 'includes/page_header.' . $phpEx);

		$template->set_filenames(array(
			'body' => 'posting_attach_rules.tpl')
		);

		$sql = 'SELECT group_id, group_name, max_filesize, forum_permissions
			FROM ' . EXTENSION_GROUPS_TABLE . ' 
			WHERE allow_group = 1 
			ORDER BY group_name ASC';

		if (!($result = $db->sql_query($sql)))
		{ 
			message_die(GENERAL_ERROR, 'Could not query Extension Groups.', '', __LINE__, __FILE__, $sql); 
		} 

		$allowed_filesize = array(); 
		$rows = $db->sql_fetchrowset($result); 
		$num_rows = $db->sql_numrows($result); 
		$db->sql_freeresult($result);

		$nothing = true;
		for ($i = 0; $i < $num_rows; $i++)
		{
			$auth_cache = trim($rows[$i]['forum_permissions']);
			$permit = ($privmsg) ? true : ((is_forum_authed($auth_cache, $forum_id)) || trim($rows[$i]['forum_permissions']) == '');

			if ($permit)
			{
				$nothing = false;
				$group_name = $rows[$i]['group_name'];
				$f_size = intval(trim($rows[$i]['max_filesize']));
				$det_filesize = (!$f_size) ? $_max_filesize : $f_size;
				$size_lang = ($det_filesize >= 1048576) ? $lang['MB'] : (($det_filesize >= 1024) ? $lang['KB'] : $lang['Bytes']); 

				if ($det_filesize >= 1048576) 
				{
					$det_filesize = round($det_filesize / 1048576 * 100) / 100; 
				}
				else if ($det_filesize >= 1024) 
				{ 
					$det_filesize = round($det_filesize / 1024 * 100) / 100; 
				} 

				$max_filesize = ($det_filesize == 0) ? $lang['Unlimited'] : $det_filesize . ' ' . $size_lang;

				$template->assign_block_vars('group_row', array(
					'GROUP_RULE_HEADER' => sprintf($lang['Group_rule_header'], $group_name, $max_filesize))
				);

				$sql = 'SELECT extension
					FROM ' . EXTENSIONS_TABLE . " 
					WHERE group_id = " . (int) $rows[$i]['group_id'] . " 
					ORDER BY extension ASC";

				if (!($result = $db->sql_query($sql))) 
				{ 
					message_die(GENERAL_ERROR, 'Could not query Extensions.', '', __LINE__, __FILE__, $sql); 
				} 

				$e_rows = $db->sql_fetchrowset($result);
				$e_num_rows = $db->sql_numrows($result);
				$db->sql_freeresult($result);

				for ($j = 0; $j < $e_num_rows; $j++)
				{
					$template->assign_block_vars('group_row.extension_row', array(
						'EXTENSION' => $e_rows[$j]['extension'])
					);
				}
			}
		}

		$template->assign_vars(array(
			'L_RULES_TITLE'			=> $lang['Attach_rules_title'],
			'L_CLOSE_WINDOW'		=> $lang['Close_window'],
			'L_EMPTY_GROUP_PERMS'	=> $lang['Note_user_empty_group_permissions'])
		);

		if ($nothing)
		{
			$template->assign_block_vars('switch_nothing', array());
		}

		$template->pparse('body');
	break;
	case 'faq':
		$page_title = '规则';
		if ( $action == 'translit' )
		{
			$catsides = '自动翻译';
			$print = '"Sch" => "Щ", "sch" => "щ"<br/>"Yu" => "Ю", "yu" => "ю"<br/>"Ju" => "Ю", "ju" => "ю"<br/>"Ya" => "Я", "ya" => "я"<br/>"Ja" => "Я", "ja" => "я"<br/>"Yo" => "Ё", "yo" => "ё"<br/>"Jo" => "Ё", "jo" => "ё"<br/>"Zh" => "Ж", "zh" => "ж"<br/>"Ch" => "Ч", "ch" => "ч"<br/>"Sh" => "Ш", "sh" => "ш"<br/>"Ts" => "Ц", "ts" => "ц"<br/>"E\'" => "Э", "e\'" => "э"<br/>"A"=>"А", "a"=>"а"<br/>"B"=>"Б", "b"=>"б"<br/>"C"=>"Ц", "c"=>"ц"<br/>"D"=>"Д", "d"=>"д"<br/>"E"=>"Е", "e"=>"е"<br/>"F"=>"Ф", "f"=>"ф"<br/>"G"=>"Г", "g"=>"г"<br/>"H"=>"Х", "h"=>"х"<br/>"I"=>"И", "i"=>"и"<br/>"J"=>"Й", "j"=>"й"<br/>"K"=>"К", "k"=>"к"<br/>"L"=>"Л", "l"=>"л"<br/>"M"=>"М",  "m"=>"м"<br/>"N"=>"Н", "n"=>"н"<br/>"O"=>"О", "o"=>"о"<br/>"P"=>"П", "p"=>"п"<br/>"R"=>"Р", "r"=>"р"<br/>"S"=>"С", "s"=>"с"<br/>"T"=>"Т", "t"=>"т"<br/>"U"=>"У", "u"=>"у"<br/>"V"=>"В", "v"=>"в"<br/>"W"=>"В", "w"=>"в"<br/> "Y"=>"Ы", "y"=>"ы"<br/>"Z"=>"З", "z"=>"з"<br/>"``"=>"Ъ", "`"=>"ъ"';
		}
		elseif ( $action == 'autologin' )
		{
			$catsides = '自动登录';
			$print = '您可以这样编辑网址：<br/>http://'.$board_config['server_name'].$board_config['script_path'].'login.php?username=用户名&password=密码<br/>然后在浏览器输入该网址即可登录本站';
		}
		elseif ( $action == 'bbcode' )
		{
			$catsides = 'BB-code';
			$print = '[b]粗体文字[/b] - <b>粗体文字</b><br/>[i]倾斜文字[/i] - <i>倾斜文字</i><br/>[u]文字下划线[/u] - <u>文字下划线</u></div><div class="row1">[color=颜色]我是一段文字[/color] - 我是一段文字<br/>颜色:<br/>red - 红色<br/>blue - 蓝色<br/>yellow - 黄色<br/>green - 绿色<br/>brown - 棕色<br/>gray - 灰色<br/>lightgreen - 浅绿色<br/>lime - 绿黄色<br/>purple - 紫色<br/>olive - 橄榄色<br/>navy - 深蓝色<br/>silver - 银色<br/>pink - 粉红色<br/>gold - 金色<br/>orange - 橙色</div><div class="row1">[url=http://域名]说明[/url] - 用户超链接</div><div class="row1">设置字体大小：[size=数字]我是一段文字[/size]';
		}
		else
		{
			$catsides = 'FAQ 列表';
			$print = '- <a href="' . append_sid("rules.$phpEx?mode=faq&amp;act=autologin") . '">自动登录</a><br/>- <a href="' . append_sid("rules.$phpEx?mode=faq&amp;act=translit") . '">翻译</a><br/>- <a href="' . append_sid("rules.$phpEx?mode=faq&amp;act=bbcode") . '">BBcode</a><br/>- <a href="' . append_sid("smiles.$phpEx") . '">表情</a>';
		}

		include($phpbb_root_path . 'includes/page_header.'.$phpEx);

		$template->set_filenames(array(
			'body' => 'rules_faq.tpl')
		);

		$template->assign_vars(array(
			'CATSIDES' => $catsides,
			'PRINT' => $print)
		);

		$template->pparse('body');

		include($phpbb_root_path . 'includes/page_tail.'.$phpEx);
	break;
	default:
		if( $rule_cat_id )
		{
			$sql = "SELECT * FROM " . RULES_CAT_TABLE . " WHERE cat_r_id = $rule_cat_id";
			if( !($result = $db->sql_query($sql)) )
			{
				message_die(GENERAL_ERROR, "Could not obtain rules information.", '', __LINE__, __FILE__, $sql);
			}
			$cat = $db->sql_fetchrow($result);

			$sql = "SELECT * FROM " . RULES_TABLE . " WHERE rule_cat_id = $rule_cat_id ORDER BY rule_id";
			if( !($result = $db->sql_query($sql)) )
			{
				message_die(GENERAL_ERROR, "Could not obtain rules information.", '', __LINE__, __FILE__, $sql);
			}

			$rulesrow = array();
			if($row = $db->sql_fetchrow($result))
			{
				do
				{
					$rulesrow[] = $row;
				}
				while ($row = $db->sql_fetchrow($result));
				$db->sql_freeresult($result);
			}

			if(!empty($HTTP_GET_VARS['hl']))
			{
				$hl = abs(intval($HTTP_GET_VARS['hl']));
			}

			for($i=0; $i<count($rulesrow); $i++)
			{
				$sss = str_replace("\n", "\n<br/>\n", $rulesrow[$i]['rule_subj']);

				$template->assign_block_vars('rulesrow', array(
					'RULE_MOD' => ( ($rulesrow[$i]['rule_moder'] == 1) && $is_rules_auth ) ? '(MOD)' : '',
		   			'RULE_NAME' => $rulesrow[$i]['rule_name'],
		   			'RULE_EDIT_DELETE' => ( $is_rules_auth ) ? ' [<a href="' . append_sid("rules.$phpEx?mode=editrule&amp;r=" . $rulesrow[$i]['rule_id']) . '">编辑</a>/<a href="' . append_sid("rules.$phpEx?mode=delrule&amp;r=" . $rulesrow[$i]['rule_id']) . '">删除</a>]' : '',
		   			'RULE_SUBJ' => ( $hl && ($hl == $rulesrow[$i]['rule_id']) ) ? '<b style="color: red">' . $sss . '</b>' : $sss)
				);
			}
		}

		$o_rules = '';

		$sql = "SELECT * FROM " . RULES_CAT_TABLE . " ORDER BY cat_r_id";
		if( !($result = $db->sql_query($sql)) )
		{
			message_die(GENERAL_ERROR, "Could not obtain category rules information.", '', __LINE__, __FILE__, $sql);
		}

		$row = array();
		if($row = $db->sql_fetchrow($result))
		{
			do
			{
				$o_rules .= '- <a href="' . append_sid("rules.$phpEx?crid=" . $row['cat_r_id']) . '">' . $row['cat_r_name'] . '</a>';
				if( $is_rules_auth )
				{
					$o_rules .= ' [<a href="'.append_sid("rules.$phpEx?mode=editcat&amp;crid=" . $row['cat_r_id']) . '">编辑</a>/<a href="' . append_sid("rules.$phpEx?mode=delcat&amp;crid=" . $row['cat_r_id']) . '">删除</a>]';
				}
				$o_rules .= '<br/>';
			}
			while ($row = $db->sql_fetchrow($result));
			$db->sql_freeresult($result);
		}
		$adm_rules = $add_rules = '';

		if( $is_rules_auth )
		{
			$adm_rules = '<br/>- <a href="' . append_sid("rules.$phpEx?mode=addcat") . '">添加分类</a>';
		  	if( $cat )
		  	{
		  		$add_rules = '<div class="row1">- <a href="' . append_sid("rules.$phpEx?mode=addrule&amp;crid=" . $cat['cat_r_id']) . '">添加规则</a></div>';
		  	}
		}

		$o_rules .= '- <a href="' . append_sid("rules.$phpEx?mode=faq") . '">FAQ 列表</a>';

		$page_title = $lang['Rules'];
		include($phpbb_root_path.'includes/page_header.'.$phpEx);

		$template->set_filenames(array(
			'body' => 'rules_body.tpl')
		);

		$template->assign_vars(array(
			'CAT_RULES' => ( $cat ) ? '<div class="catSides">' . $cat['cat_r_name'] . '</div>' : '',
			'OTHER_RULES' => $o_rules,
			'RULES_ADMIN' => $adm_rules,
			'ADD_RULE' => $add_rules)
		);

		$template->pparse('body');
	break;
}

include($phpbb_root_path . 'includes/page_tail.'.$phpEx);

?>