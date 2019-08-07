<% cached 'Header', ID, LastEdited, SiteConfig.LastEdited %><!DOCTYPE HTML>
<head>$SlideshowIncludes
	<% base_tag %>
	$MetaTags
<% cached 'GoogleAnalytics', IsGoogleAnalytics %><% if IsGoogleAnalytics %>
<script type="text/javascript">
  var _gaq = _gaq || [];
  _gaq.push(['_setAccount', '$SiteConfig.googleAnalytics']);
<% if SiteConfig.domain %>  _gaq.push(['_setDomainName', '.$SiteConfig.domain']);<% end_if %>
  _gaq.push(['_trackPageview']);
  (function() {
    var ga = document.createElement('script'); ga.type = 'text/javascript'; ga.async = true;
    ga.src = ('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js';
    var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(ga, s);
  })();
</script><% end_if %><% end_cached %>
<link href='http://fonts.googleapis.com/css?family=Droid+Serif|Droid+Sans' rel='stylesheet' type='text/css'>
</head>
<body>
<%-- include CacheStats --%>
	<%-- header --%>
	<div class="container_12 padabove">
		<div class="grid_12">
			<div class="grid_6 alpha">
				<a href="/">
					<img src="$imageDir/logo.png" alt="Mill Road Winter Fair" />
				</a>
			</div>
			<div class="grid_4" style="padding-top:64px;">
				<img src="$imageDir/date-time.png" alt="Saturday 2nd December 2017, 10:30am to 4:30pm" />
			</div>
			<div class="grid_2 omega">
<form action="https://www.paypal.com/cgi-bin/webscr" method="post" target="_top">
<input type="hidden" name="cmd" value="_s-xclick">
<input type="hidden" name="hosted_button_id" value="RNL9TBYJKM9KC">
<input type="image" src="https://www.paypalobjects.com/en_US/i/btn/btn_donateCC_LG.gif" border="0" name="submit" alt="PayPal - The safer, easier way to pay online!">
<img alt="" border="0" src="https://www.paypalobjects.com/en_GB/i/scr/pixel.gif" width="1" height="1">
</form>
			</div>
		</div>
	</div>
	<div class="container_12">
		<div class="push_2 grid_10">
			<ul id="nav1" class="nav horizontal">
				<% cached 'MainMenu', ID, SiteLastEdited %>
				<% control Menu(1) %>
				<li<% if first %> class="first"<% end_if %>><a class="$LinkingMode" href="$Link" title="Go to the $Title page"><% if Button %>$Button<% else %>$MenuTitle<% end_if %></a></li>
				<% end_control %>
				<% end_cached %>
			</ul>
		</div>
	</div>
<% end_cached %>
$Layout
<% cached 'Footer', SiteLastEdited, SiteConfig.LastEdited %>
<%-- include CacheStats --%>
	<div id="footer-top">
		<div class="container_12">
			<div class="grid_12">
				&nbsp;
			</div>
		</div>
	</div>
	<div id="footer">
		<div class="container_12">
			<% if SiteConfig.Footer %>
			<div class="push_2 grid_10 typography">
				$SiteConfig.Footer
			</div>
			<% end_if %>			
			<div class="push_2 grid_10 <% if SiteConfig.Footer %>divide<% else %>padabove<% end_if %> footer-snow">
				<div class="grid_2 alpha">
					<ul class="nav">
						<% control ChildrenOf(footer-menu-left) %>
						<li><a href="$Link">$MenuTitle</a></li>						
						<% end_control %>
					</ul>
				</div>
				<div class="grid_2">
					<ul class="nav">
						<% control ChildrenOf(footer-menu-right) %>
						<li><a href="$Link">$MenuTitle</a></li>
						<% end_control %>
					</ul>
				</div>
				<div class="grid_2">
					<h2>Mill Road Social</h2>
					<ul class="nav">
						<%-- <li><a href="$Page(subscribe).Link"><img src="$imageDir/envelope_icon.png" alt="" />Subscribe to our email list</a></li>--%>
						<li><a target="_blank" href="http://twitter.com/#!/millroadfair"><img src="$imageDir/twitter_icon.png" alt="" />Follow us on twitter</a></li>
						<li><a target="_blank" href="https://www.facebook.com/MillRoadWinterFair"><img src="$imageDir/facebook_icon.png" alt="" />Follow us on facebook</a></li>
					</ul>
				</div>
				<div class="grid_3 typography credits">
					$SiteConfig.Credits
				</div>
			</div>
		</div>
		<div class="container_12">
			<div class="push_2 grid_10 divide typography">
				<p>All copyright Mill Road Winter Fair &copy; 2005-$Now.Year</p>
			</div>
		</div>
	</div>
<% end_cached %>
$SilverStripeNavigator
</body>
</html>
