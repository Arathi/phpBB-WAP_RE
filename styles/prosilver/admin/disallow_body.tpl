<div class="navbar"><a href="{U_INDEX}">{L_INDEX}</a>&gt;<a href="{U_ADMIN}">{L_ADMIN}</a>&gt;<a href="{U_ADMIN_INDEX}">{L_ADMIN_INDEX}</a>&gt;{L_DISALLOW_TITLE}</div>
<span class="genmed">{L_DISALLOW_EXPLAIN}</span>
<form method="post" action="{S_FORM_ACTION}">
	<div class="catSides">{L_ADD_DISALLOW}</div>
	<div class="row1">
		<span class="genmed">{L_ADD_EXPLAIN}</span><br />
		<input type="text" name="disallowed_user" size="30" />
		<input type="submit" name="add_name" value="{L_ADD}" />
	</div>
	<div class="catSides">{L_DELETE_DISALLOW}</div>
	<div class="row1">
		<span class="genmed">{L_DELETE_EXPLAIN}</span><br />
		{S_DISALLOW_SELECT}
		<input type="submit" name="delete_name" value="{L_DELETE}" />
	</div>
</form>