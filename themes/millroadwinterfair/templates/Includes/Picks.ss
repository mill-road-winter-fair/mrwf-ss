<% cached 'Picks', ID, SiteLastEdited %>
	<% if PickObjects %>
			<div class="grid_10 mu divide">
				<h2>You may also be interested in these related topics...</h2>
				<% control PickObjects %>
<% include PickObject %>
				<% end_control %>
			</div>
	<% else_if Summaries %>
			<div class="grid_10 mu divide">
				<h2>See also...</h2>
				<% control Summaries %>
<% include PickPage %>
				<% end_control %>
			</div>
	<% end_if %>
<% end_cached %>
