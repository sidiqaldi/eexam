<?php

namespace App\Policies;

use App\Enums\ExamStatus;
use App\Models\Exam;
use App\Models\Section;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class SectionPolicy
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
     * @param \App\Models\User $user
     * @param \App\Models\Section $section
     * @param Exam $exam
     * @return mixed
     */
    public function view(User $user, Section $section, Exam $exam)
    {
        return $user->id === $section->user_id;
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
     * @param  \App\Models\Section  $section
     * @return mixed
     */
    public function update(User $user, Section $section)
    {
        return $user->id === $section->user_id && $section->exam->status_id === ExamStatus::Draft;
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Section  $section
     * @return mixed
     */
    public function delete(User $user, Section $section)
    {
        return $user->id === $section->user_id && $section->exam->status_id === ExamStatus::Draft;
    }

    /**
     * Determine whether the user can restore the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Section  $section
     * @return mixed
     */
    public function restore(User $user, Section $section)
    {
        return $user->id === $section->user_id && $section->exam->status_id === ExamStatus::Draft;
    }

    /**
     * Determine whether the user can permanently delete the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Section  $section
     * @return mixed
     */
    public function forceDelete(User $user, Section $section)
    {
        return $user->id === $section->user_id && $section->exam->status_id === ExamStatus::Draft;
    }
}
