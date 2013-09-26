<div class="navbar"><a href="{U_INDEX}">{L_INDEX}</a>&gt;<a href="{U_ADMIN}">{L_ADMIN}</a>&gt;<a href="{U_ADMIN_INDEX}">{L_ADMIN_INDEX}</a>&gt;<a href="{U_ADMIN_FORUMS}">论坛列表</a>&gt;{L_FORUM_DELETE}</div>
<span class="genmed">{L_FORUM_DELETE_EXPLAIN}</span>
<form action="{S_FORUM_ACTION}" method="post">
<div class="catSides">
{L_FORUM_DELETE}
</div>
<div class="row1">
{L_FORUM_NAME}: <b>{NAME}</b>
</div>
<div class="row1">
{L_MOVE_CONTENTS}:<br/>
{S_SELECT_TO}
</div>
{S_HIDDEN_FIELDS}
<input type="submit" name="submit" value="{S_SUBMIT_VALUE}" />
</form>