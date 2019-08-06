<?php

require_once ("mrwf_prog_import.php");

function reset_tables($up)
{
  if ($up->resetEventsTable() !== true)
    return false;

  if ($up->resetCategoriesTable() !== true)
    return false;

  if ($up->resetEventCategoryTable() !== true)
    return false;

  return true;
}

{
$DB_HOST = "localhost";	/* localhost:8306 */
$DB_NAME = "wp_millroadwf";
$DB_USER = "wp_millroadwf";
$DB_PASSWD = "Us5vRJfB3rLRjAKf";

/*
  $DB_HOST = "localhost";
  $DB_NAME = "web_fun";
  $DB_USER = "mrwf_prog";
  $DB_PASSWD = "xxxx";
*/

  $DB_EVENTS_TABLE = "events";
  $DB_CATS_TABLE = "categories";
  $DB_EVENT_CAT_TABLE = "event_category";
  $BASE_URL = "http://cams.millroadwinterfair.org";
  /*$BASE_URL = "http://localhost:8000";*/

  header('Content-type: text/plain');
  $start_time = microtime(1);
  printf("[%f] starting\n", $start_time);
  $up = new ProgrammeUpdater($BASE_URL, $DB_EVENTS_TABLE,
                             $DB_CATS_TABLE, $DB_EVENT_CAT_TABLE);

  $res = $up->connect($DB_HOST, $DB_USER, $DB_PASSWD, $DB_NAME);

  if ($res != true)
    {
      printf("failed to connect to database (%s)\n", $up->getError());
      $up->disconnect();
      exit(1);
    }

  if (array_key_exists('reset', $_GET))
    {
      print("doing reset\n");

      $res = reset_tables($up);

      if ($res != true)
        {
          printf("failed to reset the tables (%s)\n", $up->getError());
          $up->disconnect();
          exit(1);
        }
    }

  if (($res = $up->start()) !== true)
    printf("failed to start transaction (%s)\n", $up->getError());
  else if (($res = $up->importCategories()) !== true)
    printf("failed to import the categories (%s)\n", $up->getError());
  else if (($res = $up->importEvents()) !== true)
    printf("failed to import the events (%s)\n", $up->getError());
  else if (($res = $up->commit()) !== true)
    printf("failed to commit the changes (%s)\n", $up->getError());

  if ($res !== true)
    {
      if ($up->rollback() !== true)
        {
          $res = false;
          print("failed to roll back the changes!\n");
        }
    }

  if ($res === true)
    {
      $end_time = microtime(1);
      printf("[%f] done.\n", $end_time);

      $sql_n_events = mysql_query("SELECT COUNT(*) FROM $DB_EVENTS_TABLE;");

      if ($sql_n_events)
        {
          $array_n_events = mysql_fetch_array($sql_n_events, MYSQL_NUM);
          printf("%d events imported in %fs\n", $array_n_events[0],
                 ($end_time - $start_time));
        }
    }

  $up->disconnect();

  exit(($res === true) ? 0 : 1);
}

?>
