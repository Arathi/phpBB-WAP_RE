<div class="navbar"><a href="{U_INDEX}">{L_INDEX}</a>&gt;信息</div>
<div class="catSides">
<!-- BEGIN switch_box_size_notice -->
{BOX_SIZE_STATUS}
<!-- END switch_box_size_notice -->
</div>
<!-- BEGIN listrow -->
<div class="row1">
{listrow.L_PRIVMSG_FOLDER_ALT}{listrow.PRIVMSG_ATTACHMENTS_IMG}<a href="{listrow.U_READ}">{listrow.SUBJECT}</a><br />
[来自：{listrow.FROM}/时间：{listrow.DATE}]
</div>
<!-- END listrow -->
<!-- BEGIN switch_no_messages -->
<div class="row1">
{L_NO_MESSAGES}
</div>
<!-- END switch_no_messages -->
{PAGINATION}
<form method="post" name="privmsg_list" action="{S_PRIVMSGS_ACTION}">
{S_HIDDEN_FIELDS}
<input class="subbutton" type="submit" name="deleteall" value="{L_DELETE_ALL}"/><br/>
</form>
<br/>
{POST_PM}<br/>
{INBOX}{SENTBOX}{OUTBOX}{SAVEBOX}