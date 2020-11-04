<?php

namespace App\Http\Requests\Creator\Config;

use App\Enums\AnswerOrderStatus;
use App\Enums\PassingGradeStatus;
use App\Enums\QuestionOrderStatus;
use App\Enums\RankingStatus;
use App\Enums\ResultStatus;
use App\Enums\ScoreStatus;
use App\Enums\TimeMode;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

/**
 * @property mixed time_limit
 * @property mixed time_mode
 * @property mixed question_order
 * @property mixed answer_order
 * @property mixed result_status
 * @property mixed ranking_status
 * @property mixed default_score
 * @property mixed default_passing_grade
 * @property mixed score_status
 * @property mixed passing_grade_status
 */
class UpdateRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return Auth::user()->can('update', $this->config);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        $request = $this;

        return [
            'time_mode' => [
                'required',
                Rule::in(TimeMode::getValues())
            ],
            'time_limit' => [
                Rule::requiredIf(function () use ($request) {
                    return $request->input('time_mode') != TimeMode::NoLimit;
                }),
                'nullable',
                'numeric',
                'min:5',
                'max:360',
            ],
            'question_order' => [
                'required',
                Rule::in(QuestionOrderStatus::getValues()),
            ],
            'answer_order' => [
                'required',
                Rule::in(AnswerOrderStatus::getValues()),
            ],
            'result_status' => [
                'required',
                Rule::in(ResultStatus::getValues()),
            ],
            'ranking_status' => [
                'required',
                Rule::in(RankingStatus::getValues()),
            ],
            'score_status' => [
                'required',
                Rule::in(ScoreStatus::getValues()),
            ],
            'passing_grade_status' => [
                'required',
                Rule::in(PassingGradeStatus::getValues()),
            ],
            'default_score' => [
                'required',
                'numeric',
                'min:1',
                'max:1000'
            ],
            'default_passing_grade' => [
                'required',
                'numeric',
                'min:1',
                'max:100000',
            ],
        ];
    }

    /**
     * @return array
     */
    public function data()
    {
        return [
            'visibility_status' => $this->visibility_status,
            'time_limit' => $this->time_limit,
            'time_mode' => $this->time_mode,
            'question_order' => $this->question_order,
            'answer_order' => $this->answer_order,
            'result_status' => $this->result_status,
            'ranking_status' => $this->ranking_status,
            'score_status' => $this->score_status,
            'passing_grade_status' => $this->passing_grade_status,
            'default_score' => $this->default_score,
            'default_passing_grade' => $this->default_passing_grade,
        ];
    }
}
