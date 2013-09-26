<div class="navbar"><a href="{U_INDEX}">{L_INDEX}</a>&gt;<a href="{U_BACK_FORUM}">{FORUM_NAME}</a>&gt;<a href="{U_BACK_SPECIAL}">专题</a>&gt;编辑专题</div>
<div class="catSides">修改专题名称</div>
<form action="{S_POST_ACTION}" method="post" name="post">
	<div class="row1">
		请输入新的专题名称：<br />
		<input type="text" name="name" size="25" tabindex="1" value="{SPECIAL_NAME}" />
		{S_HIDDEN}
		<input type="submit" name="submit" value=" 编辑 " />
	</div>
</form>