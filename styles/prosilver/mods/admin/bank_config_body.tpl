<div class="navbar"><a href="{U_INDEX}">{L_INDEX}</a>&gt;<a href="{U_ADMIN}">{L_ADMIN}</a>&gt;<a href="{U_MODS_ADMIN}">{L_MODS_ADMIN}</a>&gt;{L_TABLE_TITLE}</div>
<span class="genmed">{BANKEXPLAIN}</span>
<div class="catSides">
	<span class="cattitle">{L_EDIT_ACCOUNTS}</span>
</div>
<form method="post" action="{S_CONFIG_ACTION}" name="post">
	<div class="row1">
		<input type="text" class="post" class="post" name="username" maxlength="25" size="25">
		<input type="hidden" name="action" value="edit_account" />
		<input type="submit" value="{L_EDIT_ACCOUNT}" class="liteoption" />
	</div>
</form>
<div class="catSides">
	<span class="cattitle">{L_TABLE_TITLE}</span>
</div>
<form action="{S_CONFIG_ACTION}" method="post">
	<div class="row1">
		{L_BANK_NAME}:<br />
		<input type="text" class="post" name="name" size="32" value="{BANK_NAME}" maxlength="32" />
	</div>
	<div class="row1">
		{L_INTEREST_RATE}:<br />
		<input type="text" class="post" name="interestrate" size="5" value="{BANK_INTEREST}" maxlength="3" />%
	</div>
	<div class="row1">
		{L_DISABLE_INTEREST}:<br />
		<input type="text" class="post" name="disableinterest" size="20" maxlength="14" value="{BANK_DISABLE_INTEREST}" /> {L_POINTS} {L_ZERO_FOR_NONE}
	</div>
	<div class="row1">
		{L_WITHDRAW_RATE}:<br />
		<input type="text" class="post" name="withdrawfee" size="5" value="{BANK_FEES}" maxlength="3" />%
	</div>
	<div class="row1">
		{L_MIN_DEPO}:<br />
		<input type="text" class="post" name="min_depo" size="5" value="{BANK_MIN_DEPO}" maxlength="10" /> {L_POINTS}
	</div>
	<div class="row1">
		{L_MIN_WITH}:<br />
		<input type="text" class="post" name="min_with" size="5" value="{BANK_MIN_WITH}" maxlength="10" /> {L_POINTS}
	</div>
	<div class="row1">
		{L_PAY_TIME}:<br />
		<input type="text" class="post" name="paymenttime" value="{BANK_PAY_TIME}" size="20" maxlength="14" /> {L_SECONDS}
	</div>
	<div class="row1">
		{L_STATUS}:<br />
		<select name="status">
			<option value="on" {SELECT_STATUS_ON}>开启</option>
			<option value="off" {SELECT_STATUS_OFF}>关闭</option>
		</select>
	</div>
	<div class="row1">
		{L_HOLDING}:<br />
		{BANK_HOLDING}
	</div>
	<div class="row1">
		{L_WITHDRAWN}:<br />
		{BANK_WITHDRAWS}
	</div>
	<div class="row1">
		{L_DEPOSITED}:<br />
		{BANK_DEPOSITS}
	</div>
	<div class="row1">
		{L_ACCOUNTS}:<br />
		{BANK_ACCOUNTS}
	</div>
	<input type="hidden" name="action" value="update_config" />
	<input type="submit" class="liteoption" value="{L_UPDATE}" />
</form>