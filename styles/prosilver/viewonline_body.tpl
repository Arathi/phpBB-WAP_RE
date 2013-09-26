<div id="top" class="navbar"><a href="{U_INDEX}">{L_INDEX}</a>&gt;在线状态</div>
{HOBOE}
<div class="row1">{RECORD_USERS}</div>
<div class="catSides">活跃的用户</div>
<div class="row1">
	{TOTAL_REGISTERED_USERS_ONLINE}
	{TOTAL_HIDDEN_USERS_ONLINE}
	{TOTAL_GUEST_USERS_ONLINE}
</div>
<!-- BEGIN reg_user_row -->
	<div class="row1">
		<a href="{reg_user_row.U_USER_PROFILE}">{reg_user_row.USERNAME}</a> &raquo;
		<a href="{reg_user_row.U_FORUM_LOCATION}">{reg_user_row.FORUM_LOCATION}</a>
	</div>
<!-- END reg_user_row -->
<!-- BEGIN guest_user_row -->
	<div class="row1">
		{guest_user_row.USERNAME} &raquo;
		<a href="{guest_user_row.U_FORUM_LOCATION}">{guest_user_row.FORUM_LOCATION}</a>
	</div>
<!-- END guest_user_row -->
<a href="#top" target="_self">↑返回顶部↑</a><br class="all" />