<script setup>
import { Head, Link, useForm } from '@inertiajs/vue3';
import AuthenticationCardLogo from '@/Components/AuthenticationCardLogo.vue';
import InputLabel from '@/Components/InputLabel.vue';
import TextInput from '@/Components/TextInput.vue';
import InputError from '@/Components/InputError.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import Checkbox from '@/Components/Checkbox.vue';
import { ref } from 'vue';

defineProps({
    canLogin: Boolean,
    canRegister: Boolean,
    canResetPassword: Boolean,
    laravelVersion: String,
    phpVersion: String,
});

const form = useForm({
    email: '',
    password: '',
    remember: false,
});

const showLogin = ref(false);

const isMobile = () => window.innerWidth < 768;

if (typeof window !== 'undefined') {
    // Auto-show login on desktop/tablet
    showLogin.value = !isMobile();
    window.addEventListener('resize', () => {
        showLogin.value = !isMobile();
    });
}
</script>

<template>
    <Head title="Welcome" />
    <div class="min-h-screen bg-gradient-to-br from-blue-50 via-white to-green-100 flex flex-col items-center justify-center py-6 px-2">
        <div class="w-full max-w-4xl flex flex-col md:flex-row gap-10 items-center justify-center">
            <!-- Login Card (Mobile: arriba, sin card) -->
            <div class="w-full md:w-auto md:flex-1 flex flex-col items-center md:order-2 order-1">
                <button
                    class="md:hidden w-full bg-gradient-to-r from-green-500 to-blue-500 text-white font-bold py-2 rounded-xl shadow mb-2 text-lg"
                    @click="showLogin = !showLogin"
                    v-if="!showLogin"
                >
                    Inicia sesión
                </button>
                <transition name="fade">
                    <div v-show="showLogin" class="w-full flex flex-col items-center">
                        <h2 class="text-xl md:text-2xl font-bold mb-2 text-green-700 mt-2 md:mt-0">Inicia sesión</h2>
                        <form @submit.prevent="form.post(route('login'), { onFinish: () => form.reset('password') })" class="w-full flex flex-col gap-3 max-w-xs bg-transparent p-0 shadow-none border-none">
                            <div>
                                <InputLabel for="email" value="Email" />
                                <TextInput id="email" v-model="form.email" type="email" class="mt-1 block w-full" required autofocus autocomplete="username" />
                                <InputError :message="form.errors.email" class="mt-2" />
                            </div>
                            <div>
                                <InputLabel for="password" value="Contraseña" />
                                <TextInput id="password" v-model="form.password" type="password" class="mt-1 block w-full" required autocomplete="current-password" />
                                <InputError :message="form.errors.password" class="mt-2" />
                            </div>
                            <div class="flex items-center justify-between">
                                <label class="flex items-center">
                                    <Checkbox v-model="form.remember" name="remember" />
                                    <span class="ml-2 text-sm text-gray-600">Recuérdame</span>
                                </label>
                                <Link v-if="canResetPassword" :href="route('password.request')" class="text-sm text-blue-600 hover:underline">¿Olvidaste tu contraseña?</Link>
                            </div>
                            <PrimaryButton :class="{ 'opacity-25': form.processing }" :disabled="form.processing">Entrar</PrimaryButton>
                        </form>
                    </div>
                </transition>
            </div>
            <!-- Left: App Explanation -->
            <div class="flex-1 bg-white/90 shadow-xl rounded-2xl p-8 flex flex-col justify-center items-center gap-4 border border-blue-100 md:order-1 order-2 w-full md:w-auto">
                <AuthenticationCardLogo class="mb-2 scale-125" />
                <h1 class="text-2xl md:text-3xl font-extrabold text-blue-800 mb-2 text-center">Gestiona tu equipo de pádel</h1>
                <p class="text-gray-700 text-base md:text-lg text-center mb-2">Si eres capitán, regístrate y empieza a gestionar tu equipo de forma sencilla y visual.</p>
                <ul class="list-none flex flex-col gap-2 w-full max-w-xs mx-auto">
                    <li class="flex items-center gap-2 text-gray-700"><span class="inline-block w-2 h-2 bg-green-500 rounded-full"></span> Crea tu equipo e invita a los jugadores</li>
                    <li class="flex items-center gap-2 text-gray-700"><span class="inline-block w-2 h-2 bg-green-500 rounded-full"></span> Crea y gestiona partidos fácilmente</li>
                    <li class="flex items-center gap-2 text-gray-700"><span class="inline-block w-2 h-2 bg-green-500 rounded-full"></span> Consulta estadísticas de asistencia y convocatorias</li>
                </ul>
                <div class="m-4 flex flex-col items-center gap-2 w-full">
                    <Link v-if="canRegister" :href="route('register')" class="w-full bg-gradient-to-r from-green-500 to-blue-500 hover:from-blue-500 hover:to-green-500 text-white font-bold py-3 text-center rounded-xl shadow-lg text-lg transition-all">Regístrate gratis</Link>
                </div>
            </div>
        </div>
    </div>
</template>
