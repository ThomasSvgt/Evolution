<template>
    <view-layout>

        <template #header>
            <div class="row flex-center" style="padding-bottom: 20px">
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
                            <div class="col-grow text-right">
                                <q-btn label="action groupée"
                                       color="grey-8"
                                       v-if="hasSelectedItems"
                                       @click="groupedAction"
                                />
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
                                <q-select outlined dense v-model="filters.status"
                                          @update:model-value="reloadDatatable"
                                          :options="statuses"
                                          option-label="text"
                                          option-value="value"
                                          clearable
                                          label="Statut">
                                </q-select>
                            </div>
                        </div>

                    </q-card-section>

                    <q-card-section>
                        <q-table
                            v-model:pagination="pagination"
                            title="Utilisateurs"
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
                                <q-btn label="Ajouter un utilisateur" color="green-7" @click="openCreationDialog">
                                    &nbsp;
                                        <q-icon name="add_circle" />
                                </q-btn>
                            </template>

                            <template v-slot:body-cell-active="props">
                                <q-td :props="props">
                                    <q-icon name="circle" color="green-7" v-if="props.row.active === 1"/>
                                    <q-icon v-else name="circle" color="red-8"/>
                                </q-td>
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
                                    &nbsp;

                                    <q-btn
                                        color="green-8"
                                        padding="sm"
                                        size="sm"
                                        class="cursor-pointer"
                                        icon="fa-solid fa-eye"
                                        @click="openViewDialog(props.row)"
                                    >
                                        <q-tooltip class="bg-grey-8" style="font-size: 13px">
                                            Visualiser
                                        </q-tooltip>
                                    </q-btn>
                                </q-td>
                            </template>

                        </q-table>

                    </q-card-section>

                </q-card>
            </div>

            <q-dialog v-model="suppressionDialog"
                      persistent
                      transition-show="scale"
                      transition-hide="scale">

                <q-card class="bg-radius report-style" style="width: 800px; height: 200px">

                    <q-card-section class="bg-primary text-white">
                        <div class="text-h6">Suppression de l'utilisateur</div>
                    </q-card-section>


                    <q-card-section class="bg-white">
                        <div>
                            Veuillez confirmer la suppression de l'utilisateur
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

            <q-dialog v-model="editionDialog"
                      persistent
                      transition-show="scale"
                      transition-hide="scale">

                <q-card class="bg-radius report-style" style="width: 800px; height: 300px">

                    <q-card-section class="bg-primary text-white">
                        <div class="text-h6">Édition de l'utilisateur</div>
                    </q-card-section>

                    <q-card-section class="bg-white">
                        <div class="row flex-center q-col-gutter-md">
                            <q-input  v-model="form.name"
                                      :error-message="errors['name']"
                                      :error="hasErrors('name')"
                                      label="Nom / Prénom"
                                      class="col-6"/>

                            <q-input  v-model="form.username"
                                      :error-message="errors['username']"
                                      :error="hasErrors('username')"
                                      label="Pseudo"
                                      class="col-6" />

                            <q-input  v-model="form.email"
                                      :error-message="errors['email']"
                                      :error="hasErrors('email')"
                                      label="Email"
                                      class="col-12"/>
                        </div>
                    </q-card-section>

                    <q-card-section class="bg-white absolute" style="bottom: 0; right: 10px">
                        <q-btn label="annuler" color="grey-8" @click="closeEditionDialog"/>
                        &nbsp;
                        <q-btn label="valider" color="green-8" @click="editUser"/>
                    </q-card-section>

                </q-card>

            </q-dialog>

            <q-dialog v-model="creationDialog"
                      persistent
                      transition-show="scale"
                      transition-hide="scale">

                <q-card class="bg-radius report-style" style="width: 800px; height: 300px">

                    <q-card-section class="bg-primary text-white">
                        <div class="text-h6">Création d'un l'utilisateur</div>
                    </q-card-section>

                    <q-card-section class="bg-white">
                        <div class="row flex-center q-col-gutter-md">
                            <q-input  v-model="form.name"
                                      :error-message="errors['name']"
                                      :error="hasErrors('name')"
                                      label="Nom / Prénom"
                                      class="col-6"/>

                            <q-input  v-model="form.username"
                                      :error-message="errors['username']"
                                      :error="hasErrors('username')"
                                      label="Pseudo"
                                      class="col-6" />

                            <q-input  v-model="form.email"
                                      :error-message="errors['email']"
                                      :error="hasErrors('email')"
                                      label="Email"
                                      class="col-12" />
                        </div>
                    </q-card-section>

                    <q-card-section class="bg-white absolute" style="bottom: 0; right: 10px">
                        <q-btn label="annuler" color="grey-8" @click="closeCreationDialog"/>
                        &nbsp;
                        <q-btn label="valider" color="green-8" @click="createUser"/>
                    </q-card-section>

                </q-card>

            </q-dialog>

            <q-dialog v-model="viewDialog"
                      persistent
                      transition-show="scale"
                      transition-hide="scale">

                <q-card class="bg-radius report-style" style="width: 1000px; height: 400px">

                    <q-card-section class="bg-primary text-white">
                        <div class="text-h6">Visualisation de l'utilisateur</div>
                    </q-card-section>
                    <q-card-section class="bg-white">
                        UTIISATEURS<br><br>
                           Name : {{userId.name}}<br>
                           Username : {{userId.username}}<br>
                           E-mail : {{userId.email}}<br><br>
                            INFORMATIONS<br><br>
                           Phone : {{userId.phone}}<br>
                           Adress : {{userId.adress}}<br>
                           City : {{userId.city}}<br>
                           Zip code : {{userId.zip_code}}<br>

                    </q-card-section>

                    <q-card-section class="bg-white absolute" style="bottom: 0; right: 10px">
                        <q-btn label="Fermer" color="grey-8" @click="closeViewDialog"/>

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

