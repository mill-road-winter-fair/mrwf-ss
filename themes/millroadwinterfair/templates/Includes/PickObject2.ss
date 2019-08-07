				<div class="grid_3{$GridPad(2)}">
					<% if Picture %><a class="feature-row" href="$Target.Link">$Picture.setWidth(220)</a><% end_if %>
					<h3><a href="$LinkTarget.Link">$Description</a></h3>
					<div class="grid_3 mu typography">
						$LinkTarget.Summary
					</div>
					<p><a href="$LinkTarget.Link"><% if LinkText %>$LinkText<% else %>Read more..<% end_if %></a></h3>
				</div>
