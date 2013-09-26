<div class="navbar"><a href="{U_INDEX}">{L_INDEX}</a>&gt;<a href="{U_ALBUM}">{L_ALBUM}</a>&gt;<a href="{U_VIEW_CAT}">{CAT_TITLE}</a>&gt;{PIC_TITLE}</div>
<div class="catSides">{L_PIC_TITLE}: {PIC_TITLE}</div>
{L_POSTER}: {POSTER}
<form action="{S_ALBUM_ACTION}" method="post">
<div class="catSides">评论列表</div>
<!-- BEGIN commentrow -->
<div class="{commentrow.ROW_CLASS}">
<b>{commentrow.POSTER}[{commentrow.TIME}]</b>{commentrow.EDIT}{commentrow.DELETE}<br/>
{commentrow.TEXT}
</div>
<!-- END commentrow -->
<!-- BEGIN switch_comment -->
{PAGINATION}
<!-- END switch_comment -->
<!-- BEGIN switch_comment_post -->
<form name="commentform" action="{S_ALBUM_ACTION}" method="post">
<div class="catSides">
{L_POST_YOUR_COMMENT}</div>
<!-- BEGIN logout -->
<div class="row1">{L_USERNAME}:<br/>
<input type="text" name="comment_username" maxlength="32" />
</div>
<!-- END logout -->
<div class="row1">{L_MESSAGE}:<br/>
<textarea name="comment" rows="5" style="width:98%">{S_MESSAGE}</textarea>
</div>
<input type="submit" name="submit" value="{L_SUBMIT}" class="subbutton" />
</form>
<!-- END switch_comment_post -->