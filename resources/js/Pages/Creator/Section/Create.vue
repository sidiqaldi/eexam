<template>
    <layout :title="'Buat Sesi Ujian - ' + $page.app.name" active="creator.exams.index">
        <template v-slot:header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                <inertia-link :href="'/creator/exams'" type="submit">Daftar Ujian</inertia-link>
                /
                <inertia-link :href="'/creator/exams/' + exam.uuid + '/edit'" type="submit">{{
                        exam.name
                    }}
                </inertia-link>
                /
                <inertia-link :href="'/creator/sections/' + exam.uuid" type="submit">Daftar Sesi</inertia-link>
                / Buat baru
            </h2>
        </template>

        <div>
            <div class="max-w-7xl mx-auto py-10 sm:px-6 lg:px-8">

                <jet-form-section @submitted="submit">
                    <template #title>
                        Buat Sesi : {{ form.name }}
                    </template>

                    <template #description>
                        <div class="bg-white shadow rounded overflow-hidden px-4 py-3 min-h-full" style="min-height: 100px">
                            <div class="prose" v-html="compiledMarkdown"></div>
                        </div>
                    </template>

                    <template #form>

                        <div v-if="parseInt(config.time_mode) === 3" class="col-span-6 sm:col-span-4">
                            <jet-label for="time_limit" value="Batas waktu *"/>
                            <jet-input
                                id="time_limit"
                                v-model="form.time_limit"
                                class="mt-1 block w-full"
                                placeholder="Batas waktu (dalam menit)"
                                type="number"
                                min="5"
                                max="360"
                            />
                            <jet-input-error :message="$page.errors.time_limit" class="mt-2"/>
                        </div>

                        <div class="col-span-6 sm:col-span-4">
                            <jet-label for="section-name" value="Nama Sesi *"/>
                            <jet-input id="section-name" ref="exam-name" v-model="form.name" autocomplete="section-name"
                                       class="mt-1 block w-full" placeholder="contoh: Sesi #1 pengetahuan dasar"
                                       required
                                       type="text"/>
                            <jet-input-error :message="$page.errors.name" class="mt-2"/>
                        </div>

                        <div class="col-span-6 sm:col-span-4">
                            <jet-label for="section-instruction" value="Instruksi *"/>
                            <textarea
                                id="section-instruction"
                                v-model="form.instruction"
                                class="mt-1 block w-full p-2 resize-y border rounded focus:outline-none focus:shadow-outline"
                                placeholder="contoh: Pilihlah jawaban yang paling tepat"
                                required
                            ></textarea>
                            <jet-input-error :message="$page.errors.instruction" class="mt-2"/>
                        </div>

                        <div v-if="config.score_status === 2" class="col-span-6 sm:col-span-4">
                            <jet-label for="score_per_question" value="Nilai per Soal *"/>
                            <jet-input id="score_per_question" ref="exam-name" v-model="form.score_per_question"
                                       autocomplete="score_per_question"
                                       class="mt-1 block w-full"
                                       min="0"
                                       placeholder="contoh: 10" required
                                       type="number"/>
                            <jet-input-error :message="$page.errors.score_per_question" class="mt-2"/>
                        </div>

                        <div v-if="config.passing_grade_status === 2" class="col-span-6 sm:col-span-4">
                            <jet-label for="passing_grade" value="Passing Grade *"/>
                            <jet-input id="passing_grade" ref="exam-name" v-model="form.passing_grade"
                                       autocomplete="passing_grade"
                                       class="mt-1 block w-full"
                                       min="0"
                                       placeholder="contoh: 100" required
                                       type="number"/>
                            <jet-input-error :message="$page.errors.passing_grade" class="mt-2"/>
                        </div>


                    </template>

                    <template #actions>
                        <jet-action-message :on="form.recentlySuccessful" class="mr-3">
                            Disimpan.
                        </jet-action-message>

                        <jet-button :class="{ 'opacity-25': form.processing }" :disabled="form.processing"
                                    type="submit" @click.prevent="submit">
                            Tambahkan Sesi
                        </jet-button>
                    </template>
                </jet-form-section>
            </div>
        </div>
    </layout>
</template>

<script>
import Layout from "@/Layouts/AppLayout";
import marked from "marked";
import JetActionMessage from '@/Jetstream/ActionMessage'
import JetButton from '@/Jetstream/Button'
import JetDangerButton from '@/Jetstream/DangerButton'
import JetFormSection from '@/Jetstream/FormSection'
import JetInput from '@/Jetstream/Input'
import JetInputError from '@/Jetstream/InputError'
import JetLabel from '@/Jetstream/Label'
import JetSectionBorder from '@/Jetstream/SectionBorder'
import Purify from 'dompurify'

export default {
    components: {
        Layout,
        JetActionMessage,
        JetButton,
        JetDangerButton,
        JetFormSection,
        JetInput,
        JetInputError,
        JetLabel,
        JetSectionBorder,
    },
    props: {
        exam: Object,
        config: Object
    },
    computed: {
        compiledMarkdown: function () {
            return Purify.sanitize(marked(this.form.instruction));
        }
    },
    data() {
        return {
            sending: false,
            form: this.$inertia.form({
                name: null,
                instruction: '',
                score_per_question: null,
                passing_grade: null,
                time_limit: '',
            }, {
                bag: 'createSection',
            })
        };
    },
    methods: {
        submit() {
            this.sending = true;
            this.$inertia
                .post('/creator/sections/' + this.exam.uuid + '/store', this.form)
                .then(() => (this.sending = false));
        }
    }
};
</script>
