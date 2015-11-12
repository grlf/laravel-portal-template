<?php


namespace App\Http\Utilities;


class Thumbnail {


    public function isPhoto($path)
    {

        if ( exif_imagetype($path) ) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * @param \App\Repos\Files\File $file
     * @param int $sizeConstraint
     */
    public function make($file, $sizeConstraint = 170)
    {

        if ( !$this->isPhoto($file->getSystemPath() . $file->filename) ) {
            return;
        }

        // get and make sure the thumbnail system path is created
        $thumbnail_system_path = $file->getThumbnailSystemPath();
        $thumbnail_url_path = $file->getThumbnailURLPath();
        $thumbnail_name = '$sizeConstraint-' . $file->filename;

        \Image::make($file->getSystemPath() . $file->filename)
            ->resize($sizeConstraint, null, function ($constraint) {
                $constraint->aspectRatio();
            })
            ->save($thumbnail_system_path . $thumbnail_name);
        $file->thumbnail = $thumbnail_url_path . $thumbnail_name;


    }

}