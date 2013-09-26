<div class="navbar"><a href="{U_INDEX}">{L_INDEX}</a>&gt;<a href="{U_ADMIN}">{L_ADMIN}</a>&gt;<a href="{U_ADMIN_INDEX}">{L_ADMIN_INDEX}</a>&gt;广告管理</div>
<span class="genmed">在这里您可以在您的网站放置一些广告，您需要使用到 HTML 标签</span>
<form action="{S_CONFIG_ACTION}" method="post">
	<div class="catSides">广告管理</div>
	<div class="row1">
		首页顶部：<br/>
		<textarea name="forums_index_top" rows="5" style="width: 99%">{FORUMS_INDEX_TOP}</textarea>
	</div>
	<div class="row1">
		首页的底部：<br/>
		<textarea name="forums_index_bottom" rows="5" style="width: 99%">{FORUMS_INDEX_BUTTOM}</textarea>
	</div>
	<div class="row1">
		其它页面的顶部：<br/>
		<textarea name="forums_other_top" rows="5" style="width: 99%">{FORUMS_OTHER_TOP}</textarea>
	</div>
	<div class="row1">
		其它页面的底部：<br/>
		<textarea name="forums_other_bottom" rows="5" style="width: 99%">{FORUMS_OTHER_BUTTOM}</textarea>
	</div>
	<input type="submit" name="submit" value=" 提交 " class="subbutton" />
</form>