<div class="navbar"><a href="{U_INDEX}">{L_INDEX}</a>&gt;<a href="{U_ADMIN}">{L_ADMIN}</a>&gt;<a href="{U_ADMIN_INDEX}">{L_ADMIN_INDEX}</a>&gt;<a href="{U_ADMIN_GROUPS}">选择小组</a>&gt;{L_GROUP_TITLE}</div>
<div class="catSides">{L_GROUP_EDIT_DELETE}</div>
<form action="{S_GROUP_ACTION}" method="post" name="post">
	<div class="row1">
		{L_GROUP_NAME}:<br/>
		<input type="text" name="group_name" maxlength="40" value="{GROUP_NAME}" />
	</div>
	<div class="row1">
		{L_GROUP_DESCRIPTION}:<br/>
		<textarea name="group_description" rows="5" style="width:99%;">{GROUP_DESCRIPTION}</textarea>
	</div>
	<div class="row1">
		团队LOGO:<br/>
		<input type="text" name="group_logo" maxlength="255" value="{GROUP_LOGO}" />
	</div>
	<div class="row1">
		{L_GROUP_MODERATOR}:<br/>
		<input type="text" name="username" maxlength="50" value="{GROUP_MODERATOR}" />
	</div>
	<div class="row1">
		{L_GROUP_STATUS}:<br/>
		<input type="radio" name="group_type" value="{S_GROUP_OPEN_TYPE}" {S_GROUP_OPEN_CHECKED} /> {L_GROUP_OPEN}<br/>
		<input type="radio" name="group_type" value="{S_GROUP_CLOSED_TYPE}" {S_GROUP_CLOSED_CHECKED} /> {L_GROUP_CLOSED}<br/>
		<input type="radio" name="group_type" value="{S_GROUP_HIDDEN_TYPE}" {S_GROUP_HIDDEN_CHECKED} /> {L_GROUP_HIDDEN}
	</div>
	<!-- BEGIN group_edit -->
		<div class="row1">
			{L_DELETE_MODERATOR}<br/>
				<span class="genmed">{L_DELETE_MODERATOR_EXPLAIN}</span><br/>
				<input type="checkbox" name="delete_old_moderator" value="1" /> {L_YES}
		</div>
	<div class="row1"><input type="checkbox" name="group_guestbook" value="1" {S_GROUP_GB_ENABLE} /> {L_GROUP_GB_ENABLE}</div>
	<div class="row1"><input type="checkbox" name="group_delete" value="1" /> {L_GROUP_DELETE_CHECK}</div>
	<!-- END group_edit -->
	{S_HIDDEN_FIELDS}
	<input type="submit" name="group_update" value="{L_SUBMIT}" />
</form>