<script setup>
import { ref, watch, computed, onMounted } from 'vue';
import axios from 'axios';

const props = defineProps({
    show: Boolean,
    partidoId: Number, // Cambiamos a recibir solo el id del partido
    convocadosIniciales: Array  // [user_id, ...]
});
const emit = defineEmits(['close', 'finalizar']);

const disponibles = ref([]);
const noDisponibles = ref([]);
const loading = ref(false);

const seleccionados = ref([]);

const seleccionadosCount = computed(() => seleccionados.value.length);

const seleccionadosCountClass = computed(() =>
    seleccionadosCount.value >= 10 ? 'text-green-700' : 'text-gray-400'
);

watch(
    () => props.show,
    async (val) => {
        if (val) {
            loading.value = true;
            try {
                const res = await axios.get(`/partido/${props.partidoId}/disponibles-con-estadisticas`);
                disponibles.value = res.data.disponibles;
                noDisponibles.value = res.data.noDisponibles;
                if (props.convocadosIniciales && props.convocadosIniciales.length > 0) {
                    seleccionados.value = [...props.convocadosIniciales];
                } else {
                    seleccionados.value = disponibles.value.map(d => d.user.id);
                }
            } finally {
                loading.value = false;
            }
        }
    },
    { immediate: true }
);

const finalizar = () => {
    emit('finalizar', seleccionados.value);
};

const showMasJugadores = ref(false);
</script>

<template>
    <div v-if="show" class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-40">
        <div class="bg-white rounded-xl shadow-lg p-8 w-full max-w-md relative">
            <button class="absolute top-2 right-2 text-gray-400 hover:text-gray-700" @click="$emit('close')">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
            <h3 class="font-semibold text-lg mb-4 text-green-700">Selecciona los jugadores a convocar</h3>
            <form @submit.prevent="finalizar">
                <div v-if="loading" class="text-center text-gray-400 mb-4">Cargando jugadores...</div>
                <div v-else class="max-h-60 overflow-y-auto mb-4">
                    <table class="min-w-full text-xs">
                        <thead>
                            <tr>
                                <th class="px-2 py-1 text-left">Convocar</th>
                                <th class="px-2 py-1 text-left">Nombre</th>
                                <th class="px-2 py-1 text-left">% Disp.</th>
                                <th class="px-2 py-1 text-left">% Conv.</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="disp in disponibles" :key="disp.user.id">
                                <td>
                                    <input
                                        type="checkbox"
                                        :id="'convocado-' + disp.user.id"
                                        :value="disp.user.id"
                                        v-model="seleccionados"
                                        class="rounded border-gray-300 text-green-600 shadow-sm focus:ring-green-500"
                                    />
                                </td>
                                <td>
                                    <label :for="'convocado-' + disp.user.id" class="text-gray-700">{{ disp.user.name }}</label>
                                </td>
                                <td>
                                    <span v-if="disp.user.estadisticas">{{ disp.user.estadisticas.porcentaje_disponible }}%</span>
                                </td>
                                <td>
                                    <span v-if="disp.user.estadisticas">{{ disp.user.estadisticas.porcentaje_convocado }}%</span>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <!-- Más jugadores (no disponibles) -->
                <div>
                    <button
                        type="button"
                        class="text-blue-600 font-semibold underline mb-2"
                        @click="showMasJugadores = !showMasJugadores"
                    >
                        Más jugadores
                        <svg :class="{'rotate-180': showMasJugadores}" class="inline w-4 h-4 ml-1 transition-transform" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/>
                        </svg>
                    </button>
                    <div v-if="showMasJugadores" class="max-h-40 overflow-y-auto border-t pt-2">
                        <table class="min-w-full text-xs">
                            <thead>
                                <tr>
                                    <th class="px-2 py-1 text-left">Convocar</th>
                                    <th class="px-2 py-1 text-left">Nombre</th>
                                    <th class="px-2 py-1 text-left">% Disp.</th>
                                    <th class="px-2 py-1 text-left">% Conv.</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="disp in noDisponibles" :key="disp.user.id">
                                    <td>
                                        <input
                                            type="checkbox"
                                            :id="'convocado-no-' + disp.user.id"
                                            :value="disp.user.id"
                                            v-model="seleccionados"
                                            class="rounded border-gray-300 text-green-600 shadow-sm focus:ring-green-500"
                                        />
                                    </td>
                                    <td>
                                        <label :for="'convocado-no-' + disp.user.id" class="text-gray-700">{{ disp.user.name }}</label>
                                    </td>
                                    <td>
                                        <span v-if="disp.user.estadisticas">{{ disp.user.estadisticas.porcentaje_disponible }}%</span>
                                    </td>
                                    <td>
                                        <span v-if="disp.user.estadisticas">{{ disp.user.estadisticas.porcentaje_convocado }}%</span>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                        <div v-if="noDisponibles.length === 0" class="text-gray-400 text-sm">No hay más jugadores.</div>
                    </div>
                </div>
                <!-- Contador de seleccionados -->
                <div :class="'text-center font-semibold mb-2 ' + seleccionadosCountClass">
                    Jugadores seleccionados: {{ seleccionadosCount }}
                </div>
                <button
                    type="submit"
                    class="w-full px-4 py-2 rounded bg-green-700 text-white font-bold mt-4"
                >Finalizar</button>
            </form>
        </div>
    </div>
</template>