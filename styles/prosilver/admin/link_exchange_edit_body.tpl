<div class="navbar"><a href="{U_INDEX}">{L_INDEX}</a>&gt;<a href="{U_ADMIN}">{L_ADMIN}</a>&gt;<a href="{U_ADMIN_INDEX}">{L_ADMIN_INDEX}</a>&gt;添加网站</div>
<form method="post" name="post" action="{S_LINK_EXCHANGE_ACTION}">
	<div class="catSides">
		<span class="cattitle">添加友情链接</span>
	</div>
	<div class="row1">
		是否激活该网站：<br />
		<input type="radio" name="link_exchange_active" value="1" {LINK_EXCHANGE_ACTIVE} />{L_YES}
		<input type="radio" name="link_exchange_active" value="0" {LINK_EXCHANGE_NOT_ACTIVE} /> {L_NO}
	</div>
	<div class="row1">出站：{LINK_EXCHANGE_OUT}</div>
	<div class="row1">进站：{LINK_EXCHANGE_IN}</div>
	<div class="row1">
		申请者：<br />
		<input type="text" name="link_exchange_name" maxlength="6" value="{LINK_EXCHANGE_NAME}" style="width:99%;" />
	</div>		
	<div class="row1">
		E-Mail：<br />
		<input type="text" name="link_exchange_email" maxlength="255" value="{LINK_EXCHANGE_EMAIL}" style="width:99%;" />
	</div>
	<div class="row1">
		网站名称：<br />
		<input type="text" name="link_exchange_website" maxlength="255" value="{LINK_EXCHANGE_WEBSITE}" style="width:99%;" />
	</div>
	<div class="row1">
		网站URL:<br />
		<input type="text" name="link_exchange_url" maxlength="255" value="{LINK_EXCHANGE_URL}" style="width:99%;" />
	</div>
	<div class="row1">
		网站LOGO:<br />
		<input type="text" name="link_exchange_image" maxlength="255" value="{LINK_EXCHANGE_IMG}" style="width:99%;" />
	</div>
	<div class="row1">
		描述：<br />
		<textarea name="link_exchange_description" rows="5" maxlength="255" style="width:99%;">{LINK_EXCHANGE_DESCRIPTION}</textarea>
	</div>
	{S_HIDDEN_FIELDS}
	<input type="submit" name="submit" value="提交" />
</form>