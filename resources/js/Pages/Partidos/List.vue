// resources/js/Pages/Partidos/List.vue
<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import { router } from '@inertiajs/vue3';

const props = defineProps({
    partidos: Array,
    isAdmin: Boolean,
});

const crearPartido = () => {
    router.get(route('partido.create'));
};
</script>

<template>
    <AppLayout title="Listado de Partidos">
        <template #header>
            <div class="flex items-center justify-between">
                <h2 class="font-semibold text-2xl text-blue-800 leading-tight flex items-center gap-2">
                    <svg class="w-7 h-7 text-blue-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 17v-2a4 4 0 014-4h2a4 4 0 014 4v2M7 7a4 4 0 018 0v4a4 4 0 01-8 0V7z" />
                    </svg>
                    Partidos
                </h2>
                <PrimaryButton v-if="isAdmin" @click="crearPartido">
                    Nuevo Partido
                </PrimaryButton>
            </div>
        </template>

        <div class="py-12 bg-gray-100 min-h-screen">
            <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
                <div v-if="partidos && partidos.length" class="space-y-6">
                    <div
                        v-for="partido in partidos"
                        :key="partido.id"
                        class="relative bg-white rounded-xl shadow-md p-6 flex items-center justify-between hover:shadow-lg transition-shadow cursor-pointer hover:bg-blue-50"
                        :class="{'bg-gray-100 text-gray-400 hover:bg-gray-200': partido.completado}"
                        @click="router.get(route('partido.show', partido.id))"
                    >
    
                        <div class="flex items-center gap-4">
                            <div class="flex flex-col items-center justify-center bg-blue-100 rounded-full w-16 h-16">
                                <span class="text-2xl font-bold text-blue-700">J{{ partido.jornada }}</span>
                                <span class="text-xs text-blue-400">Jornada</span>
                            </div>
                            <div>
                                <div class="text-lg font-semibold" :class="{'text-gray-500': partido.completado, 'text-gray-800': !partido.completado}">
                                    {{ partido.club }}
                                </div>
                                <div class="text-sm" :class="{'text-gray-400': partido.completado, 'text-gray-500': !partido.completado}">
                                    vs {{ partido.rival }}
                                </div>
                                <div class="text-xs mt-1" :class="{'text-gray-400': partido.completado, 'text-gray-500': !partido.completado}">
                                    <svg class="inline w-4 h-4 mr-1 text-blue-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                    </svg>
                                    {{ partido.horario }}
                                </div>
                            </div>
                        </div>
                        <div>    <span
                                v-if="partido.tiene_convocados"
                                class="inline-block bg-green-600 text-white text-xs font-bold px-3 py-1 rounded-full shadow flex items-center gap-1"
                            >
                                <svg class="w-4 h-4 inline" fill="none" stroke="currentColor" stroke-width="3" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
                                </svg>
                                CONVOCATORIA LISTA
                        </span>
                            <span
                                v-else-if="partido.completado"
                                class="inline-block  right-2 bg-red-600 text-white text-xs font-bold px-3 py-1 rounded-full shadow"
                            >
                                CERRADO
                            </span>
                        
                        </div>
                    </div>
                </div>
                <div v-else class="p-8 text-center text-gray-500 text-lg bg-white rounded-xl shadow">
                    No hay partidos creados.
                </div>
            </div>
        </div>
    </AppLayout>
</template>