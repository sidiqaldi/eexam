<template>
    <jet-form-section @submitted="submitConfig">
        <template #title>
            <icon class="inline" name="gear"/>
            Pengaturan
        </template>

        <template #description>
            Pengaturan dasar ujian. <br/><br/>
            <span class="text-red-500"><strong>Peringatan!</strong> mengganti pengaturan tidak akan mempengaruhi hasil/pengaturan peserta yang telah melakukan test. Apabila terpaksa lebih baik untuk anda agar membuat ujian baru dengan melakukan duplikasi ujian yang pernah ada.</span>
        </template>

        <template #form>
             <div class="col-span-6 sm:col-span-4">
                <jet-label for="visibility-mode" value="Visibilitas *"/>
                <input-basic-select id="visibility-mode" ref="visibility-mode" v-model="form.config.visibility_status"
                                    autocomplete="visibility-mode"
                                    class="mt-1 block w-full"
                                    type="text">
                    <option v-for="(value, key) in visibility_status" :key="key" :value="key">{{ value }}</option>
                </input-basic-select>
                <jet-input-error :message="$page.errors.visibility_status" class="mt-2"/>
            </div>

            <div class="col-span-6 sm:col-span-4">
                <jet-label for="time-mode" value="Pengaturan waktu *"/>
                <input-basic-select id="time-mode" ref="time-mode" v-model="form.config.time_mode"
                                    autocomplete="time-mode"
                                    class="mt-1 block w-full"
                                    type="text">
                    <option v-for="(value, key) in time_mode" :key="key" :value="key">{{ value }}</option>
                </input-basic-select>
                <jet-input-error :message="$page.errors.time_mode" class="mt-2"/>
            </div>

            <div v-if="parseInt(form.config.time_mode) !== 1" class="col-span-6 sm:col-span-4">
                <jet-label for="time_limit" value="Batas waktu *"/>
                <jet-input
                    id="time_limit"
                    v-model="form.config.time_limit"
                    class="mt-1 block w-full"
                    placeholder="Batas waktu (dalam menit)"
                    type="number"
                />
                <jet-input-error :message="$page.errors.time_limit" class="mt-2"/>
            </div>

            <div class="col-span-6 sm:col-span-4">
                <jet-label for="question_order" value="Urutan soal *"/>
                <input-basic-select id="question_order" ref="time-mode" v-model="form.config.question_order"
                                    autocomplete="time-mode"
                                    class="mt-1 block w-full">
                    <option v-for="(value, key) in question_order" :key="key" :value="key">{{ value }}</option>
                </input-basic-select>
                <jet-input-error :message="$page.errors.question_order" class="mt-2"/>
            </div>

            <div class="col-span-6 sm:col-span-4">
                <jet-label for="answer_order" value="Urutan Jawaban *"/>
                <input-basic-select id="answer_order" ref="time-mode" v-model="form.config.answer_order"
                                    autocomplete="answer_order"
                                    class="mt-1 block w-full"
                                    type="text">
                    <option v-for="(value, key) in answer_order" :key="key" :value="key">{{ value }}</option>
                </input-basic-select>
                <jet-input-error :message="$page.errors.answer_order" class="mt-2"/>
            </div>

            <div class="col-span-6 sm:col-span-4">
                <jet-label for="score_status" value="Pengaturan bobot soal *"/>
                <input-basic-select id="score_status" ref="time-mode" v-model="form.config.score_status"
                                    autocomplete="score_status"
                                    class="mt-1 block w-full">
                    <option v-for="(value, key) in score_status" :key="key" :value="key">{{ value }}</option>
                </input-basic-select>
                <jet-input-error :message="$page.errors.score_status" class="mt-2"/>
            </div>

            <div class="col-span-6 sm:col-span-4">
                <jet-label for="passing_grade_status" value="Pengaturan passing grade *"/>
                <input-basic-select id="passing_grade_status" ref="time-mode" v-model="form.config.passing_grade_status"
                                    autocomplete="passing_grade_status"
                                    class="mt-1 block w-full">
                    <option v-for="(value, key) in passing_grade_status" :key="key" :value="key">{{ value }}</option>
                </input-basic-select>
                <jet-input-error :message="$page.errors.passing_grade_status" class="mt-2"/>
            </div>

            <div class="col-span-6 sm:col-span-4">
                <jet-label for="default_score" value="Default bobot soal*"/>
                <jet-input
                    id="default_score"
                    v-model="form.config.default_score"
                    class="mt-1 block w-full"
                    placeholder="contoh: 10"
                    type="number"
                />
                <jet-input-error :message="$page.errors.default_score" class="mt-2"/>
            </div>

            <div class="col-span-6 sm:col-span-4">
                <jet-label for="default_passing_grade" value="Default passing grade*"/>
                <jet-input
                    id="default_passing_grade"
                    v-model="form.config.default_passing_grade"
                    class="mt-1 block w-full"
                    placeholder="contoh: 100"
                    type="number"
                />
                <jet-input-error :message="$page.errors.default_passing_grade" class="mt-2"/>
            </div>

            <div class="col-span-6 sm:col-span-4">
                <jet-label for="ranking_status" value="Tampilkan Ranking *"/>
                <input-basic-select id="ranking_status" ref="time-mode" v-model="form.config.ranking_status"
                                    autocomplete="ranking_status"
                                    class="mt-1 block w-full"
                                    type="text">
                    <option v-for="(value, key) in ranking_status" :key="key" :value="key">{{ value }}</option>
                </input-basic-select>
                <jet-input-error :message="$page.errors.ranking_status" class="mt-2"/>
            </div>

            <div class="col-span-6 sm:col-span-4">
                <jet-label for="result_status" value="Tampilkan Hasil *"/>
                <input-basic-select id="result_status" ref="time-mode" v-model="form.config.result_status"
                                    autocomplete="ranking_status"
                                    class="mt-1 block w-full"
                                    type="text">
                    <option v-for="(value, key) in result_status" :key="key" :value="key">{{ value }}</option>
                </input-basic-select>
                <jet-input-error :message="$page.errors.result_status" class="mt-2"/>
            </div>

        </template>

        <template #actions>
            <jet-action-message :on="form.config.recentlySuccessful" class="mr-3">
                Disimpan.
            </jet-action-message>

            <jet-button :class="{ 'opacity-25': form.config.processing }" :disabled="form.config.processing"
                        type="submit" @click.prevent="submitConfig">
                Simpan
            </jet-button>
        </template>
    </jet-form-section>

