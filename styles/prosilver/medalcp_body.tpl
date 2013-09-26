<div class="navbar"><a href="{U_INDEX}">{L_INDEX}</a>&gt;<a href="{U_MEDALS}">{L_MEDALS}</a>&gt;管理奖项</div>
<form method="post" name="post" action="{S_MEDALCP_ACTION}">
<div class="catSides">{L_MEDAL_INFORMATION}</div>
<div class="row1">{L_MEDAL_NAME}: <b>{MEDAL_NAME}</b></div>
<div class="row1">{L_MEDAL_DESC}: {MEDAL_DESC}</div>
<div class="row1">
{L_MEDAL_IMAGE}:<br />
{MEDAL_IMAGE_DISPLAY}
</div>
<div class="row1">
{L_MEDAL_MODERATOR}: <br />
{MEDAL_MODERATOR}
</div>
<div class="row1">
{L_MEDAL_MEMBERS}:<br />
<span class="genmed">{L_MEDAL_MEMBERS_EXPLAIN}</span><br/>
{MEDAL_MEMBER}
</div>
<div class="catSides">{L_MEDAL_USER}</div>
<div class="row1">
{L_USERNAME}:<br />
<input type="text" name="username" maxlength="50" />
<input type="hidden" name="mode" value="edit" />{S_HIDDEN_FIELDS}
</div>
<div class="row1">
{L_MEDAL_REASON}:<br/>
<span class="genmed">{L_MEDAL_REASON_EXPLAIN}</span><br/>
<textarea name="issue_reason" rows="5" cols="15" style="width: 235px;"></textarea>
</div>
<div class="catSides">
{L_UNMEDAL_USER}</div>
<div class="row1">
{S_UNMEDAL_USERLIST_SELECT}
</div>
<input type="submit" name="submit" value="{L_SUBMIT}" class="subbutton" />
{S_HIDDEN_FIELDS}
</form>
