<template>
    <layout :title="'Edit Pertanyaan - ' + $page.app.name" active="creator.exams.index">
        <template v-slot:header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                <inertia-link :href="'/creator/exams'" type="submit">Daftar Ujian</inertia-link>
                /
                <inertia-link :href="'/creator/exams/' + exam.uuid + '/edit'" type="submit">{{ exam.name }}
                </inertia-link>
                /
                <inertia-link :href="'/creator/sections/' + exam.uuid" type="submit">Daftar Sesi</inertia-link>
                /
                <inertia-link :href="'/creator/questions/' + section.uuid" type="submit">{{ section.name }}
                </inertia-link>
                /
                Edit Soal
            </h2>
        </template>

        <div>
            <div class="max-w-7xl mx-auto py-10 sm:px-6 lg:px-8">

                <jet-form-section @submitted="submit">
                    <template #title>
                        Edit {{ form.question_title }}
                    </template>

                    <template #description>
                        <div class="bg-white shadow rounded overflow-hidden px-4 py-3 min-h-full">
                            <div class="prose" v-html="compiledMarkdown"></div>
                            <div v-if="form.question_type == 2" class="mb-3">
                                <img
                                    :src="form.question_image || '/img/404.jpg'"
                                    alt="preview"
                                    class="img-fluid border"
                                    @error="imgError"
                                />
                            </div>
                            <ol class="ml-5 mt-4" style="list-style: upper-latin" type="A">
                                <li v-for="(option, key) in form.options" :key="key" class="prose">
                                    <img
                                        v-if="option.type == 2"
                                        :src="option.image || '/img/404.jpg'"
                                        alt="preview"
                                        class="img-fluid border"
                                        @error="imgError"
                                    />
                                    <span v-else-if="option.value">{{ option.value }}</span>
                                </li>
                            </ol>
                        </div>
                    </template>

                    <template #form>

                        <div v-if="parseInt(config.time_mode) === 4" class="col-span-6 sm:col-span-4">
                            <jet-label for="time_limit" value="Batas waktu *"/>
                            <jet-input
                                id="time_limit"
                                v-model="form.time_limit"
                                class="mt-1 block w-full"
                                placeholder="Batas waktu (*dalam detik)"
                                type="number"
                                min="5"
                                max="3600"
                            />
                            <jet-input-error :message="$page.errors.time_limit" class="mt-2"/>
                        </div>

                        <div v-if="config.score_status === 3" class="col-span-6 sm:col-span-4">
                            <jet-label for="score" value="Nilai per Soal *"/>
                            <jet-input id="score" ref="score" v-model="form.score"
                                       autocomplete="score"
                                       class="mt-1 block w-full"
                                       min="0"
                                       placeholder="contoh: 10" required
                                       type="number"/>
                            <jet-input-error :message="$page.errors.score" class="mt-2"/>
                        </div>

                        <div class="col-span-6 sm:col-span-4">
                            <jet-label for="question_type" value="Tipe soal:"/>
                            <input-basic-select id="question_type" ref="time-mode" v-model="form.question_type"
                                                autocomplete="question_type"
                                                class="mt-1 block w-full"
                                                type="text">
                                <option v-for="(value, key) in input_type" :key="key" :value="key">{{ value }}</option>
                            </input-basic-select>
                            <jet-input-error :message="$page.errors.question_type" class="mt-2"/>
                        </div>

                        <div class="col-span-6 sm:col-span-4">
                            <jet-label for="question_title" value="Judul *"/>
                            <jet-input id="question_title" ref="exam-name" v-model="form.question_title"
                                       autocomplete="question_title"
                                       class="mt-1 block w-full"
                                       length="100"
                                       placeholder="contoh: Soal #1 tentang penjumlahan sederhana" required
                                       type="text"/>
                            <jet-input-error :message="$page.errors.question_title" class="mt-2"/>
                            <small>digunakan untuk penanda soal, tidak akan ditampilkan ke peserta ujian</small>

                        </div>
                        <div class="col-span-6 sm:col-span-4">
                            <jet-label for="question_value" value="Soal *"/>
                            <textarea
                                id="question_value"
                                v-model="form.question_value"
                                class="mt-1 block w-full p-2 resize-y border rounded focus:outline-none focus:shadow-outline"
                                placeholder="contoh: Nilai dari penjumlahan 10cm + 10cm adalah.."
                                required
                            ></textarea>
                            <jet-input-error :message="$page.errors.question_value" class="mt-2"/>
                        </div>

                        <div v-if="form.question_type == 2" class="col-span-6 sm:col-span-4">
                            <jet-label for="question_image" value="URL Gambar *"/>
                            <jet-input id="question_image" ref="question_image" v-model="form.question_image"
                                       autocomplete="question_image"
                                       class="mt-1 block w-full"
                                       placeholder="contoh: https://dummyimage.com/300x200/b8b8b8/fff.jpg"
                                       type="text"/>
                            <jet-input-error :message="$page.errors.question_image" class="mt-2"/>
                        </div>

                        <div class="col-span-8 sm:col-span-4">
                            <jet-label for="question_image" value="Pilihan jawaban *"/>
                            <jet-input-error :message="$page.errors.options"/>
                            <div v-for="(option, key) in form.options" :key="option.key"
                                 class="w-full gap-4 justify-items-start my-2">
                                <div class="inline">
                                    <input-basic-select v-model="option.type" class="form-control">
                                        <option v-for="(type, index) in input_type" :key="index" :value="index">{{
                                                type
                                            }}
                                        </option>
                                    </input-basic-select>
                                </div>
                                <div class="inline">
                                    <jet-input
                                        v-if="option.type == 1"
                                        v-model="option.value"
                                        class="form-control"
                                        placeholder="contoh: 120cm"
                                        type="text"
                                    />
                                    <jet-input
                                        v-else-if="option.type == 2"
                                        v-model="option.image"
                                        class="form-control"
                                        placeholder="contoh: https://dummyimage.com/300x200/b8b8b8/fff.jpg"
                                        type="text"
                                    />
                                </div>
                                <div class="inline">
                                    <jet-button v-if="answer === option.key" type="button" class="py-3" @click.native="correct(key)">
                                        Benar
                                    </jet-button>
                                    <jet-secondary-button v-else class="py-3" type="button" @click.native="correct(key)">Salah
                                    </jet-secondary-button>
                                    <jet-danger-button
                                        class="py-3"
                                        type="button"
                                        @click.native="deleteOption(key)"
                                    >
                                        <icon name="trash"/>
                                    </jet-danger-button>
                                </div>
                                <jet-input-error :message="$page.errors['options.'+key+'.value']"/>
                                <jet-input-error :message="$page.errors['options.'+key+'.image']"/>
                            </div>
                        </div>

                        <div class="col-span-6 sm:col-span-4">
                            <jet-secondary-button v-if="form.options.length < 5" @click.native="addOption">
                                <icon class="inline" name="plus"/>
                                Tambah jawaban
                            </jet-secondary-button>
                        </div>


                    </template>

                    <template #actions>
                        <jet-button :class="{ 'opacity-25': form.processing }" :disabled="form.processing"
                                    type="submit" @click.prevent="submit">
                            Simpan Soal
                        </jet-button>
                    </template>
                </jet-form-section>
            </div>
        </div>
    </layout>
