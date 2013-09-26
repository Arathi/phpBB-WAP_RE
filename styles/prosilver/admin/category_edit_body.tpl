<div class="navbar"><a href="{U_INDEX}">{L_INDEX}</a>&gt;<a href="{U_ADMIN}">{L_ADMIN}</a>&gt;<a href="{U_ADMIN_INDEX}">{L_ADMIN_INDEX}</a>&gt;<a href="{U_ADMIN_FORUMS}">论坛列表</a>&gt;{L_EDIT_CATEGORY}</div>
<span class="genmed">{L_EDIT_CATEGORY_EXPLAIN}</span>
<form action="{S_FORUM_ACTION}" method="post">
<div class="catSides">
{L_EDIT_CATEGORY}
</div>
<div class="row1">
{L_CATEGORY}:<br/>
<input type="text" name="cat_title" value="{CAT_TITLE}" />
</div>
<div class="row1">
{L_ICON}:<br />
<input type="text" size="25" name="cat_icon" value="{CAT_ICON}" class="post" />
</div>
{S_HIDDEN_FIELDS}
<input type="submit" name="submit" value="{S_SUBMIT_VALUE}" />
</form>