<?php

namespace App\Services;

use App\Enums\ScoreStatus;
use App\Enums\TimeMode;
use App\Models\Answer;
use App\Models\Exam;
use App\Models\Participant;
use App\Models\Section;
use App\Services\Exams\BasicExam;
use App\Services\Exams\QuestionLimitExam;
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

        static::$service = static::routeSwitcher($config->time_mode);
    }

    public static function examRouter(Exam $exam)
    {
        $config = $exam->config;

        static::$service = static::routeSwitcher($config->time_mode);
    }

    public static function routeSwitcher($timeMode)
    {
        if ($timeMode == TimeMode::TimeLimit) {
            return new TimeLimitExam();
        }

        if ($timeMode == TimeMode::PerSection) {
            return new SectionLimitExam();
        }

        if ($timeMode == TimeMode::PerQuestion) {
            return new QuestionLimitExam();
        }

        return new BasicExam();
    }

    public static function getParticipantAnswers(Participant $participant, $section = null)
    {
        self::onGoingRouter($participant);

        return static::$service->participantAnswer($participant, $section);
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

    public static function firstQuestion(Participant $participant)
    {
        self::onGoingRouter($participant);

        return static::$service->firstQuestion($participant);
    }

    public static function finishSection(Participant $participant, Section $section, Answer $answer)
    {
        self::onGoingRouter($participant);

        static::$service->endSection($participant, $section, $answer);
    }

    public static function finish(Participant $participant)
    {
        self::onGoingRouter($participant);

        if (!$participant->finish_at) {

            static::$service->markAsFinish($participant, Carbon::now());
        }

        return redirect()->route('participant.results.show', $participant->uuid);
    }

    public static function globalTimeLimit($participant, $config)
    {
        return $config->time_mode !== TimeMode::NoLimit ? Carbon::now()->diffInSeconds(
                $participant->created_at->addSeconds($config->time_limit * 60), false
            ) * 1000 : 0;
    }

    public static function sectionTimeLimit($config, $section, $answer)
    {
        if ($config->time_mode === TimeMode::PerSection) {
            if ($answer->start_at == null) {
                self::onGoingRouter($answer->participant);

                static::$service->startSection($answer->participant, $section, $answer);
            }

            return Carbon::now()->diffInSeconds(
                $answer->start_at->addSeconds($section->time_limit * 60), false
            ) * 1000;
        }

        return 0;
    }

    public static function questionTimeLimit($config, $section, $answer)
    {
        if ($config->time_mode === TimeMode::PerQuestion) {
            if ($answer->start_at == null) {
                self::onGoingRouter($answer->participant);
                static::$service->startSection($answer->participant, $section, $answer);

                $answer = Answer::query()->find($answer->id);
            }

            return Carbon::now()->diffInSeconds(
                    $answer->start_at->addSeconds($answer->question->time_limit), false
                ) * 1000;
        }

        return 0;
    }
}
