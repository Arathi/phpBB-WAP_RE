<div class="navbar"><a href="{U_INDEX}">{L_INDEX}</a>|{L_PROFILE}<br/>
{ERROR_BOX}</div>
<form action="{S_PROFILE_ACTION}" method="post">
<div class="catSides">修改简历</div>
<div class="row1">
{L_YAHOO}:<br/>
<input type="text" name="yim" maxlength="255" value="{YIM}" />
</div>
<div class="row1">
{L_ICQ_NUMBER}:<br/>
<input type="text" name="icq" value="{ICQ}" />
</div>
<div class="row1">
{L_NUMBER}:<br/>
<input type="text" name="number" value="{NUMBER}" />
</div>
<div class="row1">
{L_WEBSITE}:<br/>
<input type="text" name="website" value="{WEBSITE}" />
</div>
<div class="row1">
{L_LOCATION}:<br/>
<input type="text" name="location" value="{LOCATION}" />
</div>
<div class="row1">
{L_OCCUPATION}:<br/>
<input type="text" name="occupation" value="{OCCUPATION}" />
</div>
<div class="row1">
{L_INTERESTS}:<br/>
<input type="text" name="interests" value="{INTERESTS}" />
</div>
<div class="row1">
{L_AIM}:<br/>
<input type="text" name="aim" maxlength="255" value="{AIM}" />
</div>
<div class="row1">
{L_MESSENGER}:<br/>
<input type="text" name="msn" maxlength="255" value="{MSN}" />
</div>
<div class="row1">
个性签名:<br/>
<input type="text" name="signature" maxlength="255" value="{SIGNATURE}" />
</div>
<!-- BEGIN pay_money -->
<div class="row1">
Номер рублёвого кошелька:<br/>
<input type="text" name="user_purse" maxlength="50" value="{PURSE}" />
</div>
<!-- END pay_money -->
<!-- Start add - Birthday MOD -->
<div class="row1">
{L_BIRTHDAY}{BIRTHDAY_REQUIRED}:<br/>
{S_BIRTHDAY}
</div>
<!-- End add - Birthday MOD -->
<!-- Start add - Gender MOD -->
<div class="row1">
{L_GENDER}:<br/>
<input type="radio" name="gender" value="1" {GENDER_MALE_CHECKED}/>{L_GENDER_MALE} <input type="radio" name="gender" value="2" {GENDER_FEMALE_CHECKED}/>{L_GENDER_FEMALE}
</div>
<!-- End add - Gender MOD -->
{S_HIDDEN_FIELDS}<input class="subbutton" type="submit" name="submit" value="{L_SUBMIT}" />
</form>