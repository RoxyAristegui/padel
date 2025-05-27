<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import { ref, computed } from 'vue';
import { router } from '@inertiajs/vue3';
import ConvocarModal from './Partials/ConvocarModal.vue';
import JugadoresFaltantes from './Partials/JugadoresFaltantes.vue';
import MarcarDisponibilidad from './Partials/MarcarDisponibilidad.vue';
import VueDatePicker from '@vuepic/vue-datepicker';
import '@vuepic/vue-datepicker/dist/main.css';

const props = defineProps({
    partido: Object,
    isAdmin: Boolean,
    completado: Boolean,
    miDisponibilidad: Object,
    disponibilidades: Array,
    convocados: Array,
    miembrosEquipo: Array
});


const cerrarPartido = () => {
    router.post(route('partido.cerrar', props.partido.id));
};

// --- Convocar jugadores ---
const showModal = ref(false);
const disponibles = computed(() =>
    props.disponibilidades ? props.disponibilidades.filter(d => d.disponible) : []
);
const disponiblesIds = computed(() => disponibles.value.map(d => d.user.id));

// Miembros que no están en disponibles
const noDisponibles = computed(() =>
    props.miembrosEquipo
        ? props.miembrosEquipo
            .filter(m => !disponiblesIds.value.includes(m.id))
            .map(m => ({ user: m }))
        : []
);
const convocadosIds = computed(() =>
    props.convocados ? props.convocados.map(c => c.user_id) : []
);

const openModal = () => {
    showModal.value = true;
};

const cerrarConvocatoria = (seleccionados) => {
    router.post(route('partido.convocar', props.partido.id), {
        convocados: seleccionados
    }, {
        onSuccess: () => {
            showModal.value = false;
        }
    });
};

const eliminarPartido = () => {
    if (confirm('¿Seguro que quieres eliminar este partido?')) {
        router.delete(route('partido.destroy', props.partido.id));
    }
};

const hayDisponibles = computed(() =>
    props.disponibilidades && props.disponibilidades.some(d => d.disponible)
);

const showEditModal = ref(false);
const editHorario = ref(props.partido.horario);
const openEditModal = () => {
    editHorario.value = props.partido.horario;
    showEditModal.value = true;
};
const submitEdit = () => {
    router.put(route('partido.update', props.partido.id), {
        horario: editHorario.value
    }, {
        onSuccess: () => {
            showEditModal.value = false;
        }
    });
};


</script>

