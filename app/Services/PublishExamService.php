<?php
namespace App\Services;

use App\Enums\PassingGradeStatus;
use App\Enums\ScoreStatus;
use App\Enums\TimeMode;
use App\Models\Exam;
use App\Models\Question;

class PublishExamService
{
    public $errors;
    public $hasError;
    public $errorLocations = [];

    const Config = 'config';
    const Section = 'section';
    const Question = 'question';

    public function __construct($errors)
    {
        $this->errors = $errors;

        if ($errors) {
            $this->hasError = true;
        } else {
            $this->hasError = false;
        }

        return $this;
    }

    public function isHasErrors()
    {
        return $this->hasError;
    }

    public function getErrors()
    {
        return $this->errors;
    }

    public static function validate(Exam $exam)
    {
        if (!$exam->sections->count()) {
            return new self(['exam' => [__('validation.no_sections')]]);
        }

        $sections = $exam->sections;

        $sectionId = $sections->pluck('id');

        if (!static::isEachSectionHasQuestions($sections)) {
            return new self(['exam' => [__('validation.no_questions')]]);
        }

        $config = $exam->config;

        $totalScore = static::getTotalScoreByConfig($sectionId, $sections, $config);

        if (!static::isTotalScoreMatchThePassingGrade($totalScore, $sections, $config)) {
            return new self(['exam' => [__('validation.need_question')]]);
        }

        if ($config->time_mode == TimeMode::PerSection) {
            if (static::isEmptySectionTimeLimitExist($exam)) {
                return new self(['exam' => [__('validation.section_time_limit_empty')]]);
            }

            if ($sections->sum('time_limit') > $config->time_limit) {
                return new self(['exam' => [__('validation.section_time_limit')]]);
            }
        }

        if ($config->time_mode == TimeMode::PerQuestion) {
            if (static::isEmptyQuestionTimeLimitExist($sectionId)) {
                return new self(['exam' => [__('validation.question_time_limit_empty')]]);
            }

            if (Question::whereIn('section_id', $sectionId)->sum('time_limit') / 60 > $config->time_limit) {
                return new self(['exam' => [__('validation.question_time_limit')]]);
            }
        }

        return new self(null);
    }

    public static function validateWithoutBreak(Exam $exam)
    {
        $validation = new self([]);

        if (!$exam->sections->count()) {

            $validation->errors[__('Section')][] = __('validation.no_sections');
            $validation->errorLocations[__('section')] = true;
        }

        $sections = $exam->sections;

        $sectionId = $sections->pluck('id');

        if (!static::isEachSectionHasQuestions($sections)) {

            $validation->errors[__('Question')][] = __('validation.no_questions');
            $validation->errorLocations['question'] = true;
        }

        $config = $exam->config;

        $totalScore = static::getTotalScoreByConfig($sectionId, $sections, $config);

        if (!static::isTotalScoreMatchThePassingGrade($totalScore, $sections, $config)) {

            $validation->errors[__('Question')][] = __('validation.need_question');
            $validation->errorLocations['question'] = true;
        }

        if ($config->time_mode == TimeMode::PerSection) {
            if (static::isEmptySectionTimeLimitExist($exam)) {

                $validation->errors[__('Section')][] = __('validation.section_time_limit_empty');
                $validation->errorLocations['section'] = true;
            }

            if ($sections->sum('time_limit') > $config->time_limit) {

                $validation->errors[__('Config')][] = __('validation.section_time_limit');
                $validation->errorLocations['config'] = true;
            }
        }

        if ($config->time_mode == TimeMode::PerQuestion) {
            if (static::isEmptyQuestionTimeLimitExist($sectionId)) {

                $validation->errors[__('Question')][] = __('validation.question_time_limit_empty');
                $validation->errorLocations['question'] = true;
            }

            if (Question::whereIn('section_id', $sectionId)->sum('time_limit') / 60 > $config->time_limit) {

                $validation->errors[__('Config')][] = __('validation.question_time_limit');
                $validation->errorLocations['config'] = true;
            }
        }

        $validation->hasError = !empty($validation->errors);

        return $validation;
    }

    public static function getErrorLocations(Exam $exam)
    {
        $validation = static::validateWithoutBreak($exam);
        return (object) $validation->errorLocations;
    }

    private static function isEachSectionHasQuestions($sections)
    {
        foreach ($sections as $section) {
            if (!$section->questions()->count()) {
                return false;
            }
        }
        return true;
    }

    private static function isTotalScoreMatchThePassingGrade($totalScore, $sections, $config)
    {
        if ($config->passing_grade_status == PassingGradeStatus::Global) {
            if ($config->default_passing_grade > $totalScore) {
                return false;
            };
            return true;
        }

        foreach ($sections as $section) {
            if ($config->score_status == ScoreStatus::Global) {
                if ($section->passing_grade > $section->questions()->count() * $config->default_score) {
                    return false;
                }
            } elseif ($config->score_status == ScoreStatus::Section) {
                if ($section->passing_grade > $section->questions()->count() * $section->score_per_question) {
                    return false;
                }
            } else {
                if ($section->passing_grade > $section->questions()->sum('score')) {
                    return false;
                }
            }
        }
        return true;
    }

    private static function getTotalScoreByConfig($sectionId, $sections, $config)
    {
        $totalScore = 0;

        if ($config->score_status == ScoreStatus::Global) {
            $totalScore = Question::query()->whereIn('section_id', $sectionId)->count() * $config->default_score;
        } elseif ($config->score_status == ScoreStatus::Section) {
            foreach ($sections as $section) {
                $totalScore += $section->questions()->count() * $section->score_per_question;
            }
        } else {
            $totalScore = Question::query()->whereIn('section_id', $sectionId)->sum('score');
        }

        return $totalScore;
    }

    private static function isEmptySectionTimeLimitExist($exam)
    {
        return $exam->sections()->where('time_limit', NUll)->orWhere('time_limit', 0)->count();
    }

    private static function isEmptyQuestionTimeLimitExist($sectionId)
    {
        return Question::whereIn('section_id', $sectionId)->where('time_limit', NUll)->orWhere('time_limit', 0)->count();
    }
}
