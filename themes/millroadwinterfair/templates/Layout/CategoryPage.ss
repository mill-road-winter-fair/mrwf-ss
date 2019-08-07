<% cached 'LayoutCategory', SelectedCategoryID, LastEdited, SiteConfig.LastEdited %>
<% include Breadcrumbs %>
	<div class="container_12">
<% include MenuCategories %>
		<div class="grid_10 main">
			<div id="Content" class="grid_10 typography mu">
				<h1>$SelectedCategoryName</h1>
				<%--<h3>2014 listings will be online soon. Information on this page is for 2013.</h3>--%>
<% uncached %>
<% control GetEvents %>
				<p><a href="$Link">$Title</a></p>
<% end_control %>
<% end_uncached %>
			</div>
		</div>
	</div>
<% end_cached %>