const statuses = ref([
    {value: true, text :'Actif'},
    {value: false, text :'Inactif'},
])

let repository = {value: 'users', text :'Utilisateurs'}

let props = defineProps({
    datatable: {
        type: Object,
        required: true,
    },
    userView: {
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

let suppressionDialog = ref(false);

function closeSuppressionDialog(){
    suppressionDialog.value = false;
    clearForm()
    resetSelectedUser();
}

function openSuppressionDialog(props){
    selectedUser.value = props
    suppressionDialog.value = true
}


function deleteUser(){
    router.post(`users/${selectedUser.value.id}/delete`, {}, {
        onSuccess: () => {
            $q.notify({
                type: 'positive',
                message: "L'utilisateur a été supprimé",
                position: "top",
                textColor: 'white',
                icon: 'info'
            })
        },
        onError: () => {
            $q.notify({
                type: 'negative',
                message: "Erreur lors de la suppression de l'utilisateur",
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

let editionDialog = ref(false);
let viewDialog = ref(false);

function closeEditionDialog(){
    editionDialog.value = false;
    clearErrors()
    clearForm()
    resetSelectedUser();
}

function openEditionDialog(props){
    selectedUser.value = props
    editionDialog.value = true
    form.value = {...props}
}
let userViewSelect = ref(null);

let userId = ref(null);

function openViewDialog(props){
    selectedUser.value = props;
    userId.value = this.props.userView.find(user => user.userId === selectedUser.value.id);
    if(userId.value != undefined)
    {
        viewDialog.value = true;
    }
    else{
        $q.notify({
            position: 'top',
            color: 'red',
            message: '<span style="color: white; font-size:18px">Aucune information de disponible pour cette utilisateur</span>',
            html: true
        });
        viewDialog.value = false;
    }

}


function editUser(){
    router.post(`users/${selectedUser.value.id}/edit`, form.value, {
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

let creationDialog = ref(false);

function closeCreationDialog(){
    creationDialog.value = false;
    clearErrors()
    clearForm()
    resetSelectedUser();
}

function closeViewDialog(){
    viewDialog.value = false;
    clearErrors()
    clearForm()
    resetSelectedUser();
}

function openCreationDialog(){
    form.value = {}
    selectedUser.value = null
    creationDialog.value = true
}

function createUser(){
    router.post(`users/create`, form.value, {
        onSuccess: () => {
            $q.notify({
                type: 'positive',
                message: "L'utilisateur a été créé",
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
                message: "Erreur lors de la création de l'utilisateur",
                position: "top",
                textColor: 'white',
                icon: 'info'
            })
        },
    })
}

let form = ref({})

function clearForm(){
    form.value = {}
}

let errors = ref([])

function hasErrors(field){
    return errors.value[field]?.length > 0
}

function clearErrors(){
    errors.value = [];
}

let selectedUser = ref(null);

let selectedUserName = computed(() => {
    return selectedUser.value?.name ?? '-'
})

function resetSelectedUser(){
    selectedUser.value = null;
}

let headers = ref([
    {
        name: 'name',
        align: 'left',
        label: 'Nom / Prénom',
        field: 'name',
        sortable: true,
        style: 'min-width: 200px; max-width:200px'
    },
    {
        name: 'username',
        align: 'left',
        label: 'Pseudo',
        field: 'username',
        sortable: true,
        style: 'min-width: 200px; max-width:200px'
    },
    {
        name: 'email',
        align: 'left',
        label: 'Email',
        field: 'email',
        sortable: true,
        style: 'min-width: 200px; max-width:200px'
    },
    {
        name: 'active',
        align: 'left',
        label: 'Statut',
        field: 'active',
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


const filters = ref({})

let search = ref('')

const loading = ref(false)

function startLoading(){
    loading.value = true
}

function stopLoading(){
    loading.value = false
}

const pagination = ref({
    sortBy: 'name',
    descending: false,
    page: 1,
    rowsPerPage: 10,
    rowsNumber: null
})

function syncPagination(props){
    pagination.value = props.pagination;
}

function reloadDatatable(){
    startLoading()
    let data = {...pagination.value, search : search.value, filters : filters.value}
    router.get('/repositories/users', data, {
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

onMounted(() => {
    reloadDatatable();
})

function onRequest (props) {
    syncPagination(props)
    reloadDatatable()
}

let selected = ref([])

let hasSelectedItems = computed(() => {
    return selected.value.length > 0
})

function groupedAction(){
    console.log(selected.value)
}

</script>

<style scoped lang="scss">

.header{
    font-size: 30px;
    padding: 20px 0 0 0;
}
</style>
