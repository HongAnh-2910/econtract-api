<?php

    namespace App\Http\States\Application;

    use App\Enums\ApplicationStatus;

    class Cancel extends ApplicationState
    {
        public function status()
        {
            return ApplicationStatus::CANCEL;
        }
    }
