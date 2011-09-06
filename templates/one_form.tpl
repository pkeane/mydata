<form method="post" action="data/{$table}/{$id}/{$att}_id">
	<select name="value">
		<option value="">select one:</option>
		{foreach from=$attobjs item=attr}
		<option {if $value == $attr->id}selected{/if} value="{$attr->id}">{$attr->name}</option>
		{/foreach}
	</select>
	<input type="submit" class="update" value="update">
</form>

<form method="get">
	<input type="hidden" value="">
	<input type="submit" class="cancel" value="cancel">
</form>

{if $value}
<form method="delete" action="data/{$table}/{$id}/{$att}_id">
	<input type="submit" class="del" value="delete">
{/if}
