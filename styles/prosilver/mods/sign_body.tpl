<div class="catSides"><a href="{U_INDEX}">{L_INDEX}</a>&gt;签到</div>
<div class="nav">小提示：每24小时可以签到一次，每次签到可以得到 <b>10</b> 红豆，<b>{SIGN_USERNAME}</b> 记得每天来签到哦！</div>

<!-- BEGIN switch_no_sign -->
	<form action="{S_PROFILE_ACTION}" method="post">
		<textarea name="talk" rows="2" style="width:98%"></textarea><br />
		{S_HIDDEN_FORM_FIELDS}
		{SMILES_SELECT}
		<input type="submit" name="post" value="签到" />
	</form>
<!-- END switch_no_sign -->



<!-- BEGIN switch_yes_sign -->
	<div class="row_hard"><b>您已经签到了，请过24小时再来吧！</b></div>
<!-- END switch_yes_sign -->
<!-- BEGIN sign_rows -->
	<div class="{sign_rows.ROW_CLASS}">
		{sign_rows.NUMBER}、<a href="{sign_rows.U_SIGN_VIEWPROFILE}">{sign_rows.SIGN_USERNAME}</a>签到说：{sign_rows.SIGN_TALK}[签到时间：{sign_rows.SIGN_TIME}]
	</div>
<!-- END sign_rows -->
{PAGINATION}



