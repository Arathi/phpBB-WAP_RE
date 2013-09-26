<div class="navbar"><a href="{U_INDEX}">{L_INDEX}</a>&gt;网站统计</div>
<div class="catSides">统计</div>
<div class="row1">{L_NUMBER_POSTS}: <b>{NUMBER_OF_POSTS}</b></div>
<div class="row1">{L_POSTS_PER_DAY}: <b>{POSTS_PER_DAY}</b></div>
<div class="row1">{L_NUMBER_TOPICS}: <b>{NUMBER_OF_TOPICS}</b></div>
<div class="row1">{L_TOPICS_PER_DAY}: <b>{TOPICS_PER_DAY}</b></div>
<div class="row1">{L_NUMBER_USERS}: <b>{NUMBER_OF_USERS}</b></div>
<div class="row1">{L_USERS_PER_DAY}: <b>{USERS_PER_DAY}</b></div>
<div class="row1">{L_BOARD_STARTED}: <b>{START_DATE}</b></div>
<div class="row1">{L_AVATAR_DIR_SIZE}: <b>{AVATAR_DIR_SIZE}</b></div>
<div class="row1">{L_DB_SIZE}: <b>{DB_SIZE}</b></div>
<div class="row1">{L_GZIP_COMPRESSION}: <b>{GZIP_COMPRESSION}</b></div>
<div class="catSides">在线状态</div>
<!-- BEGIN reg_user_row -->
<div class="row1">
<a href="{reg_user_row.U_USER_PROFILE}">{reg_user_row.USERNAME}</a>&raquo;<a href="{reg_user_row.U_FORUM_LOCATION}">{reg_user_row.FORUM_LOCATION}</a>(ip: {reg_user_row.IP_ADDRESS})
</div>
<!-- END reg_user_row -->
<!-- BEGIN guest_user_row -->
<div class="row1">
{guest_user_row.USERNAME}&raquo;<a href="{guest_user_row.U_FORUM_LOCATION}">{guest_user_row.FORUM_LOCATION}</a>(ip: {guest_user_row.IP_ADDRESS})
</div>
<!-- END guest_user_row -->