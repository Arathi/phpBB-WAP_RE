<div class="navbar"><a href="{U_INDEX}">{L_INDEX}</a>&gt;<a href="{U_ADMIN}">{L_ADMIN}</a>&gt;<a href="{U_MODS_ADMIN}">{L_MODS_ADMIN}</a>&gt;<a href="{U_ADMIN_BANK}">虚拟银行</a>&gt;修改</div>

<!-- BEGIN message -->
{MESSAGE}<br/>
<!-- END message -->

<!-- BEGIN change_form -->
<form action="{CONFIG_ACTION}" method="post">
    旧ID：<input type='text' name='olduid' value='' /><br/>
    新ID：<input type='text' name='newuid' value='' /><br/>
    <input type='submit' name='submit' value='修改'>
</form>
<!-- END -->
