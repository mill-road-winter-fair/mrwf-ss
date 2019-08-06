<?php
global $project;
$project = 'mysite';

global $databaseConfig;
$databaseConfig = array(
	"type" => 'MySQLDatabase',
	"server" => 'localhost:3306',
	"username" => 'ss_millroadwf',
	"password" => 'if-you-think-this-is-a-real-password-i-have-a-bridge-for-sale',
	"database" => 'ss_millroadwf',
	"path" => '',
);

// Sites running on the following servers will be
// run in development mode. See
// http://doc.silverstripe.org/doku.php?id=configuration
// for a description of what dev mode does.

Director::set_dev_servers(array(
	//'localhost',
	'www.incrypt.co.uk',
	'127.0.0.1',
	//'192.168.122.205'
));
//Director::set_environment_type('dev');

//=====================================
//== Debugging

// if(!Director::isDev()) Debug::send_errors_to('it@millroadwinterfair.org', true);

// Note: 'reporting' is not the same as 'displaying'
//error_reporting(E_ALL);

// Show an error message in the CMS instead of an empty message box and 'error loading page'
//ini_set("display_errors","1");

//from pippins
//ini_set("log_errors", "On");
//ini_set("error_log", "../ss-log/silverstripe.log");

//Debug::log_errors_to("../ss-log/silverstripe.log");
// then in code do:
//Debug::Log( 'message to be logged' . $some_variable );

SS_Log::add_writer(new SS_LogFileWriter('../silverstripe-log/silverstripe.log'), SS_Log::NOTICE);
//SS_Log::log( New Exception('--message-goes-here---'), SS_Log::NOTICE );

//=====================================

// Temporarily uncomment the following line to recover the admin account if the usernme & password is lost
//Security::setDefaultAdmin('default','default');

//=====================================

// This line set's the current theme. More themes can be
// downloaded from http://www.silverstripe.org/themes/
SSViewer::set_theme('millroadwinterfair');

MySQLDatabase::set_connection_charset('utf8');

// Set the site locale
i18n::set_locale('en_GB');
i18n::set_default_locale('en_GB');

// enable nested URLs for this site (e.g. page/sub-page/)
// SiteTree::enable_nested_urls();

// HTML editor configuration
// See http://wiki.moxiecode.com/index.php/TinyMCE:Control_reference
HtmlEditorConfig::get('cms')->insertButtonsAfter('redo', 'removeformat');

// Note: after adding a sortable class, the database needs to be rebuilt... dev/build?flush=1
SortableDataObject::add_sortable_classes( array('SlideObject','PickObject') );

DataObjectManager::allow_assets_override(false);

FulltextSearchable::enable();

//Requirements::set_write_js_to_body(false);

DataObject::add_extension('SiteConfig', 'SiteConfigOverride');

// Enable moderation of comments
//PageComment::enableModeration();

// Cache for 24 hours
SS_Cache::set_cache_lifetime('any', 24 * 60 * 60);
// To disable cacheing
//SS_Cache::set_cache_lifetime('any', -1);

Object::add_extension('Page', 'SlideshowDecorator');
Object::add_extension('Page_Controller', 'SlideshowDecorator_Controller');
SlideObject::SetSlideDimensions( 780, 337);

FlickrService::setAPIKey('if-you-think-this-is-a-real-key-i-have-a-bridge-to-sell-you');

