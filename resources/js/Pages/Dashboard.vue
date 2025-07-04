<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import { computed, ref } from 'vue';
import { router } from '@inertiajs/vue3';
import MarcarDisponibilidad from './Partidos/Partials/MarcarDisponibilidad.vue';
import { usePage } from '@inertiajs/vue3'
// Heroicons imports
import { CalendarDaysIcon, ClockIcon, MapPinIcon, UsersIcon } from '@heroicons/vue/24/outline';
 import dayjs from 'dayjs';
import 'dayjs/locale/es';
dayjs.locale('es');

const page = usePage();
const props = defineProps({
    partido: Object,
    miDisponibilidad: Object,
    tieneConvocados: Boolean,
    convocadosCount: Number
});



const estoyConvocado = computed(() => {
    if (!props.partido || !props.partido.convocadas) return false;
    const uid = page.props.auth.user.id;
    return props.partido.convocadas.some(c => c.user_id === uid);
});

const estado = computed(() => {
    if (!props.partido) return null;
    if (!props.partido.completado) {
        if (!props.miDisponibilidad) return { label: null, color: '', aclaracion: null };
        return { label: props.miDisponibilidad.disponible ? '¡Has marcado disponible!' : 'No disponible', color: props.miDisponibilidad.disponible ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700', aclaracion: null };
    } else {
        if (!props.tieneConvocados) {
            return { label: 'Cerrado, esperando convocatoria', color: 'bg-gray-200 text-gray-600', aclaracion: 'Ya no puedes hacer cambios en tu disponibilidad.' };
        } else {
            if (estoyConvocado.value) {
                return { label: 'Has sido convocado', color: 'bg-green-200 text-green-800', aclaracion: null };
            } else {
                return { label: 'Convocatoria lista', color: 'bg-blue-200 text-blue-800', aclaracion: null };
            }
        }
    }
});

const diaSemana = computed(() => {
    if (!props.partido || !props.partido.fecha) return '';
    return dayjs(props.partido.fecha).locale('es').format('dddd');
});

function irAPartido() {
    if (props.partido) {
        router.visit(`/partido/${props.partido.id}`);
    }
}
</script>

<template>
    <AppLayout title="Dashboard">
        <!-- <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Dashboard
            </h2>
        </template> -->

        <div class="py-6 sm:py-12">
            <div class="max-w-2xl mx-auto px-2 sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-xl rounded-2xl">
                    <div v-if="partido" class="p-4 sm:p-8 cursor-pointer hover:bg-gray-50 transition" @click="irAPartido">
                        <div class="flex flex-col gap-4">
                            <div v-if="partido.editado" class="relative flex items-center justify-center bg-gradient-to-r from-yellow-200 via-yellow-300 to-yellow-400 text-yellow-900 font-bold text-center rounded-xl px-4 py-3 mb-2 border border-yellow-400 shadow-md text-base sm:text-lg uppercase tracking-wide animate-pulse">
                                <svg class="w-6 h-6 mr-2 text-yellow-600 flex-shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                <span>Encuentro modificado</span>
                            </div>
                            <div v-if="!partido.completado && !miDisponibilidad">
                                <MarcarDisponibilidad :miDisponibilidad="miDisponibilidad" :partido="partido" />
                            </div>
                            <div v-else>
                                <div :class="'rounded px-4 py-2 text-lg font-bold text-center mb-2 ' + (estado?.color || '')">
                                    {{ estado?.label }}
                                </div>
                                <div v-if="estado?.aclaracion" class="text-xs text-gray-400 text-center mb-2">{{ estado.aclaracion }}</div>
                            </div>
                            <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 mt-2">
                                <div class="flex flex-col items-center">
                                    <CalendarDaysIcon class="w-7 h-7 text-blue-500 mb-1" />
                                    <span class="text-xs text-gray-500 font-semibold">Fecha</span>
                                    <span class="text-xs text-gray-400 font-semibold">{{ diaSemana }}</span>
                                    <span class="text-blue-700 text-sm font-bold">{{ partido.fecha }}</span>
                                </div>
                                <div class="flex flex-col items-center">
                                    <ClockIcon class="w-7 h-7 text-blue-500 mb-1" />
                                    <span class="text-xs text-gray-500 font-semibold">Hora</span>
                                    <span class="text-blue-700 text-sm font-bold">{{ partido.hora }}</span>
                                </div>
                                <div class="flex flex-col items-center">
                                    <MapPinIcon class="w-7 h-7 text-blue-500 mb-1" />
                                    <span class="text-xs text-gray-500 font-semibold">Lugar</span>
                                    <span class="text-blue-700 text-sm font-bold">{{ partido.club }}</span>
                                </div>
                                <div class="flex flex-col items-center">
                                    <UsersIcon class="w-7 h-7 text-blue-500 mb-1" />
                                    <span class="text-xs text-gray-500 font-semibold">Rival</span>
                                    <span class="text-blue-700 text-sm font-bold">{{ partido.rival }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div v-else class="p-8 text-center text-gray-400">No hay partidos creados aún.</div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
