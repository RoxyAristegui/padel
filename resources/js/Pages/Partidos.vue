<script setup>
import PrimaryButton from '@/Components/PrimaryButton.vue';
import AppLayout from '@/Layouts/AppLayout.vue';
import { router } from '@inertiajs/vue3';

defineProps({
    partidos: Array,
    permissions: Object,
});

const crearPartido= ()=>{
 router.get(route('partido.create'));
}
</script>

<template>
    <AppLayout title="Partidos">
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Partidos
            </h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
                    <div class="p-4">
                        <PrimaryButton
                        @click="crearPartido"
                        > Nuevo Partido
                        </PrimaryButton>
                    </div>
                    <div v-if="partidos" class="">
                        <div class="p-3" v-if="partidos">
                            <div class="border border-gray-500 grid grid-cols-4 mb-3" v-for="partido in partidos" :key="partido.jornada">
                                <div class="flex items-stretch  bg-green-600 text-white">
                                    <div class="m-auto">Jornada {{partido.jornada}}</div>
                                </div>
                                <div class="col-span-3">
                                    <div class="px-2 border-b">{{partido.horario}}</div>
                                    <div class="px-2 flex justify-between">
                                        <div class="uppercase">{{partido.club}} </div>
                                        <div class=""> {{ partido.rival }}</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    
    </AppLayout>
</template>
