<template>
    <jet-form-section @submitted="submitExam">
        <template #title>
            <Icon class="inline" name="grid"/>
            Informasi Umum
        </template>

        <template #description>
            Informasi umum tentang ujian ini.
        </template>

        <template #form>
            <div class="col-span-6 sm:col-span-4">
                <jet-label for="exam-name" value="Nama Ujian *"/>
                <jet-input id="exam-name" ref="exam-name" v-model="form.exam.name" autocomplete="exam-name"
                           class="mt-1 block w-full" placeholder="contoh: Ujian matematika dasar"
                           type="text"/>
                <jet-input-error :message="$page.errors.name" class="mt-2"/>
            </div>

            <div class="col-span-6 sm:col-span-4">
                <jet-label for="description" value="Deskripsi *"/>
                <jet-input id="description" v-model="form.exam.description" autocomplete="description"
                           class="mt-1 block w-full"
                           placeholder="contoh: Ujian matematika dasar tahun 2020" type="text"/>
                <jet-input-error :message="$page.errors.description" class="mt-2"/>
            </div>

            <div class="col-span-6 sm:col-span-4">
                <jet-label for="exam-code" value="Kode Ujian *"/>
                <div class="flex items-center form-input rounded-md shadow-sm py-1 mt-1 block w-full">
                    <input id="exam-code" v-model="form.exam.code"
                           autocomplete="exam-code"
                           class="appearance-none bg-transparent border-none w-full text-gray-700 mr-3 px-2 leading-tight focus:outline-none"
                           placeholder="contoh: 112233aabb"
                           type="text"/>
                    <button
                        class="w-24 rounded-md inline-flex items-center px-2 bg-gray-800 border border-transparent font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 active:bg-gray-900 focus:outline-none focus:border-gray-900 focus:shadow-outline-gray transition ease-in-out duration-150"
                        type="button" @click.prevent="generate">acak
                        kode
                    </button>
                </div>
                <jet-input-error :message="$page.errors.code" class="mt-2"/>
            </div>

            <div class="col-span-6 sm:col-span-4">
                <jet-label for="visibility-mode" value="Visibilitas *"/>
                <input-basic-select id="visibility-mode" ref="visibility-mode" v-model="form.exam.visibility_status"
                                    autocomplete="visibility-mode"
                                    class="mt-1 block w-full"
                                    type="text">
                    <option v-for="(value, key) in visibility_status" :key="key" :value="key">{{ value }}</option>
                </input-basic-select>
                <jet-input-error :message="$page.errors.visibility_status" class="mt-2"/>
            </div>

        </template>

        <template #actions>
            <jet-action-message :on="form.exam.recentlySuccessful" class="mr-3">
                Disimpan.
            </jet-action-message>

            <jet-button :class="{ 'opacity-25': form.exam.processing }" :disabled="form.exam.processing"
                        type="submit" @click.prevent="submitExam">
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
import JetDangerButton from '@/Jetstream/DangerButton'
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
        JetDangerButton,
        JetActionMessage,
        JetInputError,
        JetSectionBorder,
    },
    props: {
        exam: Object,
        visibility_status: Object,
    },
    data() {
        return {
            sending: false,
            form: {
                exam: this.$inertia.form({
                    name: this.exam.name,
                    description: this.exam.description,
                    code: this.exam.code,
                    visibility_status: this.exam.visibility_status,
                }, {
                    bag: 'updateExam',
                    resetOnSuccess: false,
                }),
            }
        }
    },
    methods: {
        generate() {
            this.form.exam.code = Math.random()
                .toString(36)
                .substring(3)
        },
        submitExam() {
            this.sending = true;
            this.form.exam.put('/creator/exams/' + this.exam.uuid, {
                preserveScroll: true
            }).then(() => (this.sending = false))
        },
    }
}
</script>
