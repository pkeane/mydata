
<ul>
	{foreach from=$item->$att item=attr}
	<li>
	{$attr->name}
	<a href="data/{$table}/{$item->id}/link/{$att}/{$attr->id}" class="delete">[remove]</a>
	</li>
	{/foreach}
</ul>
<form method="post" action="data/{$table}/{$id}/{$att}">
	<input type="text" name="value">
	<input type="submit" class="update" value="update">
</form>

<form method="get">
	<input type="hidden" value="">
	<input type="submit" class="cancel" value="cancel">
</form>
