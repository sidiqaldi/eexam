<?php

namespace App\Services;

use App\Enums\AnswerOrderStatus;
use App\Enums\PassingGradeStatus;
use App\Enums\QuestionOrderStatus;
use App\Enums\RankingStatus;
use App\Enums\ResultStatus;
use App\Enums\ScoreStatus;
use App\Enums\TimeMode;
use App\Enums\VisibilityStatus;
use App\Models\Exam;
use Illuminate\Support\Facades\Auth;

class ConfigService
{
    public static function getConfigOptions()
    {
        return [
            'question_order' => QuestionOrderStatus::asSelectArray(),
            'time_mode' => TimeMode::asSelectArray(),
            'answer_order' => AnswerOrderStatus::asSelectArray(),
            'result_status' => ResultStatus::asSelectArray(),
            'ranking_status' => RankingStatus::asSelectArray(),
            'score_status' => ScoreStatus::asSelectArray(),
            'passing_grade_status' => PassingGradeStatus::asSelectArray(),
            'visibility_status' => VisibilityStatus::asSelectArray(),
        ];
    }

    public static function defaultConfig(Exam $exam)
    {
        return [
            'user_id' => Auth::user()->id,
            'exam_id' => $exam->id,
            'visibility_status' => VisibilityStatus::Private,
            'time_limit' => null,
            'time_mode' => TimeMode::NoLimit,
            'question_order' => QuestionOrderStatus::Sequence,
            'answer_order' => AnswerOrderStatus::Sequence,
            'result_status' => ResultStatus::RecapAndResult,
            'ranking_status' => RankingStatus::Show,
            'score_status' => ScoreStatus::Global,
            'passing_grade_status' => PassingGradeStatus::Global,
            'default_score' => 10,
            'default_passing_grade' => 100,
        ];
    }
}
