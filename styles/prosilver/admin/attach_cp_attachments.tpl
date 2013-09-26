<div class="navbar"><a href="{U_INDEX}">{L_INDEX}</a>&gt;<a href="{U_ADMIN}">{L_ADMIN}</a>&gt;<a href="{U_ADMIN_INDEX}">{L_ADMIN_INDEX}</a>&gt;{L_CONTROL_PANEL_TITLE}</div>
<span class="genmed">{L_CONTROL_PANEL_EXPLAIN}</span>
<!-- BEGIN switch_user_based -->
<br /><b>{L_STATISTICS_FOR_USER}</b><br />
<!-- END switch_user_based -->
<form method="post" name="attach_list" action="{S_MODE_ACTION}">
<div class="row1">
{L_VIEW}：{S_VIEW_SELECT}<br />
{L_SELECT_SORT_METHOD}：{S_MODE_SELECT} {L_ORDER} {S_ORDER_SELECT}
<input type="submit" name="submit" value="{L_SUBMIT}" />
</div>
<div class="catSides">所有附件列表</div>
<!-- BEGIN attachrow -->
<div class="{attachrow.ROW_CLASS}">
{L_FILENAME}：<a href="{attachrow.U_VIEW_ATTACHMENT}" target="_blank">{attachrow.FILENAME}</a><br/>
{L_FILECOMMENT}：<br/><input type="text" size="40" maxlength="200" name="attach_comment_list[]" value="{attachrow.COMMENT}" /><br/>
{L_EXTENSION}：<b>{attachrow.EXTENSION}</b><br/>
{L_SIZE}：<b>{attachrow.SIZE}</b><br/>
{L_DOWNLOADS}：<input type="text" size="3" maxlength="10" name="attach_count_list[]" value="{attachrow.DOWNLOAD_COUNT}" /><br/>
{L_POST_TIME}：<b>{attachrow.POST_TIME}</b><br/>
{L_POSTED_IN_TOPIC}：{attachrow.POST_TITLE}<br/>
{L_DELETE}：{attachrow.S_DELETE_BOX}
{attachrow.S_HIDDEN}
</div>
<!-- END attachrow -->
<input type="submit" name="submit_change" value="{L_SUBMIT_CHANGES}" /><input type="submit" name="delete" value="{L_DELETE}" />
<!-- BEGIN switch_user_based -->
{S_USER_HIDDEN}
<!-- END switch_user_based -->
{PAGINATION}
</form>