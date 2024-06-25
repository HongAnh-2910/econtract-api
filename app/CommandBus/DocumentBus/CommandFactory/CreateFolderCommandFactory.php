<?php

    namespace App\CommandBus\DocumentBus\CommandFactory;

    use App\Console\Commands\CommandBus\DocumentBus\Command\CreateFolderCommand;
    use Illuminate\Http\Request;

    class CreateFolderCommandFactory
    {
        public static function make(Request $request):CreateFolderCommand
        {
            $command  = new CreateFolderCommand();
            $command->setFolderId($request->get('folder_id'));
            $command->setName($request->get('name'));
            return $command;
        }
    }
