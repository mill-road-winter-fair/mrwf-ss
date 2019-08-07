<% cached 'MobilePicks', ID, SiteLastEdited %>
	<% if PickObjects %>
			<h2>You may also be interested in these related topics...</h2>
				<% control PickObjects %>
<% include PickObject %>
				<% end_control %>
	<% else_if Summaries %>
				<h2>See also...</h2>
				<% control Summaries %>
<% include PickPage %>
				<% end_control %>
	<% end_if %>
<% end_cached %>
