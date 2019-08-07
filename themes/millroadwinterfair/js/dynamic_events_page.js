$(document).ready(function() {

	var rawBlurb = $("#blurb").html();

	function do_layout(blurb, cat) {
		$.each(cat.data, function (index, shop) {

			var contents = "<div class=\"shop_elem\"><div class=\"shop_content\"><div class=\"shop_header\">";
			if (shop.url) {
				var protocol = shop.url.startsWith("https://") ? "https://": "http://";
				var url = shop.url.startsWith(protocol) ? shop.url.substring(protocol.length) : shop.url;
				contents += "<a target=\"_blank\" href=\"" + protocol + url + "\">";
			}
			contents += shop.name;
			if (shop.url) {
				contents += "</a>";
			}
			if (shop.from) {
				if (shop.to) {
					contents += "<p style=\"padding-top:10px;\">" + shop.from + " - " + shop.to + "</p>";
				}
				else {
					contents += "<p style=\"padding-top:10px;\">Starting " + shop.from + "</p>";
				}
			}
			contents += "</div><div class=\"shop_location\">" + shop.location + "</div><p>" + shop.description + "</p></div></div>";
			var thing = $(contents);
			blurb.append(thing);
		});			
		blurb.masonry({
		  // options
		  itemSelector: '.shop_elem',
		  columnWidth: 230
		});
	}

	function get_data (dataset) {
		//var spreadsheetID = "1R9VGdWmTecqviQ179A-QOOaWl3v1zm2Srgue09q0mqM";
		// process data
		var url = "https://spreadsheets.google.com/feeds/list/" + spreadsheetID + "/od6/public/values?alt=json";
		console.log(url);
		$.getJSON(url, function(data) {

			var entry = data.feed.entry;
			 
			$(entry).each(function(){
				var item = {
					name: this.gsx$title.$t,
					description: this.gsx$description.$t,
					url: this.gsx$url.$t,
					location: map_location(this.gsx$location.$t)
				};
				
				if (this.gsx$to.$t) {
					item.to = this.gsx$to.$t;
				}
				if (this.gsx$from.$t) {
					item.from = this.gsx$from.$t;
				}
				
				for (var property in dataset) {
					if (dataset.hasOwnProperty(property)) {
						if (filterColumn(this, dataset[property].key)) {
							dataset[property].data.push(item);
						}
					}
				}
			});

			// now that's done, set up the click listeners
				
			$(".cat_nav").click(function(event) {
				var blurb = $("#blurb");
				blurb.html(rawBlurb);
				var key = $(this).attr('data-key');
				if (dataset.hasOwnProperty(key)) {
					blurb.html("");
					do_layout(blurb, dataset[key]);
					update_nav($(this));
				}
			});
		});
	}

	var eventsData = {
		name: "Timetabled Events",
		morning: {
			name: "Morning",
			key: "morning",
			data: []
		},
		noon: {
			name: "Mid-day",
			key: "midday",
			data: []
		},
		afternoon: {
			name: "Later On",
			key: "later",
			data: []
		},
		allday: {
			name: "All Day",
			key: "allday",
			data: []
		}
	};
	// query for data
	get_data(eventsData);
});
