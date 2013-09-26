<div class="navbar"><a href="{U_INDEX}">{L_INDEX}</a>&gt;<a href="{U_ADMIN}">{L_ADMIN}</a>&gt;<a href="{U_ADMIN_INDEX}">{L_ADMIN_INDEX}</a>&gt;{L_GROUP_TITLE}</div>
<span class="genmed">{L_GROUP_EXPLAIN}</span>
<div class="catSides">{L_GROUP_SELECT}</div>
<form method="post" action="{S_GROUP_ACTION}">
	<!-- BEGIN select_box -->
	<div class="row1">
		{S_GROUP_SELECT}<input type="submit" name="edit" value="{L_LOOK_UP}" />
	</div>
	<!-- END select_box -->
	
	{S_HIDDEN_FIELDS}
	<input type="submit" name="new" value="{L_CREATE_NEW_GROUP}" />
</form>