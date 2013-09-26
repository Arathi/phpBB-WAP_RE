<div class="navbar"><a href="{U_INDEX}">{L_INDEX}</a>&gt;管理员</div>
<!-- BEGIN switch_list_staff -->
<!-- BEGIN user_level -->
<div class="catSides">{switch_list_staff.user_level.USER_LEVEL}</div>
<!-- BEGIN staff -->
<div class="row1">
<b><a href="{switch_list_staff.user_level.staff.U_PROFILE}">{switch_list_staff.user_level.staff.USERNAME}</a></b>[{switch_list_staff.user_level.staff.POSTS}]{switch_list_staff.user_level.staff.USER_STATUS}<br/>
{switch_list_staff.user_level.staff.FORUMS}
</div>
<!-- END staff -->
<!-- BEGIN no_staff -->
<div class="row1">还没有设置管理员和版主</div>
<!-- END no_staff -->
<!-- END user_level -->
<!-- END switch_list_staff -->