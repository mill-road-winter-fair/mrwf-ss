<% require themedCSS(dynamic_content_page) %>
<% require javascript(themes/millroadwinterfair/js/jquery-1.11.3.min.js) %>
<% require javascript(themes/millroadwinterfair/js/masonry.min.js) %>
<% require javascript(themes/millroadwinterfair/js/dynamic_common.js) %>
<% require javascript(themes/millroadwinterfair/js/dynamic_events_page.js) %>
<% include Breadcrumbs %>
	<div class="container_12">
<% include Menu2 %>
		<div class="grid_10 main">
			<div id="Content" class="grid_10 typography mu">
				<div id="page">
					<div class="visible_nav" id="sub_nav">
						<div class="cat_nav" data-key="morning">Morning</div>
						<div class="cat_nav" data-key="noon">Noon</div>
						<div class="cat_nav" data-key="afternoon">Afternoon</div>		
						<div class="cat_nav" data-key="allday">All Day</div>
					</div>
					<div id="blurb">
$Content
					</div>
  				</div>
			</div>
		</div>
	</div>
