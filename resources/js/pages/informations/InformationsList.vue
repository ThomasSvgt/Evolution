<template>
    <view-layout>

        <template #header>
            <div class="row flex-center">
                <div class="header text-grey-8 col-11">{{ $t('repositories.informations.title') }}</div>
            </div>
        </template>

        <template #section>
            <div class="row flex-center card-container">
                <q-card class="my-card col-11">


                    <q-card-section>
                        <q-table
                            v-model:pagination="pagination"
                            title="Informations"
                            :columns="headers"
                            :rows="items"
                            :loading="loading"
                            :filter="search"
                            row-key="id"
                            selection="multiple"
                            v-model:selected="selected"
                            @request="onRequest"
                        >
                            <template v-slot:top-right>
                                <q-btn label="Ajouter des informations à un utilisateur" color="green-7" @click="openCreationDialog">
                                    &nbsp;
                                    <q-icon name="add_circle" />
                                </q-btn>
                            </template>

                            <template v-slot:body-cell-actions="props">
                                <q-td :props="props">
                                    <q-btn
                                        color="light-blue-9"
                                        padding="sm"
                                        size="sm"
                                        class="cursor-pointer"
                                        icon="fa-solid fa-pen"
                                        @click="openEditionDialog(props.row)"
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

            <q-dialog v-model="creationDialog"
                      persistent
                      transition-show="scale"
                      transition-hide="scale">

                <q-card class="bg-radius report-style">

                    <q-card-section class="bg-primary text-white">
                        <div class="text-h6">Création des informations d'un utilisateur</div>
                    </q-card-section>
                    <q-card-section class="bg-white">
                        <div class="row flex-center q-col-gutter-md">

                        <q-select outlined v-model="form.user_id" :options="this.props.userList" option-label="name_email" label="Sélection de l'utilisateur" class="col-12"/>
                            <q-input  v-model="form.adress"
                                      :error-message="errors['adress']"
                                      :error="hasErrors('adress')"
                                      label="adress"
                                      class="col-6"/>
                            <q-input  v-model="form.city"
                                      :error-message="errors['city']"
                                      :error="hasErrors('city')"
                                      label="city"
                                      class="col-6"/>
                            <q-input  v-model="form.zip_code"
                                      :error-message="errors['zip_code']"
                                      :error="hasErrors('zip_code')"
                                      label="zip_code"
                                      class="col-6"/>
                            <q-input  v-model="form.phone"
                                      :error-message="errors['phone']"
                                      :error="hasErrors('phone')"
                                      label="phone"
                                      class="col-6"/>
                        </div>
                    </q-card-section>
                    <br><br>
                    <q-card-section class="bg-white absolute" style="bottom: 0; right: 10px">
                        <q-btn label="annuler" color="grey-8" @click="closeCreationDialog"/>
                        &nbsp;
                        <q-btn label="valider" color="green-8" @click="createInformations"/>
                    </q-card-section>

                </q-card>

            </q-dialog>

            <q-dialog v-model="editionDialog"
                      persistent
                      transition-show="scale"
                      transition-hide="scale">

                <q-card class="bg-radius report-style">

                    <q-card-section class="bg-primary text-white">
                        <div class="text-h6">Édition de l'utilisateur</div>
                    </q-card-section>



                    <q-card-section class="bg-white">
                        <div class="row flex-center q-col-gutter-md">
                            <q-select outlined v-model="form.user_id" :options="this.props.userList" option-label="name_email" label="Sélection de l'utilisateur" class="col-12"/>

                            <q-input  v-model="form.adress"
                                      :error-message="errors['adress']"
                                      :error="hasErrors('adress')"
                                      label="adress"
                                      class="col-6"/>

                            <q-input  v-model="form.city"
                                      :error-message="errors['city']"
                                      :error="hasErrors('city')"
                                      label="city"
                                      class="col-6" />

                            <q-input  v-model="form.zip_code"
                                      :error-message="errors['zip_code']"
                                      :error="hasErrors('zip_code')"
                                      label="zip_code"
                                      class="col-6"/>
                            <q-input  v-model="form.phone"
                                      :error-message="errors['phone']"
                                      :error="hasErrors('phone')"
                                      label="phone"
                                      class="col-6"/>
                        </div>
                    </q-card-section>
                    <br><br>  <br><br>
                    <q-card-section class="bg-white absolute" style="bottom: 0; right: 10px">

                        <q-btn label="annuler" color="grey-8" @click="closeEditionDialog"/>
                        &nbsp;
                        <q-btn label="valider" color="green-8" @click="editUser"/>
                    </q-card-section>

                </q-card>

            </q-dialog>

            <q-dialog v-model="suppressionDialog"
                      persistent
                      transition-show="scale"
                      transition-hide="scale">

                <q-card class="bg-radius report-style" style="width: 800px; height: 200px">

                    <q-card-section class="bg-primary text-white">
                        <div class="text-h6">Suppression des informations</div>
                    </q-card-section>


                    <q-card-section class="bg-white">
                        <div>
                            Veuillez confirmer la suppression des informations de
                            <span class="text-bold"> {{ selectedUserName }}</span>
                        </div>
                    </q-card-section>

                    <q-card-section class="bg-white absolute" style="bottom: 0; right: 10px">
                        <q-btn label="annuler" color="grey-8" @click="closeSuppressionDialog"/>
                        &nbsp;
                        <q-btn label="valider" color="green-8" @click="deleteUser"/>
                    </q-card-section>

                </q-card>

            </q-dialog>

        </template>

    </view-layout>
