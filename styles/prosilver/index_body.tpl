{VERH}
{BAN_INFO}
{L_WHOSBIRTHDAY_TODAY}
{HOBOE}
<!-- BEGIN switch_user_logged_out -->
	<div class="row1">您尚未(<a href="{U_LOGIN_LOGOUT}">登录</a>/<a href="{U_REGISTER}">注册</a>)</div>
<!-- END switch_user_logged_out -->
<!-- BEGIN switch_user_logged_in -->
	<div class="row1">
		<a href="{U_PRIVATEMSGS}">{PRIVATE_MESSAGE_INFO}</a>|<a href="{U_PROFILE}">我的地盘</a>|<a href="{U_LOGIN_LOGOUT}">注销登录</a>
	</div>
<!-- END switch_user_logged_in -->
<!-- BEGIN announcement -->
	<div class="catSides">
		<span class="cattitle">网站公告</span>
	</div>
	<div class="row1">
		{announcement.ANNOUNCEMENT}
	</div>
<!-- END announcement -->
{INDEX_PAGE_BODY}
<!-- <div class="navbar">简版|<a href="web/index.php">电脑版</a></div> -->
<!-- BEGIN switch_admin_link -->
	<div class="row1">[{ADMIN_LINK} == {EDIT_INDEX}]</div>
<!-- END switch_admin_link -->
<!-- BEGIN switch_modcp_link -->
	<div class="row1">[{MODCP_LINK}]</div>
<!-- END switch_modcp_link -->
{NIZ}