<template>
    <div>
        <q-select
            v-model="repository"
            :options="items"
            option-value="value"
            option-label="text"
            @update:model-value="switchRepository"
            outlined
            dense
        />

    </div>
</template>

<script setup>

import {onMounted, ref, watch} from "vue";
import {router} from "@inertiajs/vue3";

let props = defineProps({
    repository: {
        type: Object,
        required: true,
    },
})

const items = ref([
    {value: 'users', text :'Utilisateurs'},
    {value: 'books', text :'Livres'},
    {value: 'products', text: 'Produits'},
    {value: 'informations', text: 'Informations'},
])

let repository = ref(null)

onMounted(() => {
    repository.value = props.repository
})

function switchRepository(item){
    router.get(`${item?.value}`)
}

</script>

<style scoped>


</style>
