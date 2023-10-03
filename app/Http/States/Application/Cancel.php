<?php

    namespace App\Http\States\Application;

    use App\Enums\ApplicationStatus;

    class Cancel extends ApplicationState
    {
        public static $name = ApplicationStatus::CANCEL;
//        public function status():string
//        {
//            return (string)ApplicationStatus::CANCEL;
//        }
    }
