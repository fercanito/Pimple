<?php
/**
 * The pimple controller is used to give some basic functionality that has to be exposed via urls
 */
class PimpleController extends Controller {

    /**
     * Renders a captcha
     * @See FormTagLib::tagCaptcha
     */
    public function captcha() {
        $width = Request::get('w',210);
        $height = Request::get('h',40);
        $characters = Request::get('c',6);
        $font = Pimple::instance()->getRessource('monofont.ttf');
        
        $possible = '23456789bcdfghjkmnpqrstvwxyz';
        $code = '';
        $i = 0;
        while ($i < $characters) {
         $code .= substr($possible, mt_rand(0, strlen($possible)-1), 1);
         $i++;
        }
        /* font size will be 75% of the image height */
        $font_size = $height * 0.75;
        $image = imagecreate($width, $height) or die('Cannot initialize new GD image stream');
        /* set the colours */
        $background_color = imagecolorallocate($image, 255, 255, 255);
        $text_color = imagecolorallocate($image, 20, 40, 100);
        $noise_color = imagecolorallocate($image, 100, 120, 180);
        /* generate random dots in background */
        for( $i=0; $i<($width*$height)/3; $i++ ) {
         imagefilledellipse($image, mt_rand(0,$width), mt_rand(0,$height), 1, 1, $noise_color);
        }
        /* generate random lines in background */
        for( $i=0; $i<($width*$height)/150; $i++ ) {
         imageline($image, mt_rand(0,$width), mt_rand(0,$height), mt_rand(0,$width), mt_rand(0,$height), $noise_color);
        }
        /* create textbox and add text */
        $textbox = imagettfbbox($font_size, 0, $font, $code) or die('Error in imagettfbbox function');
        $x = ($width - $textbox[4])/2;
        $y = ($height - $textbox[5])/2;
        imagettftext($image, $font_size, 0, $x, $y, $text_color, $font , $code) or die('Error in imagettftext function');
        /* output captcha image to browser */
        header('Content-Type: image/jpeg');
        imagejpeg($image);
        imagedestroy($image);
        SessionHandler::set('CAPTCHA',$code);
        Pimple::end();
    }
    /**
     * Previews an email - as it would look rendered
     * Uses all GET parms as variables
     * @param string view | the name of the view
     * @param string container | the name of the container - defaults to {view/mail.php}
     * @param boolean textonly | show the text only version - defaults to false
     */
    public function mailpreview() {
        $data = Request::get();
        $view = $data->view;

        $mail = Mail::preview($view,$data->toArray(),$data->container,$data->textonly);
        if ($data->textonly) {
            $this->asText(trim($mail));
        } else {
            echo $mail;
        }
        Pimple::end();
    }
    /**
     * Outputs concatenated javascript for the specified view
     * @param string view | the view - optional
     * @param boolean skipLayout | skip {view/application.php} - defaults to false 
     */
    public function javascript() {
        $this->setContentType('text/javascript; charset=utf-8;');
        $this->setCache(Date::SPAN_MONTH);
        //set_time_limit(0);
        require_once Pimple::instance()->getBaseDir().'lib/Javascript.php';
        $cacheDir = Pimple::instance()->getSiteDir().'cache/js/';
        Dir::ensure($cacheDir);
        $templates = array();
        if (!Request::get('skipLayout',false)) {
            $templates[] = 'application';
        }
        $view = Request::get('view',false);
        if ($view) {
            $templates[] = $view;
        }
        $used = array();
        $isDebug = Settings::get(Settings::DEBUG,false);
        foreach($templates as $template) {
            $cacheFile = $cacheDir.$template.'.js';
            echo "// $template\n";
            if (!$isDebug)
                Dir::ensure(dirname($cacheFile));
            if ($isDebug) {
                $view = new View($template);
                $files = $view->getInternalJsFiles();
				
                echo("/*FILES:\n\t".implode("\n\t",$files).'*/'.chr(10));
                foreach($files as $file) {
                    if (in_array($file,$used)
                            || String::StartsWith($file,"http://")
                            || String::StartsWith($file,"https://")) continue;
                    $used[] = $file;
                    echo("/*FILE:".basename($file).'*/'.chr(10).String::normalize(@file_get_contents($file),false));
                    echo(chr(10));
                }
            } else {
                if (!is_file($cacheFile)) {
                    File::truncate($cacheFile);
                    $view = new View($template);
                    $files = $view->getInternalJsFiles();
                    File::append($cacheFile,"/*FILES:\n\t".implode("\n\t",$files).'*/'.chr(10));
                    foreach($files as $file) {
                        if (in_array($file,$used)
                            || String::StartsWith($file,"http://")
                            || String::StartsWith($file,"https://")) continue;
                        $used[] = $file;
                        File::append($cacheFile,"/*FILE:".basename($file).'*/'.chr(10).String::normalize(@file_get_contents($file),false));
                        File::append($cacheFile,chr(10));
                    }
                }
                echo file_get_contents($cacheFile);
            }
        }

        Pimple::end();
    }
    /**
     * Outputs concatenated CSS for the specified view
     * @param string view | the view - optional
     */
    public function css() {
        $this->setContentType('text/css; charset=utf-8;');
		$this->setCache(Date::SPAN_MONTH);
        require_once Pimple::instance()->getBaseDir().'lib/Stylesheet.php';
        $cacheDir = Pimple::instance()->getSiteDir().'cache/css/';
        Dir::ensure($cacheDir);
        $templates = array();
        
        if (!Request::get('skipLayout',false)) {
            $templates[] = 'application';
        }
        
        $view = Request::get('view',false);
        if ($view) {
            $templates[] = $view;
        }
        $used = array();
        $isDebug = Settings::get(Settings::DEBUG,false);
        foreach($templates as $template) {
            $cacheFile = $cacheDir.$template.'.css';
            echo "/* $template */\n";
            if ($isDebug) {
                $view = new View($template);
                $files = $view->getInternalCssFiles();
                echo("/*FILES:\n\t".implode("\n\t",$files).'*/'.chr(10));
                foreach($files as $file) {
                    if (in_array($file,$used)
                            || String::StartsWith($file,"http://")
                            || String::StartsWith($file,"https://")) continue;
                    $used[] = $file;
                    echo("/*FILE:".basename($file).'*/'.chr(10).Stylesheet::minify($file).chr(10));
                }
            } else {
                Dir::ensure(dirname($cacheFile));
                if (!is_file($cacheFile)) {
                    File::truncate($cacheFile);
                    $view = new View($template);
                    $files = $view->getInternalCssFiles();
                    File::append($cacheFile,"/*FILES:\n\t".implode("\n\t",$files).'*/'.chr(10));
                    foreach($files as $file) {
                        if (in_array($file,$used)
                            || String::StartsWith($file,"http://")
                            || String::StartsWith($file,"https://")) continue;
                        $used[] = $file;
                        File::append($cacheFile,"/*FILE:".basename($file).'*/'.chr(10).Stylesheet::minify($file).chr(10));
                    }
                }
                echo file_get_contents($cacheFile);
            }
        }

        Pimple::end();
    }
    /**
     * Show pimple reference (only available in dev mode)
     */
    public function ref() {
        if (Pimple::getEnvironment() != 'development') {
            Pimple::end('Not allowed');
        }
        include_once 'ref/RefReader.php';
        $this->setSkipLayout(true);
        $reader = new RefReader();
        //$reader->read(PIMPLEBASE.'/lib/taglib/FormTagLib.php','RefTagLib','RefTagLibMethod');
        
        $reader->read(BASEDIR.'/taglib/','RefTagLib','RefTagLibMethod');
        $reader->read(PIMPLEBASE.'/lib/taglib/','RefTagLib','RefTagLibMethod');
        $tags = $reader->getClass('FormTagLib')->getTags();
        
        $taglibs = $reader->getClasses();
        
        
        $reader = new RefReader();
        $reader->read(BASEDIR.'/controller/','RefController','RefControllerMethod');
        $reader->read(PIMPLEBASE.'/lib/controller/','RefController','RefControllerMethod');
        $controllers = $reader->getClasses();
        return array('taglibs'=>$taglibs,'controllers'=>$controllers);
    }
}