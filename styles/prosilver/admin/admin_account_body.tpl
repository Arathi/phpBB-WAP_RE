<script language="Javascript" type="text/javascript">
function select_switch(status)
{
	for( i = 0; i < document.users.length; i++ )
	{
		document.users.elements[i].checked = status;
	}
}
</script>
<div class="navbar"><a href="{U_INDEX}">{L_INDEX}</a>&gt;<a href="{U_ADMIN}">{L_ADMIN}</a>&gt;<a href="{U_ADMIN_INDEX}">{L_ADMIN_INDEX}</a>&gt;激活、停用帐号</div>
<div class="catSides">{L_ACCOUNT_ACTIONS_EXPLAIN}</div>
<!-- BEGIN switch_message -->
	<font color="green">{MESSAGE}</font>
<!-- END switch_message -->
<form method="post" name="users" action="{S_ACCOUNT_ACTION}">
	<div class="row1">{L_ACTIVATION}</div>
	<!-- BEGIN admin_account -->
		<div class="row1">
			<input type="checkbox" name="mark[]2" value="{admin_account.S_MARK_ID}" /> 
			<b><a href="{admin_account.U_PROFILE}">{admin_account.USERNAME}</a></b><br />
			{L_EMAIL}：{admin_account.EMAIL}<br/>
			{L_JOINED}：<b>{admin_account.PERIOD}</b><br />
			注册日期：<b>{admin_account.JOINED}</b><br />
			<a href="{admin_account.U_EDIT_USER}">{L_EDIT_USER}</a>|<a href="{admin_account.U_USER_AUTH}">{L_USER_AUTH}</a>
		</div>
	<!-- END admin_account -->
	<!-- BEGIN switch_no_users -->
		<div class="row1">还没有注册用户！</div>
	<!-- END switch_no_users -->
	{S_HIDDEN_FIELDS}
	<div class="row1">
		选择：<a href="javascript:select_switch(true);">{L_MARK_ALL}</a>
		- <a href="javascript:select_switch(false);">{L_UNMARK_ALL}</a>
	</div>
	<input type="submit" name="activate" value="{L_DE_ACTIVATE_MARKED}" />
	{PAGINATION}
	<div class="row1">
		显示方式：
		<select name="days">
			{S_SELECT_DAYS}
		</select>
		<input type="submit" value="{L_GO}" name="submit_wait" />
	</div>
</form>