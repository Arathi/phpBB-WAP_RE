<div class="navbar"><a href="{U_INDEX}">{L_INDEX}</a>&gt;<a href="{U_ADMIN}">{L_ADMIN}</a>&gt;<a href="{U_ADMIN_INDEX}">{L_ADMIN_INDEX}</a>&gt;{L_CONTROL_PANEL_TITLE}</div>
<span class="genmed">{L_CONTROL_PANEL_EXPLAIN}</span>
<form method="post" action="{S_MODE_ACTION}">
<div class="row1">{L_VIEW}: {S_VIEW_SELECT} <input type="submit" name="submit" value="{L_SUBMIT}" /></div>
<div class="catSides">
{L_ATTACH_SEARCH_QUERY}
</div>
<div class="row1">
{L_FILENAME}:<br/>
<input type="text" name="search_keyword_fname" size="20" />
</div>
<div class="row1">
{L_COMMENT}:<br/>
<input type="text" name="search_keyword_comment" size="20" />
</div>
<div class="row1">
{L_SEARCH_AUTHOR}:<br/>
<input type="text" name="search_author" size="20" />
</div>
<div class="row1">
{L_SIZE_SMALLER_THAN}:<br/>
<input type="text" name="search_size_smaller" size="10" />
</div>
<div class="row1">
{L_SIZE_GREATER_THAN}:<br/>
<input type="text" name="search_size_greater" size="10" />
</div>
<div class="row1">
{L_COUNT_SMALLER_THAN}:<br/>
<input type="text" name="search_count_smaller" size="10" />
</div>
<div class="row1">
{L_COUNT_GREATER_THAN}:<br/>
<input type="text" name="search_count_greater" size="10" />
</div>
<div class="row1">
{L_MORE_DAYS_OLD}:<br/>
<input type="text" name="search_days_greater" size="10" />
</div>
<div class="catSides">
{L_SEARCH_OPTIONS}
</div>
<div class="row1">
{L_FORUM}:<br/>
<select name="search_forum">{S_FORUM_OPTIONS}</select>
</div>
<div class="row1">
{L_SORT_BY}:<br/>
{S_SORT_OPTIONS}
</div>
<div class="row1">
排序方式：<br />
{S_SORT_ORDER}
</div>
{S_HIDDEN_FIELDS}
<input type="submit" name="search" value="{L_SEARCH}" />
</form>