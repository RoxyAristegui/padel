<script setup>
import { ref } from 'vue';
import { Link, router, useForm } from '@inertiajs/vue3';
import ActionMessage from '@/Components/ActionMessage.vue';
import FormSection from '@/Components/FormSection.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
  import VueDatePicker from '@vuepic/vue-datepicker';
  import '@vuepic/vue-datepicker/dist/main.css';

// const props = defineProps({
//     user: Object,
// });

const form = useForm({
    _method: 'POST',
    rival: null,
    horario: null,
    club: null,
});


const crearPartido = () => {


    form.post(route('partido.store'), {
        errorBag: 'crearPartidoBag',
        preserveScroll: true,
        // onSuccess: () => clearPhotoFileInput(),
    });
};

</script>

<template>
    <FormSection @submitted="crearPartido">
        <template #title>
            Crear nuevo Partido
        </template>

        <!-- <template #description>
            Update your account's profile information and email address.
        </template> -->

        <template #form>

            <!-- Name -->
            <div class="col-span-6 sm:col-span-4">
                <InputLabel for="rival" value="Rival" />
                <TextInput
                    id="rival"
                    v-model="form.rival"
                    type="text"
                    class="mt-1 block w-full"
                    required
                    autocomplete="rival"
                />
                <InputError :message="form.errors.rival" class="mt-2" />
            </div>

            <!-- Email -->
            <div class="col-span-6 sm:col-span-4">
                <InputLabel for="club" value="Club" />
                <TextInput
                    id="club"
                    v-model="form.club"
                    type="text"
                    class="mt-1 block w-full"
                    required
                    autocomplete="equipo rival"
                />
                <InputError :message="form.errors.club" class="mt-2" />

            </div>

            
            <!-- horario -->
            <div class="col-span-6 sm:col-span-4">
                <InputLabel for="horario" value="horario" />
                <VueDatePicker v-model="form.horario" locale="es" />
                
                </div>
        </template>

        <template #actions>
            <ActionMessage :on="form.recentlySuccessful" class="me-3">
                Crear
            </ActionMessage>

            <PrimaryButton :class="{ 'opacity-25': form.processing }" :disabled="form.processing">
                Crear
            </PrimaryButton>
        </template>
    </FormSection>
</template>
