<div class="navbar"><a href="{U_INDEX}">{L_INDEX}</a>&gt;友情链接</div>
<span class="genmed">本站一共收录 {TOTAL_LINKS} 个友链！</span>
<div class="row1">
	<form method="post" name="search" action="{S_LINKS_ACTION}">
		按
		<select name="sort_by">
			<option value="0" selected="selected">链入排序</option>
			<option value="1">链出排序</option>
			<option value="2">名称排序</option>
			<option value="3">ID排序</option>
		</select>
		<select name="sort_dir">
			<option value="DESC" selected="selected">递减</option>
			<option value="ASC">递增</option>
			
		</select>
		<input type="submit" value="排序" />
	</form>
</div>
<div class="catSides"><span class="cattitle">友情链接</span></div>
<!-- BEGIN link_exch -->
	<div class="row1">
		网站：<a href="{link_exch.LINKURL}" target="_blank">{link_exch.LINKNAME}</a><br />
		出站：{link_exch.LINKOUT}<br />
		进站：{link_exch.LINKIN}<br />
		<!-- BEGIN switch_not_flash -->
			描述：{link_exch.switch_not_flash.LINKDESC}<br />
		<!-- END switch_not_flash -->  
	</div>
<!-- END link_exch -->
{PAGE_NUMBER}
{PAGINATION}
<br />
<a href="{U_LINK_EXCHANGE_SUBMIT}" class="buttom">申请加入</a>