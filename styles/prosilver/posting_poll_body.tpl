<div class="row1">
{L_POLL_QUESTION}:<br/>
<input type="text" name="poll_title" maxlength="255" value="{POLL_TITLE}" />
</div>
<!-- BEGIN poll_option_rows -->
<div class="row1">
{L_POLL_OPTION}:<br/>
<input type="text" name="poll_option_text[{poll_option_rows.S_POLL_OPTION_NUM}]" maxlength="255" value="{poll_option_rows.POLL_OPTION}" /><br/>
<input class="subbutton" type="submit" name="edit_poll_option" value="{L_UPDATE_OPTION}"/><input class="subbutton" type="submit" name="del_poll_option[{poll_option_rows.S_POLL_OPTION_NUM}]" value="{L_DELETE_OPTION}"/>
</div>
<!-- END poll_option_rows -->
<div class="row1">
{L_POLL_OPTION}:<br/>
<input type="text" name="add_poll_option_text" maxlength="255" value="{ADD_POLL_OPTION}"/><br/>
<input class="subbutton" type="submit" name="add_poll_option" value="{L_ADD_OPTION}"/>
</div>
<div class="row1">
{L_POLL_LENGTH} ({L_DAYS}):<br/>
<input type="text" name="poll_length" size="3" maxlength="3" value="{POLL_LENGTH}"/>
</div>
<!-- BEGIN switch_poll_delete_toggle -->
<div class="row1">
{L_POLL_DELETE} <input type="checkbox" name="poll_delete"/>
</div>
<!-- END switch_poll_delete_toggle -->