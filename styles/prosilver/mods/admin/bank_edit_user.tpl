<div class="navbar"><a href="{U_INDEX}">{L_INDEX}</a>&gt;<a href="{U_ADMIN}">{L_ADMIN}</a>&gt;<a href="{U_MODS_ADMIN}">{L_MODS_ADMIN}</a>&gt;<a href="{U_ADMIN_BANK}">虚拟银行</a>&gt;修改</div>
<span class="genmed">{BANKEXPLAIN}</span>
<form action="{S_CONFIG_ACTION}" method="post">
	<div class="catSides">
		<span class="cattitle">修改</span>
	</div>
	<div class="row1">
		{L_BALANCE}:<br />
		<input type="text" class="post" name="holding" size="10" maxlength="10" value="{USER_HOLDING}" />
	</div>
	<div class="row1">
		{L_DEPOSITED}:<br />
		<input type="text" class="post" name="withdrawn" size="10" maxlength="10" value="{USER_WITHDRAWN}" />
	</div>
	<div class="row1">
		{L_WITHDRAWN}:<br />
		<input type="text" class="post" name="deposited" size="10" maxlength="10" value="{USER_DEPOSITED}" />
	</div>
	<div class="row1">
		{L_FEES}:<br />
		<select name="fees">
			<option value="on" {SELECT_FEES_ON}>需要</option>
			<option value="off" {SELECT_FEES_OFF}>无需</option>
		</select>
	</div>
	<input type="hidden" name="action" value="update_account" />
	<input type="hidden" name="user_id" value="{USER_ID}" />
	<input class="liteoption" type="submit" value="{L_UPDATE}" />
</form>