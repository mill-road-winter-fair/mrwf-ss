<% cached 'LayoutEvent', ID, LastEdited, SiteConfig.LastEdited %>
<% include Breadcrumbs %>
	<div class="container_12">
<!--<% include MenuCategories %>-->
		<div class="grid_10 main">
			<div id="Content" class="grid_10 typography mu">

<% uncached %>
<% control GetEvent %>
				<h1>$Title</h1>
			<% if image_url %>
				<p><img src="$image_url" alt="$name" /></p>
			<% end_if %>
			<% control GetPerformances %>
				<p>$when <% if venue %> at $venue<% end_if %></p>
			<% end_control %>
			<% if Content %>
				<%--<h1>2014 listings will be online soon. Information on this page is for 2013.</h1>--%>
				<p class="eventdesc">$Content</p>
			<% end_if %>
			<% if website %>
				<p>Find out more at <a target="_blank" href="http://$website">$website</a></p>
				<p class="eventdesc">$longdescription</p>
			<% end_if %>
<% end_control %>
<% end_uncached %>
			</div>
		</div>
	</div>
<% end_cached %>
