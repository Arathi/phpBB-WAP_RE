<div class="navbar"><a href="{U_INDEX}">{L_INDEX}</a>&gt;{MESSAGE_TITLE}</div>
<form action="{S_MODCP_ACTION}" method="post">
<div class="catSides">{MESSAGE_TITLE}</div>
<div class="row1">
{L_MOVE_TO_FORUM}<br/>
{S_FORUM_SELECT}<br/>
<input type="checkbox" name="move_leave_shadow" />{L_LEAVESHADOW}<br/>
{MESSAGE_TEXT}<br/>
{S_HIDDEN_FIELDS}
<input class="subbutton" type="submit" name="confirm" value="{L_YES}" />
<input class="subbutton" type="submit" name="cancel" value="{L_NO}" />
</div>
</form>