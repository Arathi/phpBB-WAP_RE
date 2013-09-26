<div class="navbar"><a href="{U_INDEX}">{L_INDEX}</a>&gt;{L_BANK_TITLE}</div>
<div class="catSides">{L_BANK_ACCOUNT_TITLE}</div>
<!-- BEGIN has_account -->
	<div class="row1">{L_ACTIONS}</div>
	<form method="post" action="{U_DEPOSIT}">
		<div class="row1">
			您最多可以存入 {USER_GOLD} ，请问需要存入多少？<br />
			<input type="text" class="post" name="deposit" value=""> 
			<input type="submit" class="liteoption" name="Deposit" value="{L_DEPOSIT}">
		</div>
	</form>
	<form method="post" action="{U_WITHDRAW}">
		<div class="row1">
			您最多可以支出 {USER_WITHDRAW} ，请问需要支出多少？<br />
			<input type="text" class="post" name="withdraw" value="">
			<input type="submit" class="liteoption" name="Withdraw" value="{L_WITHDRAW}">
		</div>
	</form>
<!-- END has_account -->
<!-- BEGIN no_account -->
	<div class="row1">{no_account.L_NO_ACCOUNT}!</div>
	<div class="row1">{no_account.U_OPEN_ACCOUNT}</div>
<!-- END no_account -->
	<div class="catSides">
		<span class="cattitle">{L_BANK_INFO}</span>
	</div>
<!-- BEGIN has_account -->
	<div class="row1">{L_USER_BALANCE}: {USER_BALANCE} {L_POINTS}</div>
<!-- END has_account -->
	<div class="row1">{L_INTEREST_RATE}: {BANK_INTEREST}%</div>
<!-- BEGIN switch_withdraw_fees -->
	<div class="row1">{L_WITHDRAW_RATE}: {BANK_FEES}%</div>
<!-- END switch_withdraw_fees -->
<!-- BEGIN switch_min_depo -->
	<div class="row1">{L_MIN_DEPO}: {BANK_MIN_DEPO} {L_POINTS}</div>
<!-- END switch_min_depo -->
<!-- BEGIN switch_min_with -->
	<div class="row1">{L_MIN_WITH}: {BANK_MIN_WITH} {L_POINTS}</div>
<!-- END switch_min_with -->
	<div class="row1">{L_TOTAL_ACCS}: {BANK_ACCOUNTS}</div>
	<div class="row1">{L_HOLDING}: {BANK_HOLDINGS} {L_POINTS}</div>
	<div class="row1">{L_OPEN_SINCE}: {BANK_OPENED}</div>