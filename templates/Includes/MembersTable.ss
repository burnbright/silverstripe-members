<table class="table memberslist">
	<thead>
		<tr>
			<th>First Name</th>
			<th>Last Name</th>
		</tr>
	</thead>
	<tbody class="list">
		<% loop Me %>
			<tr>
				<td class="firstname"><a href="$ProfileLink">$FirstName</a></td>
				<td class="surname"><a href="$ProfileLink">$Surname</a></td>
			</tr>
		<% end_loop %>
	</tbody>
</table>