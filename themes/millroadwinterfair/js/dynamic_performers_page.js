$(document).ready(function() {

	var rawBlurb = $("#blurb").html();

	function do_layout(blurb, cat) {
		$.each(cat.data, function (index, event) {
			var contents = "<div class=\"shop_elem\"><div class=\"shop_content\"><div class=\"shop_header\">";
			if (event.performer&&event.performer.url) {
				var protocol = event.performer.url.startsWith("https://") ? "https://": "http://";
				var url = event.performer.url.startsWith(protocol) ? event.performer.url.substring(protocol.length) : event.performer.url;
				contents += "<a target=\"_blank\" href=\"" + protocol + url + "\">";
			}
			contents += event.name;
			if (event.performer&&event.performer.url) {
				contents += "</a>";
			} 
			contents += "</div><div class=\"shop_location\">" + event.location + "</div><p>"+ event.from;
			if (event.to) {
				contents += " - " + event.to;
			}
			contents += "</p>";
			if (event.performer) {
				contents += "<p>" + event.performer.description + "</p>";
				if (event.performer.events.length > 1) {
					contents += "<p>Also performing at:</p>";
					for (var i in event.performer.events) {
						var otherEvent = event.performer.events[i];
						if (otherEvent.from != event.from) {
							contents += "<p>" + otherEvent.location + "<br />" + otherEvent.from;
							if (otherEvent.to) {
								contents += " - " + otherEvent.to;
							}
							contents += "</p>";
						}
					}
				}
			}
			contents += "</div></div>";
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
		// first get all the performers.
		//var spreadsheetID = "12rlmtj0S0IfSnP2228oN7bEMTd-duXbofyyYuz2L3AI";
		var url = "https://spreadsheets.google.com/feeds/list/" + spreadsheetID + "/od6/public/values?alt=json";
	
		$.getJSON(url, function(data) {
			var entry = data.feed.entry;
			dataset.performers = [];				 
			$(entry).each(function(){
				var item = {
					name: this.gsx$name.$t,
					description: this.gsx$description.$t,
					url: this.gsx$url.$t,
					// tracking events this performer is at
					events: []
				};
				dataset.performers.push(item);
			});

			// then get the times and locations of their performances
			//var timesSheetID = "1XpuXUMjlFyAQk1H_uFJEGFl5LP6MopaNkCpTZYwq100";
			var timesUrl = "https://spreadsheets.google.com/feeds/list/" + timesSheetID + "/od6/public/values?alt=json";
			$.getJSON(timesUrl, function(data) {
				var entry = data.feed.entry;
				$(entry).each(function() {
					// skip any provisional events (i.e. invitation sent, not confirmed)
					if (filterColumn(this, "provisional")) {
						return;
					}
					var item = {
						name: this.gsx$name.$t,
						from: this.gsx$from.$t,
						location: map_location(this.gsx$location.$t)
					};
					if (this.gsx$to.$t) {
						item.to = this.gsx$to.$t;
					}
					// scan over all the performers we've identified to see if we can get a description
					for (var index in dataset.performers) {
						var performer = dataset.performers[index];
						if (performer.name == item.name) {
							item.performer = performer;
							performer.events.push(item);
							break;
						}
					}
					// scan over all the categories and see if this time fits.
					// n.b. time has to be in 24 hour format for this to work.
					for (var property in dataset) {
						if (dataset.hasOwnProperty(property)&&dataset[property].hasOwnProperty("earliest")) {
							if (item.to) {
								if (item.to >= dataset[property].earliest && item.from <= dataset[property].latest) {
									dataset[property].data.push(item);
								}
							}
							else {
								if (item.from >= dataset[property].earliest && item.from <= dataset[property].latest) {
									dataset[property].data.push(item);
								}
							}
						}
					}
				});
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

	var buskerData = {
		name: "Performers",
		performers: [],
		elevenish: {
			key: "11-ish",
			earliest:"10:31",
			latest:"11:59",
			data: []
		},
		noonish: {
			key: "12-ish",
			earliest:"11:31",
			latest:"12:59",
			data: []
		},
		oneish: {
			key: "1-ish",
			earliest:"12:31",
			latest: "13:59",
			data:[]
		},
		twoish: {
			key: "2-ish",
			earliest:"13:31",
			latest:"14:59",
			data:[]
		},
		threeish: {
			key: "3-ish",
			earliest:"14:31",
			latest:"15:59",
			data:[]
		},
		fourish: {
			key: "4-ish",
			earliest:"15:31",
			latest:"23:59",
			data:[]
		}
	};
	// query for data
	get_data(buskerData);
});
