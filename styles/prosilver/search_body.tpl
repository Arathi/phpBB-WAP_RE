<div class="navbar"><a href="{U_INDEX}">{L_INDEX}</a>&gt;搜索</div>
<div class="row1">-<a href="{U_SEARCH_USER}">{L_SEARCH_USER}</a></div>
<form action="{S_SEARCH_ACTION}" method="POST">
<div class="catSides">搜索</div>
<div class="row1">关键词搜索:<br/>
<input type="text" name="search_keywords"/>
</div>
<div class="row1">
<input type="radio" name="search_terms" value="any" checked="checked" /> {L_SEARCH_ANY_TERMS}<br />
<input type="radio" name="search_terms" value="all" /> {L_SEARCH_ALL_TERMS}
</div>
<div class="row1">{L_SEARCH_AUTHOR}:<br/>
<span class="genmed">{L_SEARCH_AUTHOR_EXPLAIN}</span><br/>
<input type="text" name="search_author" />
</div>
<div class="row1">
{L_CATEGORY}:<br/>
<select name="search_cat">{S_CATEGORY_OPTIONS}</select>
</div>
<div class="row1">{L_FORUM}:<br/>
<select name="search_forum">{S_FORUM_OPTIONS}</select>
</div>
<div class="row1">{L_SORT_BY}:<br/>
<select name="sort_by">{S_SORT_OPTIONS}</select><br/>
<input type="radio" name="sort_dir" value="ASC" /> {L_SORT_ASCENDING}<br />
<input type="radio" name="sort_dir" value="DESC" checked="checked" /> {L_SORT_DESCENDING}
</div>
<div class="row1">
{L_DISPLAY_RESULTS}:<br/>
<input type="radio" name="show_results" value="posts" /> {L_POSTS}<br/>
<input type="radio" name="show_results" value="topics" checked="checked" /> {L_TOPICS}
</div>
{S_HIDDEN_FIELDS}<input class="subbutton" type="submit" value="{L_SEARCH}" />
</form>