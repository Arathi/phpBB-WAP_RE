<div class="navbar"><a href="{U_INDEX}">{L_INDEX}</a>&gt;<a href="{U_ADMIN}">{L_ADMIN}</a>&gt;<a href="{U_ADMIN_INDEX}">{L_ADMIN_INDEX}</a>&gt;<a href="{U_MEDAL_LISTS}">奖列表</a>&gt;{L_CATEGORY_DELETE}</div>
<span class="genmed">{L_CATEGORY_DELETE_EXPLAIN}</span>
<form action="{S_MEDAL_ACTION}" method="post">
	<div class="catSides">{L_CATEGORY_DELETE}</div>
	<div class="row1">{L_CATEGORY_NAME}: <b>{NAME}</b></div>
	<div class="row1">
		{L_MOVE_MEDALS}:<br/>
		{S_SELECT_TO}
	</div>
	{S_HIDDEN_FIELDS}
	<input type="submit" name="submit" value="{S_SUBMIT_VALUE}" />
</form>