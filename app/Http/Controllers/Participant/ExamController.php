<?php

namespace App\Http\Controllers\Participant;

use App\Http\Resources\QuestionResource;
use App\Http\Resources\SectionResource;
use App\Enums\CorrectStatus;
use App\Models\Answer;
use App\Models\Exam;
use App\Http\Controllers\Controller;
use App\Http\Requests\Participant\Exam\DetailsRequest;
use App\Http\Resources\AnswerResource;
use App\Http\Resources\ConfigResource;
use App\Http\Resources\ExamResource;
use App\Http\Resources\OptionResource;
use App\Http\Resources\ParticipantResource;
use App\Models\Option;
use App\Models\Participant;
use App\Models\Section;
use App\Services\ParticipantService;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ExamController extends Controller
{
    /**
     * @param Request $request
     * @return \Inertia\Response
     */
    public function form(Request $request)
    {
        return Inertia::render('Participant/Exam/Form');
    }

    /**
     * @param DetailsRequest $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function details(DetailsRequest $request)
    {
        return redirect()->route('participant.exams.details.show', $request->input('code'));
    }

    /**
     * @param $code
     * @return \Inertia\Response
     */
    public function show($code)
    {
        $exam = Exam::query()->where('code', $code)->firstOrFail();
        return Inertia::render('Participant/Exam/Details', [
            'config' => new ConfigResource($exam->config),
            'creator' => $exam->user->name,
            'exam' => new ExamResource($exam),
        ]);
    }

    /**
     * @param Exam $exam
     * @return mixed
     */
    public function join(Exam $exam)
    {
        return ParticipantService::join($exam);
    }

    /**
     * @param Participant $participant
     * @param Exam $exam
     * @return mixed
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function continue(Participant $participant, Exam $exam)
    {
        $this->authorize('process', $participant);

        return ParticipantService::join($exam);
    }

    /**
     * @param Participant $participant
     * @param Answer $answer
     * @param Section $section
     * @return \Inertia\Response
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function section(Participant $participant, Answer $answer, Section $section)
    {
        $this->authorize('process', $participant);

        if ($redirect = ParticipantService::isInvalidStatus($participant, $section, $answer)) {
            return redirect($redirect);
        }

        $exam = $participant->exam;

        return Inertia::render('Participant/Exam/Section', [
            'answer' => new AnswerResource($answer),
            'answers' => ParticipantResource::collection(ParticipantService::getParticipantAnswers($participant)),
            'config' => new ConfigResource($exam->config),
            'exam' => new ExamResource($exam),
            'participant' => new ParticipantResource($participant),
            'section' => new SectionResource($section),
            'time_limit' => ParticipantService::globalTimeLimit($participant, $exam->config),
        ]);
    }

    /**
     * @param Participant $participant
     * @param Answer $answer
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector|\Inertia\Response
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function process(Participant $participant, Answer $answer)
    {
        $this->authorize('process', $participant);

        if ($redirect = ParticipantService::isInvalidStatus($participant, $answer->section, $answer)) {
            return redirect($redirect);
        }

        $exam = $participant->exam;

        $section = $answer->section;

        $answers = ParticipantService::getParticipantAnswers($participant, $section);

        $config = $exam->config;

        $question = $answer->question;

        $options = $question->options;

        return Inertia::render('Participant/Exam/Process', [
            'answer' => function () use ($answer) {
                return new AnswerResource(Answer::query()->withOptionUuid()->where('id', $answer->id)->first());
            },
            'answers' => AnswerResource::collection($answers),
            'config' => new ConfigResource($config),
            'exam' => new ExamResource($exam),
            'options' => OptionResource::collection($options),
            'participant' => new ParticipantResource($participant),
            'question' => new QuestionResource($question),
            'section' => new SectionResource($section),
            'time_limit' => ParticipantService::globalTimeLimit($participant, $config),
            'section_limit' => ParticipantService::sectionTimeLimit($config, $section, $answer),
            'question_limit' => ParticipantService::questionTimeLimit($config, $section, $answer),
        ]);
    }

    /**
     * @param Participant $participant
     * @param Answer $answer
     * @param Option $option
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function submit(Participant $participant, Answer $answer, Option $option)
    {
        $this->authorize('process', $participant);

        ParticipantService::isInvalidStatus($participant);

        $answer->option_id = $option->id;
        $answer->is_correct = $option->correct_id;
        $answer->score = $option->correct_id == CorrectStatus::True ? ParticipantService::getScore($participant, $answer) : 0;
        $answer->save();

        $answers = ParticipantService::getParticipantAnswers($participant);
        $navigation = ParticipantService::getNavigation($participant, $answer, $answers);

        return redirect($navigation['next']);
    }

    /**
     * @param Participant $participant
     * @param Answer $answer
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function previous(Participant $participant, Answer $answer)
    {
        $this->authorize('process', $participant);

        $answers = ParticipantService::getParticipantAnswers($participant);
        $navigation = ParticipantService::getNavigation($participant, $answer, $answers);

        return redirect($navigation['prev']);
    }

    /**
     * @param Participant $participant
     * @param Section $section
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function nextSection(Participant $participant, Section $section)
    {
        $this->authorize('process', $participant);

        ParticipantService::finishSection($participant, $section, new Answer);

        if (ParticipantService::firstQuestion($participant)) {
            return ParticipantService::join($participant->exam);
        }

        return redirect()->route('participant.exams.recap', $participant->uuid);
    }

    /**
     * @param Participant $participant
     * @param Answer $answer
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function nextQuestion(Participant $participant, Answer $answer)
    {
        $this->authorize('process', $participant);

        ParticipantService::finishSection($participant, new Section, $answer);

        $nextQuestion = ParticipantService::firstQuestion($participant);

        if ($nextQuestion) {
            if ($nextQuestion->section_id == $answer->section_id) {
                return redirect()->route('participant.exams.process', [
                    'participant' => $participant->uuid,
                    'answer' => $nextQuestion->uuid
                ]);
            }

            return redirect()->route('participant.exams.section', [
                'participant' => $participant->uuid,
                'answer' => $nextQuestion->uuid,
                'section' => $nextQuestion->section->uuid,
            ]);
        }

        return redirect()->route('participant.exams.recap', $participant->uuid);
    }

    /**
     * @param Participant $participant
     * @return \Inertia\Response
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function recap(Participant $participant)
    {
        $this->authorize('process', $participant);

        $exam = $participant->exam;

        $answers = ParticipantService::getParticipantAnswers($participant);

        return Inertia::render('Participant/Exam/Recap', [
            'answers' => AnswerResource::collection($answers),
            'config' => new ConfigResource($exam->config),
            'exam' => new ExamResource($exam),
            'participant' => new ParticipantResource($participant),
        ]);
    }

    /**
     * @param Participant $participant
     * @return \Illuminate\Http\RedirectResponse
     */
    public function finish(Participant $participant)
    {
        $this->authorize('process', $participant);

        return ParticipantService::finish($participant);
    }
}
