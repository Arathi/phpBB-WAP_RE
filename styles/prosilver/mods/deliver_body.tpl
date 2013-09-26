<div class="navbar"><a href="{U_INDEX}">{L_INDEX}</a>&gt;红包</div>
<span class="genmed">过年了，快来领红包把！</span>
<div class="catSides">红包的动态</div>
<!-- BEGIN deliver -->
	<div class="{deliver.ROW_CLASS}">
		{deliver.NUMBER}. <a href="{deliver.U_VIEW_DELIVER}">{deliver.DELIVER_TITLE}</a>[剩余 {deliver.POINT} 积分, 参与可得 {deliver.CUT_POINT} 积分, 好人:<a href="{deliver.U_DELIVER_USERNAME}">{deliver.DELIVER_USERNAME}</a>]
	</div>
<!-- END deliver -->
	{PAGINATION}
<div class="catSides">派红包</div>
<div class="row1">我也来派红包！</div>
<form action="{S_DELIVER_ACTION}" method="post">
	<div class="row1">
		标题：<br />
		<input name="title" type="text" maxlength="64" value="" style="width:99%;"/>
	</div>
	<div class="row1">
		贺词：<br />
		<textarea name="reason" rows="5" style="width:99%;"></textarea>
	</div>
	<div class="row1">你要拿出 <input name="point" type="text" size="3" maxlength="11" value="" /> 积分来派红包？</div>
	<div class="row1">来恭喜的用户可以获得 <input name="cut_point" type="text" size="3" maxlength="11" value="" /> 积分？</div>
	<input name="submit" type="submit" value=" 开始派 " />
</form>