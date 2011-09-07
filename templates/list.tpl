{extends file="layout.tpl"}

{block name="content"}
<h2>{$title} ({if $num_pages > 1}{$start} - {$end} of {/if}{$count_all})</h2>
{if $num_pages > 1}
<ul class="pager">
	<li>go to page:</li>
	{foreach item=page from=$pages}
	<li><a href="data/{$table}/list?max={$page.max}&amp;start={$page.start}&amp;sort={$sort}" {if $page_num == $page.num}class="onpage"{/if}>{$page.num}</a></li>
	{/foreach}
</ul>
{/if}
<div class="controls">
	<a href="#" id="edit_on">show editing controls</a>
	<a href="#" id="edit_off">hide editing controls</a>
</div>
<table class="editable">
	<tr>
		{foreach key=attname item=type from=$attributes}
		{if $type != 'meta'}
		<th>{$attname}</th>
		{/if}
		{/foreach}
		<th class="control"></th>
	</tr>
	{foreach from=$set item=item}
	<tr>
		{foreach item=type key=attname from=$attributes}
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
	</tr>
	{/foreach}
</table>
{/block}
