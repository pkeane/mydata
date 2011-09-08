{extends file="layout.tpl"}

{block name="content"}

{foreach item=filterset key=filter from=$filters}
<div class="filter">
<h3>filter by {$filter}</h3>
<form action="data/{$table}/list">
	<input type="hidden" name="start" value="1">
	<input type="hidden" name="max" value="{$max}">
	<input type="hidden" name="sort" value="{$sort}">
	<input type="hidden" name="fsort" value="{$fsort}">
	{foreach item=fv key=fk from=$_filters}
	<input type="hidden" name="{$fk}" value="{$fv}">
	{/foreach}
	<select name="{$filter}">
		<option value="">select one:</option>
		{foreach from=$filterset item=attobj}
		<option {if $_filters.$filter == $attobj->id}selected{/if} value="{$attobj->id}">{$attobj->name}</option>
		{/foreach}
		<option value="">[clear filter]</option>
	</select>
	<input type="submit" value="go">
</form>
</div>
{/foreach}
<h3>clear filters</h3>
<form action="data/{$table}/list">
	<input type="hidden" name="start" value="1">
	<input type="hidden" name="max" value="{$max}">
	<input type="hidden" name="sort" value="">
	<input type="hidden" name="fsort" value="">
	{foreach item=fv key=fk from=$_filters}
	<input type="hidden" name="{$fk}" value="">
	{/foreach}
	<input type="submit" value="clear filters">
</form>
<div class="clear">

<h2>{$title} ({if $num_pages > 1}{$start} - {$end} of {/if}{$count_all})</h2>
{if $num_pages > 1}
<ul class="pager">
	<li>go to page:</li>
	{foreach item=page from=$pages}
	<li><a href="data/{$table}/list?max={$page.max}&amp;start={$page.start}&amp;sort={$sort}&amp;fsort={$fsort}&amp;{foreach item=fv key=fk from=$_filters}{$fk}={$fv}&amp;{/foreach}" {if $page_num == $page.num}class="onpage"{/if}>{$page.num}</a></li>
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
		{if 'simple' == $type}
		<th {if $attname == $sort}class="sorted"{/if}><a href="data/{$table}/list?max={$max}&amp;start=1&amp;sort={$attname}&amp;{foreach item=fv key=fk from=$_filters}{$fk}={$fv}&amp;{/foreach}">{$attname}</a></th>
		{elseif 'one' == $type}
		<th {if $attname == $fsort}class="sorted"{/if}><a href="data/{$table}/list?max={$max}&amp;start=1&amp;fsort={$attname}&amp;{foreach item=fv key=fk from=$_filters}{$fk}={$fv}&amp;{/foreach}">{$attname}</a></th>
		{else}
		<th>{$attname}</th>
		{/if}
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
