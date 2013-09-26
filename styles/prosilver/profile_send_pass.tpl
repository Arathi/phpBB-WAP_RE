<div class="navbar"><a href="{U_INDEX}">{L_INDEX}</a>|Профиль</div>
<form action="{S_PROFILE_ACTION}" method="post">
<div class="catSides">
Забыли пароль?
</div>
<div class="row1">
{L_USERNAME}:*<br/>
<input type="text" name="username" maxlength="40" value="{USERNAME}" /><br/>
</div>
<div class="row1">
{L_EMAIL_ADDRESS}:*<br/>
<input type="text" name="email" maxlength="255" value="{EMAIL}" /><br/>
</div>
{S_HIDDEN_FIELDS} 
<input class="subbutton" type="submit" name="submit" value="{L_SUBMIT}"/>
</form>