<?php

namespace App\Policies;

use App\Enums\ExamStatus;
use App\Models\Exam;
use App\Models\Question;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class QuestionPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any models.
     *
     * @param \App\Models\User $user
     * @param Exam $exam
     * @return mixed
     */
    public function viewAny(User $user, Exam $exam)
    {
        return $user->id === $exam->user_id && $exam->status_id === ExamStatus::Draft;
    }

    /**
     * Determine whether the user can view the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Question  $question
     * @return mixed
     */
    public function view(User $user, Question $question)
    {
        return $user->id === $question->user_id;
    }

    /**
     * Determine whether the user can create models.
     *
     * @param \App\Models\User $user
     * @param Exam $exam
     * @return mixed
     */
    public function create(User $user, Exam $exam)
    {
        return $user->id === $exam->user_id && $exam->status_id === ExamStatus::Draft;
    }

    /**
     * Determine whether the user can update the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Question  $question
     * @return mixed
     */
    public function update(User $user, Question $question)
    {
        return $user->id === $question->user_id;
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Question  $question
     * @return mixed
     */
    public function delete(User $user, Question $question)
    {
        return $user->id === $question->user_id;
    }

    /**
     * Determine whether the user can restore the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Question  $question
     * @return mixed
     */
    public function restore(User $user, Question $question)
    {
        return $user->id === $question->user_id;
    }

    /**
     * Determine whether the user can permanently delete the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Question  $question
     * @return mixed
     */
    public function forceDelete(User $user, Question $question)
    {
        return $user->id === $question->user_id;
    }
}
