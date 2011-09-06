{extends file="layout.tpl"}
{block name="title"}Login{/block} 

{block name="content"}
<div class="list" id="browse">
	<h1>Please Login:</h1>
	<form id="loginForm" action="login" method="post">
		<p>
		<label for="username-input">username:</label>
		<input type="text" id="username-input" name="username">
		<input type="hidden" value="{$target}" name="target">
		</p>
		<p>
		<label for="password-input">password:</label>
		<input type="password" id="password-input" name="password">
		</p>
		<p>
		<input type="submit" value="login">
		</p>
	</form>
</div>
{/block}
