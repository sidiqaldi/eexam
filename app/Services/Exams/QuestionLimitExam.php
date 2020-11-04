<?php

namespace App\Services\Exams;

use App\Enums\TimeMode;
use App\Models\Answer;
use App\Models\Exam;
use App\Models\Participant;
use Carbon\Carbon;
use Illuminate\Http\Response;

class QuestionLimitExam extends BasicExam
{
    public function onGoing(Participant $participant)
    {
        if (!empty($participant->finish_at)) {
            return false;
        }

        $config = json_decode($participant->cache_config);

        $now = Carbon::now();

        if ($this->passTimeLimit($now, $participant->created_at, $config->time_limit)) {

            $this->markAsFinish($participant, $participant->created_at->addMinutes($config->time_limit));

            return false;
        }

        return true;
    }

    /**
     * @param Participant $participant
     * @return Answer
     */
    public function firstQuestion(Participant $participant)
    {
        return Answer::query()
                ->withSectionOrder()
                ->where('participant_id', $participant->id)
                ->where('option_id', NULL)
                ->where('finish_at', NULL)
                ->orderBy('section_order', 'asc')
                ->orderBy('id', 'asc')
                ->first()
            ?? null;
    }

    public function participantAnswer($participant, $section = null)
    {
        return Answer::query()
            ->withSectionOrder()
            ->withOptionUuid()
            ->where('participant_id', $participant->id)
            ->orderBy('section_order', 'asc')
            ->orderBy('id', 'asc')
            ->get();
    }

    public function isInvalidStatus(Participant $participant, $section, $answer)
    {
        $now = Carbon::now();

        if ($answer) {

            $this->startSection($participant, $section, $answer);

            $answer = Answer::query()->find($answer->id);

            if ($answer->finish_at) {

                $nextAnswer = $this->firstQuestion($participant);

                $currentSection = $nextAnswer ? $nextAnswer->section : null;

                return $this->goToNextQuestion($participant, $currentSection, $nextAnswer);
            }

            if ($this->passTimeLimit($now, $answer->start_at, $section->time_limit)) {

                $this->endSection($participant, $section, $answer);

                $nextAnswer = $this->firstQuestion($participant);

                $currentSection = $nextAnswer ? $nextAnswer->section : null;

                return $this->goToNextQuestion($participant, $currentSection, $nextAnswer);
            }
        }

        $config = json_decode($participant->cache_config);

        if ($this->passTimeLimit($now, $participant->created_at, $config->time_limit)) {

            $this->markAsFinish($participant, $participant->created_at->addMinutes($config->time_limit));

            return abort(Response::HTTP_UNAUTHORIZED);
        }

        return null;
    }

    public function goToNextQuestion($participant, $section, $answer)
    {
        if ($answer == null) {
            $this->markAsFinish($participant, Carbon::now());
            return route('participant.exams.recap', $participant->uuid);
        }

        if ($section->id == $answer->section_id) {

            return route('participant.exams.process', [
                'participant' => $participant->uuid,
                'answer' => $answer->uuid,
            ]);
        }

        return route('participant.exams.section', [
            'participant' => $participant->uuid,
            'answer' => $answer->section->uuid,
            'section' => $answer->uuid,
        ]);
    }

    public function startSection($participant, $section, $answer)
    {
        if (!$answer->start_at) {
            $answer->update(['start_at' => Carbon::now()]);
        }
    }

    public function endSection($participant, $section, $answer)
    {
        if (!$answer->finish_at) {
            $answer->update(['finish_at' => Carbon::now()]);
        }
    }
}