</template>

<script>
import Icon from '@/Shared/Icon'
import InputBasicSelect from '@/Shared/InputBasicSelect'
import JetActionMessage from '@/Jetstream/ActionMessage'
import JetButton from '@/Jetstream/Button'
import JetFormSection from '@/Jetstream/FormSection'
import JetInput from '@/Jetstream/Input'
import JetInputError from '@/Jetstream/InputError'
import JetLabel from '@/Jetstream/Label'
import JetSectionBorder from '@/Jetstream/SectionBorder'

export default {
    components: {
        Icon,
        InputBasicSelect,
        JetFormSection,
        JetInput,
        JetLabel,
        JetButton,
        JetActionMessage,
        JetInputError,
        JetSectionBorder,
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
        config: Object,
    },
    data() {
        return {
            sending: false,
            form: {
                config: this.$inertia.form({
                    visibility_status: this.config.visibility_status,
                    question_order: this.config.question_order,
                    answer_order: this.config.answer_order,
                    time_mode: this.config.time_mode,
                    time_limit: this.config.time_limit,
                    ranking_status: this.config.ranking_status,
                    result_status: this.config.result_status,
                    score_status: this.config.score_status,
                    passing_grade_status: this.config.passing_grade_status,
                    default_score: this.config.default_score,
                    default_passing_grade: this.config.default_passing_grade,
                }, {
                    bag: 'updateConfig',
                    resetOnSuccess: false,
                }),
            }
        }
    },
    methods: {
        submitConfig() {
            this.sending = true
            this.form.config.put(
                '/creator/configs/' + this.config.uuid, {
                    preserveScroll: true
                }).then(() => (this.sending = false))
        }
    }
}
</script>
