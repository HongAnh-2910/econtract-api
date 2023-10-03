<?php

    namespace App\Http\States\Application;

    use App\Enums\ApplicationStatus;

    class Success extends ApplicationState
    {
        public static $name = ApplicationStatus::SUCCESS;
//        public function status():string
//        {
//           return (string) ApplicationStatus::SUCCESS;
//        }
    }
