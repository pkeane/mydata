<form>
	<select name="sss">
		{foreach from=$attobjs item=att}
		<option>{$att->name}</option>
		{/foreach}
	<input type="submit" value="update">
</form>

