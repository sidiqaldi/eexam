<?php

namespace App\Http\Controllers\Creator;

use App\Http\Controllers\Controller;
use App\Http\Requests\Creator\Section\DestroyRequest;
use App\Http\Requests\Creator\Section\EditRequest;
use App\Http\Requests\Creator\Section\OrderRequest;
use App\Http\Requests\Creator\Section\StoreRequest;
use App\Http\Requests\Creator\Section\UpdateRequest;
use App\Models\Exam;
use App\Models\Section;
use Inertia\Inertia;

class SectionController extends Controller
{
    /**
     * @param Exam $exam
     * @return \Inertia\Response
     */
    public function index(Exam $exam)
    {
        return Inertia::render('Creator/Section/Index', [
            'exam' => $exam,
            'config' => $exam->config,
            'sections' => $exam->sections,
        ]);
    }

    /**
     * @param Exam $exam
     * @return \Inertia\Response
     */
    public function create(Exam $exam)
    {
        $this->authorize('update', $exam);

        return Inertia::render('Creator/Section/Create', [
            'exam' => $exam,
            'config' => $exam->config,
        ]);
    }

    /**
     * @param StoreRequest $request
     * @param Exam $exam
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(StoreRequest $request, Exam $exam)
    {
        Section::query()->create($request->data($exam));

        return redirect()->route('creator.sections.index', $exam->uuid)
            ->with('success', __('notification.success.add', ['model' => __('Section')]));
    }

    /**
     * @param Section $section
     * @return \Inertia\Response
     */
    public function edit(Section $section)
    {
        $this->authorize('update', $section);

        $exam = $section->exam;

        return Inertia::render('Creator/Section/Edit', [
            'exam' => $exam,
            'config' => $exam->config,
            'section' => $section,
        ]);
    }

    /**
     * @param UpdateRequest $request
     * @param Section $section
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(UpdateRequest $request, Section $section)
    {
        $section->update($request->validated());

        return redirect()->route('creator.sections.index', $section->exam->uuid)
            ->with('success', __('notification.success.update', ['model' => __('Section')]));
    }

    /**
     * @param OrderRequest $request
     * @param Section $section
     * @return \Illuminate\Http\RedirectResponse
     */
    public function order(OrderRequest $request, Section $section)
    {
        if ($request->input('from') > $request->input('to')) {
            Section::query()->where('exam_id', $section->exam_id)
                ->whereBetween('order', [$request->input('to'), $request->input('from')])
                ->increment('order');
        } else {
            Section::query()->where('exam_id', $section->exam_id)
                ->whereBetween('order', [$request->input('from'), $request->input('to')])
                ->decrement('order');
        }

        $section->order = $request->input('to');

        $section->save();

        return redirect()->back();
    }

    /**
     * @param Section $section
     * @return \Illuminate\Http\RedirectResponse
     * @throws \Exception
     */
    public function destroy(Section $section)
    {
        $this->authorize('delete', $section);

        Section::query()->where('exam_id', $section->exam_id)
            ->where('order', '>', $section->order)
            ->decrement('order');

        $section->delete();

        return redirect()->back()
            ->with('success', __('notification.success.delete', ['model' => __('Section')]));
    }
}
