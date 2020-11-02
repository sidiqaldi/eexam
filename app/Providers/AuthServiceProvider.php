<?php

namespace App\Providers;

use App\Models\Config;
use App\Models\Exam;
use App\Models\Participant;
use App\Models\Question;
use App\Models\Section;
use App\Models\Team;
use App\Policies\ConfigPolicy;
use App\Policies\ExamPolicy;
use App\Policies\ParticipantPolicy;
use App\Policies\QuestionPolicy;
use App\Policies\SectionPolicy;
use App\Policies\TeamPolicy;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array
     */
    protected $policies = [
        Team::class => TeamPolicy::class,
        Exam::class => ExamPolicy::class,
        Question::class => QuestionPolicy::class,
        Section::class => SectionPolicy::class,
        Participant::class => ParticipantPolicy::class,
        Config::class => ConfigPolicy::class,
    ];

    /**
     * Register any authentication / authorization services.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerPolicies();

        //
    }
}
