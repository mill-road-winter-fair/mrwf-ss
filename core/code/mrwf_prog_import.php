<?php

class ProgrammeUpdater
{
  protected $phpVersion;      /* array with PHP version numbers */
  protected $dbLink;          /* DB connection link */
  protected $siteUrl;         /* base site URL */
  protected $url;             /* public programme interface URL */
  protected $lastError;       /* string with description of last error */

  /* table names */
  protected $eventsTableName; /* all the events */
  protected $catsTableName;   /* all the categories */
  protected $evCatTableName;  /* relationships between categories and events */

  function __construct($url, $eventsTableName, $catsTableName, $evCatTableName)
  {
    $this->setPhpVersion();
    $this->dbLink = null;
    $this->siteUrl = $url;
    $this->url = $url . "/public/prog/";
    $this->lastError = '';
    $this->eventsTableName = $eventsTableName;
    $this->catsTableName = $catsTableName;
    $this->evCatTableName = $evCatTableName;
  }

  function getError()
  {
    return $this->lastError;
  }

  function connect($host, $user, $passwd, $db, $charset='UTF8')
  {
    $this->disconnect();
    $this->dbLink = mysql_connect($host, $user, $passwd, true);

    if (!$this->dbLink)
      {
        $this->lastError = "MySQL Error: " . mysql_error($this->dbLink);
        $this->dbLink = null;
      }
    else if (!(mysql_select_db($db, $this->dbLink)))
      {
        $this->lastError = "MySQL Error: " . mysql_error($this->dbLink);
        $this->disconnect();
      }
    else if (!$this->setCharset($charset))
      {
        $this->disconnect();
      }

    return ($this->dbLink == null) ? false : true;
  }

  function disconnect()
  {
    if ($this->dbLink != null)
      {
        mysql_close($this->dbLink);
        $this->dbLink = null;
      }
  }

  function setCharset($charset)
  {
    if ($this->comparePhpVersion(array(5, 2, 3)))
      {
        $ret = mysql_set_charset($charset, $this->dbLink) ? true : false;

        if ($ret !== true)
          $this->lastError = "MySQL Error: " . mysql_error($this->dbLink);
      }
    else
      {
        $ret = $this->runQuery("SET NAMES '$charset';");
      }

    return $ret;
  }

  function start()
  {
    return $this->runQuery("START TRANSACTION;");
  }

  function commit()
  {
    return $this->runQuery("COMMIT;");
  }

  function rollback()
  {
    return $this->runQuery("ROLLBACK;");
  }

  /* ToDo: get the year-specific categories when this is implemented */
  function importCategories()
  {
    if ($this->clearTable($this->catsTableName) !== true)
      return false;

    if ($this->clearTable($this->evCatTableName) !== true)
      return false;

    $catsUrl = $this->url . "current/categories/";

    $xml = new DOMDocument();

    if ($xml->load($catsUrl) !== true)
      {
        $this->lastError = "Failed to load the categories";
        return false;
      }

    $root = $xml->documentElement;
    $cats = $root->getElementsByTagName('category');

    foreach ($cats as $cat)
      if ($this->importCategory($cat) !== true)
        return false;

    return true;
  }

  function importEvents($onlyCurrent=true)
  {
    if ($this->clearTable($this->eventsTableName) !== true)
      return false;

    if ($onlyCurrent === true)
      return $this->importFair('current');

    $xml = new DOMDocument();

    if ($xml->load($url) !== true)
      {
        $this->lastError = "Failed to load the list of fairs";
        return false;
      }

    $root = $xml->documentElement;
    $fairs = $root->getElementsByTagName('fair');

    foreach ($fairs as $f)
      {
        $date = $f->getElementsByTagName('date');
        $date_ele = $date->item(0);

        if ($date_ele == NULL)
          {
            $this->lastError = "Failed to get fair date";
            return false;
          }

        $year = $date_ele->getAttribute('year');

        if ($this->importFair($year) !== true)
          return false;
      }
  }

