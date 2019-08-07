			<% uncached 'Breadcrumbs', ID, SiteLastEdited  %>
			<div class="grid_7 nav horizontal alpha">
				<ul>
				
					<li class="first"><img src="$imageDir/pointy_pointy.png" alt="You are here" Title="You are here" /> &nbsp;</li>
					<li class="first"><a href="<% control Page(home) %>$Link<% end_control %>">Home</a></li>
					<% control BreadcrumbSet %>
					<li><a  class="$LinkingMode" href="$Link">$MenuTitle</a></li>
					<% end_control %>
				</ul>
			</div>
			<% end_cached %>
