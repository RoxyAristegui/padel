<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import CreatePartidoForm from './Partials/CreatePartidoForm.vue';
</script>
<template>
    
    <AppLayout title="CrearPartido">
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Nuevo Partido
            </h2>            
        </template>
        <div>
            <div class="max-w-7xl mx-auto py-10 sm:px-6 lg:px-8">
                <CreatePartidoForm class="mt-10 sm:mt-0"></CreatePartidoForm>
            </div>
        </div>
    </AppLayout>
</template>