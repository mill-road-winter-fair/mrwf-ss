<?php

/*
** To use this class on a page...

** ...add this class name to the Page's has_many array

	static $has_many = array(
		  'PickObjects' => 'PickObject'
	);

** and add this to the getCMSFields function

	function getCMSFields() {
		$fields = parent::getCMSFields();
		$fields->addFieldToTab("Root.Content.Picks", PickObject::Editor($this) );
		return $fields;
	}

** To enable drag-and-drop reordering for this object, add the following to _config.php...

	SortableDataObject::add_sortable_classes( array('PickObject') );

** ...then browse to...

	http://localhost/...this.../index.php/dev/build?flush=1

*/

class PickObject extends DataObject {

	static $collection='PickObjects';
	static $label='Pick';
	static $className='PickObject';
	static $defaultImageFolder='img';

	public static $db = array(
		'IsTwitter' => 'Boolean',
		'Heading' => 'Text',
		'LinkText' => 'Text',
	);

	public static $has_one = array(
		'Page' => 'Page',
		'LinkTarget' => 'SiteTree',
		'Picture' => 'Image'
	);

	// Define the fields shown in the record editor popup
	public function getCMSFields_forPopup() {
		return new FieldSet(
			new TextField('Heading'),
			new CheckboxField('IsTwitter', 'Show twitter here'),
			new OptionalTreeDropdownField('LinkTargetID', 'Target page', 'SiteTree'),
			//new SimpleTreeDropdownField('LinkTargetID', "Target page", 'SiteTree'),
			new TextField('LinkText', 'Link Text'),
			new ImageField('Picture', 'Image', null, null, null, self::$defaultImageFolder)

			// new SimpleTinyMCEField('HTMLText')
			//new TreeDropdownField('LinkTargetID','Choose a page to link to', 'SiteTree')
		);
	}

	static function Editor($parent) {
		$tablefield = new DataObjectManager(
			  $parent
			, self::$collection
			, self::$className
			, array(
				  'Description' => 'Description'
				, "Thumbnail" => "Image"
			)
			, 'getCMSFields_forPopup'
		);
		$tablefield->setAddTitle(self::$label);
		return $tablefield;
	}

	function getThumbnail() {
		$Picture = $this->Picture();
		if ( $Picture )
			return $Picture->CMSThumbnail();
		else
			return null;
	}

	function getDescription() {
		if ( $this->Heading )
			return $this->Heading;
		elseif ( $this->IsTwitter )
			return 'Twitter';
		else {
			$page = $this->LinkTarget();
			if ( $page )
				return $page->menuTitle;
		}
		return null;
	}

	function GridPad( $rowSize ) {
		switch ( $this->Pos() % $rowSize )
		{
			case 1:	return ' alpha';
			case 0:	return ' omega';
		}
	}
}
