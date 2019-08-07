<% cached 'MobileLayoutCategory', SelectedCategoryID, SiteConfig.EventsLastUpdated, LastEdited, SiteConfig.LastEdited %>
			<div id="Navigation" class="vpad">
<% include Navigation %>
<% include MenuCategories %>
			</div>
			<div class="typography">
				<h1>Events tagged: $SelectedCategoryName</h1>
				<% cached 'MobileCategories', SelectedCategoryID, SiteConfig.EventsLastUpdated %>
				<% control GetEvents %>
					<p><a href="$Link">$Title</a></p>
				<% end_control %>
				<% end_cached %>
			</div>
<% end_cached %>
