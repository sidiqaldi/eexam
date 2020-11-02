<?php

namespace App\Services;

use App\Enums\ScoreStatus;
use App\Enums\TimeMode;
use App\Models\Answer;
use App\Models\Exam;
use App\Models\Participant;
use App\Models\Section;
use App\Services\Exams\BasicExam;
use App\Services\Exams\SectionLimitExam;
use App\Services\Exams\TimeLimitExam;
use Carbon\Carbon;
use Illuminate\Support\Collection;

class ParticipantService
{
    static $service;

    public static function join(Exam $exam)
    {
        self::examRouter($exam);

        return static::$service->join($exam);
    }

    public static function validateStatus(Participant $participant, $section = null, $answer = null)
    {
        self::onGoingRouter($participant);

        return static::$service->validateStatus($participant, $section, $answer);
    }

    public static function onGoingRouter(Participant $participant)
    {
        $config = json_decode($participant->cache_config);

        if ($config->time_mode == TimeMode::TimeLimit) {
            static::$service = new TimeLimitExam();
        } elseif ($config->time_mode == TimeMode::PerSection) {
            static::$service = new SectionLimitExam();
        } else {
            static::$service = new BasicExam();
        }

    }

    public static function examRouter(Exam $exam)
    {
        $config = $exam->config;
        if ($config->time_mode == TimeMode::TimeLimit) {
            static::$service = new TimeLimitExam();
        } else {
            static::$service = new BasicExam();
        }
    }

    public static function getParticipantAnswers(Participant $participant)
    {
        return Answer::query()
            ->withSectionOrder()
            ->withOptionUuid()
            ->where('participant_id', $participant->id)
            ->orderBy('section_order', 'asc')
            ->orderBy('id', 'asc')
            ->get();
    }

    public static function getNavigation(Participant $participant, Answer $answer, Collection $answers)
    {
        foreach ($answers as $key => $item) {
            if ($item->id === $answer->id) {
                return [
                    'next' => !isset($answers[$key + 1]) ? route('participant.exams.recap', $participant->uuid) : static::generateNextUrl($participant, $item, $answers[$key + 1]),
                    'prev' => $key == 0 ? null : route('participant.exams.process', [
                        'participant' => $participant->uuid,
                        'answer' => $answers[$key - 1]->uuid
                    ])
                ];
            }
        }

        return ['next' => null, 'prev' => null];
    }

    public static function generateNextUrl($participant, $answer, $nextAnswer)
    {
        if ($nextAnswer->section_id == $answer->section_id) {
            return route('participant.exams.process', [
                'participant' => $participant->uuid,
                'answer' => $nextAnswer->uuid
            ]);
        }
        return route('participant.exams.section', [
            'participant' => $participant->uuid,
            'answer' => $nextAnswer->uuid,
            'section' => Section::query()->find($nextAnswer->section_id)->uuid,
        ]);
    }

    public static function getScore($participant, $answer)
    {
        $config = $participant->exam->config;

        if ($config->score_status == ScoreStatus::Global) {

            return $config->default_score;
        }

        if ($config->score_status == ScoreStatus::Section) {
            return $answer->section->score_per_question;
        }

        return $answer->question->score;
    }

    public static function finish(Participant $participant)
    {
        self::examRouter($participant->exam);

        if (!$participant->finish_at) {

            static::$service->markAsFinish($participant, Carbon::now());
        }

        return redirect()->route('participant.results.show', $participant->uuid);
    }
}
