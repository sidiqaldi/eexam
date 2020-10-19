<template>
    <div>
        <jet-dialog-modal v-if="pop" :show="showPreference === true" @close="showPreference = false">
            <template #title>
                Pilih tipe user yang anda inginkan
            </template>

            <template #content>
                <div class="p-5 sm:px-5 bg-white border-b border-gray-200">
                    <div class="text-gray-500">
                        Silakan pilih tipe user sebagai preferensi anda. Apabila anda telah memilih, anda tetap masi dapat menggantinya sewaktu-waktu pada menu pengaturan anda.
                    </div>
                </div>

                <div class="bg-gray-100 bg-opacity-25 grid grid-cols-1 md:grid-cols-2">

                    <div class="p-6 hover:bg-gray-200 cursor-pointer" :class="{ 'bg-gray-200' : form.creator }" @click="form.creator = ! form.creator">
                        <div class="flex items-center">
                            <icon name="pencil-square" class="w-8 h-8 text-gray-400" />
                            <div class="ml-4 text-lg text-gray-600 leading-7 font-semibold">
                                <span>Penyelenggara Ujian</span>
                            </div>
                        </div>
                        <div class="ml-12">
                            <div class="mt-2 text-sm text-gray-500">
                                Penyelenggara adalah pengguna yang menyediakan/membuat ujian. Di mana pada aplikasi ini anda dapat membuat ujian kalian sendiri menggunakan beberapa pengaturan sederhana yang kami sediakan.
                            </div>
                            <div class="mt-3 flex items-center text-sm font-semibold text-indigo-700">
                                <input type="checkbox" v-model="form.creator" :value="true" />
                            </div>
                        </div>
                    </div>

                    <div class="p-6 border-t border-gray-200 md:border-t-0 md:border-l hover:bg-gray-200 cursor-pointer" :class="{ 'bg-gray-200' : form.participant }" @click="form.participant = ! form.participant">
                        <div class="flex items-center">
                            <icon name="people" class="w-8 h-8 text-gray-400" />
                            <div class="ml-4 text-lg text-gray-600 leading-7 font-semibold">
                                <span>Peserta Ujian</span>
                            </div>
                        </div>
                        <div class="ml-12">
                            <div class="mt-2 text-sm text-gray-500">
                                Sebagai peserta anda cukup memiliki suatu kode untuk memasuki ujian. Semua ujian yang ada saat ini bersifat umum dan terbuka selama anda memiliki kode untuk mengakses ujian tersebut.
                            </div>
                            <div class="mt-3 flex items-center text-sm font-semibold text-indigo-700">
                                <input type="checkbox" v-model="form.participant" :value="true" />
                            </div>
                        </div>
                    </div>

                </div>
            </template>

            <template #footer>
                <jet-button
                    @click.native="submit">
                    Lanjut
                </jet-button>
            </template>
        </jet-dialog-modal>
        <div v-else>


            <jet-form-section @submitted="submit">
                <template #title>
                    Preferensi
                </template>

                <template #description>
                    Silakan pilih tipe user sebagai preferensi anda. Apabila anda telah memilih, anda tetap masi dapat menggantinya sewaktu-waktu pada menu pengaturan anda.
                </template>

                <template #form>
                    <div class="col-span-6">
                        <div class="bg-gray-100 bg-opacity-25 grid grid-cols-1">

                            <div class="p-6 hover:bg-gray-200 cursor-pointer" @click="form.creator = ! form.creator">
                                <div class="flex items-center">
                                    <icon name="pencil-square" class="w-8 h-8 text-gray-400" />
                                    <div class="ml-4 text-lg text-gray-600 leading-7 font-semibold">
                                        <span>Penyelenggara Ujian</span>
                                    </div>
                                </div>
                                <div class="ml-12">
                                    <div class="mt-2 text-sm text-gray-500">
                                        Penyelenggara adalah pengguna yang menyediakan/membuat ujian. Di mana pada aplikasi ini anda dapat membuat ujian kalian sendiri menggunakan beberapa pengaturan sederhana yang kami sediakan.
                                    </div>
                                    <div class="mt-3 flex items-center text-sm font-semibold text-indigo-700">
                                        <input type="checkbox" v-model="form.creator" :value="true" />
                                    </div>
                                </div>
                            </div>

                            <div class="p-6 border-t border-gray-200 md:border-t-0 hover:bg-gray-200 cursor-pointer" @click="form.participant = ! form.participant">
                                <div class="flex items-center">
                                    <icon name="people" class="w-8 h-8 text-gray-400" />
                                    <div class="ml-4 text-lg text-gray-600 leading-7 font-semibold">
                                        <span>Peserta Ujian</span>
                                    </div>
                                </div>
                                <div class="ml-12">
                                    <div class="mt-2 text-sm text-gray-500">
                                        Sebagai peserta anda cukup memiliki suatu kode untuk memasuki ujian. Semua ujian yang ada saat ini bersifat umum dan terbuka selama anda memiliki kode untuk mengakses ujian tersebut.
                                    </div>
                                    <div class="mt-3 flex items-center text-sm font-semibold text-indigo-700">
                                        <input type="checkbox" v-model="form.participant" :value="true" />
                                    </div>
                                </div>
                            </div>

                        </div>
                    </div>
                </template>

                <template #actions>
                    <jet-button  @click.native="submit" :class="{ 'opacity-25': form.processing }" :disabled="form.processing">
                        Simpan
                    </jet-button>
                </template>
            </jet-form-section>




        </div>
    </div>
</template>


<script>
    import JetButton from '@/Jetstream/Button'
    import JetDialogModal from '@/Jetstream/DialogModal'
    import JetFormSection from '@/Jetstream/FormSection'
    import Icon from "@/Shared/Icon"

    export default {
        components: {
            JetButton,
            JetDialogModal,
            JetFormSection,
            Icon,
        },

        props: {
            pop: {
                type: Boolean,
                default: false
            },
            preference: Object,
        },

        data() {
            return {
                showPreference: true,

                form: this.$inertia.form({
                    creator: this.preference ? this.preference.creator : false,
                    participant: this.preference ? this.preference.participant : false,
                }, {
                    bag: 'updatePreference',
                    resetOnSuccess: false,
                }),
            }
        },


        methods: {
            submit() {
                this.form.post('/preference')
            },
        },

        computed: {
            path() {
                return window.location.pathname
            }
        }
    }
</script>
