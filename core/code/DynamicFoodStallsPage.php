<?php

class DynamicFoodStallsPage extends Page {
    public static $db = array (
        "Gdocskey" => 'Varchar'
    );
    
    public function getCMSFields() {
        $fields = parent::getCMSFields();
        
        $fields->addFieldToTab('Root.Content.Main', new TextField('Gdocskey', 'Google docs key'), 'Content');
        
        return $fields;
    }
}

class DynamicFoodStallsPage_Controller extends Page_Controller {
    function init() {
	parent::init();
/*<% require themedCSS(dynamic_content_page) %>
<% require javascript(themes/millroadwinterfair/js/jquery-2.1.0.min.js) %>
<% require javascript(themes/millroadwinterfair/js/masonry.min.js) %>
<% require javascript(themes/millroadwinterfair/js/dynamic_common.js) %>
<% require javascript(themes/millroadwinterfair/js/dynamic_food_stalls_page.js) %>*/
	Requirements::themedCss("dynamic_content_page");
	Requirements::javascript("themes/millroadwinterfair/js/jquery-2.1.0.min.js");
	Requirements::javascript("themes/millroadwinterfair/js/masonry.min.js");
	Requirements::javascript("themes/millroadwinterfair/js/dynamic_common.js");
	Requirements::customScript("
		var spreadsheetID = \"".$this->Gdocskey."\";
	");
	Requirements::javascript("themes/millroadwinterfair/js/dynamic_food_stalls_page.js");
    }
	
}
