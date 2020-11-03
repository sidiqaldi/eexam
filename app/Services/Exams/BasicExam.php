<?php

namespace App\Services\Exams;

use App\Models\Answer;
use App\Models\Exam;
use App\Models\Participant;
use App\Services\RecapService;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class BasicExam
{
    public function join(Exam $exam)
    {
        $participant = $this->getParticipantUser($exam);

        if ($participant) {

            if ($this->onGoing($participant)) {
                return redirect()->route('participant.exams.section', [
                    'participant' => $participant->uuid,
                    'answer' => $this->firstQuestion($participant)->uuid,
                    'section' => $this->currentSection($participant)->uuid,
                ]);
            }

            return redirect()->back()->withErrors(['join' => [__('validation.participate')]]);
        }

        $participant = Participant::query()->create([
            'user_id' => Auth::id(),
            'exam_id' => $exam->id,
            'random_key' => Auth::id(),
        ]);

        foreach ($exam->sections as $section) {
            foreach ($section->questions as $question) {
                Answer::query()->create([
                    'user_id' => Auth::id(),
                    'participant_id' => $participant->id,
                    'section_id' => $section->id,
                    'question_id' => $question->id,
                ]);
            }
        }

        $participant = Participant::find($participant->getAttribute('id'));

        RecapService::init($participant);

        return redirect()->route('participant.exams.section', [
            'participant' => $participant->uuid,
            'answer' => $this->firstQuestion($participant)->uuid,
            'section' => $this->currentSection($participant)->uuid,
        ]);
    }

    /**
     * @param Exam $exam
     * @return Participant|\Illuminate\Database\Eloquent\Model|\Illuminate\Database\Eloquent\Relations\HasMany|object
     */
    public function getParticipantUser(Exam $exam)
    {
        return $exam->participants()->where('user_id', Auth::id())->first();
    }

    public function ListQuestion(Participant $participant)
    {
        return Answer::withSectionOrder()
            ->where('participant_id', $participant->id)
            ->orderBy('section_order', 'asc')
            ->orderBy('id', 'asc')
            ->get();
    }

    /**
     * @param Participant $participant
     * @return Answer
     */
    public function firstQuestion(Participant $participant)
    {
        return Answer::withSectionOrder()
            ->where('participant_id', $participant->id)
            ->where('option_id', NULL)
            ->orderBy('section_order', 'asc')
            ->orderBy('id', 'asc')
            ->first()
            ?? Answer::withSectionOrder()
                ->where('participant_id', $participant->id)
                ->orderBy('section_order', 'asc')
                ->orderBy('id', 'desc')
                ->first();
    }

    public function currentSection(Participant $participant)
    {
        $question = $this->firstQuestion($participant);

        return $question->section;
    }

    public function participantAnswer($participant, $section = null)
    {
        return Answer::withSectionOrder()
            ->withOptionUuid()
            ->where('participant_id', $participant->id)
            ->orderBy('section_order', 'asc')
            ->orderBy('id', 'asc')
            ->get();
    }

    public function onGoing(Participant $participant)
    {
        if (!empty($participant->finish_at)) {
            return false;
        }

        return true;
    }

    public function validateStatus(Participant $participant, $section, $answer)
    {
        return true;
    }

    public function startSection($participant, $section, $answer)
    {
        return true;
    }

    public function endSection($participant, $section, $answer)
    {
        return true;
    }

    public function markAsFinish(Participant $participant, Carbon $time)
    {
        RecapService::create($participant);

        $participant->finish_at = $time;
        $participant->save();

        return true;
    }
}
