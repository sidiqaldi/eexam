<?php

use App\Enums\AnswerOrderStatus;
use App\Enums\CorrectStatus;
use App\Enums\InputType;
use App\Enums\QuestionOrderStatus;
use App\Enums\RankingStatus;
use App\Enums\ResultStatus;
use App\Enums\TimeMode;
use App\Enums\RecapResultStatus;
use App\Enums\ScoreStatus;
use App\Enums\PassingGradeStatus;


return [

    TimeMode::class => [
        TimeMode::NoLimit => 'Tanpa Batas',
        TimeMode::TimeLimit => 'Batas Waktu',
        TimeMode::PerSection => 'Per Sesi',
        TimeMode::PerQuestion => 'Per Soal',
    ],

    QuestionOrderStatus::class => [
        QuestionOrderStatus::Sequence => 'Urut',
        QuestionOrderStatus::Random => 'Acak',
    ],

    AnswerOrderStatus::class => [
        AnswerOrderStatus::Sequence => 'Urut',
        AnswerOrderStatus::Random => 'Acak',
    ],

    ResultStatus::class => [
        ResultStatus::RecapAndResult => 'Tampilkan rekapitulasi & hasil',
        ResultStatus::ResultOnly => 'Tampilkan hasil saja',
        ResultStatus::HideAll => 'Sembunyikan Semua',
    ],

    RankingStatus::class => [
        RankingStatus::Show => 'Tampilkan',
        RankingStatus::Hide => 'Sembunyikan',
    ],

    InputType::class => [
        InputType::Text => 'Teks',
        InputType::ImageUrl => 'Gambar',
    ],

    CorrectStatus::class => [
        CorrectStatus::True => 'Benar',
        CorrectStatus::False => 'Salah',
    ],

    RecapResultStatus::class => [
        RecapResultStatus::Passed => 'Lulus',
        RecapResultStatus::Failed => 'Gagal',
    ],

    ScoreStatus::class => [
        ScoreStatus::Global => 'Keseluruhan',
        ScoreStatus::Section => 'Per Sesi',
        ScoreStatus::Question => 'Per Soal',
    ],

    PassingGradeStatus::class => [
        PassingGradeStatus::Global => 'Keseluruhan',
        PassingGradeStatus::Section => 'Per Sesi',
    ],
];
