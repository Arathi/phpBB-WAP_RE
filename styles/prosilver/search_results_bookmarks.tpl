<div class="navbar"><a href="{U_INDEX}">{L_INDEX}</a>&gt;书签</div>
<div class="row1">{L_SEARCH_MATCHES}</div>
<form method="post" action="{S_BM_ACTION}">
<div class="catSides">书签列表</div>
<!-- BEGIN searchresults -->
<div class="row1">
所属论坛：<a href="{searchresults.U_VIEW_FORUM}">{searchresults.FORUM_NAME}</a><br/>
<input type="checkbox" name="topic_id_list[]" value="{searchresults.TOPIC_ID}" /> {searchresults.TOPIC_TYPE}<a href="{searchresults.U_VIEW_TOPIC}">{searchresults.TOPIC_TITLE}</a> {searchresults.LAST_POST_IMG}<br />
[回:{searchresults.REPLIES}/最后回复:{searchresults.LAST_POST_AUTHOR}/发表时间:{searchresults.LAST_POST_TIME}]
</div>
<!-- END searchresults -->
{S_HIDDEN_FIELDS}
<input type="submit" name="delete" value="{L_DELETE}" />
</form>
{PAGINATION}