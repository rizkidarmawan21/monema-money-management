<script setup>
import { bool, object } from "vue-types";
import VDialog from '@/components/VDialog/index.vue';
import VButton from '@/components/VButton/index.vue';

const props = defineProps({
    openDialog: bool(),
    data: object().def({}),
})

const emit = defineEmits(['close'])

</script>

<template>
    <VDialog :showModal="openDialog" :title="`Histori Transaksi ` + data.nama_transaksi" size="xl">
        <template v-slot:close>
            <button class="text-slate-400 hover:text-slate-500" @click="$emit('close')">
                <div class="sr-only">Close</div>
                <svg class="w-4 h-4 fill-current">
                    <path
                        d="M7.95 6.536l4.242-4.243a1 1 0 111.415 1.414L9.364 7.95l4.243 4.242a1 1 0 11-1.415 1.415L7.95 9.364l-4.243 4.243a1 1 0 01-1.414-1.415L6.536 7.95 2.293 3.707a1 1 0 011.414-1.414L7.95 6.536z" />
                </svg>
            </button>
        </template>
        <template v-slot:content>
            <div class="grid grid-cols-2 gap-3">
                <div class="col-span-2 sm:col-span-1">
                    <p class="font-bold">
                        Nama Transaksi
                    </p>
                    <p>
                        {{ data.nama_detail_transaksi }}
                    </p>
                </div>
                <div class="col-span-2 sm:col-span-1">
                    <p class="font-bold">
                       Jenis
                    </p>
                    <p>
                        {{ data.nama_transaksi }} - {{ data.jenis_transaksi }}
                    </p>
                </div>
                <div class="col-span-2 sm:col-span-1">
                    <p class="font-bold">
                        Akun Saldo
                    </p>
                    <p>
                        {{ data.akun_saldo }}
                    </p>
                </div>
                <div class="col-span-2 sm:col-span-1">
                    <p class="font-bold">
                        Nominal
                    </p>
                    <p>
                        Rp. {{ data.jumlah }}
                    </p>
                </div>
                <div class="col-span-2 sm:col-span-1">
                    <p class="font-bold">
                        Keterangan
                    </p>
                    <p>
                        {{ data.keterangan ?? '-' }}
                    </p>
                </div>
                <div class="col-span-2 sm:col-span-1" v-if="data.nama_transaksi == 'Pengeluaran'">
                    <p class="font-bold">
                        Karyawan
                    </p>
                    <p>
                        {{ data.karyawan ?? '-' }}
                    </p>
                </div>
                <div class="col-span-2 sm:col-span-1">
                    <p class="font-bold">
                        Tanggal
                    </p>
                    <p>
                        {{ data.tanggal ?? '-' }}
                    </p>
                </div>
                <div class="col-span-2 sm:col-span-1">
                    <p class="font-bold">
                        Dibuat Oleh
                    </p>
                    <p>
                        {{ data.creator ?? '-' }}
                    </p>
                </div>
            </div>
        </template>
        <template v-slot:footer>
            <div class="flex flex-wrap justify-end space-x-2">
                <VButton label="Kembali" type="default" @click="$emit('close')" />
            </div>
        </template>
    </VDialog>
</template>