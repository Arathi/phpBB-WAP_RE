		<div class="forumbg forumbg-table">
			<div class="inner"><span class="corners-top"><span></span></span>
			<table class="table1" cellspacing="1">
			<thead>
			<tr>
				<th class="name">{L_PENDING_MEMBERS}</th>
				<th class="posts">{L_POSTS}</th>
				<th class="active">{L_WEBSITE}</th>
				<th class="info">{L_FROM}</th>
				<th class="joined">{L_SELECT}</th>
			</tr>
			</thead>
			<tbody>
			<!-- BEGIN pending_members_row -->
			<tr class="bg1">
				<td><a href="{member_row.U_VIEWPROFILE}">{pending_members_row.USERNAME}</a></td>
				<td class="posts">{pending_members_row.POSTS}</td>
				<td>{pending_members_row.WWW}</td>
				<td class="info">{pending_members_row.FROM}</td>
				<td><input type="checkbox" name="pending_members[]" value="{pending_members_row.USER_ID}" checked="checked" /></td>
			</tr>
			<!-- END pending_members_row -->
			</tbody>
			</table>
			<span class="corners-bottom"><span></span></span></div>
		</div>
		<fieldset class="display-options">
			<label><input type="submit" name="approve" value="{L_APPROVE_SELECTED}" class="button2" /> <input type="submit" name="deny" value="{L_DENY_SELECTED}" class="button2" /></label>
		</fieldset>
		<hr />