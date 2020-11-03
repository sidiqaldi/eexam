<template>
    <layout
        :title="'Halaman Penyelenggara - ' + $page.app.name"
        active="creator.exams.index"
        page="Daftar Ujian"
    >
        <template v-slot:header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Daftar Peserta Ujian: {{ exam.data.name }}
            </h2>
        </template>

        <div>
            <div class="max-w-7xl mx-auto py-10 sm:px-6 lg:px-8">
                <div class="grid gap-4 justify-items-stretch my-3">
                    <table class="table-auto border-collapse border border-gray-300">
                        <thead>
                        <tr class="bg-white border border-gray-300">
                            <th class="py-3">Nama Peserta</th>
                            <th class="py-3">Rank</th>
                            <th class="py-3">Hasil</th>
                            <th class="py-3">Score</th>
                            <th class="py-3">Proses Ujian</th>
                            <th class="py-3">Aksi</th>
                        </tr>
                        </thead>
                        <tbody>
                        <tr v-for="participant in participants.data" :key="participant.uuid">
                            <td class="align-middle border px-4 py-2">{{ participant.name }}</td>
                            <td class="align-middle border px-4 py-2">{{ participant.rank }}</td>
                            <td class="align-middle border px-4 py-2">{{ participant.result }}</td>
                            <td class="align-middle border px-4 py-2">{{ participant.score }}</td>
                            <td class="align-middle border px-4 py-2">
                                <span v-if="participant.on_going"> Dalam proses</span>
                                <span v-else>Selesai</span>
                            </td>
                            <td class="align-middle border px-4 py-2">
                                <inertia-link v-if="!participant.on_going" class="underline" :href="'/creator/results/details/' + participant.uuid "> detail hasil </inertia-link>
                                <span v-else>-</span>
                            </td>
                        </tr>
                        <tr v-if="!participants.meta.total">
                            <td class="text-center py-3" colspan="6">Belum ada perserta ujian yang selesai.</td>
                        </tr>
                        </tbody>
                    </table>
                </div>
                <pagination :links="participants.meta.links" />
            </div>
        </div>
    </layout>
</template>

<script>
import Layout from "@/Layouts/AppLayout"
import Icon from "@/Shared/Icon"
import pickBy from 'lodash/pickBy'
import throttle from 'lodash/throttle'
import JetDialogModal from '@/Jetstream/DialogModal'
import JetDangerButton from '@/Jetstream/DangerButton'
import JetSecondaryButton from '@/Jetstream/SecondaryButton'
import Pagination from "@/Shared/Pagination";
import JetDropdown from "@/Jetstream/Dropdown";


export default {
    props: {
        participants: Object,
        exam: Object,
    },
    components: {
        Layout,
        Icon,
        JetDialogModal,
        JetDangerButton,
        JetSecondaryButton,
        JetDropdown,
        Pagination,
    },
    data() {
        return {
            confirmingDelete: false,
        }
    },
    methods: {
    }
};
</script>
