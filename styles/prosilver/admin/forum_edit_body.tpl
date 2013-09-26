<div class="navbar"><a href="{U_INDEX}">{L_INDEX}</a>&gt;<a href="{U_ADMIN}">{L_ADMIN}</a>&gt;<a href="{U_ADMIN_INDEX}">{L_ADMIN_INDEX}</a>&gt;<a href="{U_ADMIN_FORUMS}">论坛列表</a>&gt;{L_FORUM_TITLE}</div>
<span class="genmed">{L_FORUM_EXPLAIN}</span>
<form action="{S_FORUM_ACTION}" method="post">
<div class="catSides">{L_FORUM_SETTINGS}</div>
<div class="row1">
{L_FORUM_NAME}:<br/>
<input type="text" name="forumname" value="{FORUM_NAME}" />
</div>
<div class="row1">
{L_ICON}:<br />
<input type="text" size="25" name="forumicon" value="{F_ICON}" class="post" />
</div>
<div class="row1">
{L_CATEGORY}:<br/>
<select name="c">{S_CAT_LIST}</select>
</div>
<div class="row1">
{L_FORUM_STATUS}:<br/>
<select name="forumstatus">{S_STATUS_LIST}</select>
</div>
<div class="row1">
{L_AUTO_PRUNE}:<br/>
{L_ENABLED} <input type="checkbox" name="prune_enable" value="1" {S_PRUNE_ENABLED} /><br/>
{L_PRUNE_DAYS} <input type="text" name="prune_days" value="{PRUNE_DAYS}" size="5" /> {L_DAYS}<br/>
{L_PRUNE_FREQ} <input type="text" name="prune_freq" value="{PRUNE_FREQ}" size="5" /> {L_DAYS}
</div>
<div class="row1">
{L_POSTCOUNT}:<br/>
{L_ENABLED} <input type="checkbox" name="forum_postcount" value="1" {S_FORUM_POSTCOUNT} />
</div>
<div class="row1">
当用户发表一个帖子可以得到多少积分（0 表示没有）:<br/>
<input type="text" name="forum_money" value="{FORUM_MONEY}" size="5" />
</div>
{S_HIDDEN_FIELDS}
<input type="submit" name="submit" value="{S_SUBMIT_VALUE}" />
</form>