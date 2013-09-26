<div class="navbar"><a href="{U_INDEX}">{L_INDEX}</a>&gt;<a href="{U_MODCP}">{L_MODCP}</a>&gt;{L_DISALLOW_TITLE}</div>
<span class="genmed">{L_DISALLOW_EXPLAIN}</span>
<form method="post" action="{S_FORM_ACTION}">
	<div class="catSides">
		<span class="cattitle">{L_ADD_DISALLOW}</span>
	</div>
	<div class="row1">
		{L_USERNAME}:<br/>
		<span class="genmed">{L_ADD_EXPLAIN}</span><br/>
		<input class="post" type="text" name="disallowed_user" size="30" />
		<input type="submit" name="add_name" value="{L_ADD}" class="mainoption" />
	</div>
	<div class="catSides">
		<span class="cattitle">{L_DELETE_DISALLOW}</span>
	</div>
	<div class="row1">
		{L_USERNAME}:<br/>
		<span class="genmed">{L_DELETE_EXPLAIN}</span><br/>
		{S_DISALLOW_SELECT}<br/>
		<input type="submit" name="delete_name" value="{L_DELETE}"/>
	</div>
</form>