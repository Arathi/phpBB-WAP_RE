<div class="navbar"><a href="{U_INDEX}">{L_INDEX}</a>&gt;<a href="{U_ADMIN}">{L_ADMIN}</a>&gt;<a href="{U_ADMIN_INDEX}">{L_ADMIN_INDEX}</a>&gt;友链管理</div>
<div class="catSides"><span class="cattitle">友链管理</span></div>
<form method="post" action="{S_LINK_EXCHANGE_ACTION}">
	<!-- BEGIN links -->
		<div class="{links.ROW_CLASS}">
			网站名称：<a href="{links.LINK_EXCHANGE_LINK_URL}">{links.LINK_EXCHANGE_WEBSITE}</a><br />
			描述：{links.LINK_EXCHANGE_DESCRIPTION}<br />
			是否通过审核：{links.LINK_EXCHANGE_IS_ACTIVE}<br />
			出站：{links.LINK_EXCHANGE_CLICKS_OUT}<br />
			入站：{links.LINK_EXCHANGE_CLICKS_IN}<br />
			[<a href="{links.U_LINK_EXCHANGE_EDIT}">编辑</a>]=[<a href="{links.U_LINK_EXCHANGE_DELETE}">删除</a>]
		</div>
	<!-- END links -->			
	<input type="submit" name="add" value="新增网站" />
</form>
{PAGE_NUMBER}
{PAGINATION}