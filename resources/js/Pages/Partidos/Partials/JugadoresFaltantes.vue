<script setup>

import { ref, computed } from 'vue';
const props = defineProps({
    disponibilidades: Array,
});
// --- Disponibles/convocados count ---
const disponiblesCount = computed(() =>
    props.disponibilidades ? props.disponibilidades.filter(d => d.disponible).length : 0
);
const faltanParaDiez = computed(() => {
    const faltan = 10 - disponiblesCount.value;
    return faltan > 0 ? faltan : 0;
});
</script>
<template>
    <span v-if="faltanParaDiez > 0" class="inline-block text-yellow-800 rounded font-semibold text-sm">
        (Faltan {{ faltanParaDiez }} para completar disponibilidad)
    </span>
</template>