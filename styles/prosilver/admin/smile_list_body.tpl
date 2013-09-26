<div class="navbar"><a href="{U_INDEX}">{L_INDEX}</a>&gt;<a href="{U_ADMIN}">{L_ADMIN}</a>&gt;<a href="{U_ADMIN_INDEX}">{L_ADMIN_INDEX}</a>&gt;{L_SMILEY_TITLE}</div>
<span class="genmed">{L_SMILEY_TEXT}</span>
<div class="catSides">代码 - {L_SMILE} - {L_EMOT} - {L_ACTION}</div>
<!-- BEGIN smiles -->
<div class="{smiles.ROW_CLASS}">
{smiles.CODE} <img src="{smiles.SMILEY_IMG}" alt="" /> {smiles.EMOT}
<a href="{smiles.U_SMILEY_EDIT}">{L_EDIT}</a>
<a href="{smiles.U_SMILEY_DELETE}">{L_DELETE}</a>
</div>
<!-- END smiles -->
{PAGINATION}
<form method="post" action="{S_SMILEY_ACTION}">
{S_HIDDEN_FIELDS}
<input type="submit" name="add" value="{L_SMILEY_ADD}" /><br/>
<input type="submit" name="import_pack" value="{L_IMPORT_PACK}"><br/>
<input type="submit" name="export_pack" value="{L_EXPORT_PACK}">
</form>