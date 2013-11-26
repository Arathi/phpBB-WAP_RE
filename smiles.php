<?php
/***************************************************************************
 *                              smiles.php
 *                            -------------------
 *  Разработка и оптимизация под WAP: Гутник Игорь ( чел )
 *            2011 год
 ***************************************************************************/

define('IN_PHPBB', true);
$phpbb_root_path = './';
include($phpbb_root_path . 'extension.inc');
include($phpbb_root_path . 'common.'.$phpEx);

$inline_columns = 4;
$inline_rows = 5;
$window_columns = 8;

$userdata = session_pagestart($user_ip, $page_id);
init_userprefs($userdata);

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

$page_title = "表情";
include($phpbb_root_path . 'includes/page_header.'.$phpEx);

$template->set_filenames(array(
	'smiliesbody' => 'posting_smilies.tpl')
);

$sql = "SELECT emoticon, code, smile_url   
	FROM " . SMILIES_TABLE . " 
	ORDER BY smilies_id
	LIMIT " . $start . ", " . $board_config['topics_per_page'];
if ($result = $db->sql_query($sql))
{
	$num_smilies = 0;
	$rowset = array();
	while ($row = $db->sql_fetchrow($result))
	{
		if (empty($rowset[$row['smile_url']]))
		{
			$rowset[$row['smile_url']]['code'] = str_replace("'", "\\'", str_replace('\\', '\\\\', $row['code']));
			$rowset[$row['smile_url']]['emoticon'] = $row['emoticon'];
			$num_smilies++;
		}
	}

	if ($num_smilies)
	{
		$smilies_count = ($mode == 'inline') ? min(19, $num_smilies) : $num_smilies;
		$smilies_split_row = ($mode == 'inline') ? $inline_columns - 1 : $window_columns - 1;

		$s_colspan = 0;
		$row = 0;
		$col = 0;

		while (list($smile_url, $data) = @each($rowset))
		{
			if (!$col)
			{
				$template->assign_block_vars('smilies_row', array());
			}

			$template->assign_block_vars('smilies_row.smilies_col', array(
				'SMILEY_CODE' => $data['code'],
				'SMILEY_IMG' => $board_config['smilies_path'] . '/' . $smile_url,
				'SMILEY_DESC' => $data['emoticon'])
			);

			$s_colspan = max($s_colspan, $col + 1);

			if ($col == $smilies_split_row)
			{
				if ($mode == 'inline' && $row == $inline_rows - 1)
				{
					break;
				}
				$col = 0;
				$row++;
			}
			else
			{
				$col++;
			}
		}

		$sql = "SELECT COUNT(smilies_id) as total FROM " . SMILIES_TABLE;
		if ( !($result = $db->sql_query($sql)))
		{
			message_die(GENERAL_ERROR, 'Could not query private message information', '', __LINE__, __FILE__, $sql);
		}
		$smile_count = $db->sql_fetchrow($result);

		if ($smile_count['total'] > $board_config['topics_per_page'])
		{
			$pagination = generate_pagination("smiles.$phpEx?", $smile_count['total'], $board_config['topics_per_page'], $start);
		}

		$template->assign_vars(array(
			'L_EMOTICONS' => $lang['Emoticons'], 
			'L_CLOSE_WINDOW' => $lang['Close_window'], 
			'S_SMILIES_COLSPAN' => $s_colspan,
			'PAGINATION' => $pagination)
		);
	}
}
$template->pparse('smiliesbody');

include($phpbb_root_path . 'includes/page_tail.'.$phpEx);
?>