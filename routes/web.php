<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

Route::get('/pauli', 'App\Http\Controllers\HomeController@pauli')->name('pauli');

Route::middleware(['auth:sanctum', 'verified'])->group(function() {

    Route::get('/dashboard', 'App\Http\Controllers\HomeController@dashboard')->name('dashboard');

    Route::put('/preference', 'App\Http\Controllers\PreferenceController@update')->name('preference.update');

    Route::prefix('creator')->name('creator.')->group(function () {
        Route::get('/menu', 'App\Http\Controllers\HomeController@creator');
        Route::resource('/exams', 'App\Http\Controllers\Creator\ExamController');
        Route::put('/exams-publish/{exam}', 'App\Http\Controllers\Creator\ExamController@publish')->name('exams.publish');
        Route::put('/exams-duplicate/{exam}', 'App\Http\Controllers\Creator\ExamController@duplicate')->name('exams.duplicate');
        Route::resource('/configs', 'App\Http\Controllers\Creator\ConfigController')->only('update');
        Route::prefix('/sections')->group(function () {
            Route::get('/{exam}', 'App\Http\Controllers\Creator\SectionController@index')->name('sections.index');
            Route::get('/{exam}/create', 'App\Http\Controllers\Creator\SectionController@create')->name('sections.create');
            Route::post('/{exam}/store', 'App\Http\Controllers\Creator\SectionController@store')->name('sections.store');
            Route::get('/{section}/edit', 'App\Http\Controllers\Creator\SectionController@edit')->name('sections.edit');
            Route::put('/{section}/update', 'App\Http\Controllers\Creator\SectionController@update')->name('sections.update');
            Route::post('/{section}/order', 'App\Http\Controllers\Creator\SectionController@order')->name('sections.order');
            Route::delete('/{section}', 'App\Http\Controllers\Creator\SectionController@destroy')->name('sections.destroy');
        });
        Route::prefix('/questions')->group(function () {
            Route::get('/{section}', 'App\Http\Controllers\Creator\QuestionController@index')->name('questions.index');
            Route::get('/{section}/create', 'App\Http\Controllers\Creator\QuestionController@create')->name('questions.create');
            Route::post('/{section}/create', 'App\Http\Controllers\Creator\QuestionController@store')->name('questions.store');
            Route::get('/{question}/edit', 'App\Http\Controllers\Creator\QuestionController@edit')->name('questions.edit');
            Route::put('/{question}/update', 'App\Http\Controllers\Creator\QuestionController@update')->name('questions.update');
            Route::post('/{question}/order', 'App\Http\Controllers\Creator\QuestionController@order')->name('questions.order');
            Route::delete('/{question}', 'App\Http\Controllers\Creator\QuestionController@destroy')->name('questions.destroy');
        });
        Route::prefix('/results')->group(function () {
            Route::get('/', 'App\Http\Controllers\Creator\ResultController@index')->name('results.index');
            Route::get('/{exam}', 'App\Http\Controllers\Creator\ResultController@exam')->name('results.exam');
            Route::get('/details/{participant}', 'App\Http\Controllers\Creator\ResultController@details')->name('results.participant');
        });
    });

    Route::prefix('participant')->name('participant.')->group(function () {
        Route::get('/menu', 'App\Http\Controllers\HomeController@participant');
        Route::prefix('/exams')->group(function () {
            Route::get('form', 'App\Http\Controllers\Participant\ExamController@form')->name('exams.form');
            Route::post('details', 'App\Http\Controllers\Participant\ExamController@details')->name('exams.details.post');
            Route::get('details/{code}', 'App\Http\Controllers\Participant\ExamController@show')->name('exams.details.show');
            Route::post('join/{exam}', 'App\Http\Controllers\Participant\ExamController@join')->name('exams.join');
            Route::get('continue/{participant}/{exam}', 'App\Http\Controllers\Participant\ExamController@continue')->name('exams.continue');
            Route::get('section/{participant}/{answer}/{section}', 'App\Http\Controllers\Participant\ExamController@section')->name('exams.section');
            Route::get('process/{participant}/{answer}', 'App\Http\Controllers\Participant\ExamController@process')->name('exams.process');
            Route::post('submit/{participant}/{answer}/{option}', 'App\Http\Controllers\Participant\ExamController@submit')->name('exams.submit.post');
            Route::post('previous/{participant}/{answer}', 'App\Http\Controllers\Participant\ExamController@previous')->name('exams.previous.post');
            Route::get('recap/{participant}', 'App\Http\Controllers\Participant\ExamController@recap')->name('exams.recap');
            Route::post('finish/{participant}', 'App\Http\Controllers\Participant\ExamController@finish')->name('exams.finish');
        });
        Route::prefix('/results')->group(function () {
            Route::get('/', 'App\Http\Controllers\Participant\ResultController@index')->name('results.index');
            Route::get('/{participant}', 'App\Http\Controllers\Participant\ResultController@show')->name('results.show');
        });
    });
});
