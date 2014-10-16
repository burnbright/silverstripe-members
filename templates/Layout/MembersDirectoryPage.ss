$Content
$Form
<% if Members %>
	<table class="table memberslist">
		<thead>
			<th>First Name</th>
			<th>Last Name</th>
		</thead>
		<tbody>
			<% loop Members %>
				<tr>
					<td>$FirstName</td>
					<td>$Surname</td>
				</tr>
			<% end_loop %>
		</tbody>
	</table>
<% end_if %>