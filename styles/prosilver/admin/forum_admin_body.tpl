<div class="navbar"><a href="{U_INDEX}">{L_INDEX}</a>&gt;<a href="{U_ADMIN}">{L_ADMIN}</a>&gt;<a href="{U_ADMIN_INDEX}">{L_ADMIN_INDEX}</a>&gt;{L_FORUM_TITLE}</div>
<span class="genmed">{L_FORUM_EXPLAIN}</span>
<form method="post" action="{S_FORUM_ACTION}">
<!-- BEGIN catrow -->
<div class="catSides"><a href="{catrow.U_VIEWCAT}">{catrow.CAT_DESC}</a></div>
<div class="row_hard">
<span class="genmed">
<a href="{catrow.U_CAT_EDIT}">{L_EDIT}</a>, <a href="{catrow.U_CAT_DELETE}">{L_DELETE}</a>, <a href="{catrow.U_CAT_MOVE_UP}">{L_MOVE_UP}</a>, <a href="{catrow.U_CAT_MOVE_DOWN}">{L_MOVE_DOWN}</a>
</span>
</div>
<!-- BEGIN forumrow -->
<div class="row1">
论坛：<b><a href="{catrow.forumrow.U_VIEWFORUM}">{catrow.forumrow.FORUM_NAME}</a></b><br/>
主题：<b>{catrow.forumrow.NUM_TOPICS}</b><br/>
帖子：<b>{catrow.forumrow.NUM_POSTS}</b><br/>
<a href="{catrow.forumrow.U_FORUM_EDIT}">{L_EDIT}</a>, <a href="{catrow.forumrow.U_FORUM_DELETE}">{L_DELETE}</a>, <a href="{catrow.forumrow.U_FORUM_MOVE_UP}">{L_MOVE_UP}</a>, <a href="{catrow.forumrow.U_FORUM_MOVE_DOWN}">{L_MOVE_DOWN}</a>, <a href="{catrow.forumrow.U_FORUM_RESYNC}">{L_RESYNC}</a>
</div>
<!-- END forumrow -->
<div class="row1">
<input type="text" name="{catrow.S_ADD_FORUM_NAME}" />
<input type="submit" class="liteoption"  name="{catrow.S_ADD_FORUM_SUBMIT}" value="{L_CREATE_FORUM}" />
</div>
<!-- END catrow -->
<div class="row1">
<input type="text" name="categoryname" />
<input type="submit" name="addcategory" value="{L_CREATE_CATEGORY}" />
</div>
</form>