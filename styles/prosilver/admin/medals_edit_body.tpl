<div class="navbar"><a href="{U_INDEX}">{L_INDEX}</a>&gt;<a href="{U_ADMIN}">{L_ADMIN}</a>&gt;<a href="{U_ADMIN_INDEX}">{L_ADMIN_INDEX}</a>&gt;<a href="{U_MEDAL_LISTS}">{L_MEDAL_TITLE}</a>&gt;{L_NEW_MEDAL}</div>
<span class="genmed">{L_MEDAL_EXPLAIN}</span>
<form action="{S_MEDAL_ACTION}" method="post">
	<div class="catSides">{L_NEW_MEDAL}</div>
	<div class="row1">
		{L_MEDAL_NAME}:<br/>
		<input type="text" name="medal_name" maxlength="40" value="{MEDAL_NAME}" />
	</div>
	<div class="row1">
		{L_MEDAL_DESCRIPTION}:<br/>
		<input type="text" name="medal_description" maxlength="255" value="{MEDAL_DESCRIPTION}" />
	</div>
	<div class="row1">
		{L_CATEGORY}:<br/>
		<select name="mc">{S_CAT_LIST}</select>
	</div>
	<div class="row1">
		{L_MEDAL_IMAGE}:<br/>
		<span class="genmed">{L_MEDAL_IMAGE_EXPLAIN}</span><br/>
		<input type="text" name="medal_image" maxlength="255" value="{IMAGE}" />{IMAGE_DISPLAY}
	</div>
	{S_HIDDEN_FIELDS}	
	<input type="submit" name="submit" value="{L_SUBMIT}" />
</form>