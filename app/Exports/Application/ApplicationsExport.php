<?php

namespace App\Exports\Application;

use App\Models\Application;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\FromView;

class ApplicationsExport implements FromView , ShouldQueue
{
    use Exportable;
    protected $application;
    public function __construct($application)
    {
       $this->application = $application;
    }

    /**
     * @return View
     */

    public function view(): View
    {
        return view('exports.applications.export-application', [
            'applications' => $this->application
        ]);
    }
}
