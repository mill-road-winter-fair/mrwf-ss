<?php

// See http://kiveo.net/blog/extending-silverstripe-siteconfig/

// Add...
// DataObject::add_extension('SiteConfig', 'SiteConfigOverride');
//...to _config.php

// Add to page.php
//	function FormattedDate() {
//		return date( $this->getSiteConfig()->dateformat, strtotime($this->Date) );
//	}

// After adding extraStatics do...
// http://localhost/millroadwinterfair/silverstripe/index.php/dev/build?flush=1

class SiteConfigOverride extends DataObjectDecorator {

	function extraStatics() {
		return array(
			'db' => array(
					  "googleAnalytics" => "Varchar(16)"
					, "dateformat" => "Varchar(20)"
					, 'Footer' => 'HTMLText'
					, 'Credits' => 'HTMLText'
					, 'EventsLastUpdated' => 'SS_DateTime'

				)
			);
	}

	public function updateCMSFields(FieldSet &$fields) {
			$fields->addFieldToTab("Root.Main", new TextField("googleAnalytics", _t('SiteConfig.ADDRESSLN1',"Google Analytics account number")));
			$fields->addFieldToTab("Root.Main", new TextField("dateformat", 'Date format (see <a target="_blank" href="http://php.net/date">http://php.net/date</a>)'));
			$fields->addFieldToTab('Root.Main', new HTMLEditorField('Footer', 'Footer') );
			$fields->addFieldToTab('Root.Main', new HTMLEditorField('Credits', 'Credits') );
	}

}
