<script setup>
import axios from "axios";
import { ref } from "vue";
import { bool, object } from "vue-types";
import { notify } from "notiwind";
import VDialog from '@/components/VDialog/index.vue';
import VButton from '@/components/VButton/index.vue';
import VSelect from '@/components/VSelect/index.vue';
import VInput from '@/components/VInput/index.vue';
import dayjs from "dayjs";

const props = defineProps({
    openDialog: bool(),
    updateAction: bool().def(false),
    data: object().def({}),
    additional: object().def({})
})

const emit = defineEmits(['close', 'successSubmit'])

const isLoading = ref(false);
const formError = ref({})
const form = ref({})

const openForm = () => {
    if (props.updateAction) {
        form.value = Object.assign(form.value, props.data)
    } else {
        form.value = ref({})
    }
}

const handleDate = () => {
    if (form.value.date) {
        formError.value.date = "";
        form.value.date = dayjs(form.value.date).format("YYYY-MM-DD");
    }
}

const closeForm = () => {
    form.value = ref({})
    formError.value = ref({})
}

const submit = async () => {
    props.updateAction ? update() : create()
}

const update = async () => {
    isLoading.value = true
    axios.put(route('transaksi.keluar.update', { 'id': props.data.id }), form.value)
        .then((res) => {
            emit('close')
            emit('successSubmit')
            form.value = ref({})
            notify({
                type: "success",
                group: "top",
                text: res.data.meta.message
            }, 2500)
        }).catch((res) => {
            // Handle validation errors
            const result = res.response.data
            const metaError = res.response.data.meta?.error
            if (result.hasOwnProperty('errors')) {
                formError.value = ref({});
                Object.keys(result.errors).map((key) => {
                    formError.value[key] = result.errors[key].toString();
                });
            }

            if (metaError) {
                notify({
                    type: "error",
                    group: "top",
                    text: metaError
                }, 2500)
            } else {
                notify({
                    type: "error",
                    group: "top",
                    text: result.message
                }, 2500)
            }
        }).finally(() => isLoading.value = false)
}

const create = async () => {
    isLoading.value = true
    axios.post(route('transaksi.keluar.create'), form.value)
        .then((res) => {
            emit('close')
            emit('successSubmit')
            form.value = ref({})
            notify({
                type: "success",
                group: "top",
                text: res.data.meta.message
            }, 2500)
        }).catch((res) => {
            // Handle validation errors
            const result = res.response.data
            const metaError = res.response.data.meta?.error
            if (result.hasOwnProperty('errors')) {
                formError.value = ref({});
                Object.keys(result.errors).map((key) => {
                    formError.value[key] = result.errors[key].toString();
                });
            }

            if (metaError) {
                notify({
                    type: "error",
                    group: "top",
                    text: metaError
                }, 2500)
            } else {
                notify({
                    type: "error",
                    group: "top",
                    text: result.message
                }, 2500)
            }
        }).finally(() => isLoading.value = false)
}
</script>

<template>
    <VDialog :showModal="openDialog" :title="updateAction ? 'Ubah Pengeluaran' : 'Tambah Pengeluaran'" @opened="openForm"
        @closed="closeForm" size="xl">
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
                    <VInput placeholder="Masukkan Nama Pengeluaran" label="Nama Pengeluaran" :required="true"
                        v-model="form.nama_pengeluaran" :errorMessage="formError.nama_pengeluaran"
                        @update:modelValue="formError.nama_pengeluaran = ''" />
                </div>
                <div class="col-span-2 sm:col-span-1">
                    <VSelect placeholder="Pilih Akun Saldo" :required="true" v-model="form.akun_saldo_id"
                        :options="additional.akun_saldo" label="Akun Saldo" :errorMessage="formError.akun_saldo_id"
                        @update:modelValue="formError.akun_saldo_id = ''" />
                </div>
                <div class="col-span-2 sm:col-span-1">
                    <VInput placeholder="Masukkan Nominal" label="Nominal" :required="true" v-model="form.nominal"
                        :errorMessage="formError.nominal" @update:modelValue="formError.nominal = ''" type="number" />
                </div>
                <div class="col-span-2 sm:col-span-1">
                    <VSelect placeholder="Pilih Karyawan" v-model="form.karyawan_id" :options="additional.karyawans"
                        label="Karyawan" :errorMessage="formError.karyawan_id"
                        @update:modelValue="formError.karyawan_id = ''" />
                </div>
                <div class="col-span-2 sm:col-span-1">
                    <label class="block mb-1 text-sm font-medium text-slate-600">
                        Tanggal
                    </label>
                    <Datepicker v-model="form.tanggal" @update:modelValue="handleDate" :enableTimePicker="false"
                        position="left" :clearable="false" format="dd MMMM yyyy" previewFormat="dd MMMM yyyy"
                        placeholder="Tanggal" :class="{ 'date_error': formError.tanggal }" />
                    <div class="text-xs" :class="[{ 'text-rose-500': formError.tanggal }]" v-if="formError.tanggal">
                        {{ formError.tanggal }}
                    </div>
                </div>
                <div class="col-span-2 sm:col-span-1">
                    <VInput placeholder="Masukkan Keterangan" label="Keterangan" v-model="form.keterangan"
                        :errorMessage="formError.keterangan" @update:modelValue="formError.keterangan = ''" />
                </div>
            </div>
        </template>
        <template v-slot:footer>
            <div class="flex flex-wrap justify-end space-x-2">
                <VButton label="Cancel" type="default" @click="$emit('close')" />
                <VButton :is-loading="isLoading" :label="updateAction ? 'Ubah' : 'Buat'" type="primary" @click="submit" />
            </div>
        </template>
    </VDialog>
</template>