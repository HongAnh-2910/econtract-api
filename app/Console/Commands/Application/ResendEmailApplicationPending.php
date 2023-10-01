<?php

namespace App\Console\Commands\Application;

use App\Enums\ApplicationStatus;
use App\Mail\ApplicationSendUser;
use App\Models\Application;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;

class ResendEmailApplicationPending extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:resend-email-pending';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    protected Application $application;

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct(Application $application)
    {
        $this->application = $application;
        parent::__construct();
    }

    /**
     * @return void
     */

    public function handle(): void
    {
        $this->application->with('user')->whereHas('dateTimeApplications', function ($query) {
            return $query->ByMonthAndYear(Carbon::now()->month, Carbon::now()->year);
        })->ByStatus(ApplicationStatus::PENDING)->chunk(200, function ($applications) {
            foreach ($applications as $application) {
                $user = $application->user;
                $message = (new ApplicationSendUser('Người Kiểm duyệt'))
                    ->onQueue('emails');
                Mail::to($user->email)->queue($message);
            }
        });
    }
}
