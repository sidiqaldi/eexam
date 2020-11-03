<template>
    <layout :title="'Rekapitulasi Ujian - ' + $page.app.name" active="participant.exams.form">

        <template v-slot:header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Rekapitulasi Ujian: {{ exam.data.name }}
            </h2>
        </template>

        <div>
            <div class="grid max-w-7xl mx-auto py-10 sm:px-6 lg:px-8">
                <div class="w-full bg-white p-5">
                    <div class="p-3">
                        <div v-if="config.data.time_mode === 3 || config.data.time_mode === 4">
                            <p>Anda telah mengerjakan ujian ini.</p>
                        </div>
                        <div v-else>
                            <p>Anda telah mengerjakan <b>{{ totalFilled }}</b> dari <b>{{ totalQuestion }}</b> Soal.</p>
                            <p>Untuk memeriksa jawaban anda kembali dapat dengan menekan tombol pada indeks berikut:.</p>
                        </div>
                    </div>

                    <div v-if="config.data.time_mode === 1 || config.data.time_mode === 2" class="col-span-3 md:col-span-1 p-3 mb-5">
                        <b> Indeks: </b>
                        <div class="flex flex-wrap mt-3">
                            <inertia-link
                                v-for="(answerItem, index) in answers.data"
                                :key="answerItem.uuid"
                                :href="'/participant/exams/process/' + participant.data.uuid + '/' + answerItem.uuid"
                                class="flex m-1 border py-2 px-4"
                                type="submit"
                                v-bind:class="{ 'bg-green-300' : answerItem.filled }">
                                {{ index + 1 }}
                            </inertia-link>
                        </div>
                    </div>

                    <div class="p-3">
                        <div class="mb-5">
                            <p>Dengan menekan tombol <b>"selesai ujian"</b> maka anda menyatakan telah selesai mengerjakan ujian ini.</p>
                        </div>
                        <jet-button @click.native="submit">Selesai Ujian</jet-button>
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
import JetDialogModal from "@/Jetstream/DialogModal"
import JetSecondaryButton from "@/Jetstream/SecondaryButton"

export default {

    components: {
        Icon,
        Layout,
        ButtonLoading,
        JetButton,
        JetDialogModal,
        JetSecondaryButton,
    },

    props: {
        answers: Object,
        config: Object,
        exam: Object,
        participant: Object,
    },

    computed: {
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
        return {}
    },

    methods: {
        submit() {
            this.$inertia.post('/participant/exams/finish/' + this.participant.data.uuid)
        }
    }
}
</script>
