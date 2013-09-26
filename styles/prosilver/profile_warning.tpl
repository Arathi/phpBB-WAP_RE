<div class="navbar"><a href="{U_INDEX}">{L_INDEX}</a>&gt;<a href="{U_WARNING_BACK_PROFILE}">{L_PROFILE}</a>&gt;警告黑名单</div>
<form action="{S_PROFILE_ACTION}" name="modify_reputation" method="post">
<div class="catSides">{L_MODIFY_REPUTATION}、警告</div>
<div class="row1">
<!-- BEGIN postrow -->
举报者：<b>{postrow.USER_NAME}</b>
"{postrow.POSTER_MESSAGE}"<br/>
<!-- END postrow -->
执行警告或加黑名单的原因:<br />
<textarea name="message" rows="5" wrap="virtual" style="width:99%">{MESSAGE}</textarea>
</div>
<div class="row1">
{L_EXPIRE}:<br />
<!-- BEGIN switch_expire_fixed -->
{L_EXPIRE_MESSAGE}
<!-- END switch_expire_fixed -->
<!-- BEGIN switch_expire_limited -->
{L_EXPIRE_DAYS_0}
<input type="input" name="expire_days" size="3" maxlength="3" value="{S_EXPIRE_DAYS}" />
<select name="ban_unit">
<option selected="selected" value="60">分钟</option>
<option value="3600">小时</option>
<option value="86400">天</option>
</select>
<!-- END switch_expire_limited -->
<!-- BEGIN switch_expire_limited_bottom -->
<input type="radio" name="expire_never" id="expire_never_1" value="1" />
{L_EXPIRE_NEVER}<br />
<input type="radio" name="expire_never" id="expire_never_0" value="0" checked="checked" />
{L_EXPIRE_DAYS_0}
<input type="input" name="expire_days" size="3" maxlength="3" value="{S_EXPIRE_DAYS}" />
<select name="ban_unit">
<option selected="selected" value="60">分钟</option>
<option value="3600">小时</option>
<option value="86400">天</option>
</select>
<!-- END switch_expire_limited_bottom -->
<br />违反了哪条规则：<br />
{RULES_LIST}
</div>
<!-- BEGIN allow_pm -->
<div class="row1"><input type="checkbox" name="allow_pm" /> 禁止信息功能</div>
<!-- END allow_pm -->
{S_HIDDEN_FORM_FIELDS}
<input class="subbutton" type="submit" name="submit" value="{L_SUBMIT}" />
</form>