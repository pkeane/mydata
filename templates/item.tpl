{extends file="layout.tpl"}

{block name="content"}


<h2>{$title}</h2>
<div class="controls">
	<a href="#" id="edit_on">show editing controls</a>
	<a href="#" id="edit_off">hide editing controls</a>
</div>
<table class="editable">
	<tr>
		{foreach key=attname item=type from=$item->attributes}
		{if 'simple' == $type}
		<th {if $attname == $sort}class="sorted"{/if}><a href="data/{$table}/list?max={$max}&amp;start=1&amp;sort={$attname}&amp;{foreach item=fv key=fk from=$_filters}{$fk}={$fv}&amp;{/foreach}">{$attname}</a></th>
		{elseif 'one' == $type}
		<th {if $attname == $fsort}class="sorted"{/if}><a href="data/{$table}/list?max={$max}&amp;start=1&amp;fsort={$attname}&amp;{foreach item=fv key=fk from=$_filters}{$fk}={$fv}&amp;{/foreach}">{$attname}</a></th>
		{else}
		<th>{$attname}</th>
		{/if}
		{/foreach}
		<th class="control"></th>
	</tr>
	<tr>
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
		{if 'meta' == $type}
		<td class="simple">
			{if $item->$attname}
			{$item->$attname}
			<a href="data/{$table}/{$item->id}/edit/{$attname}/simple_form" class="edit">[edit]</a>
			{else}
			<a href="data/{$table}/{$item->id}/edit/{$attname}/simple_form" class="add">[add]</a>
			{/if}
		</td>
		{/if}
		{if 'view_only' == $type}
		<td class="many">
			<ul>
				{foreach from=$item->$attname item=att}
				<li>
				<a href="data/{$attname}/{$att->id}">{$att->name}</a>
				<a href="data/{$table}/{$item->id}/link/{$attname}/{$att->id}" class="delete">[remove]</a>
				</li>
				{/foreach}
			</ul>
			<a href="data/{$table}/{$item->id}/edit/{$attname}/many_form" class="add">[add]</a>
		</td>
		{/if}
		{if 'one' == $type}
		<td class="one">
			{if $item->$attname->name}
			<a href="data/{$attname}/{$item->$attname->id}">{$item->$attname->name}</a>
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
				<a href="data/{$attname}/{$att->id}">{$att->name}</a>
				<a href="data/{$table}/{$item->id}/link/{$attname}/{$att->id}" class="delete">[remove]</a>
				</li>
				{/foreach}
			</ul>
			<a href="data/{$table}/{$item->id}/edit/{$attname}/many_form" class="add">[add]</a>
		</td>
		{/if}
		{/foreach}
		<td class="control">
			<a href="data/{$table}/{$item->id}">[DELETE]</a>
		</td>
	</tr>
</table>
{/block}
