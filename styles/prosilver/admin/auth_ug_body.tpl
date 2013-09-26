<div class="navbar"><a href="{U_INDEX}">{L_INDEX}</a>&gt;<a href="{U_ADMIN}">{L_ADMIN}</a>&gt;<a href="{U_ADMIN_INDEX}">{L_ADMIN_INDEX}</a>&gt;<a href="{U_UG_SELECT}">{L_AUTH_TITLE}</a>&gt;{L_PERMISSIONS}</div>
<span class="genmed">{L_AUTH_EXPLAIN}</span>
<div class="catSides">{USERNAME}</div>
<form method="post" action="{S_AUTH_ACTION}">
	<!-- BEGIN switch_user_auth -->
		<div class="row1">
			{USER_LEVEL}<br />
			{USER_GROUP_MEMBERSHIPS}
		</div>
	<!-- END switch_user_auth -->
	
	<!-- BEGIN switch_group_auth -->
		<div class="row1">{GROUP_MEMBERSHIP}</div>
	<!-- END switch_group_auth -->
	
	<!-- BEGIN forums -->
		<div class="catSides">{forums.FORUM_NAME}</div>
		<!-- BEGIN aclvalues -->
			<div class="row1">{forums.aclvalues.L_UG_ACL_TYPE}:{forums.aclvalues.S_ACL_SELECT}</div>
		<!-- END aclvalues -->
		
		<div class="row1">{L_MODERATOR_STATUS}:{forums.S_MOD_SELECT}</div>

	<!-- END forums -->
	
	<div class="row1">{U_SWITCH_MODE}</div>
	{S_HIDDEN_FIELDS}
	<input type="submit" name="submit" value="{L_SUBMIT}" />
</form>