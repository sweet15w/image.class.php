<?php

class Image {
    public $format;
    public $image;
    public $filename;
    public $width;
    public $height;
    public $path;
    public $name;
    public $quality;
    public $size;
    public $watermark;
    public $prefix;

    function __construct($filename) {
        $this->filename = $filename;

        try {
            if(!file_exists($this->filename)) {
                throw new Exception("Filename is not exists");
            }

            $mime = strtolower(getimagesize($this->filename)['mime']);

            switch($mime) {
                case "image/jpeg":
                    $this->image = imagecreatefromjpeg($this->filename);
                    $this->format = "jpg";
                    break;

                case "image/gif":
                    $this->image = imagecreatefromgif($this->filename);
                    $this->format = "gif";
                    break;

                case "image/png":
                    $this->image = imagecreatefrompng($this->filename);
                    $this->format = "png";
                    break;

                default:
                    throw new Exception("Trying to create unsupported image format");
            }
        } catch(Exception $error) {
            throw new Exception([$error->getFile(), $error->getLine(), $error->getMessage()]);
        }
    }

    function save($params) {
        $this->width = isset($params["width"]) ? $params["width"] : 150;
        $this->height = isset($params["height"]) ? $params["height"] : 150;
        $this->path = isset($params["path"]) ? $params["path"] : ".";
        $this->name = isset($params["name"]) ?
            preg_replace("/\.".$this->format."$/", "", $params["name"]) :
            strtotime("now")."-".rand(1000000000, 9999999999);
        $this->quality = isset($params["quality"]) ? $params["quality"] : 100;
        $this->size = isset($params["size"]) ? $params["size"] : "proportion";
        $this->watermark = isset($params["watermark"]) ? $params["watermark"] : null;
        $this->prefix = isset($params["prefix"]) ? $params["prefix"] : null;

        if(!is_dir($this->path)) {
            throw new Exception("Directory not found: ".$this->path);
        }

        switch($this->size) {
            case "proportion":
                return $this->saveProportion();

            case "fixed":
                return $this->saveFixed();

            case "crop":
                return $this->saveCrop();

            case "noresize":
                return $this->saveNoresize();

            case "square":
                return $this->saveSquare();

            default:
                return $this->image = null;
        }
    }

    function __saveImage($image) {
        try {
            $file = $this->path.$this->prefix.$this->name.".".$this->format;

            switch ($this->format) {
                case "jpg":
                    imagejpeg($image, $file, $this->quality);
                    break;

                case "gif" :
                    imagegif($image, $file);
                    break;

                case "png" :
                    imagepng($image, $file, 9);
                    break;
            }

            return $this->prefix.$this->name.".".$this->format;
        } catch(Exception $error) {
            throw new Exception([$error->getFile(), $error->getLine(), $error->getMessage()]);
        }
    }

    function saveProportion() {
        list($width, $height) = getimagesize($this->filename);

        if($width >= $height) {
            $this->height = $height / $width * $this->width;
        } else {
            $this->width = $width / $height * $this->height;
        }

        $image = imagecreatetruecolor($this->width, $this->height);
        $transparent = imagecolorallocatealpha($image, 0, 0, 0, 127);

        imagefill($image, 0, 0, $transparent);
        imagesavealpha($image, true);
        imagecopyresampled($image, $this->image, 0, 0, 0, 0, $this->width, $this->height, $width, $height);

        $this->setWatermark($image);

        return $this->__saveImage($image);
    }


    function saveFixed() {
        list($width, $height) = getimagesize($this->filename);

        if($width >= $height) {
            //$this->height = $height / $width * $this->width;
        } else {
            //$this->width = $width / $height * $this->height;
        }

        $image=imagecreatetruecolor($this->width, $this->height);
        $transparent = imagecolorallocatealpha($image, 0, 0, 0, 127);

        imagefill($image, 0, 0, $transparent);
        imagesavealpha($image, true);
        imagecopyresampled($image, $this->image, 0, 0, 0, 0, $this->width, $this->height, $width, $height);

        $this->setWatermark($image);

        return $this->__saveImage($image);
    }

    function saveCrop() {
        list($width, $height) = getimagesize($this->filename);

        $image=imagecreatetruecolor($this->width, $this->height);
        $transparent = imagecolorallocatealpha($image, 0, 0, 0, 127);

        imagefill($image, 0, 0, $transparent);
        imagesavealpha($image, true);
        imagecopyresampled($image, $this->image, 0, 0, ($width - $this->width) / 2, ($height - $this->height) / 2, $this->width, $this->height, $this->width, $this->height);

        $this->setWatermark($image);

        return $this->__saveImage($image);
    }

    function saveNoresize() {
        list($width,$height) = getimagesize($this->filename);

        $this->height = $height;
        $this->width = $width;

        $image=imagecreatetruecolor($this->width, $this->height);
        $transparent = imagecolorallocatealpha($image, 0, 0, 0, 127);

        imagefill($image, 0, 0, $transparent);
        imagesavealpha($image, true);
        imagecopyresampled($image, $this->image, 0, 0, 0, 0, $this->width, $this->height, $width, $height);

        $this->setWatermark($image);

        return $this->__saveImage($image);
    }

    function saveSquare() {
        list($width,$height) = getimagesize($this->filename);

        if($width >= $height) {
            $this->width = $this->height;
            $__width2 = $height;
            $__height2 = $height;
        } else {
            $this->height = $this->width;
            $__width2 = $width;
            $__height2 = $width;
        }

        $image=imagecreatetruecolor($this->width, $this->height);
        $transparent = imagecolorallocatealpha($image, 0, 0, 0, 127);

        imagefill($image, 0, 0, $transparent);
        imagesavealpha($image, true);
        imagecopyresampled($image, $this->image, 0, 0, ($width - $__width2) / 2, ($height - $__height2) / 2, $this->width, $this->height, $__width2, $__height2);

        $this->setWatermark($image);

        return $this->__saveImage($image);
    }


    function setWatermark($image) {
        if (empty($this->watermark)) {
            return;
        }

        try {
            $watermark_fileinfo = getimagesize($this->watermark);

            switch(strtolower($watermark_fileinfo['mime'])) {
                case "image/jpeg":
                    $watermark_image = imagecreatefromjpeg($this->watermark);
                    break;

                case "image/gif":
                    $watermark_image = imagecreatefromgif($this->watermark);
                    break;

                case "image/png":
                    $watermark_image = imagecreatefrompng($this->watermark);
                    break;

                default:
                    throw new Exception("Trying to create unsupported image format for watermark");
            }

            imagecopy($image, $watermark_image, ($this->width - $watermark_fileinfo[0]) - 10, ($this->height - $watermark_fileinfo[1]) - 10, 0, 0, $watermark_fileinfo[0], $watermark_fileinfo[1]);
        } catch(Exception $error) {
            throw new Exception([$error->getFile(), $error->getLine(), $error->getMessage()]);
        }
    }

    function __destruct() {
        $this->format = null;
        $this->image = null;
        $this->filename = null;
        $this->width = null;
        $this->height = null;
        $this->path = null;
        $this->name = null;
        $this->quality = null;
        $this->size = null;
        $this->watermark = null;
        $this->prefix = null;
    }
}
?>