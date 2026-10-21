<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreGrievanceRequest;
use App\Http\Requests\TrackGrievanceRequest;
use App\Models\Grievance;
use App\Models\WorkerCategory;
use App\Services\AuditLogger;
use App\Services\GrievanceService;
use Illuminate\Support\Facades\URL;
use Illuminate\Validation\ValidationException;

class GrievanceController extends Controller
{
    public function create()
    {
        return view('grievances.create', [
            'categories' => WorkerCategory::query()->active()->ordered()->get(),
        ]);
    }

    public function store(StoreGrievanceRequest $request, GrievanceService $grievances, AuditLogger $audit)
    {
        $grievance = $grievances->create(
            collect($request->validated())->except(['consent', 'website'])->all(),
            $request->ip()
        );

        $audit->log('grievance.created', $grievance, [], ['case_no' => $grievance->case_no, 'subject' => $grievance->subject]);

        return redirect(URL::temporarySignedRoute(
            'grievances.show',
            now()->addHours(2),
            ['grievance' => $grievance->case_no]
        ))->with('success', 'आपकी शिकायत दर्ज हो गई है। शिकायत संख्या: '.$grievance->case_no);
    }

    public function trackForm()
    {
        return view('grievances.track');
    }

    public function track(TrackGrievanceRequest $request, GrievanceService $grievances)
    {
        $grievance = $grievances->findByCredentials(
            $request->validated('case_no'),
            $request->validated('phone')
        );

        if (! $grievance) {
            throw ValidationException::withMessages([
                'case_no' => 'इस शिकायत संख्या और मोबाइल नंबर से कोई शिकायत नहीं मिली।',
            ]);
        }

        return redirect(URL::temporarySignedRoute(
            'grievances.show',
            now()->addHour(),
            ['grievance' => $grievance->case_no]
        ));
    }

    public function show(Grievance $grievance)
    {
        return view('grievances.show', [
            'grievance' => $grievance->load(['category', 'publicUpdates.user']),
        ]);
    }
}
