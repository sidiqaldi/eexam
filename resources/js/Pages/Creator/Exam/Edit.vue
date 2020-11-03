<template>
    <layout :title="'Edit Ujian - ' + $page.app.name" active="creator.exams.index">
        <template v-slot:header>
            <div class="grid sm:grid-cols-2 gap-4 justify-items-start">
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                    <inertia-link :href="'/creator/exams'" type="submit">Daftar Ujian</inertia-link>
                    / {{ exam.name }} / Edit
                </h2>
                <div class="justify-self-end">
                    <jet-button v-if="exam.status_id == 1" @click.native="publish"><span>publish</span></jet-button>
                    <jet-danger-button v-else @click.native="publish"><span>unpublish</span></jet-danger-button>
                </div>
            </div>
        </template>

        <div v-if="$page.errors.exam && publishError">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="mb-1 flex items-center justify-between bg-red-500 rounded-b max-w">
                    <div class="flex items-center">
                        <svg class="ml-4 mr-2 flex-shrink-0 w-4 h-4 fill-white" viewBox="0 0 20 20"
                             xmlns="http://www.w3.org/2000/svg">
                            <path
                                d="M2.93 17.07A10 10 0 1 1 17.07 2.93 10 10 0 0 1 2.93 17.07zm1.41-1.41A8 8 0 1 0 15.66 4.34 8 8 0 0 0 4.34 15.66zm9.9-8.49L11.41 10l2.83 2.83-1.41 1.41L10 11.41l-2.83 2.83-1.41-1.41L8.59 10 5.76 7.17l1.41-1.41L10 8.59l2.83-2.83 1.41 1.41z"/>
                        </svg>
                        <div v-if="$page.errors.exam" class="py-4 text-white text-sm font-medium">{{
                                $page.errors.exam
                            }}
                        </div>
                    </div>
                    <inertia-link class="mx-4 inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest shadow-sm hover:text-gray-500 focus:outline-none focus:border-blue-300 focus:shadow-outline-blue active:text-gray-800 active:bg-gray-50 transition ease-in-out duration-150" :href="'/creator/sections/' + exam.uuid">Menuju Sesi & Soal</inertia-link>
                </div>
            </div>
        </div>

        <div>
            <div class="max-w-7xl mx-auto py-10 sm:px-6 lg:px-8">

                <edit-exam :exam="exam"/>

                <jet-section-border/>

                <edit-config class="md:grid md:grid-cols-3 md:gap-6 mt-10 sm:mt-0" :answer_order="answer_order"
                             :visibility_status="visibility_status"
                             :config="config"
                             :exam="exam"
                             :question_order="question_order"
                             :ranking_status="ranking_status"
                             :result_status="result_status"
                             :time_mode="time_mode"
                             :score_status="score_status"
                             :passing_grade_status="passing_grade_status"/>
                <jet-section-border/>

                <jet-form-section v-if="exam.status_id === 1" @submitted="false" class="md:grid md:grid-cols-3 md:gap-6 mt-10 sm:mt-0">
                    <template #title>
                        <icon class="inline" name="list-task"/>
                        Sesi & Soal
                    </template>

                    <template #description>
                        Managemen sesi ujian dan soal-soal yang digunakan dalam ujian.
                    </template>

                    <template #form>
                        <div class="col-span-6 sm:col-span-4">
                        <inertia-link class="w-full inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest shadow-sm hover:text-gray-500 focus:outline-none focus:border-blue-300 focus:shadow-outline-blue active:text-gray-800 active:bg-gray-50 transition ease-in-out duration-150" :href="'/creator/sections/' + exam.uuid">Menuju Sesi & Soal</inertia-link>
                        </div>
                    </template>

                </jet-form-section>
            </div>
        </div>
    </layout>
</template>

<script>
import Layout from '@/Layouts/AppLayout'
import Icon from '@/Shared/Icon'
import JetButton from '@/Jetstream/Button'
import JetDangerButton from "@/Jetstream/DangerButton";
import JetSectionBorder from '@/Jetstream/SectionBorder'
import JetFormSection from "@/Jetstream/FormSection";
import EditExam from "./EditExam";
import EditConfig from "./EditConfig";

export default {
    components: {
        Layout,
        Icon,
        JetButton,
        JetDangerButton,
        JetSectionBorder,
        JetFormSection,
        EditExam,
        EditConfig,
    },
    props: {
        visibility_status: Object,
        question_order: Object,
        answer_order: Object,
        ranking_status: Object,
        result_status: Object,
        time_mode: Object,
        score_status: Object,
        passing_grade_status: Object,
        exam: Object,
        config: Object
    },
    data() {
        return {
            publishError: true,
            sending: false,
        }
    },
    methods: {

        publish() {
            this.$inertia.put('/creator/exams-publish/' + this.exam.uuid)
        },

    }
}
</script>
