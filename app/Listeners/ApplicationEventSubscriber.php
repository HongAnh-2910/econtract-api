<?php

namespace App\Listeners;

use App\Events\HandleSendMailApplication;
use App\Mail\ApplicationSendUser;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Events\Dispatcher;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Mail;

class ApplicationEventSubscriber
{
    /**
     * @param $event
     * @return void
     */
    public function handleCreateApplicationSendMailUser($event): void {
        $message = (new ApplicationSendUser('Người kiểm duyệt'))
            ->onQueue('emails');
        $application = $event->getApplication();
        $user = $application->user;
        Mail::to($user->email)->queue($message);
    }

    /**
     * @param $event
     * @return void
     */

    public function handleCreateApplicationSendMailUserFollow($event)
    {
        $application = $event->getApplication();
        $users = $application->users;

        foreach ($users as $user)
        {
            $message = (new ApplicationSendUser('Người follow'))
                ->onQueue('emails');
            Mail::to($user->email)->queue($message);
        }
    }



    public function subscribe(Dispatcher $events): array
    {
        return [
            HandleSendMailApplication::class => [
                'handleCreateApplicationSendMailUser' ,
                'handleCreateApplicationSendMailUserFollow'],
        ];
    }
}
