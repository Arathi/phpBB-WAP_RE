<div class="navbar"><a href="{U_INDEX}">{L_INDEX}</a>&gt;<a href="{U_ADMIN}">{L_ADMIN}</a>&gt;<a href="{U_ADMIN_INDEX}">{L_ADMIN_INDEX}</a>&gt;删除会员</div>
<span class="genmed">在这里，您可以皮脸删除会员，本站共有注册会员 {TOTAL_USERS} 人！</span>
<form action="{U_LIST_ACTION}" method="post">
	{L_SELECT_SORT_METHOD}：
	<select name="sort">
		<option value="user_id" {ID_SELECTED} >用户ID</option>
		<option value="username" {USERNAME_SELECTED} >用户名</option>
		<option value="user_posts" {POSTS_SELECTED} >帖子数量</option>
		<option value="user_lastvisit" {LASTVISIT_SELECTED} >最后访问</option>
	</select>
	<select name="order">
		<option value="" {ASC_SELECTED} >{L_SORT_ASCENDING}</option>
		<option value="DESC" {DESC_SELECTED} >{L_SORT_DESCENDING}</option>
	</select>
	<input type="submit" value="显示"><br/>
</form>
<div class="catSides">会员列表</div>
<form action="{U_DELETE_ACTION}" method="post">
	<div class="row_hard">
		<span class="genmed"><b>提示</b>：删除用户是没有确认提示的，且删除后不可恢复！</span>
	</div>
	<!-- BEGIN userrow -->
		<div class="{userrow.COLOR}">
			<input type="checkbox" name="user_id_list[]" value="{userrow.NUMBER}" />
			{userrow.L_NUMBER}、
			<a href="{userrow.U_ADMIN_USER}">{userrow.USERNAME}</a><br />
			发帖数量：<b>{userrow.POSTS}</b><br />
			注册日期：<b>{userrow.JOINED}</b><br />
			最后访问：<b>{userrow.LAST_VISIT}</b>
		</div>
	<!-- END userrow -->
	<input class="subbutton" type="submit" value=" 删除 " />
</form>
{PAGINATION}