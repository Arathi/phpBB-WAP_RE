<div class="row1">
{L_POSTED_ATTACHMENTS}:<br/>
<!-- BEGIN attach_row -->
<a href="{attach_row.U_VIEW_ATTACHMENT}">{attach_row.FILE_NAME}</a> <input class="subbutton" type="submit" name="del_attachment[{attach_row.ATTACH_FILENAME}]" value="{L_DELETE_ATTACHMENT}"/><br/>
<!-- BEGIN switch_update_attachment -->
<input class="subbutton" type="submit" name="update_attachment[{attach_row.ATTACH_ID}]" value="{L_UPLOAD_NEW_VERSION}"/><br/>
<!-- END switch_update_attachment -->
<!-- BEGIN switch_thumbnail -->
<input class="subbutton" type="submit" name="del_thumbnail[{attach_row.ATTACH_FILENAME}]" value="{L_DELETE_THUMBNAIL}"/>
<!-- END switch_thumbnail -->
{attach_row.S_HIDDEN}
<!-- END attach_row -->
</div>