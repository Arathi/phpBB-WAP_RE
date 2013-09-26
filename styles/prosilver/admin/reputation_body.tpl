<div class="navbar"><a href="{U_INDEX}">{L_INDEX}</a>&gt;<a href="{U_ADMIN}">{L_ADMIN}</a>&gt;<a href="{U_ADMIN_INDEX}">{L_ADMIN_INDEX}</a>&gt;{L_DEMOCRACY}</div>
<form action="{S_REPUTATION_ACTION}" method="post" name="democracy">
	<div class="catSides">{L_REPUTATION_OPTIONS}</div>
	<div class="row1">
		<input id="reputation" type="checkbox" name="reputation" {S_REPUTATION_CHECKED}/> {L_ENABLE_REPUTATION}<br/>
		<input type="checkbox" name="positive_only" id="positive_only" {S_POSITIVE_ONLY}/> {L_POSITIVE_ONLY}<br/>
		<span class="genmed">{L_POSITIVE_ONLY_EXP}</span><br/>
		<input type="checkbox" name="empty_reviews" id="empty_reviews" {S_EMPTY_REVIEWS}/> {L_EMPTY_REVIEWS}
	</div>
	<div class="row_hard"><b>{L_ACCESS_RIGHTS}</b></div>
	<div class="row1">
		<b>{L_ADD_REP}</b>:<br/>
		<input name="auth_add_rep" type="radio" value="{AUTH_REG}" {S_ADD_REP_CHECKED_0}/> {L_USER}<br/>
		<input name="auth_add_rep" type="radio" value="{AUTH_MOD}" {S_ADD_REP_CHECKED_1}/> {L_MODERATOR}<br/>
		<input name="auth_add_rep" type="radio" value="{AUTH_ADMIN}" {S_ADD_REP_CHECKED_2}/> {L_ADMIN}
	</div>
	<div class="row1">
		<b>{L_ADD_REP_NONPOST}</b>:<br/>
		<input name="auth_add_rep_nonpost" type="radio" value="{AUTH_REG}" {S_ADD_REP_NONPOST_CHECKED_0}/> {L_USER}<br/>
		<input name="auth_add_rep_nonpost" type="radio" value="{AUTH_MOD}" {S_ADD_REP_NONPOST_CHECKED_1}/> {L_MODERATOR}<br/>
		<input name="auth_add_rep_nonpost" type="radio" value="{AUTH_ADMIN}" {S_ADD_REP_NONPOST_CHECKED_2}/> {L_ADMIN}
	</div>
	<div class="row1">
		<b>{L_EDIT_REP}</b>:<br/>
		<input name="auth_edit_rep" type="radio" value="{AUTH_REG}" {S_EDIT_REP_CHECKED_0}/> {L_USER}<br/>
		<input name="auth_edit_rep" type="radio" value="{AUTH_MOD}" {S_EDIT_REP_CHECKED_1}/> {L_MODERATOR}<br/>
		<input name="auth_edit_rep" type="radio" value="{AUTH_ADMIN}" {S_EDIT_REP_CHECKED_2}/> {L_ADMIN}
	</div>
	<div class="row1">
		<b>{L_DELETE_REP}</b>:<br/>
		<input name="auth_delete_rep" type="radio" value="{AUTH_REG}" {S_DELETE_REP_CHECKED_0}/> {L_USER}<br/>
		<input name="auth_delete_rep" type="radio" value="{AUTH_MOD}" {S_DELETE_REP_CHECKED_1}/> {L_MODERATOR}<br/>
		<input name="auth_delete_rep" type="radio" value="{AUTH_ADMIN}" {S_DELETE_REP_CHECKED_2}/> {L_ADMIN}
	</div>
	<div class="row1">
		<b>{L_NO_LIMITS}</b>:<br/>
		<input name="auth_no_limits" type="radio" value="{AUTH_MOD}" {S_NO_LIMITS_CHECKED_1}/> {L_MODERATOR}<br/>
		<input name="auth_no_limits" type="radio" value="{AUTH_ADMIN}" {S_NO_LIMITS_CHECKED_2}/> {L_ADMIN}
	</div>
	<div class="row1">
		<b>{L_NOT_APPLICABLE}</b>:<br/>
		<input name="mod_norep" type="checkbox" {S_MOD_NOREP_CHECKED}/> {L_MODERATOR}<br/>
		<input name="admin_norep" type="checkbox" {S_ADMIN_NOREP_CHECKED}/> {L_ADMIN}
	</div>
	<div class="row1">
		<input type="checkbox" id="anonymous_view_rep" name="anonymous_view_rep" {S_ANONYMOUS_VIEW_REP_CHECKED}/> {L_ANONYMOUS_VIEW_REP}
	</div>
	<div class="row_hard"><b>{L_PREREQUIREMENTS}</b></div>
	<div class="row1">
		<input type="checkbox" name="enable_days_req" id="enable_days_req" {S_ENABLE_DAYS_REQ}/> {L_DAYS_REQ_0} 
		<input type="text" name="days_req" size="5" maxlength="4" value="{S_DAYS_REQ}" /> {L_DAYS_REQ_1}
	</div>
	<div class="row1">
		<input type="checkbox" name="enable_posts_req" id="enable_posts_req" {S_ENABLE_POSTS_REQ}/> {L_POSTS_REQ_0} 
		<input type="text" name="posts_req" size="5" maxlength="4" value="{S_POSTS_REQ}" /> {L_POSTS_REQ_1}
	</div>
	<div class="row1">
		<input type="checkbox" name="enable_warnings_req" id="enable_warnings_req" {S_ENABLE_WARNINGS_REQ}/> {L_WARNINGS_REQ_0} 
		<input type="text" name="warnings_req" size="5" maxlength="4" value="{S_WARNINGS_REQ}" /> {L_WARNINGS_REQ_1}
	</div>
	<div class="row1">
		<input type="checkbox" name="enable_reputation_req" id="enable_reputation_req" {S_ENABLE_REPUTATION_REQ}/> {L_REPUTATION_REQ_0} 
		<input type="text" name="reputation_req" size="5" maxlength="5" value="{S_REPUTATION_REQ}" /> {L_REPUTATION_REQ_1}
	</div>
	<div class="row_hard"><b>{L_LIMITS}</b></div>
	<div class="row1">
		<input type="checkbox" name="enable_time_limit" id="enable_time_limit" {S_ENABLE_TIME_LIMIT}/> {L_TIME_LIMIT_0} 
		<input type="text" name="time_limit" size="7" maxlength="8" value="{S_TIME_LIMIT}" /> {L_TIME_LIMIT_1}
	</div>
	<div class="row1"> 
		<input type="checkbox" name="enable_rotation_limit" id="enable_rotation_limit" {S_ENABLE_ROTATION_LIMIT}/> {L_ROTATION_LIMIT_0} 
		<input type="text" name="rotation_limit" size="5" maxlength="4" value="{S_ROTATION_LIMIT}" /> {L_ROTATION_LIMIT_1}<br/>
		<span class="genmed">{L_ROTATION_LIMIT_EXP}</span>
	</div>
	<div class="row_hard"><b>{L_REPUTATION_DISPLAY}</b></div>
	<div class="row1">
		<input type="radio" id="reputation_display_0" name="reputation_display" value="{REPUTATION_SUM}" {S_REPUTATION_DISPLAY_0}/> {L_DISPLAY_SUM}<br/>
		<input type="radio" id="reputation_display_1" name="reputation_display" value="{REPUTATION_PLUSMINUS}" {S_REPUTATION_DISPLAY_1}/> {L_DISPLAY_PLUSMINUS}
	</div>
	<div class="catSides">{L_WARNINGS_OPTIONS}</div>
	<div class="row1">
		<input type="checkbox" id="warnings" name="warnings" {S_WARNINGS_CHECKED}/> {L_ENABLE_WARNINGS}
	</div>
	<div class="row_hard"><b>{L_ACCESS_RIGHTS}</b></div>
	<div class="row1">
		<b>{L_WARN}</b>:<br/>
		<input name="auth_warn" type="radio" value="{AUTH_MOD}" {S_WARN_CHECKED_1}/> {L_MODERATOR}<br/>
		<input name="auth_warn" type="radio" value="{AUTH_ADMIN}" {S_WARN_CHECKED_2}/> {L_ADMIN}
	</div>
	<div class="row1">
		<b>{L_WARN_NONPOST}</b>:<br/>
		<input name="auth_warn_nonpost" type="radio" value="{AUTH_MOD}" {S_WARN_NONPOST_CHECKED_1}/> {L_MODERATOR}<br/>
		<input name="auth_warn_nonpost" type="radio" value="{AUTH_ADMIN}" {S_WARN_NONPOST_CHECKED_2}/> {L_ADMIN}
	</div>
	<div class="row1">
		<b>{L_BAN}</b>:<br/>
		<input name="auth_ban" type="radio" value="{AUTH_MOD}" {S_BAN_CHECKED_1}/> {L_MODERATOR}<br/>
		<input name="auth_ban" type="radio" value="{AUTH_ADMIN}" {S_BAN_CHECKED_2}/> {L_ADMIN}
	</div>
	<div class="row1">
		<b>{L_BAN_NONPOST}</b>:<br/>
		<input name="auth_ban_nonpost" type="radio" value="{AUTH_MOD}" {S_BAN_NONPOST_CHECKED_1}/> {L_MODERATOR}<br/>
		<input name="auth_ban_nonpost" type="radio" value="{AUTH_ADMIN}" {S_BAN_NONPOST_CHECKED_2}/> {L_ADMIN}
	</div>
	<div class="row1">
		<b>{L_EDIT_WARNS}</b>:<br/>
		<input name="auth_edit_warn" type="radio" value="{AUTH_MOD}" {S_EDIT_WARN_CHECKED_1}/> {L_MODERATOR}<br/>
		<input name="auth_edit_warn" type="radio" value="{AUTH_ADMIN}" {S_EDIT_WARN_CHECKED_2}/> {L_ADMIN}
	</div>
	<div class="row1">
		<b>{L_DELETE_WARNS}</b>:<br/>
		<input name="auth_delete_warn" type="radio" value="{AUTH_MOD}" {S_DELETE_WARN_CHECKED_1}/> {L_MODERATOR}<br/>
		<input name="auth_delete_warn" type="radio" value="{AUTH_ADMIN}" {S_DELETE_WARN_CHECKED_2}/> {L_ADMIN}
	</div>
	<div class="row1">
		<input type="checkbox" id="anonymous_view_warns" name="anonymous_view_warns" {S_ANONYMOUS_VIEW_WARNS_CHECKED}/> {L_ANONYMOUS_VIEW_WARNS}
	</div>
	<div class="row_hard"><b>{L_WARNING_EXPIRY}</b></div>
	<div class="row1">
		<input type="radio" name="expire_mode" id="expire_mode_0" value="0" {S_EXPIRE_MODE_0}/> {L_INFINITE}<br/>
		<span class="genmed">{L_INFINITE_EXP}</span><br/>
		<input type="radio" name="expire_mode" id="expire_mode_1" value="1" {S_EXPIRE_MODE_1}/> {L_FIXED_0} <input type="text" name="expire_fixed" size="5" maxlength="4" value="{S_EXPIRE_FIXED}" />{L_FIXED_1}<br/>
		<input type="radio" name="expire_mode" id="expire_mode_2" value="2" {S_EXPIRE_MODE_2}/> {L_MODIFIABLE_0} <input type="text" name="expire_min" size="5" maxlength="4" value="{S_EXPIRE_MIN}" />{L_MODIFIABLE_1}<input type="text" name="expire_max" size="5" maxlength="4" value="{S_EXPIRE_MAX}" />{L_MODIFIABLE_2}<br/>
		<span class="genmed">{L_MODIFIABLE_EXP}</span>
	</div>
	<div class="row_hard"><b>{L_BAN_EXPIRY}</b></div>
	<div class="row1">
		<input type="radio" name="ban_expire_mode" id="ban_expire_mode_0" value="0" {S_BAN_EXPIRE_MODE_0}/> {L_INFINITE}<br/>
		<span class="genmed">{L_INFINITE_BAN_EXP}</span><br/>
		<input type="radio" name="ban_expire_mode" id="ban_expire_mode_1" value="1"{S_BAN_EXPIRE_MODE_1}/> {L_FIXED_0} <input type="text" name="ban_expire_fixed" size="5" maxlength="4" value="{S_BAN_EXPIRE_FIXED}" />{L_FIXED_1}<br/>
		<input type="radio" name="ban_expire_mode" id="ban_expire_mode_2" value="2"{S_BAN_EXPIRE_MODE_2}/> {L_MODIFIABLE_0} <input type="text" name="ban_expire_min" size="5" maxlength="4" value="{S_BAN_EXPIRE_MIN}" />{L_MODIFIABLE_1}<input type="text" name="ban_expire_max" size="5" maxlength="4" value="{S_BAN_EXPIRE_MAX}" />{L_MODIFIABLE_2}<br/>
		<span class="genmed">{L_MODIFIABLE_EXP}</span>
	</div>
	<div class="row_hard"><b>{L_EXPIRED_WARNINGS}</b></div>
	<div class="row1">
		<input type="radio" id="enable_delete_expired_0" name="enable_delete_expired" value="0" {S_DELETE_EXPIRED_CHECKED_0}/> {L_STORE}<br/>
		<input type="radio" id="enable_delete_expired_1" name="enable_delete_expired" value="1" {S_DELETE_EXPIRED_CHECKED_1}/> {L_DELETE_DAYS_0} <input type="text" name="delete_expired" size="5" maxlength="4" value="{S_DELETE_EXPIRED_DAYS}" />{L_DELETE_DAYS_1}
	</div>
	<div class="row1">
		{L_CHECK_RATE_0}<input type="text" name="check_rate" size="5" maxlength="4" value="{S_CHECK_RATE}" />{L_CHECK_RATE_1}<br/>
		<span class="genmed">{L_CHECK_RATE_EXP}</span>
	</div>
	<div class="row1">
		<input type="checkbox" id="enable_ban_warnings" name="enable_ban_warnings" {S_BAN_WARNINGS_CHECKED}/> {L_BAN_WARNINGS_0} 
		<input type="text" name="ban_warnings" size="5" maxlength="4" value="{S_BAN_WARNINGS}" />{L_BAN_WARNINGS_1}<br/>
		<span class="genmed">{L_BAN_WARNINGS_EXP}</span>
	</div>
	<input type="checkbox" id="confirm" name="confirm" value="1" /> {L_CONFIRM}<br/>
	<input type="submit" name="submit" value="{L_SUBMIT}" />
	{S_HIDDEN_FIELDS}
</form>
<br/>
<form action="{S_REPUTATION_ACTION}" method="post">
	<input type="checkbox" name="resync" id="resync" /> {L_RESYNC}<br/>
	<span class="genmed">{L_RESYNC_EXP}</span><br/>
	<input type="submit" name="submit" value="{L_SUBMIT}" />
</form>