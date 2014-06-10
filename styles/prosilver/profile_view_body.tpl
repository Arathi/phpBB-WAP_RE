<div class="navbar"><a href="{U_INDEX}">{L_INDEX}</a>&gt;个人空间</div>
{HOBOE}
<div class="row1">{L_VIEWING_PROFILE}</div>
<div class="catSides">主页 | <a href="{U_PERSONAL_GALLERY}">相册</a> | <a href="{U_GUESTBOOK}">留言板</a> | {PM}</div>
{AVATAR_IMG}
<div class="row1">
	<b>{L_USERNAME}</b>:{USERNAME}<br />
	{USER_DELETE}{USER_CLONE}{USER_LOCK}{U_ADMIN_PROFILE}
</div>
<div class="row1">
	<b>用户ID</b>:{USER_ID}
</div>
<div class="row1">
	<b>等级</b>:{POSTER_RANK}
</div>
<!-- BEGIN usergroup -->
	<div class="row1">
		<b>所在用户组</b>:<br />
		{USERGROUP}
	</div>
<!-- END usergroup -->
<!-- BEGIN switch_display_medal -->
	<div class="row1">
		<!-- BEGIN medal -->
			<a href="{switch_display_medal.medal.MEDAL_BUTTON}">它共获得 {USER_MEDAL_COUNT} 个奖章</a><br/>
		<!-- END medal -->
		
		<!-- BEGIN details -->
			{switch_display_medal.details.MEDAL_IMAGE}
		<!-- END details -->
	</div>
<!-- END switch_display_medal -->
<div class="row1"><b>{L_JOINED}</b>:{JOINED}</div>
<div class="row1"><b>上次访问</b>:{LASTVISIT}</div>
<div class="row1">
	{L_TOTAL_POSTS}: <b><a href="{U_SEARCH_USER}">{POSTS}</a></b><br/>
	主题: <b><a href="{U_SEARCH_USER_TOPICS}">{TOPICS}</a></b><br/>
	附件: <b>{ATTACH}</b>
</div>
<!-- BEGIN warnings -->
	<div class="row1">
		<br/>警告: <b>{warnings.WARNINGS}</b><br/>
		{warnings.YELLOW_IMG}
		{warnings.RED_IMG}
		
		<!-- BEGIN details -->
			<a href="{warnings.details.U_SEARCH}">详细信息...</a><br/>
		<!-- END details -->
		
		<!-- BEGIN details_all -->
			<a href="{warnings.details_all.U_SEARCH_ALL}">详细记录...</a>
		<!-- END details_all -->
	</div>
<!-- END warnings -->
<div class="row1">
	积分: <b>{MONEY}</b>
	<!-- BEGIN money -->
		- （<a href="{U_MONEY_SEND}">赠送</a>）
	<!-- END money -->
</div>
<div class="row1">
	<b>虚拟银行</b>:{BANK_GOLD}
</div>
<!-- BEGIN pay_money -->
	<div class="row1">
		已赚取：<b>{MONEY_EARNED}</b> руб<br/>
		已充值：<b>{MONEY_PAYMENT}</b> руб
	</div>
<!-- END pay_money -->
<!-- BEGIN reputation -->
	<div class="row1">
		<b>{reputation.L_REPUTATION}</b>:<br/>
		{reputation.POLOSKA}
		{reputation.REPUTATION}<br/>
		<!-- BEGIN details -->
			<a href="{reputation.details.U_SEARCH}">{reputation.details.L_SEARCH}</a>
		<!-- END details -->
	</div>
<!-- END reputation -->
<!-- BEGIN yim -->
	<div class="row1"><b>{L_YAHOO}</b>:{YIM}</div>
<!-- END yim -->
<!-- BEGIN from -->
	<div class="row1"><b>{L_LOCATION}</b>:{LOCATION}</div>
<!-- END from -->
<!-- BEGIN birthday -->
	<div class="row1"><b>{L_BIRTHDAY}</b>:{BIRTHDAY}</div>
	<div class="row1"><b年龄></b>:{POSTER_AGE}</div>
	<div class="row1"><b>星座</b>:<font color="green">{ZODIAC}</font></div>
<!-- END birthday -->
<!-- BEGIN gender -->
	<div class="row1"><b>{L_GENDER}</b>:{GENDER}</div>
<!-- END gender -->
<!-- BEGIN occ -->
	<div class="row1"><b>{L_OCCUPATION}</b>:{OCCUPATION}</div>
<!-- END occ -->
<!-- BEGIN interests -->
	<div class="row1"><b>{L_INTERESTS}</b>:{INTERESTS}</div>
<!-- END interests -->
<!-- BEGIN msn -->
	<div class="row1"><b>{L_MESSENGER}</b>:{MSN}</div>
<!-- END msn -->
<!-- BEGIN aim -->
	<div class="row1"><b>{L_AIM}</b>:{AIM}</div>
<!-- END aim -->
<!-- BEGIN email -->
	<div class="row1"><b>{L_EMAIL_ADDRESS}</b>:{EMAIL}</div>
<!-- END email -->
<!-- BEGIN icq -->
	<div class="row1"><b>{L_ICQ_NUMBER}</b>:{ICQ}</div>
<!-- END icq -->
<!-- BEGIN number -->
	<div class="row1"><b>{L_NUMBER}</b>:{NUMBER}</div>
<!-- END number -->
<!-- BEGIN www -->
	<div class="row1">
	<b>{L_WEBSITE}</b>:<br />{WWW}</div>
<!-- END www -->
<!-- BEGIN signature -->
	<div class="row1">
	<b>签名</b>:<br />{SIGNATURE}</div>
<!-- END signature -->
<!-- BEGIN editprofile -->
	<div class="row1">
		<b>我的设定</b>:<br/>
		{U_EDITPROFILE}=={U_SELECTSTYLE}<br/>
		{U_EDITCONFIG}=={U_EDITPROFILEINFO}
	</div>
<!-- END editprofile -->