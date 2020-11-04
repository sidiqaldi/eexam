<?php

namespace App\Services\Exams;

use App\Enums\TimeMode;
use App\Models\Answer;
use App\Models\Exam;
use App\Models\Participant;
use Carbon\Carbon;
use Illuminate\Http\Response;

class SectionLimitExam extends BasicExam
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
                ->where('finish_at', NULL)
                ->orderBy('section_order', 'asc')
                ->orderBy('id', 'asc')
                ->first()
            ?? null;
    }

    public function participantAnswer($participant, $section = null)
    {
        return Answer::query()
            ->when($section, function ($q) use ($section) {
                $q->where('section_id', $section->id);
            })
            ->withSectionOrder()
            ->withOptionUuid()
            ->where('participant_id', $participant->id)
            ->orderBy('section_order', 'asc')
            ->orderBy('id', 'asc')
            ->get();
    }

    public function isInvalidStatus(Participant $participant, $section, $answer)
    {
        if ($section) {
            $this->startSection($participant, $section, $answer);
        }

        $config = json_decode($participant->cache_config);

        $now = Carbon::now();

        if ($answer) {
            $answer = Answer::query()->find($answer->id);

            if ($this->passTimeLimit($now, $answer->start_at, $section->time_limit)) {

                $this->endSection($participant, $section, $answer);

                $nextAnswer = $this->nextSectionFirstQuestion($participant);

                $nextSection = $nextAnswer ? $nextAnswer->section : null;

                return $this->goToNextSection($participant, $nextSection, $nextAnswer);
            }
        }

        if ($this->passTimeLimit($now, $participant->created_at, $config->time_limit)) {

            $this->markAsFinish($participant, $participant->created_at->addMinutes($config->time_limit));

            return abort(Response::HTTP_UNAUTHORIZED);
        }
    }

    public function nextSectionFirstQuestion(Participant $participant)
    {
        return Answer::query()
                ->withSectionOrder()
                ->where('participant_id', $participant->id)
                ->where('option_id', NULL)
                ->where('start_at', NULL)
                ->where('finish_at', NULL)
                ->orderBy('section_order', 'asc')
                ->orderBy('id', 'asc')
                ->first()
            ?? (
                Answer::query()
                    ->withSectionOrder()
                    ->where('participant_id', $participant->id)
                    ->where('finish_at', NULL)
                    ->orderBy('section_order', 'asc')
                    ->orderBy('id', 'asc')
                    ->first()
                ??
                null
            );
    }

    public function goToNextSection($participant, $section, $answer)
    {
        if ($answer == null) {

            $this->markAsFinish($participant, Carbon::now());

            return redirect()->route('participant.exams.recap', $participant->uuid);
        }

        return redirect()->route('participant.exams.section', [
            'participant' => $participant->uuid,
            'answer' => $section->uuid,
            'section' => $answer->uuid,
        ]);
    }

    public function startSection($participant, $section, $answer)
    {
        Answer::query()
            ->where('participant_id', $participant->id)
            ->where('section_id', $section->id)
            ->where('start_at', NUll)
            ->update(['start_at' => Carbon::now()]);
    }

    public function endSection($participant, $section, $answer)
    {
        Answer::query()
            ->where('participant_id', $participant->id)
            ->where('section_id', $section->id)
            ->where('finish_at', NUll)
            ->update(['finish_at' => Carbon::now()]);
    }
}
