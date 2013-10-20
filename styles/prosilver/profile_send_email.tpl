<div class="navbar"><a href="{U_INDEX}">{L_INDEX}</a>|E-Mail<br/>
{ERROR_BOX}</div>
<form action="{S_POST_ACTION}" method="post" name="post">
<div class="catSides">
发送E-Mail
</div>
<div class="row1">
{L_SUBJECT}:<br/>
<input type="text" name="subject" maxlength="100" value="{SUBJECT}" />
</div>
<div class="row1">
{L_MESSAGE_BODY}:<br/>
<textarea name="message" rows="5" cols="20">{MESSAGE}</textarea>
</div>
{S_HIDDEN_FIELDS}
<input class="subbutton" type="submit" name="submit" value="{L_SEND_EMAIL}" />
</form>