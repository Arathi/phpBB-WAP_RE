<div class="navbar"><a href="{U_INDEX}">{L_INDEX}</a>&gt;<a href="{U_ADMIN}">{L_ADMIN}</a>&gt;<a href="{U_EDIT_PAGE}">排版选择</a>&gt;首页排版</div>
<form action="{S_EDITPAGES_ACTION}" method="post">
	<div class="catSides">首页BBcode排版</div>
	<!-- BEGIN switch_pages_page -->
		<div class="row1">
			本页面的BBcode：<br />
			<input type="text" value="[page={PAGES_ID}]{PAGES_TITLE}[/page]" />
		</div>
		<div class="row1">
			页面的标题：<br />
			<input type="text" name="pages_title" value="{PAGES_TITLE}" />
		</div>
	<!-- END switch_pages_page -->
	
	<div class="row1">
		<font color="red">
			小提示：可使用BBcode、表情、HTML排版<br />
			目前该功能处于测试阶段，建议BBcode、和表情不要嵌套使用！<br />
		</font>
		<textarea type="text" name="pages_body" rows="10" style="width:99%">{PAGES_BODY}</textarea>
	</div>
	{S_HIDDEN_FIELDS}
	<input type="submit" value="{L_SUBMIT}" />
</form>