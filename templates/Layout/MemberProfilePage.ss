<h1 class="pagetile">$Member.Name</h1>
<% if Member %>
	<div class="memberdetails">
		<% with Member %>
			<div class="memberdetails_profileimage">
				$Image.ResizedImage(300,300)
			</div>
			<% if Created %><p>Member Since : $Created.Nice</p><% end_if %>
			<% if Email %><p>Email: $Email</p><% end_if %>
		<% end_with %>
	</div>
<% end_if %>