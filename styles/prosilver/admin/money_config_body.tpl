<div class="navbar"><a href="{U_INDEX}">{L_INDEX}</a>&gt;<a href="{U_ADMIN}">{L_ADMIN}</a>&gt;<a href="{U_ADMIN_INDEX}">{L_ADMIN_INDEX}</a>&gt;商店全局设置</div>
<span class="genmed">在这里，您可以管理积分的一些设置</span>
<form action="{S_CONFIG_ACTION}" method="post">
	<div class="catSides">商店设置</div>
	<div class="row1">
		是否开启商店功能：<br/>
		<input type="radio" name="shop" value="1" {SHOP_YES} />{L_YES} 
		<input type="radio" name="shop" value="0" {SHOP_NO} />{L_NO}
	</div>
	<div class="row1">
		{L_OPEN_PAY_MONEY}：<br/>
		<input type="radio" name="pay_money" value="1" {PAYMENT_YES} />{L_YES}
		<input type="radio" name="pay_money" value="0" {PAYMENT_NO} />{L_NO}
	</div>
	<div class="row1">
		{L_KURS_PAYMENT}：<br/>
			<span class="genmed">{L_KURS_PAYMENT_EXPLAIN}</span><br />
		<input type="text" maxlength="255" name="kurs_payment" value="{KURS_PAYMENT}" />
	</div>
	<div class="row1">
		是否开启广告点击赚取积分：<br/>
		<input type="radio" name="sites" value="1" {SITES_YES} />{L_YES}
		<input type="radio" name="sites" value="0" {SITES_NO} />{L_NO}
	</div>
	<div class="row1">
		是否开启顶部、底部广告出售：<br/>
		<input type="radio" name="ref_url" value="1" {REF_YES} />{L_YES}
		<input type="radio" name="ref_url" value="0" {REF_NO} />{L_NO}
	</div>
	<div class="row1">
		出售一个顶部广告链接需要多少积分：<br/>
		<input type="text" maxlength="255" name="verh_pay" value="{VERH_PAY}" />
	</div>
	<div class="row1">
		顶部最多可以出售多少个广告链接：<br/>
		<input type="text" maxlength="255" name="verh" value="{VERH}" />
	</div>
	<div class="row1">
		出售一个底部广告链接需要多少积分？<br/>
		<input type="text" maxlength="255" name="niz_pay" value="{NIZ_PAY}" />
	</div>
	<div class="row1">
		底部最多可以出售多少个广告链接：<br/>
		<input type="text" maxlength="255" name="niz" value="{NIZ}" />
	</div>
	<div class="row1">
		点击赚取积分时间间隔：<br />
		<input type="text" maxlength="255" name="time_click" value="{TIME_CLICK}" />
	</div>
	<div class="row1">
		修改用户名需要多少积分：<br />
		<input type="text" maxlength="255" name="smena_nika" value="{SMENA_NIKA}" />
	</div>
	<div class="row1">
		修改用户名颜色需要多少积分：<br />
		<input type="text" maxlength="255" name="smena_cveta" value="{SMENA_CVETA}" />
	</div>
	<div class="row1">
		修改等级需要多少积分：<br />
		<input type="text" maxlength="255" name="smena_zvaniya" value="{SMENA_ZVANIYA}" />
	</div>
	<div class="row1">
		解除黑名单每小时需要多少积分：<br />
		<input type="text" maxlength="255" name="razblokirovka_druga" value="{RAZBLOKIROVKA_DRUGA}" />
	</div>
	<div class="row1">
		购买网站帐号需要多少积分：<br />
		<input type="text" maxlength="255" name="pokupka_uchetki" value="{POKUPKA_UCHETKI}" />
	</div>
	<div class="catSides">购买网站帐号的限制</div>
	<div class="row1">
		购买网站帐号需要发表有多少帖子：<br/>
		<input type="text" maxlength="255" name="pokupka_uchetki_posts" value="{POKUPKA_UCHETKI_POSTS}" />
	</div>
	<div class="row1">
		购买网站帐号注册时间需要达到多少周：<br/>
		<input type="text" maxlength="255" name="pokupka_uchetki_nedeli" value="{POKUPKA_UCHETKI_NEDELI}" />
	</div>
	<input type="submit" name="submit" value=" 提交 " class="subbutton" />
</form>