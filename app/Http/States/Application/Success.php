<?php

    namespace App\Http\States\Application;

    use App\Enums\ApplicationStatus;

    class Success extends ApplicationState
    {
        public function status()
        {
           return ApplicationStatus::SUCCESS;
        }
    }
