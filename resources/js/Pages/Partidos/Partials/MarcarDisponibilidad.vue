<script setup>
import { computed, ref } from 'vue';
import { router } from '@inertiajs/vue3';
const props = defineProps({
    partido: Object,
    miDisponibilidad: Object,
});
const seleccion = ref(props.miDisponibilidad ? (props.miDisponibilidad.disponible) : null);
const emits = defineEmits(['close']);

function marcarDisponibilidad(valor) {
    if (!props.partido) return;
    router.post(route('partido.disponibilidad', props.partido.id), { disponible: valor });
    seleccion.value = valor;
    emits('close');
}
</script>

<template>
       <div class="flex gap-4 justify-center mb-2">
                                    <button
                                        class="px-4 py-2 rounded bg-green-500 text-white font-bold"
                                        :class="{ 'opacity-50': seleccion === 1 }"
                                        @click.stop="marcarDisponibilidad(1)"
                                    >Disponible</button>
                                    <button
                                        class="px-4 py-2 rounded bg-red-500 text-white font-bold"
                                        :class="{ 'opacity-50': seleccion === 0 }"
                                        @click.stop="marcarDisponibilidad(0)"
                                    >No disponible</button>
                                </div>
</template>