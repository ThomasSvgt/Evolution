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
                            :separator="separator"
                            wrap-cells="wrap-cells"
                            @request="onRequest">

                            <template v-slot:top-right>
                                <q-btn label="Ajouter un produit" color="green-7">
                                    &nbsp;
                                    <q-icon name="add_circle" />
                                </q-btn>
                            </template>

                            <template v-slot:body-cell-price="props">
                                <q-td :props="props">
                                    {{ props.row.price + " €" }}
                                </q-td>
                            </template>

                            <template v-slot:body-cell-actions="props">
                                <q-td
                                    :props="props"
                                    auto-width="auto-width">
                                    <q-btn
                                        color="light-blue-9"
                                        padding="sm"
                                        size="sm"
                                        class="cursor-pointer"
                                        icon="fa-solid fa-pen"
                                        @click="openEditDialog(props.row)"
                                    >
                                        <q-tooltip class="bg-grey-8" style="font-size: 13px">
                                            Éditer
                                        </q-tooltip>
                                    </q-btn>
                                    &nbsp;
                                    <q-btn
                                        color="red-8"
                                        padding="sm"
                                        size="sm"
                                        class="cursor-pointer"
                                        icon="fa-solid fa-trash-can"
                                        @click="openSuppressionDialog(props.row)"
                                    >
                                        <q-tooltip class="bg-grey-8" style="font-size: 13px">
                                            Supprimer
                                        </q-tooltip>
                                    </q-btn>
                                </q-td>
                            </template>

                        </q-table>

                    </q-card-section>
                </q-card>
            </div>

            <q-dialog v-model="editDialog"
                      persistent
                      transition-show="scale"
                      transition-hide="scale">

                <q-card class="bg-radius report-style" style="width: 800px; height: 400px">

                    <q-card-section class="bg-primary text-white">
                        <div class="text-h6">Édition du produit</div>
                    </q-card-section>

                    <q-card-section class="bg-white">
                        <div class="row flex-center q-col-gutter-md">
                            <q-input  v-model="form.name"
                                      :error-message="errors['name']"
                                      :error="hasErrors('name')"
                                      label="Nom"
                                      class="col-6"/>

                            <q-input  v-model="form.price"
                                      :error-message="errors['price']"
                                      :error="hasErrors('price')"
                                      type="number"
                                      prefix="€"
                                      label="Prix"
                                      class="col-6" />

                            <q-input  v-model="form.description"
                                      :error-message="errors['description']"
                                      :error="hasErrors('description')"
                                      label="Description"
                                      type="textarea"
                                      class="col-12"/>
                        </div>
                    </q-card-section>

                    <q-card-section class="bg-white absolute" >
                        <q-btn label="annuler" color="grey-8" @click="closeEditDialog"/>
                        &nbsp;
                        <q-btn label="valider" color="green-8" @click="editProduct"/>
                    </q-card-section>

                </q-card>

            </q-dialog>

            <q-dialog v-model="suppressionDialog"
                      persistent
                      transition-show="scale"
                      transition-hide="scale">

                <q-card class="bg-radius report-style" style="width: 800px; height: 200px">

                    <q-card-section class="bg-primary text-white">
                        <div class="text-h6">Suppression du produit</div>
                    </q-card-section>


                    <q-card-section class="bg-white">
                        <div>
                            Veuillez confirmer la suppression du produit
                            <span class="text-bold"> {{ selectedProductName }}</span>
                        </div>
                    </q-card-section>

                    <q-card-section class="bg-white absolute" style="bottom: 0; right: 10px">
                        <q-btn label="annuler" color="grey-8" @click="closeSuppressionDialog"/>
                        &nbsp;
                        <q-btn label="valider" color="green-8" @click="deleteProduct"/>
                    </q-card-section>

                </q-card>

            </q-dialog>

        </template>

    </view-layout>
</template>

<script setup>

import ViewLayout from "@/components/ViewLayout.vue";
import RepositorySelector from "@/components/repositories/RepositorySelector.vue";
import {computed, ref} from "vue";
import {router} from "@inertiajs/vue3";
import {useQuasar} from "quasar"
const $q = useQuasar()

let repository = {value: 'products', text :'Produits'}

let props = defineProps({
    datatable: {
        type: Object,
        required: true,
    }
})

let suppressionDialog = ref(false);

let editDialog = ref(false);

let items = computed(() => {
    return props.datatable?.items
})

let total = computed(() => {
    return props.datatable?.total;
})

let separator = ref('cell');

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
        align: 'left',
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

let selectedProduct = ref(null);

let errors = ref([]);

let selectedProductName = computed(() => {
    return selectedProduct.value?.name ?? '-'
})

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

function hasErrors(field){
    return errors.value[field]?.length > 0
}

function clearErrors(){
    errors.value = [];
}

function openEditDialog(props){
    selectedProduct.value = props
    editDialog.value = true
    form.value = {...props}
}

function closeEditDialog(){
    selectedProduct.value = null
    editDialog.value = false
    clearForm()
}

function openSuppressionDialog(props){
    selectedProduct.value = props
    suppressionDialog.value = true
}

function closeSuppressionDialog(){
    selectedProduct.value = null
    suppressionDialog.value = false
}

function editProduct(){
    router.post(`products/${selectedProduct.value.id}/edit`, form.value, {
        onSuccess: () => {
            $q.notify({
                type: 'positive',
                message: "Le produit a été modifié",
                position: "top",
                textColor: 'white',
                icon: 'info'
            })
            closeEditDialog()
            clearForm()
        },
        onError: (server_errors) => {
            errors.value = server_errors
            $q.notify({
                type: 'negative',
                message: "Erreur lors de la modification du produit",
                position: "top",
                textColor: 'white',
                icon: 'info'
            })
        },
    })
}

function deleteProduct(){
    router.post(`products/${selectedProduct.value.id}/delete`, {}, {
        onSuccess: () => {
            $q.notify({
                type: 'positive',
                message: "Le produit a été supprimé",
                position: "top",
                textColor: 'white',
                icon: 'info'
            })
        },
        onError: () => {
            $q.notify({
                type: 'negative',
                message: "Erreur lors de la suppression du produit",
                position: "top",
                textColor: 'white',
                icon: 'info'
            })
        },
        onFinish: () => {
            closeSuppressionDialog()
            clearForm()
        }
    })
}

let form = ref({})

function clearForm(){
    form.value = {}
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
