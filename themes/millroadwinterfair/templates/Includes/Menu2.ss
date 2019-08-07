<% cached "Menu2", ID, SiteLastEdited %>
		<div class="grid_2">
			<% control Level(1) %>
				<% if Children %>
			<ul id="nav2" class="nav">
					<% control Children %>
						<% if Children %> 
				<li class="parent"><a class="$LinkingMode" href="$Link">$MenuTitle</a>
					<ul>
							<% control Children %>
						<li><a class="$LinkingMode" href="$Link">$MenuTitle</a></li>
							<% end_control %>
					</ul>
				</li>
						<% else %>
				<li><a class="$LinkingMode" href="$Link">$MenuTitle</a></li>
						<% end_if %>
				<% end_control %>
			</ul>
			<% else %>
			&nbsp;
			<% end_if %>
			<% end_control %>
		</div>
<% end_cached %>
