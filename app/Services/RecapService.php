<?php

namespace App\Services;

use App\Enums\PassingGradeStatus;
use App\Enums\RankingStatus;
use App\Enums\RecapResultStatus;
use App\Enums\ResultStatus;
use App\Models\Answer;
use App\Models\Exam;
use App\Models\Participant;
use App\Models\Recap;

class RecapService
{
    public static function init(Participant $participant)
    {
        $exam = $participant->exam;

        if ($exam) {

            Recap::query()->updateOrCreate([
                'user_id' => $participant->user_id,
                'participant_id' => $participant->id,
            ], [
                'exam_id' => $exam->id,
                'exam' => $exam->name,
                'creator' => $exam->user->name ?? 'Anon',
            ]);

            $config = $exam->config;

            $participant->cache_config = json_encode($config->toArray());

            $participant->save();
        }
    }

    public static function create(Participant $participant)
    {
        $exam = $participant->exam;

        $recap = [];

        if ($exam) {
            $recap['exam'] = self::exam($exam);
        }

        $sections = $exam->sections;

        $recap['result'] = self::result($participant, $sections);

        $recap = Recap::query()->updateOrCreate([
            'user_id' => $participant->user_id,
            'participant_id' => $participant->id,
        ], [
            'details' => json_encode($recap),
            'status' => $recap['result']['status'] ?? RecapResultStatus::Failed,
            'total_score' => $recap['result']['total_score'] ?? 0,
        ]);

        return Recap::query()->find($recap->getAttribute('id'));
    }

    public static function exam(Exam $exam)
    {
        return [
            'name' => $exam->name,
            'description' => $exam->description,
            'creator' => $exam->user->name ?? 'Anon',
        ];
    }

    public static function result($participant, $sections)
    {
        $config = json_decode($participant->cache_config);

        if ($config->passing_grade_status == PassingGradeStatus::Global) {

            $result = self::checkPassGlobal($sections, $config, $participant);
        } else {
            $result = self::checkPassPerSection($sections, $participant);
        }

        return $result;
    }

    public static function checkPassGlobal($sections, $config, $participant)
    {
        $totalScore = 0;

        $sectionResult = [];

        foreach ($sections as $section) {

            $answers = Answer::query()->where('participant_id', $participant->id)
                ->where('section_id', $section->id)
                ->get();

            $score = $answers->sum('score');

            $totalScore += $score;

            $sectionResult[] = [
                'name' => $section->name,
                'total_question' => $answers->count(),
                'total_score' => $score,
            ];
        }

        return [
            'status' => $totalScore >= $config->default_passing_grade ? RecapResultStatus::Passed : RecapResultStatus::Failed,
            'section' => $sectionResult,
            'total_score' => $totalScore,
            'passing_grade' => $config->default_passing_grade,
        ];

    }

    public static function checkPassPerSection($sections, $participant)
    {
        $status = RecapResultStatus::Passed;

        $sectionResult = [];

        $totalScore = 0;

        foreach ($sections as $section) {

            $answers = Answer::query()->where('participant_id', $participant->id)
                ->where('section_id', $section->id)
                ->get();

            $score = $answers->sum('score');

            $totalScore += $score;

            $sectionStatus = $score >= $section->passing_grade ? RecapResultStatus::Passed : RecapResultStatus::Failed;

            $sectionResult[] = [
                'name' => $section->name,
                'passing_grade' => $section->passing_grade,
                'total_question' => $answers->count(),
                'total_score' => $score,
                'status' => $sectionStatus,
            ];

            if ($sectionStatus == RecapResultStatus::Failed) {
                $status = RecapResultStatus::Failed;
            }
        }

        return [
            'status' => $status,
            'sections' => $sectionResult,
            'total_score' => $totalScore,
        ];
    }

    public static function participantReport(Participant $participant)
    {
        $config = json_decode($participant->cache_config);

        $parseConfig = new \stdClass;

        $parseConfig->result_status = $config->result_status;

        $parseConfig->ranking_status = $config->ranking_status;

        $parseConfig->result_status_verb = ResultStatus::getDescription($config->result_status);

        $parseConfig->ranking_status_verb = RankingStatus::getDescription($config->ranking_status);

        $participantRecap = $participant->recap;

        $recap = json_decode($participantRecap->details);

        $report = new \stdClass;

        $report->exam = $recap->exam;

        $report->config = $parseConfig;

        $report->participant_name = $participant->user->name;

        if ($config->result_status == ResultStatus::RecapAndResult) {

            $report->result = isset($recap->result->status) ? RecapResultStatus::getDescription($recap->result->status)
                : RecapResultStatus::getDescription(RecapResultStatus::Failed);

            if ($config->passing_grade_status == PassingGradeStatus::Global) {
                $sections = [];

                foreach ($recap->result->section as $section) {
                    $sections[] = [
                        'name' => $section->name ?? '-',
                        'score' => $section->total_score ?? 0,
                    ];
                }

                $report->section = $sections;
                $report->passing_grade = $recap->result->passing_grade ?? 0;
                $report->total_score = $recap->result->total_score ?? 0;

            } else {
                $sections = [];

                foreach ($recap->result->sections as $section) {
                    $sections[] = [
                        'name' => $section->name ?? '-',
                        'score' => $section->total_score ?? 0,
                        'passing_grade' => $section->passing_grade ?? 0,
                        'status' => isset($section->status) ? RecapResultStatus::getDescription($section->status)
                            : RecapResultStatus::getDescription(RecapResultStatus::Failed)
                    ];
                }

                $report->section = $sections;
                $report->total_score = $recap->result->passing_grade ?? 0;
            }

        } elseif ($config->result_status == ResultStatus::ResultOnly) {
            $report->result = isset($recap->result->status) ? RecapResultStatus::getDescription($recap->result->status)
                : RecapResultStatus::getDescription(RecapResultStatus::Failed);
        } else {
            $report->result = 'N/A';
        }

        if ($config->ranking_status == RankingStatus::Show) {

            if ($participantRecap->status == RecapResultStatus::Passed) {
                $rank = Recap::query()->select('user_id', 'total_score')
                    ->where('status', RecapResultStatus::Passed)
                    ->where('exam_id', $participant->exam_id)
                    ->where('total_score', '>' , $participantRecap->total_score)
                ->distinct('total_score')
                ->count();

                $report->rank = $rank + 1;

            } else {
                $topRank = Recap::query()->select('user_id', 'total_score')
                    ->where('exam_id', $participant->exam_id)
                    ->where('status', RecapResultStatus::Passed)
                    ->distinct('total_score')
                    ->count();

                $rank = Recap::query()->select('user_id', 'total_score')
                    ->where('status', RecapResultStatus::Failed)
                    ->where('exam_id', $participant->exam_id)
                    ->where('total_score', '>' , $participantRecap->total_score)
                    ->where('id' , '<>' , $participantRecap->id)
                    ->distinct('total_score')
                    ->count() + 1;

                $report->rank = $rank + $topRank;

            }
            $report->rank_from = Recap::query()->where('exam_id', $participant->exam_id)->count();
        } else {
            $report->rank = 'N/A';
            $report->rank_from = 'N/A';
        }

        return $report;
    }
}
