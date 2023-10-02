<?php

    namespace App\Http\States\Application;

    use App\Enums\ApplicationStatus;

    class Pending extends ApplicationState
    {
        public function status()
        {
            return ApplicationStatus::PENDING;
        }
    }
