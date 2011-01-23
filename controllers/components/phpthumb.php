<?php

class PhpthumbComponent extends Object {

    /**
     * Setting PHPThumb object
     * @return object
     */
    private function getPHPThumb() {
        App::import('Vendor', 'phpthumb', array(
            'file'   => 'phpthumb' . DS . 'phpthumb.class.php',
            'plugin' => 'phpthumb'
        ));
        $phpThumb = new phpThumb();
        $phpThumb->SetParameter('config_temp_directory', CACHE);
        $phpThumb->SetParameter('config_document_root', ROOT);
        $phpThumb->SetParameter('config_max_source_pixels', 0);
        $phpThumb->SetParameter('config_imagemagick_path', Configure::read('ImageMagick.path') ? Configure::read('ImageMagick.path') : '/usr/bin/convert');
        $phpThumb->SetParameter('config_prefer_imagemagick', true);
        $phpThumb->SetParameter('config_imagemagick_use_thumbnail', true);
        $phpThumb->SetParameter('config_use_exif_thumbnail_for_speed', true);
        $phpThumb->SetParameter('config_error_message_image_default', 'Image not found');
        return $phpThumb;
    }

    /**
     * Create thumbnail
     *
     * @param string $method
     * @param string $file
     * @param string $size
     * @param array $options
     * @return mixed
     */
    public function create($method, $file, $size, $options = array()) {
        if(!file_exists($file)) return false;
        $phpThumb = $this->getPHPThumb();
        $phpThumb->setSourceFilename($file);
        $phpThumb->setParameter('config_output_format', low(array_pop(explode(".", basename($file))))); // Output file format
        $phpThumb->setParameter('q', 95); // JPEG compression
        $w = null;
        $h = null;
        if (strpos($size, "x")) {
            $size = explode("x", $size);
            $w = $size[0];
            $h = $size[1];
        } else {
            $w = $size;
            $h = $size;
        }
        if ($w) $phpThumb->setParameter('w', $w); // Width
        if ($h) $phpThumb->setParameter('h', $h); // Height
        $phpThumb->setParameter('bg', 'FFFFFF'); // background hex color
        if ($method == 'crop') {
            $phpThumb->setParameter('zc', "C"); // Zoom cropping
        }
        if ($method == 'rcrop') {
            $phpThumb->setParameter('zc', "C"); // Zoom cropping
            $phpThumb->setParameter('fltr', implode("|", array('ric', 6, 6))); // Round conrner
            $phpThumb->setParameter('config_output_format', 'png');
        }
        // Custome usage of the ffmpeg
        if (is_array($method)) {
            foreach ($method as $key => $value) {
                $phpThumb->setParameter($key, $value);
            }
        }
        if (!empty($options['wmt'])) {
            $phpThumb->setParameter('fltr', implode("|", array('wmt', $options['wmt'], '2', 'C', '000000')));
        }
        if (!empty($options['f'])) {
            $phpThumb->setParameter('config_output_format', $options['f']); // Output file format
        }
        $phpThumb->generateThumbnail();
        $phpThumb->RenderOutput();
        return $phpThumb->outputImageData;
    }
}
