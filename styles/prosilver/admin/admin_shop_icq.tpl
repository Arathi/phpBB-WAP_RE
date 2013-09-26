<div class="navbar"><a href="{U_INDEX}">{L_INDEX}</a>&gt;<a href="{U_ADMIN}">{L_ADMIN}</a>&gt;<a href="{U_ADMIN_INDEX}">{L_ADMIN_INDEX}</a>&gt;销售ICQ</div>
<span class="genmed">在这里您可以添加或删除在商店出售的ICQ号码！</span>
<div class="catSides">待出售的ICQ号码！</div>
<!-- BEGIN memberrow -->
	<div class="{memberrow.ROW_CLASS}">
		{memberrow.NUMBER}、帐号：{memberrow.UIN}<br />
		密码：{memberrow.PASS}<br />
		需要积分：{memberrow.COST}<br />
		<a href="{memberrow.U_DEL}">删除</a>
	</div>
<!-- END memberrow -->
<!-- BEGIN no_pay -->
	<div class="row1">没有ICQ号码！</div>
<!-- END no_pay -->
{PAGINATION}
<div class="catSides">添加一个新的ICQ帐号！</div>
<form method="post" action="{S_ACTION}">
	<div class="row1">
		帐号：<br />
		<input type="text" name="uin"/>
	</div>
	<div class="row1">
		密码：<br/>
		<input type="text" name="pass"/>
	</div>
	<div class="row1">
		需要多少积分:<br />
		<input type="text" name="cost"/>
	</div>
	<input type="submit" name="add" value=" 提交 " />
</form>