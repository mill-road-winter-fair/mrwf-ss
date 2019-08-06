<?php

class EventObject extends DataObject {

	public static $db = array(
		'year' => 'int(10)',
		'date' => 'date',
		'time' => 'time',
		'end_date' => 'date',
		'end_time' => 'time',
		'Title' => 'varchar(128)',
		'Content' => 'text',
		'venue' => 'varchar(128)',
		'line_1' => 'varchar(64)',
		'line_2' => 'varchar(64)',
		'line_3' => 'varchar(64)',
		'postcode' => 'varchar(16)',
		'town' => 'varchar(64)',
		'addr_order' => 'int(10)',
		'addr_sub_order' => 'int(10)',
		'website' => 'varchar(256)',
		'image_url' => 'varchar(256)',
		'image_width' => 'int(10)',
		'image_height' => 'int(10)',
		'age_min' => 'int(3)',
		'age_max' => 'int(3)',
		//'MetaKeywords' => 'varchar(255)',
	);

	public static $indexes = array(
		'fulltext (Title, Content)'
	);

	/*
	// See http://www.electrictoolbox.com/silverstripe-populate-defaults-dynamically/
	// This doesn't seem to work
	public function populateDefaults() {
		parent::populateDefaults();
		$this->Created = date('Y-m-d H:i:s');
		$this->LastEdited = date('Y-m-d H:i:s');
	}
	*/

	static $many_many = array(
		'Categories' => 'CategoryObject'
	);

	function formatTime($time)
	{
		if ( $time )
		{
			// See php time functions: http://www.w3schools.com/php/php_ref_date.asp
			$time = gmstrftime("%I:%M%p", strtotime($time) );
			$time = trim($time, '0');
			$time = strtolower($time);
		}

		return $time;
	}

	// Get all the times this event is happening 
	function GetPerformances() {
		return DataObject::get('EventObject', "EventObject.Title='{$this->Title}'");
	}

	function when()
	{
		$timeFrom = $this->formatTime( $this->time );
		$timeTo = $this->formatTime( $this->end_time );

		// Time
		if ( $timeFrom )
		{
			if ( $timeTo )
				$time = $timeFrom . ' to ' . $timeTo;
			else
				$time = 'From ' . $timeFrom;
		}
		elseif ( $timeTo )
				$time = 'Until ' . $timeTo;
		else
			$time = 'All day';
		return $time;
	}

/*
	function location() {
		if ( $this->venue
				if ( $record['venue'] )
				{
					$html = 'At ' . htmlspecialchars($record['venue']);
					if ( $record['line_1'] )
						$html = $html . ', ' . htmlspecialchars($record['line_1']);

					//$html = $html . ' (addr_order=' . $record['addr_order'] . ', addr_sub_order=' . $record['addr_order'] . ')';
					if ( $format == 1 )
						$html = '<p>' . $html . '<p>' . "\n";
					else
						$html = '<br/>' . $html . "\n";

				}
*/


	function url() {
		return trim(preg_replace('/[^[:alnum:]]+/', '-', $this->Title),'-');
	}

	function Link() {

		$categoryURL = '';
		$page = $this->Top();
		if ( $page )
		{
			$categoryID = $this->Top()->SelectedCategoryId();
			if ( $categoryID )
				$categoryURL = '?catid=' . $categoryID . '&catname=' . rawurlencode($this->Top()->SelectedCategoryName()) ;
		}
		return htmlspecialchars('events/event/' . $this->ID . '/' . $this->url() . $categoryURL);
	}

	// Return search results found text, including highlighted search keywords
	// Copied from forum post: http://www.silverstripe.org/form-questions/show/262339?start=0
	// Originally based on Text->ContextSummary
	function SearchSummary($characters = 500, $string = false, $striphtml = true, $highlight = true)
	{
		if(!$string)
			$string = $_REQUEST['Search']; // Use the default "Search" request variable (from SearchForm)

		/*** Prepare Content ***/

		// Replace <br /> in order to get separate words
		$content = str_replace('<br />', ' ', $this->Content);

		// Decoding entities prevents XML validation error
		$content = html_entity_decode($content, ENT_COMPAT , 'UTF-8');

		// Remove HTML tags so we don't have to deal with matching tags
		$text = strip_tags($content);

		// Remove BBCode
		$pattern = '|[[\/\!]*?[^\[\]]*?]|si';

		$replace = '';
		$text = preg_replace($pattern, $replace, $text);

		// Find the search string
		$position = (int) stripos($text, $string);

		// We want the search string to be in the middle of our block to give it some context
		$position = max(0, $position - ($characters / 2));

		// We don't want to start mid-word
		if($position > 0)
		{
			$position = max((int) strrpos(substr($text, 0, $position), ' '), (int) strrpos(substr($text, 0, $position), "\n"));
		}

		$summary = substr($text, $position, $characters);

		// We also don't want to end mid-word
		$offset = $characters+$position;
		if ($offset > strlen($text)-1)
			$offset = strlen($text)-1;
		$position = min((int) strpos($text, ' ', $offset), (int) strpos($text, "\n", $offset));
		if ($position)
			$summary = $summary.substr($text, $offset, $position-$offset);

		if($highlight)
		{
			// Setting the content that will be inserted before and after the highlighted words
			$before_content = "<span class=\"highlight\">";
			$after_content = "</span>";

			// We have to insert content without any html-entities in the first place
			// The following two lines should not be altered
			$before_placeholder = "%BEFORE_CONTENT%";
			$after_placeholder = "%AFTER_CONTENT%";

			// Save the different search values into an array
			$stringPieces = explode(' ', $string);

			foreach($stringPieces as $stringPiece)
			{
				//Setting the start from where $stringPiece will be searched
				$offset = 0;

				// Recursively add before_placeholder and after_placeholder around all found $stringPiece
				while($position = stripos($summary, $stringPiece, $offset))
				{
					$summary =
						substr($summary, 0, $position)
						.$before_placeholder
						.substr($summary, $position, strlen($stringPiece))
						.$after_placeholder
						.substr($summary, $position + strlen($stringPiece), strlen($summary)-($position+1));
					$offset = $position + strlen($before_placeholder) + strlen($after_placeholder);
				}
			}
		}

		// Re-add all htmlentities in order to get well-formed XML
		$summary = htmlentities($summary, ENT_COMPAT , 'UTF-8');

		if ($highlight)
		{
			// Replacing the placeholders with the set content
			$summary = str_replace($before_placeholder , $before_content , $summary);
			$summary = str_replace($after_placeholder , $after_content , $summary);
		}

		return trim($summary);
	}
}