<template>
    <AppLayout :title="`Partido Jornada ${partido.jornada}`">
        <template #header>
            <h2 class="font-semibold text-2xl text-blue-800 leading-tight flex items-center gap-2">
                <svg class="w-7 h-7 text-blue-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 17v-2a4 4 0 014-4h2a4 4 0 014 4v2M7 7a4 4 0 018 0v4a4 4 0 01-8 0V7z" />
                </svg>
                Partido - Jornada {{ partido.jornada }}
            </h2>
        </template>

        <div class="py-12 bg-gray-100 min-h-screen">
            <div class="max-w-5xl mx-auto sm:px-6 lg:px-8">
                <div class="flex flex-col lg:flex-row gap-8">
                    <!-- Card: Datos del partido y disponibilidad -->
                    <div class="flex-1">
                        <div class="bg-white rounded-xl shadow-md p-8 mb-6 relative">
                            <!-- Trash icon button for delete -->
                            <button
                                v-if="isAdmin && !hayDisponibles"
                                @click.stop="eliminarPartido"
                                class="absolute top-4 right-4 text-red-500 hover:text-red-700 transition-colors p-2 rounded-full bg-red-50 hover:bg-red-100 shadow focus:outline-none focus:ring-2 focus:ring-red-400"
                                title="Eliminar partido"
                            >
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6M1 7h22M8 7V5a2 2 0 012-2h4a2 2 0 012 2v2" />
                                </svg>
                            </button>
                            <!-- Info partido -->
                            <div class="mb-6 flex items-center gap-4">
                                <div class="flex flex-col items-center justify-center bg-blue-100 rounded-full w-20 h-20">
                                    <span class="text-3xl font-bold text-blue-700">J{{ partido.jornada }}</span>
                                    <span class="text-xs text-blue-400">Jornada</span>
                                </div>
                                <div>
                                    <div class="text-xl font-semibold text-gray-800">{{ partido.club }}</div>
                                    <div class="text-lg text-gray-500">vs {{ partido.rival }}</div>
                                </div>
                            </div>
                            <div class="mb-4 flex items-center gap-2">
                                <span class="font-medium text-gray-700">Horario:</span>
                                <span
                                    class="ml-2"
                                    :class="partido.editado ? 'bg-yellow-100 text-yellow-800 font-bold px-2 py-1 rounded' : 'text-blue-700'"
                                >
                                    {{ partido.horario }}
                                </span>
                                <button v-if="isAdmin" @click="openEditModal" class="ml-2 text-gray-400 hover:text-blue-600">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                        <path d="M17.414 2.586a2 2 0 00-2.828 0l-9.5 9.5A2 2 0 004 13.414V16a1 1 0 001 1h2.586a2 2 0 001.414-.586l9.5-9.5a2 2 0 000-2.828l-2-2zM5 15v-1.586l9-9L16.586 6l-9 9H5z" />
                                    </svg>
                                </button>
                                <span v-if="partido.editado" class="ml-2 text-xs text-yellow-600 font-semibold">(Editado)</span>
                            </div>
                            <div>
                                <span class="font-medium text-gray-700">Club:</span>
                                <span class="ml-2 text-blue-700">{{ partido.club }}</span>
                            </div>

                            <!-- Botones de disponibilidad -->
                            <div v-if="!completado " class="mt-8 flex gap-4">
                         
                                <MarcarDisponibilidad :miDisponibilidad="miDisponibilidad" :partido="partido"  />
                               
                            </div>
                            <div v-else class="mt-8">
                                <span class="text-gray-700 font-semibold">Partido cerrado.</span>
                            </div>
                            <div class="mt-4">
                                <span class="text-gray-600">Tu disponibilidad: </span>
                                <span v-if="miDisponibilidad">
                                    <span v-if="miDisponibilidad.disponible" class="text-green-600 font-bold">Disponible</span>
                                    <span v-else class="text-red-600 font-bold">No disponible</span>
                                </span>
                                <span v-else class="text-gray-400">No has marcado tu disponibilidad.</span>
                            </div>
                            <!-- Mensaje de cuántos faltan para 10 (solo si el partido NO está cerrado) -->
                            <div v-if="!completado"  class="mt-2">                                
                                <JugadoresFaltantes :disponibilidades="disponibilidades"/>
                            </div>

                            <!-- Botón cerrar partido solo admin -->
                            <div v-if="isAdmin && !completado" class="mt-8">
                                <button
                                    class="w-full px-4 py-2 rounded bg-blue-700 text-white font-bold"
                                    @click="cerrarPartido"
                                >Cerrar partido</button>
                            </div>

                            <!-- Botón convocar jugadores solo admin y partido cerrado -->
                            <div v-if="isAdmin && completado" class="mt-8">
                                <button
                                    class="px-4 py-2 rounded bg-green-700 text-white font-bold"
                                    @click="openModal"
                                >Convocar jugadores</button>
                            </div>
                        </div>
                    </div>

                    <!-- Card: Tabla de disponibilidades solo para admin -->
                    <div v-if="isAdmin" class="flex-1">
                        <div class="bg-white rounded-xl shadow-md p-8">
                            <h3 class="font-semibold text-lg mb-4 text-blue-700">Disponibilidad de los usuarios</h3>
                            <div class="overflow-x-auto">
                                <table class="min-w-full divide-y divide-gray-200">
                                    <thead>
                                        <tr>
                                            <th class="px-4 py-2 text-left text-xs font-medium text-green-700 uppercase bg-green-50">Disponibles</th>
                                            <th class="px-4 py-2 text-left text-xs font-medium text-red-700 uppercase bg-red-50">No disponibles</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <!-- Disponibles -->
                                            <td class="align-top px-4 py-2">
                                                <ul>
                                                    <li v-for="disp in disponibilidades.filter(d => d.disponible)" :key="disp.user.id" class="mb-1">
                                                        <span class="font-medium">{{ disp.user.name }}</span>
                                                    </li>
                                                </ul>
                                            </td>
                                            <!-- No disponibles -->
                                            <td class="align-top px-4 py-2">
                                                <ul>
                                                    <li v-for="disp in disponibilidades.filter(d => !d.disponible)" :key="disp.user.id" class="mb-1">
                                                        <span class="font-medium">{{ disp.user.name }}</span>
                                                    </li>
                                                </ul>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Tabla de convocados (visible para todos) -->
                <div v-if="convocados && convocados.length" class="max-w-2xl mx-auto mt-8">
                    <div class="bg-white rounded-xl shadow-md p-8">
                        <h3 class="font-semibold text-lg mb-4 text-green-700">Convocados</h3>
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead>
                                <tr>
                                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-700 uppercase">Nombre</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="conv in convocados" :key="conv.user.id">
                                    <td class="px-4 py-2">{{ conv.user.name }}</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Modal de convocatoria -->
                <ConvocarModal
                    :show="showModal"
                    :disponibles="disponibles"
                    :no-disponibles="noDisponibles"
                    :convocados-iniciales="convocadosIds"
                    @close="showModal = false"
                    @finalizar="cerrarConvocatoria"
                />

                <!-- Modal de edición de horario -->
                <teleport to="body">
                    <div v-if="showEditModal" class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-40">
                        <div class="bg-white rounded-lg shadow-lg p-6 w-full max-w-md">
                            <h3 class="text-lg font-semibold mb-4">Editar fecha y hora</h3>
                            <VueDatePicker v-model="editHorario" locale="es" class="mb-4 w-full" />
                            <div class="flex justify-end gap-2">
                                <button @click="showEditModal = false" class="px-4 py-2 rounded bg-gray-200 text-gray-700">Cancelar</button>
                                <button @click="submitEdit" class="px-4 py-2 rounded bg-blue-700 text-white font-bold">Guardar</button>
                            </div>
                        </div>
                    </div>
                </teleport>
            </div>
        </div>
    </AppLayout>
</template>