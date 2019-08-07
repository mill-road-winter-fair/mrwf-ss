<% cached 'MobileLayout', ID, LastEdited, SiteConfig.LastEdited %>
			<div id="Navigation" class="vpad">
<% include Navigation %>
			</div>
			<div id="Layout" class="clear">
				<% if Intro %>
				<div id="intro">
$Intro
				</div>
				<% end_if %>
			</div>
	<% if PickObjects %>
		<% control PickObjects %>
			<% if IsTwitter %>
			<div class="divide">
				<h3><a href="http://twitter.com/$Top.TwitterUsername">$Description</a></h3>
				<% if Top.UserTimeLine %>
				<ul class="TwitterTimeLine">
					<% control Top.UserTimeLine %>
					<li>
						<% if Html %>
						$Html
						<% else %>
						<a href="http://twitter.com/$Top.TwitterUsername/statuses/$id">$Text</a>
						<% end_if %>
						<br/>$Time
					</li>
					<% end_control %>
				</ul>
				<% else %>
				<h4>There are no recent tweets</h4>
				<% end_if %>
				<p><a href="http://twitter.com/$Top.TwitterUsername"><% if LinkText %>$LinkText<% else %>Read more..<% end_if %></a></h3>
			</div>
			<% else %>
<% include PickObject %>
			<% end_if %>
		<% end_control %>
	<% end_if %>
<% end_cached %>
