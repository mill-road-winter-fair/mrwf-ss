<% require themedCSS(dynamic_content_page) %>
<% require javascript(themes/millroadwinterfair/js/jquery-1.11.3.min.js) %>
<% require javascript(themes/millroadwinterfair/js/masonry.min.js) %>
<% require javascript(themes/millroadwinterfair/js/dynamic_common.js) %>
<% require javascript(themes/millroadwinterfair/js/dynamic_food_stalls_page.js) %>
<% include Breadcrumbs %>
	<div class="container_12">
<% include Menu2 %>
		<div class="grid_10 main">
			<div id="Content" class="grid_10 typography mu">
				<div id="page">
					<div class="visible_nav" id="sub_nav">
						<div class="cat_nav" data-key="drinks">Drinks</div>
						<div class="cat_nav" data-key="treats">Treats</div>
						<div class="cat_nav" data-key="gourmet">Speciality / Gourmet</div>		
						<div class="cat_nav" data-key="streetfood">Street Food</div>
					</div>
					<div id="blurb">
				<%--<h1>2014 listings will be online soon. Information on this page is for 2013.</h1>--%>
$Content
					</div>
  				</div>
			</div>
		</div>
	</div>
