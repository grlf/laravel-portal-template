<?php

namespace App\Repos\Files;

use App\Repos\RepositoryInterface;

interface FileRepositoryInterface extends RepositoryInterface {

    /**
     * Method to delete multiple files by a file id
     *
     * @param array $fileIds An array of file ids to delete
     * @return bool
     */
    public function deleteMultipleFiles($fileIds);

}