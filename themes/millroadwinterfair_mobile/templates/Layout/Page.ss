<% cached 'MobileLayout', ID, LastEdited, SiteConfig.LastEdited %>
			<div id="Navigation" class="vpad">
<% include Navigation %>
			</div>
			<div id="Layout" class="clear">
				<div class="typography">
					$Content
					$Form
					$PageComments
				</div>
<% include Picks %>
			</div>
<% end_cached %>