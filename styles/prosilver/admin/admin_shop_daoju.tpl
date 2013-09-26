<div class="navbar"><a href="{U_INDEX}">{L_INDEX}</a>&gt;<a href="{U_ADMIN}">{L_ADMIN}</a>&gt;<a href="{U_ADMIN_INDEX}">{L_ADMIN_INDEX}</a>&gt;道具后台管理</div>
<span class="genmed">在这里您可以管理道具功能!<br/><font color=red>抢楼卡暂未实现，敬请期待！</font></span>
<div class="catSides">管理道具功能</div>
<form method="post" action="{S_ACTION}">
	<div class="row1">
		是否开启道具功能：<br/>
		<input type="text" name="daoju_swtich" value="{DAOJU_SWTICH}" />(0为关闭，其它值均为开启)
	</div>
	<div class="row1">
		置顶卡价格：({POINT_NAME})<br/>
		<input type="text" name="stick_price" value="{STICK_PRICE}"/>
	</div>
	<div class="row1">
		置顶卡有效时间：<br/>
		<input type="text" name="stick_time" value="{STICK_TIME}"/> (单位:小时)
	</div>
	<div class="row1">
		高亮卡价格：({POINT_NAME})<br/>
		<input type="text" name="highlight_price" value="{HIGHLIGHT_PRICE}"/>
	</div>
	<div class="row1">
		高亮卡有效时间：<br/>
		<input type="text" name="highlight_time" value="{HIGHLIGHT_TIME}"/> (单位:时间)
	</div>
	<input type="submit" name="submit" value=" 提交 " />
</form>