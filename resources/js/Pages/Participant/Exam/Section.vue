<template>
    <layout :title="'Proses Ujian - ' + exam.data.name +' - '+ $page.app.name" active="participant.exams.form">

        <template v-slot:header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Jeda Sesi: {{ exam.data.name }}
            </h2>
            <vue-countdown v-if="config.data.time_mode == 2" :time="time_limit" tag="p" @end="forceFinish">
                <template
                    slot-scope="props"
                >
                    Sisa waktu：<span v-if="props.hours">{{ props.hours }} jam, </span><span v-if="props.minutes">{{ props.minutes }} menit, </span>{{ props.seconds }} detik.
                </template>
            </vue-countdown>
        </template>

        <div>
            <div class="grid max-w-7xl mx-auto py-10 sm:px-6 lg:px-8 justify-items-center" v-if="section">
                <div class="grid w-full md:w-4/5 sm:w-full xs:w-full rounded bg-white justify-items-center p-5">
                <h4>{{ section.data.name }}</h4>
                <div class="py-3 min-h-full" style="min-height: 100px">
                    <div v-html="compiledMarkdown" class="prose"></div>
                </div>
                <div class="row justify-content-center pt-5">
                    <inertia-link v-if="config.data.time_mode == 3" :href="'/participant/exams/process/' + participant.data.uuid + '/' + answer.data.uuid"
                                  class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 active:bg-gray-900 focus:outline-none focus:border-gray-900 focus:shadow-outline-gray transition ease-in-out duration-150 ml-5"
                                  type="submit">Mulai
                    </inertia-link>
                    <inertia-link v-else :href="'/participant/exams/process/' + participant.data.uuid + '/' + answer.data.uuid"
                                  class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 active:bg-gray-900 focus:outline-none focus:border-gray-900 focus:shadow-outline-gray transition ease-in-out duration-150 ml-5"
                                  type="submit">Mulai
                    </inertia-link>
                </div>
                </div>
            </div>
        </div>
    </layout>
</template>

<script>
import Layout from '@/Layouts/Exam'
import InputText from '@/Shared/InputText'
import ButtonLoading from '@/Shared/ButtonLoading'
import marked from 'marked'
import VueCountdown from '@chenfengyuan/vue-countdown'
import Purify from 'dompurify'

export default {
    components: {
        Layout,
        InputText,
        ButtonLoading,
        VueCountdown,
    },
    props: {
        answer: Object,
        answers: Object,
        config: Object,
        exam: Object,
        section: Object,
        participant: Object,
        time_limit: Number,
    },
    computed: {
        compiledMarkdown: function() {
            return Purify.sanitize(marked(this.section.data.instruction));
        }
    },
    data () {
        return {
            form: {
                code: null
            }
        }
    },
    methods: {
        alpha (key) {
            return String.fromCharCode('A'.charCodeAt() + key)
        },
        details () {
            this.$inertia.post('/participant/exams/details', this.form)
        }
    }
}
</script>
