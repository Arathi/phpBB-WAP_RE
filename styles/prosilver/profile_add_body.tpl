<div class="navbar"><a href="{U_INDEX}">{L_INDEX}</a>&gt;{L_PROFILE}</div>
{ERROR_BOX}
<form action="{S_PROFILE_ACTION}" method="post">
<div class="catSides">{L_PROFILE_INFO}</div>
<!-- BEGIN switch_namechange_disallowed -->
<div class="row1">
{L_USERNAME}: <input type="hidden" name="{VAR_USERNAME}" value="{USERNAME}" /> <b>{USERNAME}</b>
</div>
<!-- END switch_namechange_disallowed -->
<!-- BEGIN switch_namechange_allowed -->
<div class="row1">
{L_USERNAME}:*<br/>
<input type="text" name="{VAR_USERNAME}" value="{USERNAME}" />
</div>
<!-- END switch_namechange_allowed -->
<div class="row1">
{L_EMAIL_ADDRESS}:{L_WARNING}<br/>
<input type="text" name="{VAR_EMAIL}" value="{EMAIL}" />
</div>
<!-- Start add - Gender MOD -->
<div class="row1">
{L_GENDER}:<br/>
<input type="radio" name="gender" value="1" {GENDER_MALE_CHECKED}/> {L_GENDER_MALE}
<input type="radio" name="gender" value="2" {GENDER_FEMALE_CHECKED}/> {L_GENDER_FEMALE}
</div>
<!-- End add - Gender MOD -->
<!-- BEGIN switch_edit_profile -->
<div class="row1">
{L_CURRENT_PASSWORD}:{L_WARNING}<br/>
<input type="password" name="{VAR_CUR_PASSWORD}" value="{CUR_PASSWORD}" />
</div>
<!-- END switch_edit_profile -->
<div class="row1">
{L_NEW_PASSWORD}:{L_WARNING}<br/>
<input type="password" name="{VAR_NEW_PASSWORD}" value="{NEW_PASSWORD}" />
</div>
<div class="row1">
{L_CONFIRM_PASSWORD}:{L_WARNING}<br/>
<input type="password" name="{VAR_PASSWORD_CONFIRM}" value="{PASSWORD_CONFIRM}" />
</div>
<!-- Visual Confirmation -->
<!-- BEGIN switch_confirm -->
<div class="row1">
{CONFIRM_IMG}<br />
请输入上图中显示的数字:{L_WARNING}<br/>
<input type="text" name="{VAR_CONFIRM_CODE}" value="" />
</div>
<!-- END switch_confirm -->
{S_HIDDEN_FIELDS}<input class="subbutton" type="submit" name="submit" value="{L_SUBMIT}" />
</form>
<br/>{L_WARNING_TEXT}