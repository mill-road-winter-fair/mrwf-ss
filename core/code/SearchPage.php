<?php
/**
 * Defines the SearchPage page type
 */
class SearchPage extends Page {

	function SearchText() {
		if ( isset($_REQUEST['Search']) )
			return $_REQUEST['Search'];
		else
			return Director::URLParam('Action');
	}

	function SearchForm() {
		// $searchText = isset($_REQUEST['Search']) ? $_REQUEST['Search'] : 'Search';
		$searchText = isset($_REQUEST['Search']) ? $_REQUEST['Search'] : '';
		$fields = new FieldSet(
			array(
				// new LabelField ( "label", "Search", false, $searchText )
				new TextField("Search", "", $searchText)
			)
	  	);
		$actions = new FieldSet(
	      	new FormAction('results', 'Search')
	      	// new ImageFormAction('results', 'GO', )
	  	);

	  	$form = new SearchForm($this, "SearchForm", $fields, $actions);
	  	$form->setFormAction ( Director::baseURL() . 'search/SearchForm' );
	  	return $form;
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

class SearchPage_Controller extends Page_Controller {
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
		'SearchForm', 'results'
	);

	function Query() {
		return isset($_REQUEST['Search']) ? $_REQUEST['Search'] : '';
	}

	/**
	 * Site search form
	 */
	function SearchForm() {
		// $searchText = isset($_REQUEST['Search']) ? $_REQUEST['Search'] : 'Search';
		$searchText = isset($_REQUEST['Search']) ? $_REQUEST['Search'] : '';
		$fields = new FieldSet(
			array(
				// new LabelField ( "label", "Search", false, $searchText )
				new TextField("Search", "", $searchText)
			)
	  	);
		$actions = new FieldSet(
	      	new FormAction('results', 'Search')
	      	// new ImageFormAction('results', 'GO', )
	  	);

	  	$form = new SearchForm($this, "SearchForm", $fields, $actions);
	  	$form->setFormAction ( Director::baseURL() . 'search/SearchForm' );
	  	return $form;
	}

	/* Get events
	function Events() {
		$past = $this->Past;

		$start = isset($_GET['start']) ? (int) $_GET['start'] : 0;

		if ( $past )
		{
			$searchText = $this->SearchText();

			if ( Director::URLParam('Action') != 'search' )
			//if ( strlen($searchText) == 0)
				// There's no search, so display an empty page
				return null;

			$where = 'StartDate < Now()';
			if ( $searchText )
				// There is a text filter
				$where .= ' and MATCH (Name,Summary) AGAINST (\'' . $searchText . '\')';
		}
		else
		{
			// Display all current & future events
			$where = 'StartDate >= Now()';
		}

		//return DataObject::get('EventObject');
		return DataObject::get(
			  'EventObject'
			, $where
			, $past ? 'StartDate desc' : 'StartDate'
			, ''
			, $start . ',10'
			);
	}
	*/

	/**
	 * Process and render search results
	 */
	public function results($data, $form){
		$data = $_REQUEST;

		$query = htmlspecialchars($data['Search'], ENT_QUOTES,'UTF-8');
		$start = isset($_GET['start']) ? (int) $_GET['start'] : 0;

		$pages = DataObject::get("SiteTree","MATCH (Title,Content) AGAINST ('$query' IN BOOLEAN MODE)");
		/*$events = DataObject::get("EventObject","MATCH (Title,Content) AGAINST ('$query' IN BOOLEAN MODE)");*/

		$searchresults = new DataObjectSet();
		$searchresults->merge($pages);
//		$searchresults->merge($events);
		$searchresults->pageLength=10;

		if($searchresults){
			$data['Results'] = $searchresults;
		} else {
			$data['Results'] = '';
		}

		$data['Title'] = 'Search Results';

		return $this->customise($data)->renderWith(array('SearchPage','Page'));
	}

	/*
	function results($data, $form){
	  	$data = array(
	     	'Results' => $form->getResults(),
	     	'Query' => $form->getSearchQuery(),
	      	'Title' => 'Search Results',
	      	'ClassName' => 'SearchPage'
	  	);

	  	return $this->customise($data)->renderWith(array('SearchPage', 'Page'));
	}
	*/

	/*
	function xResults() {
		return $this->SearchForm()->getResults();
	}
	function Query() {
		return $this->SearchForm()->getSearchQuery();
	}
	*/
}
