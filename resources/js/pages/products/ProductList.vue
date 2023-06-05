<template>
    <view-layout>

        <template #header>
            <div class="row flex-center">
                <div class="header text-grey-8 col-11">{{ $t('repositories.title') }}</div>
            </div>
        </template>

        <template #section>
            <div class="row flex-center card-container">
                <q-card class="my-card col-11">
                    <q-card-section>

                        <div class="row">
                            <div class="text-bold text-grey-8 col-auto" style="margin-top: 10px">
                                {{ $t('repositories.selected_repository') }}
                            </div>
                            <div class="col-3" style="margin-left: 30px">
                                <repository-selector :repository="repository"/>
                            </div>
                        </div>

                        <div class="row" style="padding-top: 40px">
                            <div class="text-bold text-grey-8 col-auto" style="margin-top: 10px">
                                Filtres
                            </div>
                            <div class="col-3" style="margin-left: 30px">
                                <q-input outlined dense debounce="300" v-model="search" placeholder="Recherche">
                                    <template v-slot:append>
                                        <q-icon name="search" />
                                    </template>
                                </q-input>
                            </div>
                            <div class="col-2" style="margin-left: 30px">
                                <q-input outlined dense v-model="filters.prices"
                                          @update:model-value="reloadDatatable"
                                          option-label="text"
                                          option-value="value"
                                          clearable
                                          label="Prix">
                                </q-input>
                            </div>
                        </div>

                    </q-card-section>

                    <q-card-section>

                        <q-table
                            v-model:pagination="pagination"
                            title="Produits"
                            :columns="headers"
                            :rows="items"
                            :loading="loading"
                            :filter="search"
                            row-key="id"
                            selection="multiple"
                            v-model:selected="selected"
                            @request="onRequest">

                        </q-table>

                    </q-card-section>
                </q-card>
            </div>
        </template>

    </view-layout>
</template>

<script setup>

import ViewLayout from "@/components/ViewLayout.vue";
import RepositorySelector from "@/components/repositories/RepositorySelector.vue";
import {computed, ref} from "vue";
import {router} from "@inertiajs/vue3";

let repository = {value: 'products', text :'Produits'}

let props = defineProps({
    datatable: {
        type: Object,
        required: true,
    }
})

let items = computed(() => {
    return props.datatable?.items
})

let total = computed(() => {
    return props.datatable?.total;
})

let headers = ref([
    {
        name: 'name',
        align: 'left',
        label: 'Nom',
        field: 'name',
        sortable: true,
        style: 'min-width: 200px; max-width:200px'
    },
    {
        name: 'price',
        align: 'left',
        label: 'Prix',
        field: 'price',
        sortable: true,
        style: 'min-width: 200px; max-width:200px'
    },
    {
        name: 'description',
        align: 'left',
        label: 'Description',
        field: 'description',
        sortable: true,
        style: 'min-width: 200px; max-width:200px'
    },
    {
        name: 'actions',
        align: 'right',
        label: 'Actions',
        sortable: false,
        style: 'min-width: 300px; max-width:150px'
    },
]);

const pagination = ref({
    sortBy: 'name',
    descending: false,
    page: 1,
    rowsPerPage: 10,
    rowsNumber: null
})

const filters = ref({})

let search = ref('')

const loading = ref(false)

let selected = ref([])

function syncPagination(props){
    pagination.value = props.pagination;
}

function onRequest (props) {
    syncPagination(props)
    reloadDatatable()
}

function startLoading(){
    loading.value = true
}

function stopLoading(){
    loading.value = false
}

function reloadDatatable(){
    startLoading()
    let data = {...pagination.value, search : search.value, filters : filters.value}
    router.get('/repositories/products', data, {
        preserveState: true,
        preserveScroll: true,
        replace: true,
        only: ['datatable'],
        onFinish: () => {
            stopLoading()
            pagination.value.rowsNumber = total;
        }
    })
}

</script>

<style scoped lang="scss">

.header{
    font-size: 30px;
    padding: 20px 0 0 0;
}

.card-container{
    padding: 20px 0 0 0;
}

.repository-choice{

}

</style>
