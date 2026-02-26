<template>
  <div class="min-h-screen flex items-center justify-center bg-gradient-to-br from-indigo-50 via-white to-pink-50 p-6">
    
    <div class="w-full max-w-md bg-white/80 backdrop-blur-md p-8 rounded-3xl shadow-[0_20px_50px_rgba(0,0,0,0.05)] border border-white">
      
      <div class="flex justify-center mb-6">
        <div class="h-12 w-12 bg-indigo-600 rounded-2xl rotate-12 flex items-center justify-center shadow-lg shadow-indigo-200">
          <span class="text-white text-2xl font-black -rotate-12">G</span>
        </div>
      </div>

      <div class="text-center mb-8">
        <h2 class="text-2xl font-bold text-gray-800 tracking-tight">Hey there!</h2>
        <p class="text-gray-500 text-sm">Great to see you again.</p>
      </div>

      <form @submit.prevent="submitLogin" class="space-y-5">
        <div>
          <label class="block text-xs font-bold text-gray-400 uppercase tracking-widest ml-1 mb-2">Email</label>
          <input
            v-model="loginForm.email"
            type="email"
            placeholder="hello@example.com"
            class="w-full px-5 py-4 bg-gray-100/50 border-none rounded-2xl focus:ring-2 focus:ring-indigo-400 focus:bg-white transition-all duration-300 outline-none placeholder:text-gray-400"
            required
          />
          <div v-if="validationErrors?.email" class="mt-2 text-xs text-rose-500 font-medium px-1">
            {{ validationErrors.email[0] }}
          </div>
        </div>

        <div>
          <label class="block text-xs font-bold text-gray-400 uppercase tracking-widest ml-1 mb-2">Password</label>
          <input
            v-model="loginForm.password"
            type="password"
            placeholder="••••••••"
            class="w-full px-5 py-4 bg-gray-100/50 border-none rounded-2xl focus:ring-2 focus:ring-indigo-400 focus:bg-white transition-all duration-300 outline-none"
            required
          />
          <div v-if="validationErrors?.password" class="mt-2 text-xs text-rose-500 font-medium px-1">
            {{ validationErrors.password[0] }}
          </div>
        </div>

        <div class="flex items-center justify-between px-1">
          <label class="flex items-center cursor-pointer group">
            <input 
              type="checkbox" 
              v-model="loginForm.remember"
              class="w-5 h-5 rounded-lg border-gray-200 text-indigo-600 focus:ring-indigo-500 transition-all"
            />
            <span class="ml-3 text-sm text-gray-500 group-hover:text-gray-700">Remember me</span>
          </label>
          <a href="#" class="text-sm font-semibold text-indigo-600 hover:text-indigo-800">Forgot?</a>
        </div>

        <button
          :disabled="processing"
          class="w-full py-4 bg-indigo-600 hover:bg-indigo-700 text-white font-bold rounded-2xl shadow-lg shadow-indigo-200 hover:shadow-indigo-300 hover:-translate-y-0.5 active:translate-y-0 transition-all duration-200 disabled:opacity-60 disabled:transform-none"
        >
          <span v-if="!processing">Login</span>
          <span v-else class="flex justify-center">
            <svg class="animate-spin h-5 w-5 text-white" viewBox="0 0 24 24">
              <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
              <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
          </span>
        </button>
      </form>

      <div class="mt-8 text-center">
        <p class="text-sm text-gray-500">
          New here? 
          <a href="#" class="text-indigo-600 font-bold hover:underline">Create an account</a>
        </p>
      </div>
    </div>
  </div>
</template>

<script setup>
import useAuth from "@/composables/auth";
const { loginForm, validationErrors, processing, submitLogin } = useAuth();
</script>