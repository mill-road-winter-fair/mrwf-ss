<?php

class Page extends SiteTree {

	public static $db = array(
		// See StandardPage.php for examples of adding fields of different types

		'Summary' => 'HTMLText'
	);

	public static $has_one = array(
		// Add button image to the Behaviour tab
		//"Button" => "Image"
	);

	static $has_many = array(
		  'PickObjects' => 'PickObject'
	);

	function getCMSFields() {
		$fields = parent::getCMSFields();

		$fields->addFieldToTab("Root.Content.Picks", PickObject::Editor($this) );

		// Add button image to the Behaviour tab
		//$fields->addFieldToTab("Root.Behaviour", new ImageField("Button", "Menu button", null, null, null, "img/buttons"), 'ShowInSearch');

		$fields->addFieldToTab('Root.Content.Main', new HTMLEditorField('Summary', 'Summary of this page (that appears on onther pages)') );

		return $fields;
	}

	function themeDir() {
		return parent::ThemeDir();
	}

	function assetsDir() {
		return Director::baseURL() . ASSETS_DIR;
	}
	
	function imageDir() {
		return Director::baseURL() . ASSETS_DIR . "/img/system";
	}

	// Return an array of ancestor pages from hightest (top level page) to lowest (parent)
	function Ancestors($page) {

		$breadcrumbs = array();

		if ( $page->URLSegment == 'home' )
			return $breadcrumbs;

		//Debug::Log( 'Ancestors: ' . $page->ID . ' - ' .$page->MenuTitle . ' has parent ' . $page->ParentID );

		//  Add ancestor pages to the array
		if ( $page->ParentID )
			$breadcrumbs += $this->Ancestors( $page->Parent() );

		//  Add the page to the array
		if ( get_class($page) != 'SubmenuHolder' )
			$breadcrumbs[] = $page;

		return $breadcrumbs;
	}

	// Return a DataObjectSet containing breadcrumbs
	function BreadcrumbSet() {
		return new DataObjectSet( $this->Ancestors( $this ) );
	}

	/*
	// This doesn't work for dynamic pages because the header is cached
	// See EventsPage.php
	<link rel="canonical" href="$CanonicalURL" />

	function CanonicalURL() {
		return Director::protocolAndHost() . $this->Link();
	}
	*/

	function CurrentDateTime() {
		return date('Y-m-d H:i:s');
	}

	function SiteLastEdited() {
		// return $this->Aggregate('SiteTree')->Max('LastEdited');

		$sqlQuery = new SQLQuery();
		$sqlQuery->select = array('Max(LastEdited) as LastEdited');
		$sqlQuery->from = array('SiteTree');
		$sqlQuery->execute();

		// get the raw SQL
		$rawSQL = $sqlQuery->sql();

		// execute and return a Query-object
		$result = $sqlQuery->execute();

		$row = $result->first();

		return $row['LastEdited'];
	}

	/*
	function Menu1CacheKey() {
		return implode('_', array(
								'mainmenu',
								$this->ID,
								$this->Aggregate('SiteTree','ParentID=0 and ShowInMenus=1')->Max('LastEdited')
							)
			);
	}

	function FooterCacheKey() {

		// Get most recent (i.e. max) last edited date/time
		// from the children both the footer menus

		// sc is SiteTree Child, sp is SiteTree Parent
		$sqlQuery = new SQLQuery();
		$sqlQuery->select = array('Max(sc.LastEdited) as LastEdited');
		$sqlQuery->from = array('SiteTree sc inner join SiteTree sp on sc.ParentID=sp.ID');
		$sqlQuery->where = array('sp.URLSegment=\'footer-menu-left\' or sp.URLSegment=\'footer-menu-right\'');
		$sqlQuery->execute();

		// get the raw SQL
		$rawSQL = $sqlQuery->sql();

		// execute and return a Query-object
		$result = $sqlQuery->execute();

		$row = $result->first();

		return implode('_', array(
								'footer',
								$row['LastEdited']
							)
			);
	}


	function Hello() {
		return "hello";
	}
	*/

	function GridPad( $rowSize ) {
		switch ( $this->Pos() % $rowSize )
		{
			case 1:	return ' alpha';
			case 0:	return ' omega';
		}
	}

	function Summaries() {
		return DataObject::get('Page', 'ParentID=' . $this->ID . ' and Summary is not null');
	}

	// Get a dataset of categories
	function Categories() {
		return DataObject::get('categoryobject', '', 'Title');
	}
	function SelectedCategoryName() {
		return urldecode( Director::URLParam('OtherID') );
	}
	function SelectedCategoryID() {
		return intval( Director::URLParam('ID') );
	}

	function SelectedEventID() {
		return intval( Director::URLParam('ID') );
	}

	// Returns 1 if the user is not logged in
	function IsGoogleAnalytics() {
		// SiteConfig::current_site_config()->googleAnalytics;
		return Member::currentUserID() ? 0 : 1;
	}

}

class Page_Controller extends ContentController {

	public function init() {

		parent::init();

		// Note: you should use SS template require tags inside your templates
		// instead of putting Requirements calls here.  However these are
		// included so that our older themes still work

		if ( Director::isDev() ) {
			Requirements::themedCSS('layout');
			Requirements::themedCSS('960_12_col_dev');
			Requirements::themedCSS('typography');
			Requirements::themedCSS('form');
		}
		else {
			//Set the folder to inside our theme so that relative css image paths work
			Requirements::set_combined_files_folder(parent::ThemeDir() . '/combinedfiles');

			Requirements::combine_files(
				'combined.css',
				array(
					  parent::ThemeDir() . "/css/layout.css",
					  parent::ThemeDir() . "/css/960_12_col.css",
					  parent::ThemeDir() . "/css/typography.css",
					  parent::ThemeDir() . "/css/form.css"
				)
			);
		}
	}

	/**
	 * Redirect to the first child page
	 */
	function RedirectToFirstChild(){
		if($children = $this->Children()) {
			if($firstChild = $children->First()) {
				Director::redirect($firstChild->Link());
			}
		}
	}


}