<template>
    <layout :title="'Proses Ujian - ' + $page.app.name" active="participant.exams.form">

        <template v-slot:header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Proses Ujian: {{ exam.data.name }}
            </h2>

            <vue-countdown v-if="config.data.time_mode == 2" :time="time_limit" tag="p" @end="forceFinish">
                <template
                    slot-scope="props"
                >
                    Sisa waktu：<span v-if="props.hours">{{ props.hours }} jam, </span><span v-if="props.minutes">{{ props.minutes }} menit, </span>{{ props.seconds }} detik.
                </template>
            </vue-countdown>
            <vue-countdown v-if="config.data.time_mode == 3" :time="section_limit" tag="p" @end="forceNextSection">
                <template
                    slot-scope="props"
                >
                    Sisa waktu sesi：<span v-if="props.hours">{{ props.hours }} jam, </span><span v-if="props.minutes">{{ props.minutes }} menit, </span>{{ props.seconds }} detik.
                </template>
            </vue-countdown>
            <vue-countdown v-if="config.data.time_mode == 4" :time="question_limit" tag="p" @end="forceNextQuestion">
                <template
                    slot-scope="props"
                >
                    Sisa waktu sesi：<span v-if="props.hours">{{ props.hours }} jam, </span><span v-if="props.minutes">{{ props.minutes }} menit, </span>{{ props.seconds }} detik.
                </template>
            </vue-countdown>
        </template>

        <div>
            <div class="grid max-w-7xl mx-auto py-10 sm:px-6 lg:px-8">
                <div class="w-full bg-white p-5">
                    <div class="grid grid-cols-3 gap-4">
                        <div class="col-span-3 md:col-span-2">
                            <span class="font-semibold text-lg text-gray-800 leading-tight">Sesi: {{
                                section.data.name
                            }}</span>
                        </div>
                        <div class="col-span-3 md:col-span-1 justify-self-start md:justify-self-end">
                            <sup class="cursor-pointer" @click="openInstruction = true">
                                instruksi&nbsp;<icon class="inline" name="question"/>
                            </sup>
                        </div>
                        <jet-dialog-modal :show="openInstruction" @close="openInstruction = false">
                            <template #title>
                                Instruksi
                            </template>

                            <template #content>
                                <div class="prose" v-html="compiledInstructionMarkdown"></div>
                            </template>

                            <template #footer>
                                <jet-secondary-button
                                    @click.native="openInstruction = false">
                                    Tutup
                                </jet-secondary-button>
                            </template>
                        </jet-dialog-modal>
                    </div>
                    <div class="grid grid-cols-3 gap-4">
                        <div class="col-span-3 md:col-span-2 py-3 my-3">
                            <b> Pertanyaan. {{ questionNumber }} </b>
                            <div v-if="question" class="col-auto mt-3 border p-3 mb-5">
                                <div class="prose" v-html="compiledQuestionMarkdown"></div>
                                <div v-if="question.type == 2" class="mb-3">
                                    <img :src="question.image" alt="preview" class="img-fluid border" @error="imgError"/>
                                </div>
                            </div>
                            <div v-for="(option, key) in options.data" :key="key" class="cursor-pointer my-3"
                                 @click.prevent="selectOption(option.uuid)">
                                <div>
                                    <icon v-if="form.option == option.uuid" class="inline" name="circle-checked"/>
                                    <icon v-else class="inline" name="circle"/>
                                    <span>{{ alpha(key) }}.</span>
                                    <img
                                        v-if="option.type == 2"
                                        :src="option.image"
                                        alt="preview"
                                        class="img-fluid border"
                                        @error="imgError"
                                    />
                                    <span v-else>{{ option.value }}</span>
                                </div>
                            </div>
                            <div class="row pt-5">
                                <div class="col-12">
                                    <jet-secondary-button v-if="questionNumber !== 1" class="btn btn-outline-secondary float-left"
                                            @click.native="prev">kembali
                                    </jet-secondary-button>
                                    <div class="float-right">
                                        <div v-if="cantSave" class="text-red-500 mb-2">
                                            mohon memilih jawaban terlebih dahulu.
                                        </div>
                                        <jet-button class="float-right" @click.native="saveNext">
                                            Simpan dan Lanjut
                                        </jet-button>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div v-if="config.data.time_mode !== 4" class="col-span-3 md:col-span-1 border-l-0 md:border-l p-3">
                            <div class="row">
                                <p><b> Indeks: </b></p>
                                <p>Anda telah mengerjakan <b>{{ totalFilled }}</b> dari <b>{{ totalQuestion }}</b> Soal.</p>
                            </div>
                            <div class="flex flex-wrap mt-3">
                                <inertia-link
                                    v-for="(answerItem, index) in answers.data"
                                    :key="answerItem.uuid"
                                    :href="'/participant/exams/process/' + participant.data.uuid + '/' + answerItem.uuid"
                                    class="flex m-1 border py-2 px-4"
                                    v-bind:class="{ 'bg-green-300' : answerItem.filled && !isActiveAnswer(answerItem.uuid) ,'bg-gray-300' : isActiveAnswer(answerItem.uuid)}">
                                    {{ index + 1 }}
                                </inertia-link>
                                <div class="flex" v-if="config.data.time_mode === 3">
                                    <button
                                        @click="finishSectionConfirm = true"
                                        class="flex m-1 border py-2 px-4">Selesai Sesi ini
                                    </button>
                                    <jet-dialog-modal :show="finishSectionConfirm" @close="finishSectionConfirm = false">
                                        <template #title>
                                            Konfirmasi
                                        </template>

                                        <template #content>
                                            <p>Apakah anda yakin untuk menyelesaikan sesi ini sebelum waktu selesai?</p>
                                            <small class="text-red-500">
                                                <strong>Peringatan!</strong> anda tidak bisa kembali ke sesi sebelumnya.
                                            </small>
                                        </template>

                                        <template #footer>
                                            <jet-secondary-button
                                                @click.native="finishSectionConfirm = false">
                                                Batal
                                            </jet-secondary-button>
                                            <jet-button
                                                @click.native="forceNextSection">
                                                Lanjutkan
                                            </jet-button>
                                        </template>
                                    </jet-dialog-modal>
                                </div>
                                <inertia-link v-else
                                    :href="'/participant/exams/recap/' + participant.data.uuid"
                                    class="flex m-1 border py-2 px-4">Selesai
                                </inertia-link>

                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </layout>
