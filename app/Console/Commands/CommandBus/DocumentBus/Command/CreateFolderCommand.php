<?php

namespace App\Console\Commands\CommandBus\DocumentBus\Command;


class CreateFolderCommand
{
    private $folderId = null;
    private $name;

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

    public function setName(string $name): void
    {
        $this->name = $name;
    }

    public function getName()
    {
        return $this->name;
    }
}
