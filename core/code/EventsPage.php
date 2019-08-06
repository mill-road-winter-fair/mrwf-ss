<?php

class DummyPage {
	public $Link;
	public $MenuTitle;

	function __construct($link, $title) {
		$this->Link = $link;
		$this->MenuTitle = $title;
	}
}

class EventsPage extends Page {

	// Get a dataset of contacts for the selected country
	function GetEvents() {
		$categoryID = $this->SelectedCategoryID();
		if (!$categoryID || $categoryID==-1) {
			return false;
		} 
		// Get all the events for this category/venue
		$set = DataObject::get('EventObject', "EventObject_Categories.CategoryObjectID=$categoryID", "Title", "inner join EventObject_Categories on EventObject.ID = EventObject_Categories.EventObjectID");
		// Now make sure we only show one event title - it doesn't matter which 
		// as the page we link to is going to show all venue/time pairings for the performer / stall
		$events = $set->toArray();
		$ret = array();
		$previousTitle = "";
		foreach($events as $event) {
			if (strcmp($event->Title, $previousTitle)!=0) {
				$ret[] = $event;
				$previousTitle = $event->Title;
			}
		}
		return new DataObjectSet($ret);
	}

	// Get one event to show in a page
	function GetEvent() {
		$eventID = $this->SelectedEventID();

		if ( !$eventID || $eventID==-1 )
			return false;

		return DataObject::get_by_id('EventObject', $eventID );
	}

	/*
	// This doesn't work for dynamic pages because the header is cached
	// See Page.php
	function CanonicalURL() {
		$url = Director::protocolAndHost() . $this->Link();

		$id = (int) Director::URLParam('ID');
		$name = Director::URLParam('OtherID');

		if ( Director::URLParam('Action') == 'category' ) {
			$url .= 'category/' . $id . '/' . $name;
		}
		else if ( Director::URLParam('Action') == 'category' ) {
			$url .= 'event/' . $id . '/' . $name;
		}

		return $url;
	}
	*/

	function BreadcrumbSet() {

		$pages = $this->Ancestors( $this );

		$catid = 0;
		//$eventid = 0;
		if ( Director::URLParam('Action') == 'category' )
		{

			$catid = Director::URLParam('ID');
			$catname = urldecode(Director::URLParam('OtherID'));
		}
		else if ( isset($_GET['catid']) )
		{
			$catid = (int) $_GET['catid'];
			$catname = isset($_GET['catname']) ? urldecode($_GET['catname']) : '';

			//$eventid = Director::URLParam('ID');
			//$eventname = Director::URLParam('OtherID');
		}

		if ( $catid )
		{
			$page = new DummyPage($this->Link() . 'category/' . $catid . '/' . rawurlencode($catname), $catname );
			array_push( $pages, $page );
		}

		//if ( $eventid ) {
		//	$page = new DummyPage($this->Link() . 'event/' . $eventid . '/' . $eventname, $eventname );
		//	array_push( $pages, $page );
		//}

		return new DataObjectSet( $pages );
	}


}

class EventsPage_Controller extends Page_Controller {

	/**
	 * An array of actions that can be accessed via a request. Each array element should be an action name, and the
	 * permissions or conditions required to allow the user to access it.
	 *
	 * <code>
	 * array (
	 *     'action', // anyone can access this action
	 *     'action' => true, // same as above
	 *     'action' => 'ADMIN', // you must have ADMIN permissions to access this action
	 *     'action' => '->checkAction' // you can only access this action if $this->checkAction() returns true
	 * );
	 * </code>
	 *
	 * @var array
	 */
	public static $allowed_actions = array (
		'category', 'event'
	);

	public function init() {
		parent::init();
	}

	function category() {
		return $this->renderWith(array('CategoryPage','Page'));
	}

	function event() {
		return $this->renderWith(array('EventPage','Page'));
	}

}