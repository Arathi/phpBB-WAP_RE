<div class="navbar"><a href="{U_INDEX}">{L_INDEX}</a>&gt;<a href="{U_MODCP}">{L_MODCP}</a>&gt;{L_EMAIL_TITLE}</div>
{ERROR_BOX}
<span class="genmed">{L_EMAIL_EXPLAIN}</span>
<form method="post" action="{S_USER_ACTION}">
<div class="catSides">
	<span class="cattitle">{L_COMPOSE}</span>
</div>
<div class="row1">
	{L_RECIPIENTS}:<br />
	{S_GROUP_SELECT}
</div>
<div class="row1">
	{L_EMAIL_SUBJECT}:<br />
	<input type="text" name="subject" size="45" value="{SUBJECT}" />
</div>
<div class="row1">
	{L_EMAIL_MSG}:<br/>
	<textarea name="message" rows="5" wrap="virtual" style="width:99%;">{MESSAGE}</textarea>
</div>
<input type="submit" value="{L_EMAIL}" name="submit" />
</form>