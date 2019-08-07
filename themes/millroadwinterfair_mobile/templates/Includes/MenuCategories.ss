<% cached "MobileMenuCategories", SiteConfig.EventsLastUpdated, SiteLastEdited %>
			<ul class="nav">
			<% control Categories %>
				<li><a class="$LinkingMode<% if First %> first<% end_if %>" href="$Link">$Title</a></li>
			<% end_control %>
			</ul>
<% end_cached %>
