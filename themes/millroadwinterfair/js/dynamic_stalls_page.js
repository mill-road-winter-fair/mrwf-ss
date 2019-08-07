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
			contents += "</div><div class=\"shop_location\">" + shop.location + "</div><p>" + shop.description + "</p></div></div>";
			var thing = $(contents);
			blurb.append(thing);
		});			
/*		blurb.masonry({
		  // options
		  itemSelector: '.shop_elem',
		  columnWidth: 200
		});*/
	}

	function get_data (dataset) {
		//var spreadsheetID = "1mFt99VXk7NbvQAc0jVul0Pmlxka0F1nIiffbWpMUsYA";
		// process data
		var url = "https://spreadsheets.google.com/feeds/list/" + spreadsheetID + "/od6/public/values?alt=json";
		
		// process the stalls data into categories
		$.getJSON(url, function(data) {
	
			var entry = data.feed.entry;
			 
			$(entry).each(function(){
				var item = {
					name: this.gsx$stallname.$t,
					description: this.gsx$stalldescription.$t,
					url: this.gsx$stallwebsite.$t,
					location: map_location(this.gsx$location.$t)
				};

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
				if (stallsData.hasOwnProperty(key)) {
					blurb.html("");
					do_layout(blurb, stallsData[key]);
					update_nav($(this));
				}
			});
		});
	}

	var stallsData = {
		name: "Stalls",
		christmas : {
			name: "Christmas",
			key: "christmas",
			data: []
		},
		textiles : {
			name: "Textiles & Knitwork",
			key: "textilesknits",
			data: []
		},
		books : {
			name: "Books",
			key: "books",
			data: []
		},
		art : {
			name: "Artwork / Cards",
			key: "artcards",
			data: []
		},
		jewellery : {
			name: "Jewellery / Hair Accessories",
			key: "jewelleryhairaccessories", 
			data: []
		},
		pots : {
			name: "Pots / Ceramics",
			key: "potsceramics",
			data: []
		},
		health : {
			name: "Wellbeing",
			key: "wellbeing",
			data: []
		},
		gifts : {
			name: "Gifts",
			key: "gifts",
			data: []
		},
		charity : {
			name: "Charities",
			key: "charity",
			data: []
		},
		community : {
			name: "Community",
			key: "community",
			data: []
		}
	};
	// query for data
	get_data(stallsData);
});
