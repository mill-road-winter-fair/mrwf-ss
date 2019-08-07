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
			contents += "</div><div class=\"shop_location\">" + shop.location + "</div>";
			if (shop.description) {
				contents += "<p>" + shop.description + "</p>";
			} 
			contents += "</div>";
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
		// process data
		var url = "https://spreadsheets.google.com/feeds/list/" + spreadsheetID + "/od6/public/values?alt=json";
		// process the stalls data into categories
		$.getJSON(url, function(data) {
	
			var entry = data.feed.entry;
			 
			$(entry).each(function(){
				var item = {
					name: this.gsx$name.$t,
					url: this.gsx$url.$t,
					location: map_location(this.gsx$location.$t),
				};

				if (this.gsx$description) {
					item.description = this.gsx$description.$t;
				}

				if (!item.location) return;

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

	var foodData = {
		name: "Food Stalls",
		drinks : {
			name: "Drinks",
			key: "drinks",
			data: []
		},
		treats : {
			name: "Treats",
			key: "treats",
			data: []
		},
		gourmet : {
			name: "Speciality / Gourmet",
			key: "gourmet",
			data: []
		},
		streetfood : {
			name: "Street Food",
			key: "streetfood",
			data: []
		}
	};
	// query for data
	get_data(foodData);
});
