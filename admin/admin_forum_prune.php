<?php
/*************************************************
 *		admin_forum_prune.php
 *		-------------------
 *      Разработка: phpBB Group.
 *      Оптимизация под WAP: Гутник Игорь ( чел ).
 *          2010 год
 *		简体中文：爱疯的云
 *************************************************/

define('IN_PHPBB', true);

if ( !empty($setmodules) )
{
	$filename = basename(__FILE__);
	$module['Forums']['Forums_prune'] = $filename;

	return;
}

$phpbb_root_path = "./../";
require($phpbb_root_path . 'extension.inc');
require('./pagestart.' . $phpEx);
require($phpbb_root_path . 'includes/prune.'.$phpEx);
require($phpbb_root_path . 'includes/functions_admin.'.$phpEx); 

if( isset($HTTP_GET_VARS[POST_FORUM_URL]) || isset($HTTP_POST_VARS[POST_FORUM_URL]) )
{
	$forum_id = ( isset($HTTP_POST_VARS[POST_FORUM_URL]) ) ? $HTTP_POST_VARS[POST_FORUM_URL] : $HTTP_GET_VARS[POST_FORUM_URL];

	if( $forum_id == -1 )
	{
		$forum_sql = '';
	}
	else
	{
		$forum_id = intval($forum_id);
		$forum_sql = "AND forum_id = $forum_id";
	}
}
else
{
	$forum_id = '';
	$forum_sql = '';
}

$sql = "SELECT f.*
	FROM " . FORUMS_TABLE . " f, " . CATEGORIES_TABLE . " c
	WHERE c.cat_id = f.cat_id
	$forum_sql
	ORDER BY c.cat_order ASC, f.forum_order ASC";
if( !($result = $db->sql_query($sql)) )
{
	message_die(GENERAL_ERROR, 'Could not obtain list of forums for pruning', '', __LINE__, __FILE__, $sql);
}

$forum_rows = array();
while( $row = $db->sql_fetchrow($result) )
{
	$forum_rows[] = $row;
}

if( isset($HTTP_POST_VARS['doprune']) )
{
	$prunedays = ( isset($HTTP_POST_VARS['prunedays']) ) ? intval($HTTP_POST_VARS['prunedays']) : 0;

	$prunedate = time() - ( $prunedays * 86400 );

	$template->set_filenames(array(
		'body' => 'admin/forum_prune_result_body.tpl')
	);

	for($i = 0; $i < count($forum_rows); $i++)
	{
		$p_result = prune($forum_rows[$i]['forum_id'], $prunedate);
		sync('forum', $forum_rows[$i]['forum_id']);
	
		$row_color = '';
		$row_class = ( !($i % 2) ) ? 'row_easy' : 'row_hard';
	
		$template->assign_block_vars('prune_results', array(
			'ROW_COLOR' => '#' . $row_color, 
			'ROW_CLASS' => $row_class, 
			'FORUM_NAME' => $forum_rows[$i]['forum_name'],
			'FORUM_TOPICS' => $p_result['topics'],
			'FORUM_POSTS' => $p_result['posts'])
		);
	}

	$template->assign_vars(array(
		'L_FORUM_PRUNE' => $lang['Forum_Prune'],
		'L_FORUM' => $lang['Forum'],
		'L_TOPICS_PRUNED' => $lang['Topics_pruned'],
		'L_POSTS_PRUNED' => $lang['Posts_pruned'],
		'L_PRUNE_RESULT' => $lang['Prune_success'],
		'U_SELECT_FORUM' => append_sid("admin_forum_prune.$phpEx"))
	);
}
else
{

	if( empty($HTTP_POST_VARS[POST_FORUM_URL]) )
	{
		$template->set_filenames(array(
			'body' => 'admin/forum_prune_select_body.tpl')
		);

		$select_list = '<select name="' . POST_FORUM_URL . '">';
		$select_list .= '<option value="-1">' . $lang['All_Forums'] . '</option>';

		for($i = 0; $i < count($forum_rows); $i++)
		{
			$select_list .= '<option value="' . $forum_rows[$i]['forum_id'] . '">' . $forum_rows[$i]['forum_name'] . '</option>';
		}
		$select_list .= '</select>';

		$template->assign_vars(array(
			'L_FORUM_PRUNE' => $lang['Forum_Prune'],
			'L_SELECT_FORUM' => $lang['Select_a_Forum'], 
			'L_LOOK_UP' => $lang['Look_up_Forum'],

			'S_FORUMPRUNE_ACTION' => append_sid("admin_forum_prune.$phpEx"),
			'S_FORUMS_SELECT' => $select_list)
		);
	}
	else
	{
		$forum_id = intval($HTTP_POST_VARS[POST_FORUM_URL]);

		$template->set_filenames(array(
			'body' => 'admin/forum_prune_body.tpl')
		);

		$forum_name = ( $forum_id == -1 ) ? $lang['All_Forums'] : $forum_rows[0]['forum_name'];

		$prune_data = $lang['Prune_topics_not_posted'] . " "; 
		$prune_data .= '<input type="text" name="prunedays" size="4"> ' . $lang['Days'];

		$hidden_input = '<input type="hidden" name="' . POST_FORUM_URL . '" value="' . $forum_id . '" />';

		$template->assign_vars(array(
			'FORUM_NAME' => $forum_name,

			'L_FORUM' => $lang['Forum'], 
			'L_FORUM_PRUNE' => $lang['Forum_Prune'], 
			'L_FORUM_PRUNE_EXPLAIN' => $lang['Forum_Prune_explain'], 
			'L_DO_PRUNE' => $lang['Do_Prune'],

			'U_SELECT_FORUM' => append_sid("admin_forum_prune.$phpEx"),
			
			'S_FORUMPRUNE_ACTION' => append_sid("admin_forum_prune.$phpEx"),
			'S_PRUNE_DATA' => $prune_data,
			'S_HIDDEN_VARS' => $hidden_input)
		);
	}
}

$template->pparse('body');

include('./page_footer_admin.'.$phpEx);

?>