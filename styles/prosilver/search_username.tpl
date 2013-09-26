<div class="navbar"><a href="{U_INDEX}">{L_INDEX}</a>&gt;<a href="{U_SEARCH}">{L_SEARCH}</a>&gt;搜索会员</div>
<form method="post" name="search" action="{S_SEARCH_ACTION}">
<div class="catSides">
{L_SEARCH_USERNAME}
</div>
<div class="row1">用户名:<br/>
<span class="genmed">{L_SEARCH_EXPLAIN}</span><br/>
<input type="text" name="search_username" value="{USERNAME}" /><br/>
<input type="submit" name="search" value="{L_SEARCH}" class="subbutton" />
</div>
<!-- BEGIN switch_select_name -->
<div class="row1">
{L_UPDATE_USERNAME}<br/>
<select name="username_list">{S_USERNAME_OPTIONS}</select><br/>
<input type="submit" class="subbutton" name="use" value="{L_SELECT}" />
</div>
<!-- END switch_select_name -->
</form>