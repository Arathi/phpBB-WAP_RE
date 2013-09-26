<div class="navbar"><a href="{U_INDEX}">{L_INDEX}</a>&gt;<a href="{U_ADMIN}">{L_ADMIN}</a>&gt;<a href="{U_ADMIN_INDEX}">{L_ADMIN_INDEX}</a>&gt;浏览所有信息</div>
<span class="genmed">在这里，您可以查看所有存储在数据库中的信息！</span>
<div class="catSides">{L_PRIVMSGS}</div>
<!-- BEGIN pmrow -->
<div class="{pmrow.ROW_CLASS}">
	{pmrow.L_MUNBER}
	{L_FROM}：<a href="{pmrow.FROM_URL}">{pmrow.FROM}</a><br/>
	{L_TO}：<a href="{pmrow.TO_URL}">{pmrow.TO}</a><br/>
	{L_SUBJ}：</b><br/>{pmrow.SUBJ}<br/>
	{L_DATE}：{pmrow.DATE}<br/>
	{L_IP}：{pmrow.IP}<br/>
	类型：{pmrow.TYPE}<br/>
	内容：{pmrow.MESSAGE}
</div>
<!-- END pmrow -->
{PAGINATION}