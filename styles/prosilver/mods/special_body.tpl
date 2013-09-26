<div class="navbar"><a href="{U_INDEX}">{L_INDEX}</a>&gt;<a href="{U_FORUM}">论坛</a>&gt;<a href="{U_BACK_FORUM}">{FORUM_NAME}</a>&gt;专题</div>
<div class="catSides">{FORUM_NAME} 的专题列表</div>
<!-- BEGIN specials_row -->
	<div class="row1">
		{specials_row.L_NUMBER}、<a href="{specials_row.U_SPECIAL}">{specials_row.SPECIAL_TITLE}</a>{specials_row.L_CUT_1}{specials_row.EDIT}{specials_row.DELETE}{specials_row.L_CUT_2}
	</div>
<!-- END specials_row -->
<!-- BEGIN switch_no_special -->
	<div class="row1">{L_NO_SPECIAL}</div>
<!-- END switch_no_special -->

<!-- BEGIN switch_mod_link -->
	<div class="row1">管理：<a href="{ADD_SPECIAL}">添加专题</a></div>
<!-- END switch_mod_link -->
{PAGINATION}