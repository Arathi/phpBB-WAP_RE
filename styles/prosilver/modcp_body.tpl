<div class="navbar"><a href="{U_INDEX}">{L_INDEX}</a>&gt;<a href="{U_VIEW_FORUM}">{FORUM_NAME}</a>&gt;{L_MOD_CP}</div>
<div class="row1">{DOB_URL}</div>
<form method="post" action="{S_MODCP_ACTION}">
<div class="catSides">{L_MOD_CP}</div>
<!-- BEGIN topicrow -->
<div class="row1">
{topicrow.TOPIC_TYPE}<a href="{topicrow.U_VIEW_TOPIC}">{topicrow.TOPIC_TITLE}</a>[回:{topicrow.REPLIES}/时间:{topicrow.LAST_POST_TIME}]<input type="checkbox" name="topic_id_list[]" value="{topicrow.TOPIC_ID}" />
</div>
<!-- END topicrow -->
<div class="catSides">将选中的项目:</div>
{S_HIDDEN_FIELDS}
<input class="subbutton" type="submit" name="delete" value="{L_DELETE}" />
<input class="subbutton" type="submit" name="move" value="{L_MOVE}" />
<input class="subbutton" type="submit" name="lock" value="{L_LOCK}" />
<input class="subbutton" type="submit" name="unlock" value="{L_UNLOCK}" />
</form>
{PAGINATION}