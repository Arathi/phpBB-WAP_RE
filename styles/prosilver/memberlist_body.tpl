<div class="navbar"><a href="{U_INDEX}">{L_INDEX}</a>&gt;会员列表</div>
<div class="row1">-<a href="{U_SEARCH_USER}">{L_SEARCH_USER}</a></div>
<div class="catSides">会员列表</div>
<!-- BEGIN memberrow -->
<div class="{memberrow.ROW_CLASS}">
{memberrow.NUMBER}.<b><a href="{memberrow.U_VIEWPROFILE}"{memberrow.COLOR}>{memberrow.USERNAME}</a></b>[{memberrow.POSTS}]
</div>
<!-- END memberrow -->
{PAGINATION}
<form method="post" action="{S_MODE_ACTION}">
{L_SELECT_SORT_METHOD}:<br/>
{S_MODE_SELECT}{S_ORDER_SELECT}<input type="submit" name="submit" value="{L_SUBMIT}" />
</form>