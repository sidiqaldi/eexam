<?php

namespace App\Services\Exams;

use App\Enums\TimeMode;
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

        if ($config->time_mode == TimeMode::TimeLimit) {
            if ($now->gt($participant->created_at->addMinutes($config->time_limit))) {
                $this->markAsFinish($participant, $participant->created_at->addMinutes($config->time_limit));
                return false;
            }
        }

        return true;
    }

    public function validateStatus(Participant $participant, $section, $answer)
    {
        $config = json_decode($participant->cache_config);

        $now = Carbon::now();

        if ($config->time_mode == TimeMode::TimeLimit) {
            if ($now->gt($participant->created_at->addMinutes($config->time_limit))) {
                $this->markAsFinish($participant, $participant->created_at->addMinutes($config->time_limit));
                return abort(Response::HTTP_UNAUTHORIZED);
            }
        }
    }
}
