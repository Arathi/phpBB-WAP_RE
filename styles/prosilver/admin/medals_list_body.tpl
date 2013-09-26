<div class="navbar"><a href="{U_INDEX}">{L_INDEX}</a>&gt;<a href="{U_ADMIN}">{L_ADMIN}</a>&gt;<a href="{U_ADMIN_INDEX}">{L_ADMIN_INDEX}</a>&gt;{L_MEDAL_TITLE}</div>
<span class="genmed">{L_MEDAL_EXPLAIN}</span>
<form method="post" action="{S_MEDAL_ACTION}">

	<!-- BEGIN catrow -->
		<div class="catSides">
			类名:{catrow.CAT_DESC}
		</div>
		<div class="row_hard">
			<span class="genmed">
				<a href="{catrow.U_CAT_MOVE_UP}">{L_MOVE_UP}</a>, 
				<a href="{catrow.U_CAT_MOVE_DOWN}">{L_MOVE_DOWN}</a>, 
				<a href="{catrow.U_CAT_EDIT}">{L_EDIT}</a>, 
				<a href="{catrow.U_CAT_DELETE}">{L_DELETE}</a>
			</span>
		</div>
		
		<!-- BEGIN medals -->
		
		<div class="row1">
			{L_MEDAL_NAME}: <b>{catrow.medals.MEDAL_NAME}</b><br />
			奖章:<br />
			{catrow.medals.MEDAL_IMAGE}<br />
			说明:{catrow.medals.MEDAL_DESCRIPTION}<br/>
			<a href="{catrow.medals.U_MEDAL_MOD}">{L_MEDAL_MOD}</a>,
			<a href="{catrow.medals.U_MEDAL_EDIT}">{L_EDIT}</a>, 
			<a href="{catrow.medals.U_MEDAL_DELETE}">{L_DELETE}</a>
		</div>
		
		<!-- END medals -->
		
		<!-- BEGIN nomedals -->
			<div class="row1">{catrow.nomedals.L_NO_MEDAL_IN_CAT}</div>
		<!-- END nomedals -->
		
	<!-- END catrow -->
	
	<div class="row1">
		{L_CREATE_NEW_MEDAL_CAT}:
		<input type="text" name="name" />
		<input type="submit" name="addcat" value="创建" />
	</div>
	
	<input type="submit" name="addmedal" value="{L_CREATE_NEW_MEDAL}" />
</form>