  function resetEventsTable()
  {
    $q =
" id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  year INT UNSIGNED NOT NULL,
  date DATE NOT NULL,
  time TIME,
  end_date DATE,
  end_time TIME,
  name VARCHAR(128) NOT NULL,
  description TEXT NOT NULL,
  venue VARCHAR(128) NOT NULL,
  line_1 VARCHAR(64) NOT NULL,
  line_2 VARCHAR(64) NOT NULL,
  line_3 VARCHAR(64) not null,
  postcode VARCHAR(16) NOT NULL,
  town VARCHAR(64) NOT NULL,
  addr_order INT UNSIGNED NOT NULL,
  addr_sub_order INT UNSIGNED NOT NULL,
  website VARCHAR(256) NOT NULL,
  image_url VARCHAR(256) NOT NULL,
  image_width INT UNSIGNED,
  image_height INT UNSIGNED,
  age_min TINYINT UNSIGNED,
  age_max TINYINT UNSIGNED";

    return $this->resetTable($this->eventsTableName, $q);
  }

  function resetCategoriesTable()
  {
    $q = "id INT UNSIGNED PRIMARY KEY, name VARCHAR(64)";
    return $this->resetTable($this->catsTableName, $q);
  }

  function resetEventCategoryTable()
  {
    $q = "event_id INT UNSIGNED NOT NULL, cat_id INT UNSIGNED NOT NULL";
    return $this->resetTable($this->evCatTableName, $q);
  }

  /* --------------------------------------------------------------------------
   * private functions
   */

  private function setPhpVersion()
  {
    $version = phpversion();
    $sep = '.-';
    $tok = strtok($version, $sep);
    $this->phpVersion = array();

    while ($tok !== false)
      {
        $this->phpVersion[] = (int) $tok;
        $tok = strtok($sep);
      }
  }

  private function comparePhpVersion($version)
  {
    for ($i = 0; $i < 3; $i++)
      {
        if ($this->phpVersion[$i] > $version[$i])
          return true;

        if ($this->phpVersion[$i] < $version[$i])
          return false;
      }

    return true;
  }

  private function runQuery($q)
  {
    if (!mysql_query($q, $this->dbLink))
      {
        $this->lastError = "MySQL Error: " . mysql_error($this->dbLink);
        return false;
      }

    return true;
  }

  private function clearTable($name)
  {
    return $this->runQuery("DELETE FROM $name;");
  }

  private function resetTable($name, $query)
  {
    if ($this->dropTable($name) !== true)
      return false;

    return $this->runQuery("CREATE TABLE $name ($query);");
  }

  private function dropTable($table)
  {
    return $this->runQuery("DROP TABLE IF EXISTS $table;");
  }

  private function importCategory($cat)
  {
    $id = mysql_real_escape_string($cat->getAttribute('id'));
    $name = mysql_real_escape_string($cat->getAttribute('name'));

    $q =
      "INSERT INTO " . $this->catsTableName
      . " (id, name) VALUES ('$id', '$name')";

    return $this->runQuery($q);
  }

  private function importFair($fairYear)
  {
    $fairUrl = $this->url . $fairYear . "/dump/";

    $xml = new DOMDocument();

    if ($xml->load($fairUrl) !== true)
      {
        $this->lastError = "Failed to load the fair events for $year";
        return false;
      }

    $root = $xml->documentElement;
    $year = $root->getAttribute('year');
    $month = $root->getAttribute('month');
    $day = $root->getAttribute('day');
    $events = $root->getElementsByTagName('event');

    foreach ($events as $e)
      {
        if ($this->importEvent($e, $year, $month, $day) !== true)
          return false;

        if ($this->importEventCat($e) !== true)
          return false;
      }

    return true;
  }

