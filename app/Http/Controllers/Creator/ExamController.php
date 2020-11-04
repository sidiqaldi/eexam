<?php

namespace App\Http\Controllers\Creator;

use App\Models\Config;
use App\Enums\ExamStatus;
use App\Models\Exam;
use App\Filters\ExamFilter;
use App\Http\Controllers\Controller;
use App\Http\Requests\Creator\Exam\DuplicateRequest;
use App\Http\Requests\Creator\Exam\UpdateRequest;
use App\Http\Requests\Creator\Exam\StoreRequest;
use App\Http\Resources\ExamResource;
use App\Services\ConfigService;
use App\Services\PublishExamService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class ExamController extends Controller
{
    /**
     * @param Request $request
     * @return \Inertia\Response
     */
    public function index(Request $request)
    {
        $perPage = $request->input('per_page', 10);

        return Inertia::render('Creator/Exam/Index', [
            'filters' => $request->all('search'),
            'exams' => ExamResource::collection(
                Exam::filter(new ExamFilter($request))
                    ->select(['uuid', 'name' , 'description', 'code', 'status_id'])
                    ->owner(Auth::user())
                    ->paginate($perPage)
                    ->appends($request->all('search'))
            )
        ]);
    }

    /**
     * @return \Inertia\Response
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function create()
    {
        $this->authorize('create', Exam::class);

        return Inertia::render('Creator/Exam/Create');
    }

    /**
     * @param StoreRequest $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(StoreRequest $request)
    {
        $exam = Exam::query()->create($request->data());

        Config::query()->create(ConfigService::defaultConfig($exam));

        return redirect()->route('creator.exams.edit', $exam->uuid)
            ->with('success', __('notification.success.add', ['model' => __('Exam')]));
    }

    /**
     * @param Exam $exam
     * @return \Inertia\Response
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function edit(Exam $exam)
    {
        $this->authorize('update', $exam);

        $options = ConfigService::getConfigOptions();

        return Inertia::render('Creator/Exam/Edit', array_merge([
            'exam' => $exam,
            'config' => $exam->config,
        ], $options));
    }

    /**
     * @param UpdateRequest $request
     * @param Exam $exam
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(UpdateRequest $request, Exam $exam)
    {
        $exam->update($request->data());

        return redirect()->back()
            ->with('success', __('notification.success.update', ['model' => __('Exam')]));
    }

    /**
     * @param Exam $exam
     * @return \Illuminate\Http\RedirectResponse
     * @throws \Exception
     */
    public function destroy(Exam $exam)
    {
        $this->authorize('delete', $exam);

        $exam->delete();

        return redirect()->route('creator.exams.index')
            ->with('success', __('notification.success.delete', ['model' => __('Exam')]));
    }

    /**
     * @param Exam $exam
     * @return \Illuminate\Http\RedirectResponse
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function publish(Exam $exam)
    {
        $this->authorize('update', $exam);

        $validation = PublishExamService::validate($exam);

        if ($validation->isHasErrors()) {
            return redirect()->back()->withErrors($validation->getErrors());
        }

        $exam->status_id = $exam->status_id == ExamStatus::Publish ? ExamStatus::Draft : ExamStatus::Publish;
        $exam->save();

        return redirect()->back()
            ->with('success', __('notification.success.update', ['model' => __('Exam')]));
    }

    /**
     * @param DuplicateRequest $request
     * @param Exam $exam
     * @return string
     */
    public function duplicate(DuplicateRequest $request, Exam $exam)
    {
        $exam = Exam::with(['config', 'sections.questions.options'])->where('id', $exam->id)->firstOrFail();

        $new = $exam->replicate();

        $new->name = $request->name;

        $new->code = $request->code;

        $new->status_id = ExamStatus::Draft;

        $new->save();

        $new->config()->save($exam->config->replicate());

        foreach ($exam->sections as $section) {

            $newSections = $new->sections()->save($section->replicate());

            foreach ($section->questions as $question) {
                $newQuestion = $newSections->questions()->save($question->replicate());

                foreach($question->options as $option) {
                    $newQuestion->options()->save($option->replicate());
                }
            }
        }

        return redirect()->route('creator.exams.index')
            ->with('success', __('notification.success.add', ['model' => __('Exam')]));
    }
}
