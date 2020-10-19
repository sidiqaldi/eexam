<template>
    <div>
        <div class="p-6 sm:px-20 bg-white border-b border-gray-200">
            <div>
                Eexam
            </div>

            <div class="mt-8 text-2xl">
                Selamat datang di aplikasi Ujian Online!
            </div>

            <div class="mt-6 text-gray-500">
                Ujian online merupakan platform ujian gratis, dan akan selalui diperbarui sesuai minat pengguna. Di sini anda dapat menjadi
                penyelenggara sebuah ujian atau anda dapat mengikuti ujian sebagai peserta. Kami akan sangat senang terhadap segala jenis masukan
                yang anda berikan. Terima kasih.
            </div>
        </div>

        <div class="bg-gray-200 bg-opacity-25 grid grid-cols-1 md:grid-cols-2">
            <div v-if="this.$page.preference != null && this.$page.preference.creator" class="p-6">
                <div class="flex items-center">
                    <icon name="pencil-square" class="w-8 h-8 text-gray-400" />
                    <div class="ml-4 text-lg text-gray-600 leading-7 font-semibold">
                        <inertia-link :href="'/creator/menu'">Penyelenggara Ujian</inertia-link>
                    </div>
                </div>

                <div class="ml-12">
                    <div class="mt-2 text-sm text-gray-500">
                        Penyelenggara adalah pengguna yang menyediakan/membuat ujian. Di mana pada aplikasi ini anda dapat membuat ujian kalian sendiri menggunakan beberapa pengaturan sederhana yang kami sediakan.
                    </div>

                    <link-arrow href="/creator/menu">Lanjut sebagai penyelenggara</link-arrow>
                </div>
            </div>

            <div v-if="this.$page.preference != null && this.$page.preference.participant" class="p-6 border-t border-gray-200 md:border-t-0 md:border-l">
                <div class="flex items-center">
                    <icon name="people" class="w-8 h-8 text-gray-400" />
                    <div class="ml-4 text-lg text-gray-600 leading-7 font-semibold">
                        <inertia-link :href="'/participant/menu'">Peserta Ujian</inertia-link>
                    </div>
                </div>

                <div class="ml-12">
                    <div class="mt-2 text-sm text-gray-500">
                        Sebagai peserta anda cukup memiliki suatu kode untuk memasuki ujian. Semua ujian yang ada saat ini bersifat umum dan terbuka selama anda memiliki kode untuk mengakses ujian tersebut.
                    </div>

                    <link-arrow href="/participant/menu">Lanjut sebagai peserta</link-arrow>
                </div>
            </div>
        </div>
    </div>
</template>

<script>

import Icon from "./Icon";
import LinkArrow from './LinkArrow'

export default {
    components: {
        Icon,
        LinkArrow
    },
}
</script>
