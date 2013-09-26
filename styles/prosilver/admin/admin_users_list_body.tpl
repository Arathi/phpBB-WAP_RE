<div class="navbar"><a href="{U_INDEX}">{L_INDEX}</a>&gt;<a href="{U_ADMIN}">{L_ADMIN}</a>&gt;<a href="{U_ADMIN_INDEX}">{L_ADMIN_INDEX}</a>&gt;会员列表</div>
<span class="genmed">本站共有会员 {TOTAL_USERS} 人！</span>
<form action="{U_LIST_ACTION}" method="post">
	{L_SELECT_SORT_METHOD}：
	<select name="sort">
		<option value="user_id" class="genmed" {ID_SELECTED} >用户ID</option>
		<option value="username" class="genmed" {USERNAME_SELECTED} >用户名称</option>
		<option value="user_posts" class="genmed" {POSTS_SELECTED} >用户帖子</option>
		<option value="user_lastvisit" class="genmed" {LASTVISIT_SELECTED} >最后登录</option>
	</select>
	<select name="order">
		<option value="" {ASC_SELECTED} >{L_SORT_ASCENDING}</option>
		<option value="DESC" {DESC_SELECTED} >{L_SORT_DESCENDING}</option>
	</select>
	<input type="submit" value=" 显示 ">
</form>
<div class="catSides">会员列表</div>
<div class="row_hard">
	<span class="genmed">
		提示：要编辑用户的资料请点击Ta的用户名！
	</span>
</div>
<!-- BEGIN userrow -->
	<div class="{userrow.COLOR}">
		{userrow.L_NUMBER}、<b><a href="{userrow.U_ADMIN_USER}">{userrow.USERNAME}</a></b>
		- <a href="{userrow.U_ADMIN_USER_AUTH}">权限</a><br />
		用户ID：{userrow.NUMBER}<br/>
		发表帖子：<b>{userrow.POSTS}</b><br />
		电子邮箱：<b>{userrow.EMAIL}</b><br/>
		注册日期：<b>{userrow.JOINED}</b><br/>
		最后访问：<b>{userrow.LAST_VISIT}</b><br/>
		是否激活：<b>{userrow.ACTIVE}</b>
	</div>
<!-- END userrow -->
{PAGINATION}