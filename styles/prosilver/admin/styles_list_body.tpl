<div class="navbar"><a href="{U_INDEX}">{L_INDEX}</a>&gt;<a href="{U_ADMIN}">{L_ADMIN}</a>&gt;<a href="{U_ADMIN_INDEX}">{L_ADMIN_INDEX}</a>&gt;风格列表</div>
<span class="genmed">在这里您可以管理网站的所有默认风格！</span>
<form action="{S_PROFILE_ACTION}" method="post">
	<div class="catSides">已安装的风格</div>
	<!-- BEGIN styles -->
		<div class="{styles.ROW_CLASS}">
			{styles.STYLE_ID}. {styles.STYLE_NAME}（<a href="{styles.U_STYLE_DELETE}">卸载</a>）
		</div>
	<!-- END styles -->
	<div class="catSides">未安装的风格</div>
	<div class="row1">创建一个新的风格之前请确保已将风格上传到【styles】文件夹中！</div>
	<!-- BEGIN not_add_styles -->
		<div class="{not_add_styles.ROW_CLASS}">
			{not_add_styles.NUMBER}. {not_add_styles.STYLE_NAME}（<a href="{not_add_styles.U_STYLES_INSTALL}">安装</a>）
		</div>
	<!-- END not_add_styles -->
</form>