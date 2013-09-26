<div class="navbar"><a href="{U_INDEX}">{L_INDEX}</a>&gt;<a href="{U_ADMIN}">{L_ADMIN}</a>&gt;<a href="{U_ADMIN_INDEX}">{L_ADMIN_INDEX}</a>&gt;{L_COMPOSE}</div>
<span class="genmed">{L_EMAIL_EXPLAIN}</span><br/>
{ERROR_BOX}
<form method="post" action="{S_USER_ACTION}">
<div class="catSides">
{L_COMPOSE}
</div>
<div class="row1">{L_RECIPIENTS}:<br/>
{S_GROUP_SELECT}
</div>
<div class="row1">{L_EMAIL_SUBJECT}:<br/>
<input type="text" name="subject" value="{SUBJECT}" style="width: 235px;"/>
</div>
<div class="row1">{L_EMAIL_MSG}:<br/>
<textarea name="message" rows="5" cols="15" style="width: 235px;">{MESSAGE}</textarea>
</div>
<input type="submit" value="Разослать" name="submit" />
</form>