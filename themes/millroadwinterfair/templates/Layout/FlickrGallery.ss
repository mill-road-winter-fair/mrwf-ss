<% cached 'Layout', ID, LastEdited, SiteConfig.LastEdited %>
<% include Breadcrumbs %>
	<div class="container_12">
<% include Menu2 %>
		<div class="grid_10 main">
<% include Slideshow %>
			<div id="Content" class="grid_10 typography mu">
<%-- Gallery is uncached because content is paged --%>
<% uncached %>
$Content
$Form
<% end_uncached %>
			</div>
<% include Picks %>
		</div>
	</div>
<% end_cached %>
