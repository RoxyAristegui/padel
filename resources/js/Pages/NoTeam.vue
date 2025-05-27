<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
// import CreateTeamForm from './Teams/Partials/CreateTeamForm.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import { router } from '@inertiajs/vue3';


const createTeam = () => {
    router.get(route('teams.create'))
};

</script>

<template>
    <AppLayout title="NoTeam">
    
        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
                    <div class="p-4">
                        <p class="mb-2">
                        Parece que no tienes ningún equipo asignado 
                    </p>
                    <PrimaryButton
                        type="button"
                        class="ms-3"
                        @click="createTeam"
                    >
                        Crear un Equipo
                    </PrimaryButton>
                </div>
                </div>
            </div>
        </div>
    
    </AppLayout>
</template>
