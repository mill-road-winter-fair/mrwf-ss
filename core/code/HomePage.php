<?php

//require_once('TwitterAPI.php');

class HomePage extends Page {

	public static $db = array(
		'Intro' => 'HTMLText',
		'TwitterUsername' => 'Varchar',
		'HowManyStatuses' => 'Int',
		//'SinceWhen' => 'Text'
	);

	public static $has_one = array(
		//"ExtraPicture" => "Image"
	);

	function getCMSFields() {
		$fields = parent::getCMSFields();

		// Remove content field
		$fields->removeFieldFromTab('Root.Content.Main','Summary');

		//$fields->addFieldToTab('Root.Content.Main', new TextAreaField('Intro', 'Introduction', 3) );
		$fields->addFieldToTab('Root.Content.Main', new HTMLEditorField('Intro', 'Introduction') );

		//$fields->addFieldToTab("Root.Content.Main", new ImageField("ExtraPicture", "Extra Picture Prompt", null, null, null, "img/default-upload-folder") );
		//$fields->addFieldToTab('Root.Content.Main', new TextField('ExtraTextEdit', 'Extra Text Edit Prompt') );

		// Add a field that's already been added by a parent class to change
		// the order of controls in the CMS and/or change the prompt
		$fields->addFieldToTab('Root.Content.Main', new HTMLEditorField('Content') );

		$fields->addFieldToTab('Root.Content.Twitter', new TextField('TwitterUsername','Your twitter username'));
		//$fields->addFieldToTab('Root.Content.Twitter', new DatePickerField('SinceWhen','Since when do you need to show the messages, this will show the messages which are up to 24 hours old and created after the date, leave it blank if you dont want to use this feature'));
		$fields->addFieldToTab('Root.Content.Twitter', new NumericField('HowManyStatuses','How many status messages you need to show?'));

		return $fields;
	}
}

class HomePage_Controller extends Page_Controller {

	public function init() {
		parent::init();

		// Twitter feed
		//Requirements::javascript( parent::ThemeDir() . "/js/twitter-1.13.1.min.js" );



		// Minified version 2.0 doesn't seem to work
		Requirements::javascript( parent::ThemeDir() . "/js/twitterjs-2.0.0.min.js" );
		//Requirements::javascript( parent::ThemeDir() . "/js/twitter.js" );
		//Requirements::javascript( parent::ThemeDir() . "/js/twitter.min.js" );

		$count = $this->HowManyStatuses ? "\n\tcount: " . $this->HowManyStatuses . "," : "";

		Requirements::customScript(<<<JS
getTwitters('twitter', {
	id: '$this->TwitterUsername',$count
	ignoreReplies: true,
	newwindow: true,
	template: '%text%<br/><em class="twitterTime"><a href="https://twitter.com/%user_screen_name%/statuses/%id_str%">%time%</a></em>'
});
JS
			);

	}

	/*
	Old server-side twitter feed
	Conflicts with cacheing
	*
	 * Time since function taken from wickett-twitter-widget.php, which took it from WordPress.com
	 *
	function wpcom_time_since( $original, $do_more = 0 ) {
			// array of time period chunks
			$chunks = array(
					array(60 * 60 * 24 * 365 , 'year'),
					array(60 * 60 * 24 * 30 , 'month'),
					array(60 * 60 * 24 * 7, 'week'),
					array(60 * 60 * 24 , 'day'),
					array(60 * 60 , 'hour'),
					array(60 , 'minute'),
			);

			$today = time();
			$since = $today - $original;

			for ($i = 0, $j = count($chunks); $i < $j; $i++) {
					$seconds = $chunks[$i][0];
					$name = $chunks[$i][1];

					if (($count = floor($since / $seconds)) != 0)
							break;
			}

			$print = ($count == 1) ? '1 '.$name : "$count {$name}s";

			if ($i + 1 < $j) {
					$seconds2 = $chunks[$i + 1][0];
					$name2 = $chunks[$i + 1][1];

					// add second item if it's greater than 0
					if ( (($count2 = floor(($since - ($seconds * $count)) / $seconds2)) != 0) && $do_more )
							$print .= ($count2 == 1) ? ', 1 '.$name2 : ", $count2 {$name2}s";
			}
			return $print;
	}

	**
	 * Get the friends time line
	 *
	function UserTimeLine(){
		if ( !$this->TwitterUsername )
			user_error("Set the twitter username to retrieve tweets", E_USER_NOTICE);

		$params = array(
			//'screen_name' => $this->TwitterUsername,
			'count' => $this->HowManyStatuses
		);

		$since = null;
		$stamp = 0;
		if($this->SinceWhen){
			$time = explode('-', $this->SinceWhen);
			$stamp = mktime(0,0,0,$time[1], $time[2], $time[0]);
		}
		if($stamp)
			$params['scince'] = $stamp;

		$twitterApi = new TwitterAPI();

		//$xml = $twitterApi->doCall('http://twitter.com/statuses/user_timeline/'.$this->TwitterUsername.'.xml', true, $params);
		$xml = $twitterApi->doCall('http://api.twitter.com/statuses/user_timeline/'.$this->TwitterUsername.'.xml', false, $params);

		if($xml){
			// init var
			$msgs = array();

			// loop statuses
			foreach ($xml->status as $status)
				$msgs[] = $twitterApi->statusXMLToArray($status);

			$output = new DataObjectSet();
			foreach($msgs as $status){
				$text = $status['text'];
				$html = $this->checkForURLs($text);
				if ($html)
					$text='';

				$output->push(new ArrayData(array(
					'id' => $status['id'],
					//'Text' => $this->checkForURLs($status['text']),
					'Text' => $text,
					'Html' => $html,
					//'Time' => $this->wpcom_time_since($status['created_at'])
					'Time' => str_replace(' ', '&nbsp;', $this->wpcom_time_since($status['created_at'])) . "&nbsp;ago"
				)));
			}
			return $output;
		}
		else
			return false;
	}

	//	echo "<li>{$before_tweet}{$text}{$before_timesince}<a href=\"" . esc_url( "
	//http://twitter.com/{$account}/statuses/{$tweet_id}

	**
	 * Check for any URLs and make them usable
	 *
	 function checkForURLs($text){
		$html = '';
		$isLinked = false;
	 	if($text){
			$words = explode(' ',$text);

			foreach($words as $word){
				if(preg_match('/^http/', $word)){
					$htmlword = '<a href=\'' . $word . '\' target=\'_blank\'>' . $word . '</a>';
					$isLinked = true;
				}
				else if(strcmp('$word', '&') == 0)
					$htmlword = '&amp;';
				else
					$htmlword = $word;
				$html .= $htmlword . ' ';
			}
		}

		if ( $isLinked )
			return $html;
		else
			return '';

	 }
	*/

}
