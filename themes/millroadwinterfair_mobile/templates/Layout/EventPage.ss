<% cached 'MobileLayoutEvent', SelectedEventID, SiteConfig.EventsLastUpdated, LastEdited, SiteConfig.LastEdited %>
			<div id="Navigation" class="vpad">
<% include Navigation %>
<% include MenuCategories %>
			</div>
			<div class="typography">
<% control GetEvent %>
				<h1>$Title</h1>
			<% if image_url %>
				<p><img src="$image_url" alt="$name" /></p>
			<% end_if %>
			<% control GetPerformances %>
				<p>$when <% if venue %> at $venue<% end_if %></p>
			<% end_control %>
			<% if Content %>
				<p class="eventdesc">$Content</p>
			<% end_if %>
			<% if website %>
				<p>Find out more at <a target="_blank" href="http://$website">$website</a></p>
				<p class="eventdesc">$longdescription</p>
			<% end_if %>
<% end_control %>
			</div>
<% end_cached %>
