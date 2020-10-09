<?php

namespace App\Http\Controllers\Creator;

use App\Http\Controllers\Controller;
use App\Http\Requests\Creator\Result\ShowExamRequest;
use App\Http\Requests\Creator\Result\ShowParticipantRequest;
use App\Http\Resources\ExamCreatorResultResource;
use App\Http\Resources\ExamResource;
use App\Http\Resources\ParticipantCreatorReportResource;
use App\Models\Exam;
use App\Models\Participant;
use App\Models\Recap;
use App\Services\RecapService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class ResultController extends Controller
{
    public function index(Request $request)
    {
        $perPage = $request->input('per_page', 10);

        return Inertia::render('Creator/Result/Index', [
            'filters' => $request->all('search'),
            'exams' => ExamCreatorResultResource::collection(
                Exam::query()->select(['uuid', 'name', 'description', 'code', 'status_id'])
                    ->addSelect(['participant' => Participant::query()
                        ->selectRaw('count(id)')
                        ->whereColumn('exam_id', 'exams.id')
                        ->limit(1)
                    ])
                    ->addSelect(['finish_participant' => Participant::query()
                        ->selectRaw('count(id)')
                        ->whereColumn('exam_id', 'exams.id')
                        ->whereNotNull('finish_at')
                        ->limit(1)
                    ])
                    ->owner(Auth::user())
                    ->paginate($perPage)
                    ->appends($request->all('search'))
            )
        ]);
    }

    public function exam(ShowExamRequest $request, Exam $exam)
    {
        $participants = Recap::with('participant')
            ->select(DB::raw('recaps.* , CASE WHEN @curScore = NULL THEN @curRank := @curRank + 1 WHEN @curScore = recaps.total_score THEN @curRank ELSE @curRank := @curRank + 1 END AS rank, @curScore := recaps.total_score as Socre'))
            ->from(DB::raw('recaps, (SELECT @curScore:= NULL, @curRank := 0) r'))
            ->where('exam_id', $exam->id)
            ->whereHas('participant', function($q) {
                $q->whereNotNull('finish_at');
            })
            ->orderBy('status', 'asc')
            ->orderBy('total_score', 'desc')
            ->paginate();

        return Inertia::render('Creator/Result/Exam', [
            'exam' => new ExamResource($exam),
            'participants' => ParticipantCreatorReportResource::collection($participants)
        ]);

    }

    public function details(ShowParticipantRequest $request, Participant $participant)
    {
        return Inertia::render('Creator/Result/Participant', [
            'report' => RecapService::participantReport($participant)
        ]);
    }
}
