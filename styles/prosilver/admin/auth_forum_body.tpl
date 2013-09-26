<div class="navbar"><a href="{U_INDEX}">{L_INDEX}</a>&gt;<a href="{U_ADMIN}">{L_ADMIN}</a>&gt;<a href="{U_ADMIN_INDEX}">{L_ADMIN_INDEX}</a>&gt;<a href="{U_AUTH_SELECT}">选择论坛</a>&gt;{L_AUTH_TITLE}</div>
<span class="genmed">{L_AUTH_EXPLAIN}</span>
<form method="post" action="{S_FORUMAUTH_ACTION}">
	<div class="catSides">{FORUM_NAME}</div>
	<!-- BEGIN forum_auth_titles -->
		<div class="row1">
			{forum_auth_titles.CELL_TITLE}:{forum_auth_titles.S_AUTH_LEVELS_SELECT}
		</div>
	<!-- END forum_auth_titles -->
	<div class="row1"> - {U_SWITCH_MODE}</div>
	{S_HIDDEN_FIELDS}
	<input type="submit" name="submit" value="{L_SUBMIT}" />
</form>
