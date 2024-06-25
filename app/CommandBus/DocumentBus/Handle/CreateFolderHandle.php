<?php

    namespace App\CommandBus\DocumentBus\Handle;

    use App\Console\Commands\CommandBus\DocumentBus\Command\CreateFolderCommand;
    use App\Models\Folder;
    use Illuminate\Support\Facades\Auth;

    class CreateFolderHandle
    {
        public function handle(CreateFolderCommand $create_folder_command)
        {
            $folderId = $create_folder_command->getFolderId();
            Folder::create([
                "name"      => $folder->name,
                "user_id"   => Auth::id(),
                "parent_id" => $folderIdNew,
                "slug"      => $folder->slug,
            ]);
        }
    }
