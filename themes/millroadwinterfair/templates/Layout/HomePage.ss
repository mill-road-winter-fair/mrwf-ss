<% cached 'Layout', ID, LastEdited, SiteConfig.LastEdited %>
<% include Breadcrumbs %>
	<div class="container_12">
<% include Menu2 %>
		<div class="grid_10 main">
<% if Intro %>
			<div id="intro" class="grid_10 mu">
$Intro
			</div>
<% end_if %>
<% include Slideshow %>

<% cached 'Picks', ID, SiteLastEdited %>
			<div class="grid_10 mu">
				<% if PickObjects %>
				<div class="grid_6 alpha">
					<% control PickObjects %>
<% include PickObject2 %>
					<% end_control %>
				</div>
				<% end_if %>
				<div class="grid_4 omega" style="padding-top: 10px;">
					<a class="twitter-timeline" href="https://twitter.com/MillRoadFair" data-widget-id="375278121773060097">Tweets by @MillRoadFair</a>
					<script>!function(d,s,id){var js,fjs=d.getElementsByTagName(s)[0],p=/^http:/.test(d.location)?'http':'https';if(!d.getElementById(id)){js=d.createElement(s);js.id=id;js.src=p+"://platform.twitter.com/widgets.js";fjs.parentNode.insertBefore(js,fjs);}}(document,"script","twitter-wjs");</script>
				</div>
			</div>
<% end_cached %>
		</div>
	</div>
<% end_cached %>
