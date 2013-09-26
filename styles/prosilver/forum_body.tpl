<div class="navbar"><a href="{U_INDEX}">{L_INDEX}</a>&gt;论坛</div>
<div class="catSides">论坛帖子[<a href="{U_NEW_TOPIC}">新</a>_<a href="{U_VERB_TOPIC}">动</a>_<a href="{U_HOT_TOPIC}">热</a>]</div>
<!-- BEGIN recent_topic_row -->
	<div class="row1">
		{recent_topic_row.L_THEME_NUMBER}.<a href="{recent_topic_row.U_TITLE}">{recent_topic_row.L_TITLE}</a><br/>
	</div>
<!-- END recent_topic_row -->
<!-- BEGIN empty_recent_topic_row -->
	<div class="row1">{EMPTY_SHOW}</div>
<!-- END empty_recent_topic_row -->
<!-- BEGIN catrow -->
	<div class="catSides">
		{catrow.CAT_ICON}<a href="{catrow.U_VIEWCAT}" class="cattitle">{catrow.CAT_DESC}</a>
	</div>
	<!-- BEGIN forumrow -->
		<div class="row1">
			<span class="genmed">
				{catrow.forumrow.FORUM_ICON}<a href="{catrow.forumrow.U_VIEWFORUM}">{catrow.forumrow.FORUM_NAME}</a>[{catrow.forumrow.TOPICS}/{catrow.forumrow.POSTS}]
			</span>
		</div>
	<!-- END forumrow -->
<!-- END catrow -->

<!-- BEGIN switch_user_logged_in -->
<div class="catSides">论坛快捷面板</div>
	<div class="row1">- <a href="{U_BOOKMARKS}" >{L_BOOKMARKS}</a></div>
	<div class="row1">- <a href="{U_PRIVATEMSGS}">{PRIVATE_MESSAGE_INFO}</a></div>
	<div class="row1">- <a href="{U_SEARCH_NEW}">{L_SEARCH_NEW}</a></div>
<!-- END switch_user_logged_in -->

<div class="cat">
	{TOTLA_TOPICS}<br />
	{TOTAL_POSTS}<br />
	{TOTAL_ATTACH}<br />
	{TOTAL_USERS}<br />
	{NEWEST_USER}<br />
	<a href="{U_VIEWONLINE}">{TOTAL_USERS_ONLINE}</a>
</div>