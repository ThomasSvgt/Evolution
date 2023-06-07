<template>
    <q-header bordered class="bg-white text-white" height-hint="98">
        <q-toolbar>
        <q-btn class="text-grey-7" dense flat round icon="fa-solid fa-bars" size="10px"
               @click="toggleLeftDrawer" />

        <q-toolbar-title class="text-overline text-uppercase text-grey-8">
            <q-btn flat no-caps no-wrap class="q-ml-xs" @click="goToHome()">
                <q-icon :name="fasShieldCat" color="primary" size="28px" />

                <q-toolbar-title shrink class="text-weight-bold text-overline">
                    QUASAR DEMO
                </q-toolbar-title>
            </q-btn>
        </q-toolbar-title>

    </q-toolbar>
    </q-header>
</template>

<script setup>

import {fasShieldCat} from "@quasar/extras/fontawesome-v6";
import {router} from "@inertiajs/vue3";

const emit = defineEmits(['toggleLeftDrawer', 'resetDrawers'])

let props = defineProps({
    leftDrawerIsOpen: {
        type: Boolean,
        required: true,
        default: false
    }
})

function toggleLeftDrawer () {
    emit('toggleLeftDrawer')
}

function goToHome(){
    emit('resetDrawers')
    router.get('/');
}

</script>
