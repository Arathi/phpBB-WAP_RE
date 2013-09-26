<div class="navbar"><a href="{U_INDEX}">{L_INDEX}</a>&gt;<a href="{U_ADMIN}">{L_ADMIN}</a>&gt;<a href="{U_ADMIN_INDEX}">{L_ADMIN_INDEX}</a>&gt;点击赚取管理</div>
<span class="genmed">在这里您可以添加、删除点击赚取中的链接！</span>
<div class="catSides">链接</div>
<!-- BEGIN memberrow -->
	<div class="{memberrow.ROW_CLASS}">
		{memberrow.NUMBER}、{memberrow.NAZVANIE}（<a href="{memberrow.U_DEL}">删除</a>）<br/>
		URL：{memberrow.URL}<br />
		点击可获得 {memberrow.COST} 积分！
	</div>
<!-- END memberrow -->
<!-- BEGIN no_pay -->
	<div class="row1">没有添加任何链接！</div>
<!-- END no_pay -->
{PAGINATION}
<div class="catSides">添加一个新的链接</div>
<form method="post" action="{S_ACTION}">
	<div class="row1">
		URL：<br/>
		<input type="text" name="url1" value="http://" />
	</div>
	<div class="row1">
		链接名称：<br/>
		<input type="text" name="nazvanie"/>
	</div>
	<div class="row1">
		用户点击链接可获得多少积分：<br/>
		<input type="text" name="cost"/>
	</div>
	<input type="submit" name="add" value=" 提交 " />
</form>