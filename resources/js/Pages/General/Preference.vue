<template>
    <layout :title="'Preferensi Pengguna - ' + $page.app.name" >
        <template v-slot:header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Preferensi Pengguna
            </h2>
        </template>

        <div>
            <div class="grid max-w-7xl mx-auto py-10 sm:px-6 lg:px-8 justify-items-center">

                <preference-form :preference="this.$page.preference" :pop="false" />

            </div>
        </div>

    </layout>
</template>

<script>
import Layout from "@/Layouts/AppLayout";
import PreferenceForm from '@/Shared/PreferenceForm'

export default {
    components: {
        Layout,
        PreferenceForm,
    },
    data() {
        return {
            sending: false,
            form: {
                code: null
            }
        };
    },
    methods: {
        details() {
            this.$inertia.post('/participant/exams/details', this.form);
        }
    }
};
</script>
