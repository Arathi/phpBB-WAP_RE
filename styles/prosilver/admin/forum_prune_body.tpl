<div class="navbar"><a href="{U_INDEX}">{L_INDEX}</a>&gt;<a href="{U_ADMIN}">{L_ADMIN}</a>&gt;<a href="{U_ADMIN_INDEX}">{L_ADMIN_INDEX}</a>&gt;<a href="{U_SELECT_FORUM}">选择论坛</a>&gt;{L_FORUM_PRUNE}</div>
<span class="genmed">{L_FORUM_PRUNE_EXPLAIN}</span>
<form method="post" action="{S_FORUMPRUNE_ACTION}">
<div class="catSides">{FORUM_NAME}</div>
<div class="row1">{S_PRUNE_DATA}</div>
{S_HIDDEN_VARS}
<input type="submit" name="doprune" value="{L_DO_PRUNE}" />
</form>