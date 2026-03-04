<template>
  <div class="min-h-screen bg-gray-50/50 py-10 px-4">
    <div class="max-w-2xl mx-auto">
      <div v-if="loading" class="text-center py-20">
        <div class="animate-spin inline-block w-8 h-8 border-4 border-indigo-600 border-t-transparent rounded-full mb-4"></div>
        <p class="text-gray-400 font-bold uppercase tracking-widest text-xs">Loading Admin Data...</p>
      </div>

      <form v-else @submit.prevent="updateAdmin" class="bg-white p-8 rounded-3xl shadow-sm border border-gray-100">
        <div class="mb-8">
          <h2 class="text-2xl font-black text-gray-900">Edit Administrator</h2>
          <p class="text-sm text-gray-500 font-medium">Updating profile for {{ form.name }}</p>
        </div>
        
        <div class="space-y-4">
          <div class="flex flex-col items-center p-4 bg-gray-50 rounded-2xl border-2 border-dashed border-gray-200 mb-4">
              <div class="mb-4 relative">
                <img v-if="photoPreview || currentPicture" 
                     :src="photoPreview || '/storage/' + currentPicture"
                     class="w-24 h-24 rounded-full object-cover border-4 border-white shadow-md" />
                <div v-else class="w-24 h-24 rounded-full bg-indigo-100 flex items-center justify-center">
                  <span class="text-indigo-600 font-bold text-xl uppercase">{{ form.name[0] }}</span>
                </div>
              </div>
              <label class="cursor-pointer bg-white px-4 py-2 rounded-xl shadow-sm border border-gray-100 text-xs font-bold text-gray-600 hover:bg-gray-50 transition">
                Change Photo
                <input type="file" class="hidden" @change="handleFileUpload" accept="image/*" />
              </label>
          </div>

          <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
              <label class="block text-xs font-black text-gray-400 uppercase mb-2 ml-1">First Name</label>
              <input v-model="form.name" type="text" class="form-input-custom" required />
            </div>
            <div>
              <label class="block text-xs font-black text-gray-400 uppercase mb-2 ml-1">Last Name</label>
              <input v-model="form.lastname" type="text" class="form-input-custom" required />
            </div>
          </div>
          
          <div>
            <label class="block text-xs font-black text-gray-400 uppercase mb-2 ml-1">Email Address</label>
            <input v-model="form.email" type="email" class="form-input-custom" required />
          </div>
          
          <div class="p-6 bg-indigo-50/50 rounded-3xl border border-indigo-100">
            <label class="block text-xs font-black text-indigo-400 uppercase mb-2">Security Update</label>
            <div class="relative">
              <input 
                v-model="form.password" 
                :type="showPassword ? 'text' : 'password'" 
                placeholder="New Password (min 8 chars)" 
                class="w-full border-none bg-white p-4 rounded-2xl focus:ring-2 focus:ring-indigo-400 font-medium pr-12"
                :class="{'ring-2 ring-red-300': form.password && form.password.length < 8}"
              />
              <button type="button" @click="showPassword = !showPassword" class="absolute right-4 top-4 text-gray-400 hover:text-indigo-600">
                <svg v-if="!showPassword" xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                </svg>
                <svg v-else xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l18 18" />
                </svg>
              </button>
            </div>
            <p v-if="form.password && form.password.length < 8" class="mt-2 text-[10px] text-red-500 font-bold uppercase animate-pulse">Too short (8 characters minimum)</p>
            <p v-else class="mt-2 text-[10px] text-indigo-400 font-medium italic">Only fill this if you want to change their login password.</p>
          </div>
        </div>

        <div v-if="Object.keys(validationErrors).length > 0" class="mt-4 p-4 bg-red-50 rounded-2xl text-red-600 text-sm">
          <ul class="list-disc list-inside font-bold">
            <li v-for="(errors, field) in validationErrors" :key="field">{{ errors[0] }}</li>
          </ul>
        </div>

        <div class="mt-8 flex gap-3">
          <button type="submit" :disabled="processing" class="flex-1 bg-indigo-600 text-white py-4 rounded-2xl font-bold hover:bg-indigo-700 transition shadow-lg shadow-indigo-100">
            {{ processing ? 'Saving...' : 'Update Administrator' }}
          </button>
          <router-link :to="{ name: 'admins.index' }" class="px-6 py-4 bg-gray-100 text-gray-500 rounded-2xl font-bold hover:bg-gray-200 transition">
            Cancel
          </router-link>
        </div>
      </form>
    </div>
  </div>
</template>

<script setup>
import { ref, reactive, onMounted } from 'vue';
import { useRoute, useRouter } from 'vue-router';
import useAuth from '@/composables/auth';
import axios from 'axios';
import Swal from 'sweetalert2';

const route = useRoute();
const router = useRouter();
const { processing, validationErrors } = useAuth();
const loading = ref(true);
const showPassword = ref(false);
const photoPreview = ref(null);
const currentPicture = ref(null);

const form = reactive({
    name: '',
    lastname: '',
    email: '',
    password: '',
    picture: null,
    role: 'admin'
});

const handleFileUpload = (e) => {
    const file = e.target.files[0];
    if (file) {
        form.picture = file;
        photoPreview.value = URL.createObjectURL(file);
    }
};

const fetchAdminData = async () => {
    try {
        const res = await axios.get(`/api/students/${route.params.id}`);
        form.name = res.data.name;
        form.lastname = res.data.lastname;
        form.email = res.data.email;
        currentPicture.value = res.data.picture;
    } catch (e) {
        Swal.fire('Error', 'Could not find this administrator.', 'error');
        router.push({ name: 'admins.index' });
    } finally {
        loading.value = false;
    }
};

const updateAdmin = async () => {
    // Frontend Password Length Check
    if (form.password && form.password.length < 8) {
        Swal.fire('Wait!', 'The password must be at least 8 characters.', 'warning');
        return;
    }

    processing.value = true;
    validationErrors.value = {};

    // Use FormData for File Upload Support
    const data = new FormData();
    data.append('_method', 'PUT');
    data.append('name', form.name);
    data.append('lastname', form.lastname);
    data.append('email', form.email);
    data.append('role', 'admin');
    
    if (form.password) data.append('password', form.password);
    if (form.picture) data.append('picture', form.picture);

    try {
        await axios.post(`/api/students/${route.params.id}`, data, {
            headers: { 'Content-Type': 'multipart/form-data' }
        });
        
        Swal.fire({
            icon: 'success',
            title: 'Profile Updated',
            showConfirmButton: false,
            timer: 1500
        });
        router.push({ name: 'admins.index' });
    } catch (e) {
        if (e.response?.data?.errors) {
            validationErrors.value = e.response.data.errors;
        } else {
            Swal.fire('Error', 'An unexpected error occurred.', 'error');
        }
    } finally {
        processing.value = false;
    }
};

onMounted(fetchAdminData);
</script>

<style scoped>
.form-input-custom {
  @apply w-full border-none bg-gray-50 p-4 rounded-2xl focus:ring-2 focus:ring-indigo-400 font-bold transition-all;
}
</style>