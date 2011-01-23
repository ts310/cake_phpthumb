# Simple PHPThumb wrapper component

Download and phpThumb from http://phpthumb.sourceforge.net/
and extract to vendors/phpthumb

Usage : 
    
      App::import('Component', 'Phpthumb.Phpthumb');
      $Thumbnail = new PhpthumbComponent;
      $data = $this->PhpThumb->create('crop', '50x100');
      $cache = '/path/to/save/as/cache/image/file/filename.ext';
      $fp = fopen($cache, 'w');
      fwrite($fp, $data);
      fclose($fp);
      chmod($cache, 0777);
