<template>
  <div class="min-h-screen bg-gray-50/50 py-10 px-4">
    <div class="max-w-2xl mx-auto">
      <form @submit.prevent="saveAdmin" class="bg-white p-8 rounded-3xl shadow-sm border border-gray-100">
        <div class="mb-8">
          <h2 class="text-2xl font-black text-gray-900">New Administrator</h2>
          <p class="text-sm text-gray-500 font-medium">Create a user with full system access</p>
        </div>
        <div v-if="photoPreview" class="mb-4 flex justify-center">
    <img :src="photoPreview" class="w-24 h-24 object-cover rounded-full border-4 border-indigo-100 shadow-sm" />
</div>
        <div class="space-y-4">
          <div class="mb-6">
            <label class="block text-xs font-black text-gray-400 uppercase mb-2 ml-1">Profile Picture</label>
            <div class="flex items-center justify-center w-full">
              <label class="flex flex-col items-center justify-center w-full h-32 border-2 border-gray-200 border-dashed rounded-2xl cursor-pointer bg-gray-50 hover:bg-gray-100 transition">
                <div class="flex flex-col items-center justify-center pt-5 pb-6">
                  <span class="text-sm text-gray-500 font-bold" v-if="!fileName">Click to upload photo</span>
                  <span class="text-sm text-indigo-600 font-bold" v-else>{{ fileName }}</span>
                </div>
                <input type="file" class="hidden" @change="handleFileUpload" accept="image/*" />
              </label>
            </div>
          </div>

          <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
              <label class="block text-xs font-black text-gray-400 uppercase mb-2 ml-1">First Name</label>
              <input v-model="form.name" type="text" class="w-full border-none bg-gray-50 p-4 rounded-2xl focus:ring-2 focus:ring-indigo-400 font-bold" required />
            </div>
            <div>
              <label class="block text-xs font-black text-gray-400 uppercase mb-2 ml-1">Last Name</label>
              <input v-model="form.lastname" type="text" class="w-full border-none bg-gray-50 p-4 rounded-2xl focus:ring-2 focus:ring-indigo-400 font-bold" required />
            </div>
          </div>
          
          <div>
            <label class="block text-xs font-black text-gray-400 uppercase mb-2 ml-1">Email Address</label>
            <input v-model="form.email" type="email" class="w-full border-none bg-gray-50 p-4 rounded-2xl focus:ring-2 focus:ring-indigo-400 font-bold" required />
          </div>
          
          <div>
            <label class="block text-xs font-black text-gray-400 uppercase mb-2 ml-1">Temporary Password</label>
            <input v-model="form.password" type="password" class="w-full border-none bg-gray-50 p-4 rounded-2xl focus:ring-2 focus:ring-indigo-400 font-bold" required />
          </div>
        </div>

        <div v-if="Object.keys(validationErrors).length > 0" class="mt-4 p-4 bg-red-50 rounded-2xl text-red-600 text-sm">
          <ul class="list-disc list-inside">
            <li v-for="(errors, field) in validationErrors" :key="field">{{ errors[0] }}</li>
          </ul>
        </div>

        <div class="mt-8 flex gap-3">
          <button type="submit" :disabled="processing" class="flex-1 bg-indigo-600 text-white py-4 rounded-2xl font-bold hover:bg-indigo-700 transition disabled:opacity-50">
            {{ processing ? 'Creating...' : 'Create Admin Account' }}
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
import { reactive, ref } from 'vue';
import { useRouter } from 'vue-router';
import useAuth from '@/composables/auth';
import axios from 'axios';
import Swal from 'sweetalert2';

const router = useRouter();
const { processing, validationErrors } = useAuth();
const fileName = ref('');

const form = reactive({ 
    name: '', 
    lastname: '', 
    email: '', 
    password: '', 
    role: 'admin',
    picture: null // Placeholder for the file
});

const photoPreview = ref(null);

const handleFileUpload = (event) => {
    const file = event.target.files[0];
    if (file) {
        form.picture = file;
        fileName.value = file.name;
        // This creates a temporary URL for the browser
        photoPreview.value = URL.createObjectURL(file);
    }
};

const saveAdmin = async () => {
    processing.value = true;
    validationErrors.value = {};

    // IMPORTANT: Use FormData for file uploads
    const formData = new FormData();
    formData.append('name', form.name);
    formData.append('lastname', form.lastname);
    formData.append('email', form.email);
    formData.append('password', form.password);
    formData.append('role', form.role);
    
    // Only append if a picture was actually selected
    if (form.picture) {
        formData.append('picture', form.picture);
    }

    try {
        await axios.post('/api/students', formData, {
            headers: {
                'Content-Type': 'multipart/form-data'
            }
        });
        
        Swal.fire({
            icon: 'success',
            title: 'Admin Created',
            timer: 2000,
            showConfirmButton: false
        });
        
        router.push({ name: 'admins.index' });
    } catch (e) {
        if (e.response?.data?.errors) {
            validationErrors.value = e.response.data.errors;
        } else {
            Swal.fire('Error', e.response?.data?.message || 'Check your console', 'error');
        }
    } finally {
        processing.value = false;
    }
};
</script>