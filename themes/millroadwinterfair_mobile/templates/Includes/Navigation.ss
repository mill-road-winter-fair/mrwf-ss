				<ul class="nav">
					<% control Menu(1) %>
					<li><a class="$LinkingMode<% if First %> first<% end_if %>" href="$Link" title="Go to the $Title.XML page" >$MenuTitle.XML</a></li> 
					<% end_control %>
				</ul>

				<% if Level(1) %>
				<ul class="nav">
					<% control Level(1) %>
					<% if Children %>
					<% control Children %>
					<li><a class="$LinkingMode<% if First %> first<% end_if %>" href="$Link">$MenuTitle</a></li>
					<% end_control %>
					<% end_if %>
					<% end_control %>
				</ul>
				<% end_if %>

