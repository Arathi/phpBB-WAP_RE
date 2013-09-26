<div class="navbar"><a href="{U_INDEX}">{L_INDEX}</a>&gt;{L_MEDALS}</div>
<!-- BEGIN catrow -->
<div class="catSides">{catrow.CAT_DESC}</div>
<!-- BEGIN medals -->
<div class="row1">
<b>名称</b>：{catrow.medals.MEDAL_NAME}
<!-- BEGIN switch_mod_option -->
 - <a href="{catrow.medals.U_MEDAL_CP}">{L_LINK_TO_CP}</a>
<!-- END switch_mod_option -->
<br />
{catrow.medals.MEDAL_IMAGE}<br />
<b>说明</b>：{catrow.medals.MEDAL_DESCRIPTION}
</div>
<div class="row_hard">
<b>{L_USERS_LIST}</b>：{catrow.medals.USERS_LIST}<br/>
<b>{L_MEDAL_MODERATOR}</b>：{catrow.medals.MEDAL_MOD}
</div>
<!-- END medals -->
<!-- END catrow -->