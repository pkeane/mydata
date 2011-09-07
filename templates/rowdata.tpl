{foreach item=type key=attname from=$item->attributes}
{if 'simple' == $type}
<td class="simple">
	{if $item->$attname}
	{$item->$attname}
	<a href="data/{$table}/{$item->id}/edit/{$attname}/simple_form" class="edit">[edit]</a>
	{else}
	<a href="data/{$table}/{$item->id}/edit/{$attname}/simple_form" class="add">[add]</a>
	{/if}
</td>
{/if}
{if 'one' == $type}
<td class="one">
	{if $item->$attname->name}
	{$item->$attname->name}
	<a href="data/{$table}/{$item->id}/edit/{$attname}/one_form" class="edit">[edit]</a>
	{else}
	<a href="data/{$table}/{$item->id}/edit/{$attname}/one_form" class="add">[add]</a>
	{/if}
</td>
{/if}
{if 'many' == $type}
<td class="many">
	<ul>
		{foreach from=$item->$attname item=att}
		<li>
		{$att->name}
		<a href="data/{$table}/{$item->id}/link/{$attname}/{$att->id}" class="delete">[remove]</a>
		</li>
		{/foreach}
	</ul>
	<a href="data/{$table}/{$item->id}/edit/{$attname}/many_form" class="add">[add]</a>
</td>
{/if}
{/foreach}
<td class="control">
	<a href="data/{$table}/{$item->id}">[VIEW]</a>
</td>
