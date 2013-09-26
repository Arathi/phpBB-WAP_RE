<div class="navbar"><a href="{U_INDEX}">{L_INDEX}</a>&gt;IP信息</div>
<div class="catSides">雷同 ip 地址的用户</div>
<div class="row_easy">
<b>{L_THIS_POST_IP}:</b><br/>
{IP}[{POSTS}]<br/>
<a href="{U_LOOKUP_IP}">{L_LOOKUP_IP}</a>
</div>
<div class="row_hard">
{L_OTHER_USERS}
</div>
<!-- BEGIN userrow -->
<div class="row_easy">
<b><a href="{userrow.U_PROFILE}">{userrow.USERNAME}</a></b>[{userrow.POSTS}] <a href="{userrow.U_SEARCHPOSTS}" title="{userrow.L_SEARCH_POSTS}">{L_SEARCH}</a>
</div>
<!-- END userrow -->
<div class="row_hard">
{L_OTHER_IPS}
</div>
<!-- BEGIN iprow -->
<div class="row_easy">
{iprow.IP}[{iprow.POSTS}]<a href="{iprow.U_LOOKUP_IP}">{L_LOOKUP_IP}</a>
</div>
<!-- END iprow -->