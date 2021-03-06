<?php
require_once 'lib/Server.php';

define('BASEDIR', Server::IsPlatform(Server::PLATFORM_WINDOWS) ? exec('echo %cd%') : exec('pwd'));
define('PIMPLEBASE', dirname(__FILE__));
if (BASEURL == 'BASEURL')
    throw new Exception("BASEURL must be defined!");
    
if (!is_dir(BASEDIR))
    throw new Exception(BASEDIR." not found!");

//Various functions
require_once 'lib/functions.php';
require_once 'lib/interfaces/wrappers.php';
require_once 'lib/Settings.php';
require_once 'lib/IncludePath.php';

//Zend easy access 
require_once 'lib/Zend.php';

//DB Handling
require_once 'lib/Db.php';

//Localization
require_once 'lib/Locale.php';
require_once 'lib/Validate.php';

//Mail handling
require_once 'lib/Mail.php';

//Pdf handling
require_once 'lib/Pdf.php';

//Filesystem handling
require_once 'lib/Url.php';
require_once 'lib/File.php';
require_once 'lib/Dir.php';

//Utils
require_once 'lib/Date.php';
require_once 'lib/Cli.php';
require_once 'lib/Util.php';
require_once 'lib/Math.php';
require_once 'lib/String.php';
require_once 'lib/ArrayUtil.php';
require_once 'lib/DataUtil.php';
require_once 'lib/Thread.php';

//MVC
require_once 'lib/Request.php';
require_once 'lib/model/ISession.php';
require_once 'lib/model/IUser.php';
require_once 'lib/Model.php';
require_once 'lib/Phtml.php';
require_once 'lib/View.php';
require_once 'lib/Controller.php';
require_once 'lib/controller/PimpleController.php';

//Handlers
require_once 'lib/handlers/MessageHandler.php';
require_once 'lib/handlers/SessionHandler.php';
require_once 'lib/handlers/AccessHandler.php';



//Main class
require_once 'lib/Pimple.php';

require_once 'lib/TagLib.php';
require_once 'lib/taglib/CoreTagLib.php';
require_once 'lib/taglib/ValueTagLib.php';
require_once 'lib/taglib/BasicTagLib.php';
require_once 'lib/taglib/FormTagLib.php';
require_once 'lib/taglib/WidgetTagLib.php';
require_once 'lib/taglib/JavascriptTagLib.php';
require_once 'lib/taglib/UserTagLib.php';
require_once 'lib/taglib/JSTemplateTagLib.php';

//Add pimple lib to include path
IncludePath::instance()->addPath(Pimple::instance()->getBaseDir().'/lib');
//Add pimple ressources lib to include path
IncludePath::instance()->addPath(Pimple::instance()->getRessource('lib/'),1);

define('CACHEDIR',Dir::normalize(BASEDIR).'cache');
Dir::ensure(CACHEDIR);

Pimple::instance()->registerTagLib('c',new CoreTagLib());
Pimple::instance()->registerTagLib('val',new ValueTagLib());
Pimple::instance()->registerTagLib('p',new BasicTagLib());
Pimple::instance()->registerTagLib('user',new UserTagLib());
Pimple::instance()->registerTagLib('f',new FormTagLib());
Pimple::instance()->registerTagLib('w',new WidgetTagLib());
Pimple::instance()->registerTagLib('js',new JavascriptTagLib());
Pimple::instance()->registerTagLib('jst',new JSTemplateTagLib());

