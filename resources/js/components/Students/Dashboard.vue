<template>
    <div class="max-w-4xl mx-auto p-6">
        <div class="bg-white rounded-3xl shadow-sm p-8 border border-gray-100">
            <h1 class="text-3xl font-black text-gray-900">Hola, {{ user.name || 'Estudiante' }}</h1>
            <p class="text-gray-500 mt-2">Bienvenido a tu panel de control.</p>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-10">
                <router-link :to="{ name: 'attend.index' }" 
                    class="group p-6 bg-indigo-50 rounded-3xl border-2 border-transparent hover:border-indigo-200 transition-all">
                    <div class="w-12 h-12 bg-indigo-600 text-white rounded-2xl flex items-center justify-center mb-4 shadow-lg">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                        </svg>
                    </div>
                    <h3 class="text-lg font-bold text-indigo-900">Portal de Exámenes</h3>
                    <p class="text-sm text-indigo-700 mt-1">Entra para ver tus pruebas disponibles o generar una nueva.</p>
                </router-link>

                <router-link :to="{ name: 'results.index' }" 
                    class="group p-6 bg-gray-50 rounded-3xl border-2 border-transparent hover:border-gray-200 transition-all">
                    <div class="w-12 h-12 bg-gray-400 text-white rounded-2xl flex items-center justify-center mb-4">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 002 2h2a2 2 0 002-2" />
                        </svg>
                    </div>
                    <h3 class="text-lg font-bold text-gray-900">Mis Resultados</h3>
                    <p class="text-sm text-gray-600 mt-1">Revisa tus notas y el historial de exámenes realizados.</p>
                </router-link>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref } from 'vue';
const user = ref(JSON.parse(localStorage.getItem('user_data')) || {});
</script>