</template>

<script>
import Layout from '@/Layouts/Exam'
import Icon from '@/Shared/Icon'
import JetButton from "@/Jetstream/Button";
import ButtonLoading from '@/Shared/ButtonLoading'
import JetDialogModal from '@/Jetstream/DialogModal'
import JetSecondaryButton from '@/Jetstream/SecondaryButton'
import marked from 'marked'
import VueCountdown from '@chenfengyuan/vue-countdown'
import Purify from 'dompurify';
import Button from "../../../Jetstream/Button";

export default {
    components: {
        Button,
        Icon,
        Layout,
        ButtonLoading,
        JetButton,
        JetDialogModal,
        JetSecondaryButton,
        VueCountdown,
    },

    props: {
        answer: Object,
        answers: Object,
        config: Object,
        exam: Object,
        options: Object,
        participant: Object,
        question: Object,
        section: Object,
        navigation: Object,
        time_limit: Number,
        section_limit: Number,
        question_limit: Number,
    },

    computed: {
        compiledInstructionMarkdown: function () {
            return Purify.sanitize(marked(this.section.data.instruction));
        },

        compiledQuestionMarkdown: function () {
            return Purify.sanitize(marked(this.question.data.value));
        },

        questionNumber: function () {
            let number = 0
            let answer = this.answer.data
            this.answers.data.forEach(function (value, index,) {
                if (value.uuid === answer.uuid) {
                    number = index + 1
                }
            })
            return number
        },

        totalQuestion: function () {
            return (this.answers.data).length;
        },

        totalFilled: function () {
            let filled = 0;
            (this.answers.data).forEach(function (item) {
                if (item.filled) filled++;
            })
            return filled;
        }
    },

    data() {
        return {
            openInstruction: false,

            finishSectionConfirm: false,

            cantSave: false,

            form: {
                option: this.answer.data.option_uuid
            }
        }
    },

    watch: {
        question(current, old) {
            if (current.data.uuid === old.data.uuid) return;

            this.form.option = this.answer.data.option_uuid;

            this.cantSave = false;
        }
    },

    methods: {
        isActiveAnswer(answerUUID) {
            return answerUUID === this.answer.data.uuid
        },

        alpha(key) {
            return String.fromCharCode('A'.charCodeAt() + key)
        },

        prev() {
            this.$inertia.post('/participant/exams/previous/' + this.participant.data.uuid + '/' + this.answer.data.uuid)
        },

        selectOption(uuid) {
            this.form.option = uuid
        },

        saveNext() {
            if (this.form.option) {
                this.$inertia.post('/participant/exams/submit/' + this.participant.data.uuid + '/' + this.answer.data.uuid + '/' + this.form.option)
            } else {
                this.cantSave = true
            }
        },

        forceNextSection() {
            this.$inertia.post('/participant/exams/next-section/' + this.participant.data.uuid  + '/' + this.section.data.uuid)
        },

        forceNextQuestion() {
            this.$inertia.post('/participant/exams/next-question/' + this.participant.data.uuid  + '/' + this.answer.data.uuid)
        },

        forceFinish() {
            this.$inertia.post('/participant/exams/finish/' + this.participant.data.uuid)
        }
    }
}
</script>
