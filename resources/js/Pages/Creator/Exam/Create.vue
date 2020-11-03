<template>
    <layout :title="'Buat Ujian - ' + $page.app.name" active="creator.exams.index">
        <template v-slot:header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                <inertia-link :href="'/creator/exams'" type="submit">Daftar Ujian</inertia-link>
            </h2>
        </template>
        <div>
            <div class="max-w-7xl mx-auto py-10 sm:px-6 lg:px-8">
                <jet-form-section @submitted="submit">
                    <template #title>
                        Buat ujian baru
                    </template>

                    <template #description>
                        Sebisa mungkin informasi yang anda berikan agar sangat jelas untuk menggambarkan detail ujian tersebut.
                       <br/>
                       <br/>
                        Pastikan juga anda memilih kode ujian yang unik.
                    </template>

                    <template #form>
                        <div class="col-span-6 sm:col-span-4">
                            <jet-label for="exam-name" value="Nama Ujian *"/>
                            <jet-input id="exam-name" ref="exam-name" v-model="form.name" autocomplete="exam-name"
                                       class="mt-1 block w-full" placeholder="contoh: Ujian matematika dasar"
                                       type="text"/>
                            <jet-input-error :message="$page.errors.name" class="mt-2"/>
                        </div>

                        <div class="col-span-6 sm:col-span-4">
                            <jet-label for="description" value="Deskripsi *"/>
                            <jet-input id="description" v-model="form.description" autocomplete="description"
                                       class="mt-1 block w-full"
                                       placeholder="contoh: Ujian matematika dasar tahun 2020" type="text"/>
                            <jet-input-error :message="$page.errors.description" class="mt-2"/>
                        </div>

                        <div class="col-span-6 sm:col-span-4">
                            <jet-label for="exam-code" value="Kode Ujian *"/>
                            <div class="flex items-center form-input rounded-md shadow-sm py-1 mt-1 block w-full">
                                <input id="exam-code" v-model="form.code"
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
                    </template>

                    <template #actions>
                        <jet-button :class="{ 'opacity-25': form.processing }" :disabled="form.processing">
                            Buat ujian
                        </jet-button>
                    </template>
                </jet-form-section>
            </div>
        </div>
    </layout>
</template>

<script>
import Layout from "@/Layouts/AppLayout";
import JetButton from '@/Jetstream/Button'
import JetFormSection from '@/Jetstream/FormSection'
import JetInput from '@/Jetstream/Input'
import JetInputError from '@/Jetstream/InputError'
import JetLabel from '@/Jetstream/Label'

export default {
    components: {
        Layout,
        JetButton,
        JetFormSection,
        JetInput,
        JetInputError,
        JetLabel,
    },
    props: {
        random_question: Object
    },
    data() {
        return {
            sending: false,
            form: {
                name: '',
                description: '',
                code: '',
            }
        };
    },
    methods: {
        generate() {
            this.form.code = Math.random()
                .toString(36)
                .substring(3);
        },
        submit() {
            this.sending = true
            this.$inertia.post('/creator/exams', this.form, { preserveScroll: true }).then(() => this.sending = false)
        }
    }
};
</script>
