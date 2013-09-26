
<h1>{L_USER_TITLE}</h1>

<p>{L_USER_EXPLAIN}</p>

<form action="{S_PROFILE_ACTION}" method="post">

	<div> 
	  <th class="thHead" colspan="{S_COLSPAN}" height="25" valign="middle">{L_AVATAR_GALLERY}</th>
	</div>
	<div> 
	  <div class="catBottom" align="center" valign="middle" colspan="6" height="28"><span class="genmed">{L_CATEGORY}:&nbsp;<select name="avatarcategory">{S_OPTIONS_CATEGORIES}</select>&nbsp;<input type="submit" class="liteoption" value="{L_GO}" name="avatargallery" /></span></div>
	</div>
	<!-- BEGIN avatar_row -->
	<div> 
	<!-- BEGIN avatar_column -->
		<div class="row1" align="center"><img src="{avatar_row.avatar_column.AVATAR_IMAGE}" /></div>
	<!-- END avatar_column -->
	</div>
	<div>
	<!-- BEGIN avatar_option_column -->
		<div class="row2" align="center"><input type="radio" name="avatarselect" value="{avatar_row.avatar_option_column.S_OPTIONS_AVATAR}" /></div>
	<!-- END avatar_option_column -->
	</div>

	<!-- END avatar_row -->
	<div> 
	  <div class="catBottom" colspan="{S_COLSPAN}" align="center" height="28">{S_HIDDEN_FIELDS} 
		<input type="submit" name="submitavatar" value="{L_SELECT_AVATAR}" class="mainoption" />
		&nbsp;&nbsp; 
		<input type="submit" name="cancelavatar" value="{L_RETURN_PROFILE}" class="liteoption" />
	  </div>
	</div>
</form>