</template>

<script>
import Layout from '@/Layouts/AppLayout'
import Icon from '@/Shared/Icon'
import marked from 'marked'
import JetButton from '@/Jetstream/Button'
import JetDangerButton from '@/Jetstream/DangerButton'
import JetFormSection from '@/Jetstream/FormSection'
import JetInput from '@/Jetstream/Input'
import JetInputError from '@/Jetstream/InputError'
import JetLabel from '@/Jetstream/Label'
import JetSectionBorder from '@/Jetstream/SectionBorder'
import InputBasicSelect from '@/Shared/InputBasicSelect'
import JetSecondaryButton from '@/Jetstream/SecondaryButton'
import Purify from 'dompurify'

export default {
    components: {
        Layout,
        Icon,
        JetButton,
        JetDangerButton,
        JetFormSection,
        JetInput,
        JetInputError,
        JetLabel,
        JetSectionBorder,
        JetSecondaryButton,
        InputBasicSelect,
    },
    props: {
        config: Object,
        exam: Object,
        section: Object,
        input_type: Object,
        question: Object,
        options: Array,
        correct_answer: Object
    },
    data() {
        return {
            sending: false,
            answer: null,
            answerKey: null,
            form: {
                time_limit: "",
                score: "",
                question_title: "",
                question_value: "",
                question_type: 1,
                question_image: "",
                options: []
            }
        };
    },
    mounted() {
        this.answer = this.correct_answer ? this.correct_answer.answer : null;
        this.answerKey = this.correct_answer ? this.correct_answer.key : null;
        this.form.score = this.question.score;
        this.form.time_limit = this.question.time_limit;
        this.form.question_title = this.question.title;
        this.form.question_value = this.question.value;
        this.form.question_type = this.question.type;
        this.form.question_image = this.question.image;
        this.form.options = this.question.options;
    },
    computed: {
        compiledMarkdown: function () {
            return Purify.sanitize(marked(this.form.question_value));
        }
    },
    methods: {
        correct(key) {
            let val = null;
            if (this.answer !== null) this.setIsCorrect(this.answerKey, 1);
            this.setIsCorrect(key, 2);
        },
        setIsCorrect(key, val) {
            let form = this.form.options[key];
            form.correct_id = val;
            Vue.set(this.form.options, key, form);
            this.answer = form.key;
            this.answerKey = key;
        },
        deleteOption(key) {
            if ((this.form.options).length <= 2) return;
            if (this.answerKey == key) {
                this.answer = null;
                this.answerKey = null;
            }
            Vue.delete(this.form.options, key);
        },
        addOption() {
            this.form.options.push({
                key: Date.now(),
                value: "",
                image: "",
                type: 1,
                correct_id: 1
            });
        },
        imgError(e) {
            e.target.src = "/img/404.jpg";
        },
        submit() {
            this.sending = true;
            this.$inertia.put('/creator/questions/' + this.question.uuid + '/update',
                this.form
            ).then(() => (this.sending = false));
        }
    }
};
</script>
