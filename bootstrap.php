<?php
define('BASEDIR',exec('pwd'));

if (BASEURL == 'BASEURL')
    throw new Exception("BASEURL must be defined!");

if (!is_dir(BASEDIR))
    throw new Exception(BASEDIR." not found!");

//Various functions
require_once 'lib/functions.php';
require_once 'lib/Settings.php';

//DB Handling
require_once 'lib/Db.php';

//Localization
require_once 'lib/Locale.php';

//Mail handling
require_once 'lib/Mail.php';

//Filesystem handling
require_once 'lib/Url.php';
require_once 'lib/File.php';
require_once 'lib/Dir.php';

//String handling
require_once 'lib/String.php';

//MVC
require_once 'lib/Request.php';
require_once 'lib/model/ISession.php';
require_once 'lib/model/IUser.php';
require_once 'lib/Model.php';
require_once 'lib/View.php';
require_once 'lib/Controller.php';
require_once 'lib/controller/PimpleController.php';

//Handlers
require_once 'lib/handlers/MessageHandler.php';
require_once 'lib/handlers/SessionHandler.php';
require_once 'lib/handlers/AccessHandler.php';

//Main class
require_once 'lib/TagLib.php';
require_once 'lib/taglib/CoreTagLib.php';
require_once 'lib/taglib/FormTagLib.php';
require_once 'lib/Pimple.php';
define('CACHEDIR',Dir::normalize(BASEDIR).'cache');
Dir::ensure(CACHEDIR);


