<?php

class SiteMap extends Page {

	static $db = array(
	);

	static $has_one = array(
	);

	function canCreate() {
		return true;
	}

}

class SiteMap_Controller extends Page_Controller {

	/**
	* This function will return a unordered list of all pages on the site.
	* Watch for the switch between $page and $child in the second line of the foreach().
	*
	* Note that this will only skip ErrorPage's at the top/root level of the site.
	* If you have an ErrorPage class somewhere else in the hierarchy, it will be displayed.
	*/
	function SiteMap() {
		$rootLevel = DataObject::get("Page", "ParentID=0 and ShowInMenus=1"); // Pages at the root level only
		$output = "";
		$output = $this->makeList($rootLevel, 3);
		return $output;
	}

	private function makeList($pages, $levels) {
		$output = "";
		if(count($pages)) {
			$output = "\t<ul>\n";
			foreach($pages as $page) {
				if(!($page instanceof ErrorPage) && $page->ShowInMenus && $page->Title != $this->Title){
					$output .= "\t\t<li><a href=\"".$page->Link()."\" title=\"Go to the " . Convert::raw2xml($page->Title) . " page\">" . Convert::raw2xml($page->MenuTitle)."</a>";

					if(count($pages) && $levels>1)
					{
						$whereStatement = "ParentID = ".$page->ID." and ShowInMenus=1";
						$childPages = DataObject::get("Page", $whereStatement);
						$output .= "\n" . $this->makeList($childPages, $levels-1) . "\t\t</li>\n";
					}
					else
					{
						$output .= "</li>\n";
					}
				}
			}
			$output .= "\t</ul>\n";
		}
		return $output;
	}
}

?>