<?php

namespace App\Console\Commands\CommandBus\DocumentBus\Command;


class CreateFolderCommand
{
    private $folderId = null;

    /**
     * @param  null  $folderId
     */
    public function setFolderId($folderId): void
    {
        $this->folderId = $folderId;
    }

    /**
     * @return null
     */
    public function getFolderId()
    {
        return $this->folderId;
    }
}
