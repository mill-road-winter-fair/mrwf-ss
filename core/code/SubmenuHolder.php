<?php

class SubmenuHolder extends Page {

    function canCreate() {
        return true;
    }

	function getCMSFields() {
		$fields = parent::getCMSFields();

		$fields->removeFieldFromTab("Root.Content.Main","Content");

		return $fields;
	}

}

class SubmenuHolder_Controller extends Page_Controller {

    function init() {
        parent::init();
        parent::redirectToFirstChild();
    }
}

?>