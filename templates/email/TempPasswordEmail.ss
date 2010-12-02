<p><% _t('HELLO', 'Hi') %> $FirstName,</p>

<p>
	Here is your email address, and a new temporary password for logging into the website:
	<ul>
		<li><% _t('EMAIL', 'Email') %>: $Email</li>
		<li><% _t('PASSWORD', 'Password') %>: $CleartextTempPassword</li>
	</ul>
</p>

<p><a href="{$BaseHref}Security/login">Click here to log in</a></p>