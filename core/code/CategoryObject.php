<?php

class CategoryObject extends DataObject {

	public static $db = array(
 		'Title' => 'varchar(64)'
	);

	static $belongs_many_many = array(
		'Events' => 'EventObject'
	);

	//function url() {
	//	return rawurlencode( $this->Title );
	//}

	function Link() {
		return htmlspecialchars('events/category/' . $this->ID . '/' . rawurlencode($this->Title) );
	}
}
