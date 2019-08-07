<% cached 'MobileLayout', ID, LastEdited, SiteConfig.LastEdited %>
			<div id="Navigation" class="vpad">
<% include Navigation %>
			</div>
<% if Content %>
			<div id="Layout" class="clear">
				<div class="typography">
$Content
				</div>
			</div>
<% end_if %>
<% end_cached %>
<% uncached %>
<% if SearchText %>
		<% if Results %>
		<h1>Found $Results.Count pages matching '$Query'</h1>
		<ul id="SearchResults" class="nav">
			<% control Results %>
			<li class="divide">
				<% if MenuTitle %>
				<h2><a class="searchResultHeader" href="$Link">$MenuTitle</a></h2>
				<% else %>
				<h2><a class="searchResultHeader" href="$Link">$Title</a></h2>
				<% end_if %>
				<% if Content %>
					<p>$SearchSummary</p>
				<% end_if %>
				<a class="readMoreLink" href="$Link" title="Read more about &quot;{$Title}&quot;">Read more about &quot;{$Title}&quot;...</a>
			</li>
			<% end_control %>
		</ul>
		<% else %>
		<h1>Sorry, I can't find anything matching '$Query'</h1>
		<% end_if %>
<% end_if %>
<% end_cached %>
