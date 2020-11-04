<?php

namespace App\Http\Controllers\Creator;

use App\Enums\InputType;
use App\Http\Controllers\Controller;
use App\Http\Requests\Creator\Question\OrderRequest;
use App\Http\Requests\Creator\Question\StoreRequest;
use App\Http\Requests\Creator\Question\UpdateRequest;
use App\Models\Question;
use App\Models\Section;
use App\Services\OptionService;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class QuestionController extends Controller
{
    /**
     * @param Section $section
     * @return \Inertia\Response
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function index(Section $section)
    {
        $this->authorize('viewAny', [Question::class, $section->exam]);

        return Inertia::render('Creator/Question/Index', [
            'exam' => $section->exam,
            'section' => $section,
            'questions' => function () use ($section) {
                return $section->questions;
            },
        ]);
    }

    /**
     * @param Section $section
     * @return \Inertia\Response
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function create(Section $section)
    {
        $this->authorize('create', [Question::class, $section->exam]);

        return Inertia::render('Creator/Question/Create', [
            'exam' => $section->exam,
            'section' => $section,
            'config' => $section->exam->config,
            'input_type' => InputType::asSelectArray(),
        ]);
    }

    /**
     * @param StoreRequest $request
     * @param Section $section
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(StoreRequest $request, Section $section)
    {
        DB::beginTransaction();

        $question = Question::query()->create($request->dataQuestion($section));

        OptionService::storeOptions($question, $request->dataOptions());

        DB::commit();

        return redirect()->route('creator.questions.index', $section->uuid)
            ->with('success', __('notification.success.add', ['model' => __('Question')]));
    }

    /**
     * @param Question $question
     * @return \Inertia\Response
     */
    public function edit(Question $question)
    {
        $this->authorize('update', $question);

        $section = $question->section;

        return Inertia::render('Creator/Question/Edit', [
            'exam' => $section->exam,
            'section' => $section,
            'config' => $section->exam->config,
            'input_type' => InputType::asSelectArray(),
            'question' => $question,
            'options' => $question->options->transform(function ($option) {
                return [
                    'id' => $option->id,
                    'key' => $option->uuid,
                    'value' => $option->value,
                    'image'=> $option->image,
                    'type' => $option->type,
                    'correct_id' => $option->correct_id,
                ];
            }),
            'correct_answer' => $question->answer,
        ]);
    }

    /**
     * @param UpdateRequest $request
     * @param Question $question
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(UpdateRequest $request, Question $question)
    {
        DB::beginTransaction();

        $question->update($request->dataQuestion());

        OptionService::updateOptions($question, $request->dataOptions());

        DB::commit();

        return redirect()->back()->with('success', __('notification.success.update', ['model' => __('Question')]));
    }

    /**
     * @param OrderRequest $request
     * @param Question $question
     * @return \Illuminate\Http\RedirectResponse
     */
    public function order(OrderRequest $request, Question $question)
    {
        if ($request->input('from') > $request->input('to')) {
            Question::query()->where('section_id', $question->section_id)
                ->whereBetween('order', [$request->input('to'), $request->input('from')])
                ->increment('order');
        } else {
            Question::query()->where('section_id', $question->section_id)
                ->whereBetween('order', [$request->input('from'), $request->input('to')])
                ->decrement('order');
        }

        $question->order = $request->input('to');

        $question->save();

        return redirect()->back();
    }

    /**
     * @param Question $question
     * @return \Illuminate\Http\RedirectResponse
     * @throws \Exception
     */
    public function destroy(Question $question)
    {
        $this->authorize('delete', $question);

        Question::query()->where('section_id', $question->section_id)
            ->where('order', '>', $question->order)
            ->decrement('order');

        $question->delete();

        return redirect()->back()
            ->with('success', __('notification.success.delete', ['model' => __('Question')]));
    }
}
