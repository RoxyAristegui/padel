<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import { ref, computed, onMounted } from 'vue';
import { router, usePage } from '@inertiajs/vue3';

const props = defineProps({
    jugadores: Array,
});

</script>

<template>
    <AppLayout title="Estadísticas">
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Estadísticas de Jugadores
            </h2>
        </template>
        <div class="py-12 bg-gray-100 min-h-screen">
            <div class="max-w-5xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white rounded-xl shadow-md p-8">
                    <h3 class="font-semibold text-lg mb-4 text-blue-700">Listado de jugadores y estadísticas</h3>
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead>
                                <tr>
                                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-700 uppercase">Nombre</th>
                                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-700 uppercase">% Disponible</th>
                                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-700 uppercase">% Convocado (de disponibles)</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="jugador in jugadores" :key="jugador.id">
                                    <td class="px-4 py-2">{{ jugador.name }}</td>
                                    <td class="px-4 py-2">{{ jugador.porcentaje_disponible }}%</td>
                                    <td class="px-4 py-2">{{ jugador.porcentaje_convocado }}%</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
