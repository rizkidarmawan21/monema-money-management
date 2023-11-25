<script>
export default {
    layout: AppLayout
}
</script>
<script setup>
import axios from "axios";
import { notify } from "notiwind";
import { string } from "vue-types";
import { Head } from "@inertiajs/inertia-vue3";
import { ref, onMounted, reactive } from "vue";
import AppLayout from '@/layouts/apps.vue';
import debounce from "@/composables/debounce"
import VDropdownEditMenu from '@/components/VDropdownEditMenu/index.vue';
import VDataTable from '@/components/VDataTable/index.vue';
import VPagination from '@/components/VPagination/index.vue'
import VBreadcrumb from '@/components/VBreadcrumb/index.vue';
import VLoading from '@/components/VLoading/index.vue';
import VEmpty from '@/components/src/icons/VEmpty.vue';
import VButton from '@/components/VButton/index.vue';
import VDetail from '@/components/src/icons/VDetail.vue';
import VFilter from './Filter.vue';
import VModalForm from './ModalForm.vue';
import { Inertia } from "@inertiajs/inertia";

const query = ref([])
const searchFilter = ref("");
const breadcrumb = [
    {
        name: "Dashboard",
        active: false,
        to: route('dashboard.index')
    },
    {
        name: "Histori Transaksi",
        active: true,
    },
]
const pagination = ref({
    count: '',
    current_page: 1,
    per_page: '',
    total: 0,
    total_pages: 1
})
const itemSelected = ref({})
const openModalForm = ref(false)
const heads = ["No", "Transaksi", "Akun Saldo", "Nominal", "Tanggal", "Dibuat Oleh", ""]
const isLoading = ref(true)

const props = defineProps({
    title: string()
})

const getData = debounce(async (page) => {
    axios.get(route('transaksi.histori.get-data'), {
        params: {
            page: page,
            search: searchFilter.value
        }
    }).then((res) => {
        query.value = res.data.data
        pagination.value = res.data.meta.pagination
    }).catch((res) => {
        notify({
            type: "error",
            group: "top",
            text: res.response.data.message
        }, 2500)
    }).finally(() => isLoading.value = false)
}, 500);

const nextPaginate = () => {
    pagination.value.current_page += 1
    isLoading.value = true
    getData(pagination.value.current_page)
}

const previousPaginate = () => {
    pagination.value.current_page -= 1
    isLoading.value = true
    getData(pagination.value.current_page)
}

const searchHandle = (search) => {
    searchFilter.value = search
    isLoading.value = true
    getData(1)
};


const handleAddModalForm = (data) => {
    itemSelected.value = data
    openModalForm.value = true
}


const closeModalForm = () => {
    itemSelected.value = ref({})
    openModalForm.value = false
}


onMounted(() => {
    getData(1);
});
</script>

<template>
    <Head :title="props.title" />
    <VBreadcrumb :routes="breadcrumb" />
    <div class="flex items-center justify-between mb-4 sm:mb-6">
        <h1 class="text-2xl font-bold md:text-3xl text-slate-800">Data Histori Transaksi</h1>
    </div>
    <div class="bg-white border rounded-sm shadow-lg border-slate-200" :class="isLoading && 'min-h-[40vh] sm:min-h-[50vh]'">
        <header class="items-center justify-between block px-4 py-6 sm:flex">
            <h2 class="font-semibold text-slate-800">
                Semua Transaksi <span class="text-slate-400 !font-medium ml">({{ pagination.total }})</span>
            </h2>
            <div class="sm:flex justify-end mt-3 sm:space-x-2 sm:mt-0 sm:justify-between">
                <!-- Filter -->
                <VFilter @search="searchHandle" />
                <!-- <VButton label="Tambah" type="primary" @click="handleAddModalForm" class="sm:mt-auto mt-2" /> -->
            </div>
        </header>

        <VDataTable :heads="heads" :isLoading="isLoading">
            <tr v-if="isLoading">
                <td class="h-[100%] overflow-hidden my-2" :colspan="heads.length">
                    <VLoading />
                </td>
            </tr>
            <tr v-else-if="query.length === 0 && !isLoading">
                <td class="my-2 overflow-hidden" :colspan="heads.length">
                    <div class="flex flex-col items-center w-full my-32">
                        <VEmpty />
                        <div class="text-xl font-medium mt-9 text-slate-500 md:text-xl">Data tidak ditemukan.</div>
                    </div>
                </td>
            </tr>
            <tr v-for="(data, index) in query" :key="index" v-else>
                <td class="h-16 px-4 whitespace-nowrap"> {{ index + 1 }} </td>
                <td class="px-4 whitespace-nowrap h-16">
                    {{ data.nama_transaksi }} </td>
                <td class="h-16 px-4"> {{ data.akun_saldo }} </td>
                <td class="h-16 px-4" :class="data.nama_transaksi == 'Pengeluaran' ? 'text-red-500' : 'text-green-500'">
                    {{ data.jumlah < 0 ? '-' : '' }} Rp. {{ Math.abs(data.jumlah).toLocaleString('id-ID') ?? '-' }} </td>

                <td class="h-16 px-4"> {{ data.tanggal ?? '-' }} </td>
                <td class="h-16 px-4"> {{ data.creator ?? '-' }} </td>
                <td class="h-16 px-4 text-right whitespace-nowrap">
                    <VDropdownEditMenu class="relative inline-flex r-0" :align="'right'"
                        :last="index === query.length - 1 ? true : false">
                        <li class="cursor-pointer hover:bg-slate-100" @click="handleAddModalForm(data)">
                            <div class="flex items-center p-3 space-x-2">
                                <span>
                                    <VDetail color="primary" />
                                </span>
                                <span>Detail</span>
                            </div>
                        </li>
                    </VDropdownEditMenu>
                </td>
            </tr>
        </VDataTable>
        <div class="px-4 py-6">
            <VPagination :pagination="pagination" @next="nextPaginate" @previous="previousPaginate" />
        </div>
    </div>
    <VModalForm :open-dialog="openModalForm" :data="itemSelected" @close="closeModalForm" />
</template>