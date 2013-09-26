<div class="nav"><a href="{U_INDEX}">{L_INDEX}</a>&gt;{FORUM_NAME}</div>
{HOBOE}
<div class="row1">
	<form action="{S_SEARCH_ACTION}" method="post">
		<input type="text" name="search_keywords" value="" />
		<input type="submit" value=" 搜索 " />
	</form>
</div>
<div class="catSides">
	<a href="{U_POST_NEW_TOPIC}"><font color="#FFFFFF">发帖</font></a> . <a href="{U_SPECIAL}">专题</a> . <a href="{U_SEARCH}">搜索</a> . <a href="mods/sign/index.php">签到</a></font>
</div>
<!-- BEGIN topicrow -->
	<div class="row1">
		{topicrow.NOMER_POSTA}.{topicrow.L_TOPIC_FOLDER_ALT}{topicrow.A_TOPIC_COLOR}{topicrow.TOPIC_ATTACHMENT_IMG}{topicrow.TOPIC_TYPE}<a href="{topicrow.U_VIEW_TOPIC}">{topicrow.TOPIC_TITLE}</a>[回:{topicrow.REPLIES}/作者:{topicrow.LAST_POST_AUTHOR}]{topicrow.LAST_POST_IMG}
	</div>
<!-- END topicrow -->

<!-- BEGIN switch_no_topics -->
	<div class="row1">{L_NO_TOPICS}</div>
<!-- END switch_no_topics -->
{PAGINATION}
<div class="nav">
{MODER}
版主：{MODERATORS}
</div>