  private function importEvent($e, $year, $month, $day)
  {
    $id = mysql_real_escape_string($e->getAttribute('id'));
    $name = mysql_real_escape_string($e->getAttribute('name'));
    $venue = mysql_real_escape_string($e->getAttribute('venue'));

    $imgs = $e->getElementsByTagName('image');
    $img = $imgs->item(0);

    if ($img != NULL)
      {
        $img_url =
          mysql_real_escape_string($this->siteUrl . $img->getAttribute('url'));
        $img_width = $img->getAttribute('width');
        $img_height = $img->getAttribute('height');
      }
    else
      {
        $img_url = '';
        $img_width = 0;
        $img_height = 0;
      }

    $descs = $e->getElementsByTagName('description');
    $desc = $descs->item(0);

    if ($desc != NULL)
      $description = mysql_real_escape_string(trim($desc->nodeValue));
    else
      $description = '';

    $addrs = $e->getElementsByTagName('address');
    $addr = $addrs->item(0);

    if ($addr == NULL)
      {
        $this->lastError = "no address found in event information";
        return false;
      }

    $line_1 = mysql_real_escape_string($addr->getAttribute('line_1'));
    $line_2 = mysql_real_escape_string($addr->getAttribute('line_2'));
    $line_3 = mysql_real_escape_string($addr->getAttribute('line_3'));
    $postcode =
      mysql_real_escape_string($addr->getAttribute('postcode'));
    $town = mysql_real_escape_string($addr->getAttribute('town'));
    $order = mysql_real_escape_string($addr->getAttribute('addr_order'));
    $subOrder = mysql_real_escape_string($addr->getAttribute('addr_suborder'));
    $website = mysql_real_escape_string($addr->getAttribute('website'));

    $ev_year = 0;
    $ev_month = 0;
    $ev_day = 0;

    if ($this->getDate($e, 'date', $ev_year, $ev_month, $ev_day) == true)
      $date = sprintf("'%d-%d-%d'", $ev_year, $ev_month, $ev_day);
    else
      $date = sprintf("'%d-%d-%d'", $year, $month, $day);

    if ($this->getDate($e, 'end_date', $ev_year, $ev_month, $ev_day) == true)
      $end_date = sprintf("'%d-%d-%d'", $ev_year, $ev_month, $ev_day);
    else
      $end_date = 'NULL';

    $ev_hour = 0;
    $ev_minute = 0;

    if ($this->getTime($e, 'time', $ev_hour, $ev_minute) == true)
      $time = sprintf("'%d:%d'", $ev_hour, $ev_minute);
    else
      $time = 'NULL';

    if ($this->getTime($e, 'end_time', $ev_hour, $ev_minute) == true)
      $end_time = sprintf("'%d:%d'", $ev_hour, $ev_minute);
    else
      $end_time = 'NULL';

    $ages = $e->getElementsByTagName('age');
    $age = $ages->item(0);
    $age_min = $this->getAgeValue($age, 'min');
    $age_max = $this->getAgeValue($age, 'max');

    $q =
"INSERT INTO " . $this->eventsTableName . " (
  id, year, date, time, end_date, end_time, name, description,
  venue, line_1, line_2, line_3, postcode, town,
  addr_order, addr_sub_order, website, image_url, image_width, image_height,
  age_min, age_max)
VALUES (
  $id, $year, $date, $time, $end_date, $end_time, '$name', '$description',
  '$venue', '$line_1', '$line_2', '$line_3', '$postcode', '$town',
  $order, $subOrder, '$website', '$img_url', $img_width, $img_height,
  $age_min, $age_max);";

    return $this->runQuery($q);
  }

  private function importEventCat($e)
  {
    $id = $e->getAttribute('id');
    $cats = $e->getElementsByTagName('category');

    foreach ($cats as $cat)
      {
        $catId = $cat->getAttribute('id');

        $q =
          "INSERT INTO " . $this->evCatTableName
          . " (event_id, cat_id) VALUES ($id, $catId);";

        if ($this->runQuery($q) !== true)
          return false;
      }

    return true;
  }

  private function getDate($ele, $tag, &$year, &$month, &$day)
  {
    $dates = $ele->getElementsByTagName($tag);
    $date = $dates->item(0);

    if ($date != NULL)
      {
        $year = (int) $date->getAttribute('year');
        $month = (int) $date->getAttribute('month');
        $day = (int) $date->getAttribute('day');
        return true;
      }
    else
      {
        return false;
      }
  }

  private function getTime($ele, $tag, &$hour, &$minute)
  {
    $times = $ele->getElementsByTagName($tag);
    $time = $times->item(0);

    if ($time != NULL)
      {
        $hour = (int) $time->getAttribute('hour');
        $minute = (int) $time->getAttribute('minute');
        return true;
      }
    else
      {
        return false;
      }
  }

  private function getAgeValue($e, $attr)
  {
    if ($e != NULL)
      {
        $age = $e->getAttribute($attr);

        if ($age != NULL)
          return (int) $age;
      }

    return 'NULL';
  }
}

?>
