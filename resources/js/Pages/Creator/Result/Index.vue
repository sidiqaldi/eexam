<template>
    <layout
        :title="'Halaman Penyelenggara - ' + $page.app.name"
        active="creator.exams.index"
        page="Daftar Ujian"
    >
        <template v-slot:header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Daftar Ujian - Lihat hasil Ujian
            </h2>
        </template>

        <div>
            <div class="max-w-7xl mx-auto py-10 sm:px-6 lg:px-8">
                <div class="grid sm:grid-cols-2 gap-4 justify-items-start">
                    <inertia-link
                        class="px-4 py-2 bg-gray-900 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 active:bg-gray-900 focus:outline-none focus:border-gray-900 focus:shadow-outline-gray transition ease-in-out duration-150"
                        href="/creator/exams/create"
                    >Buat ujian
                    </inertia-link>
                    <div class="flex items-center border-b border-teal-500 sm:w-64 justify-self-end">
                        <input v-model="form.search"
                               class="appearance-none bg-transparent border-none w-full text-gray-700 mr-3 py-1 px-2 leading-tight focus:outline-none"
                               maxlength="50" type="text"
                               placeholder="pencarian..."
                               @input="$emit('input', $event.target.value)"/>
                        <icon name="search"/>
                    </div>
                </div>
                <div class="grid gap-4 justify-items-stretch my-3">
                    <table class="table-auto border-collapse border border-gray-300">
                        <thead>
                        <tr class="bg-white border border-gray-300">
                            <th rowspan="2" class="border py-3">Nama</th>
                            <th rowspan="2" class="border py-3">Deskripsi</th>
                            <th colspan="2" class="border py-3">Peserta</th>
                            <th rowspan="2" class="border py-3">Aksi</th>
                        </tr>
                        <tr class="bg-white border border-gray-300">
                            <th class="border p-3">Total</th>
                            <th class="border p-3">Selesai</th>
                        </tr>
                        </thead>
                        <tbody>
                        <tr v-for="exam in exams.data" :key="exam.uuid">
                            <td class="align-middle border px-4 py-2">{{ exam.name }}</td>
                            <td class="align-middle border px-4 py-2">{{ exam.description }}</td>
                            <td class="align-middle border px-4 py-2">{{ exam.participant }}</td>
                            <td class="align-middle border px-4 py-2">{{ exam.finish_participant }}</td>
                            <td class="align-middle border px-4 py-2">
                                <inertia-link class="underline" :href="'/creator/results/' + exam.uuid "> lihat hasil </inertia-link>
                            </td>
                        </tr>
                        <tr v-if="!exams.meta.total">
                            <td class="text-center py-3" colspan="5">Tidak ada ujian</td>
                        </tr>
                        </tbody>
                    </table>
                </div>
                <pagination :links="exams.meta.links" />
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
        exams: Object,
        filters: Object
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
            form: {
                search: this.filters.search
            }
        }
    },
    watch: {
        form: {
            handler: throttle(function () {
                let query = pickBy(this.form)
                const params = new URLSearchParams(Object.keys(query).length ? query : {remember: 'forget'});
                this.$inertia.replace('/creator/exams?' + params.toString())
            }, 150),
            deep: true,
        }
    },
};
</script>
