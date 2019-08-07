<!DOCTYPE html>
<html lang="en">
	<head>$MobileIncludes
		<% base_tag %>
		<title><% if MetaTitle %>$MetaTitle<% else %>$Title<% end_if %> &raquo; $SiteConfig.Title</title>
		$MetaTags(false)
		<meta name="viewport" content="width=device-width; initial-scale=1.0; maximum-scale=1.0;" />
		<link rel="shortcut icon" href="/favicon.ico" />

	</head>
	<body>
		<div id="Container">
			<div id="Header">
				<a href="$Page(home).Link"><img src="assets/img/mobile/logo.png" /></a>
			</div>
			<div>
				<form id="SearchForm_SearchForm" action="$page(search).Link/SearchForm" method="get" enctype="application/x-www-form-urlencoded">
					<fieldset>
						<table width="100%" border="0">
							<tr>
								<td><input type="text" class="text nolabel" id="SearchForm_SearchForm_Search" name="Search" value="" /></td>
								<td><input class="action" id="SearchForm_SearchForm_action_results" type="submit" name="action_results" value="Search" title="Search" /></td>
							</tr>
						</table>
					</fieldset>
				</form>
			</div>
$Layout
<% include Footer %>
		</div>
	</body>
</html>