</template>

<script setup>

import ViewLayout from "@/components/ViewLayout.vue";
import RepositorySelector from "@/components/repositories/RepositorySelector.vue";
import {computed, onMounted, ref} from "vue";
import {router} from "@inertiajs/vue3";
import { useQuasar } from 'quasar'
const $q = useQuasar()
let repository = {value: 'informations', text :'informations'}
let props = defineProps({
    datatable: {
        type: Object,
        required: true,
    },
    userList: {
        type: Object,
        required: true,
    }
})


let selectedUserName = computed(() => {
    return selectedUser.value?.name ?? '-'
})
function createInformations(){
    router.post(`informations/create`, form.value, {
        onSuccess: () => {
            $q.notify({
                type: 'positive',
                message: "Les informations ont été créé",
                position: "top",
                textColor: 'white',
                icon: 'info'
            })
            closeCreationDialog()
            clearForm()
        },
        onError: (server_errors) => {
            errors.value = server_errors
            $q.notify({
                type: 'negative',
                message: "Erreur lors de la création des informations",
                position: "top",
                textColor: 'white',
                icon: 'info'
            })
        },
    })
}
function closeSuppressionDialog(){
    suppressionDialog.value = false;
    clearForm()
    resetSelectedUser();
}
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
        name: 'email',
        align: 'left',
        label: 'E-mail',
        field: 'email',
        sortable: true,
        style: 'min-width: 200px; max-width:200px'
    },
    {
        name: 'adress',
        align: 'left',
        label: 'Adresse',
        field: 'adress',
        sortable: true,
        style: 'min-width: 200px; max-width:200px'
    },
    {
        name: 'city',
        align: 'left',
        label: 'Ville',
        field: 'city',
        sortable: true,
        style: 'min-width: 200px; max-width:200px'
    },
    {
        name: 'zip_code',
        align: 'left',
        label: 'Code postal',
        field: 'zip_code',
        sortable: true,
        style: 'min-width: 200px; max-width:200px'
    },
    {
        name: 'phone',
        align: 'left',
        label: 'Téléphone',
        field: 'phone',
        sortable: true,
        style: 'min-width: 100px; max-width:100px'
    },
    {
        name: 'actions',
        align: 'left',
        label: 'Actions',
        sortable: false,
        style: 'min-width: 300px; max-width:150px'
    },
]);
function hasErrors(field){
    return errors.value[field]?.length > 0
}
let items = computed(() => {
    return props.datatable?.items
})
let itemsUser = computed(() => {
    return props.userList?.items
})

let search = ref('')

let errors = ref([])
const loading = ref(false)



let selected = ref([])


function closeCreationDialog(){
    creationDialog.value = false;
    clearErrors()
    clearForm()
    resetSelectedUser();
}
function resetSelectedUser(){
    selectedUser.value = null;
}
function clearForm(){
    form.value = {}
}
function clearErrors(){
    errors.value = [];
}
function onRequest (props) {
    syncPagination(props)
    reloadDatatable()
}
function syncPagination(props){
    pagination.value = props.pagination;
}
function startLoading(){
    loading.value = true
}
function stopLoading(){
    loading.value = false
}

const filters = ref({})
let total = computed(() => {
    return props.datatable?.total;
})
let totalUser = computed(() => {
    return props.userList?.total;
})
function reloadDatatable(){
    startLoading()
    let data = {...pagination.value, search : search.value, filters : filters.value}
    router.get('/repositories/informations', data, {
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

const pagination = ref({
    sortBy: 'name',
    descending: false,
    page: 1,
    rowsPerPage: 10,
    rowsNumber: null
})

let form = ref({})
let selectedUser = ref(null);
let creationDialog = ref(false);
function openCreationDialog(){
    form.value = {}
    selectedUser.value = null
    creationDialog.value = true
}


onMounted(() => {
    reloadDatatable();
})




function openEditionDialog(props){
    selectedUser.value = props
    editionDialog.value = true
    form.value = {...props}
}



let editionDialog = ref(false);



function deleteUser(){
    router.post(`informations/${selectedUser.value.informationsId}/delete`, {}, {
        onSuccess: () => {
            $q.notify({
                type: 'positive',
                message: "Les informations ont été supprimé",
                position: "top",
                textColor: 'white',
                icon: 'info'
            })
        },
        onError: () => {
            $q.notify({
                type: 'negative',
                message: "Erreur lors de la suppression des informations",
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



function closeEditionDialog(){
    editionDialog.value = false;
    clearErrors()
    clearForm()
    resetSelectedUser();


}


function openSuppressionDialog(props){
    selectedUser.value = props
    suppressionDialog.value = true
}

let suppressionDialog = ref(false);

function editUser(){
    router.post(`informations/${selectedUser.value.id}/edit`, form.value, {
        onSuccess: () => {
            $q.notify({
                type: 'positive',
                message: "L'utilisateur a été modifié",
                position: "top",
                textColor: 'white',
                icon: 'info'
            })
            closeEditionDialog()
            clearForm()
        },
        onError: (server_errors) => {
            errors.value = server_errors
            $q.notify({
                type: 'negative',
                message: "Erreur lors de la modification de l'utilisateur",
                position: "top",
                textColor: 'white',
                icon: 'info'
            })
        },
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
