<?php

    namespace App\Http\States\Application;

    use App\Enums\ApplicationStatus;

    class Pending extends ApplicationState
    {
        public static $name = ApplicationStatus::PENDING;
//        public function status():string
//        {
//            return  (string)ApplicationStatus::PENDING;
//        }
    }
