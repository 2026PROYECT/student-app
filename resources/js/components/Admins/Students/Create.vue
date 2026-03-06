<template>
  <div class="min-h-screen bg-gray-50/50 py-10 px-4">
    <div class="max-w-4xl mx-auto">
      <form @submit.prevent="saveStudent" class="bg-white p-8 rounded-3xl shadow-sm border border-gray-100">
        <div class="mb-8">
          <h2 class="text-2xl font-black text-gray-900">Enroll New Student</h2>
          <p class="text-sm text-gray-500 font-medium">Create a user account and academic profile</p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
          <div class="space-y-4">
            <h3 class="text-xs font-black text-indigo-500 uppercase tracking-widest ml-1">Personal Information</h3>
            
            <div class="flex flex-col items-center p-4 bg-gray-50 rounded-2xl border-2 border-dashed border-gray-200" 
                 :class="{'border-red-300 bg-red-50': validationErrors.picture}">
              <div v-if="photoPreview" class="mb-4">
                <img :src="photoPreview" class="w-24 h-24 rounded-full object-cover border-4 border-white shadow-md" />
              </div>
              <label class="cursor-pointer bg-white px-4 py-2 rounded-xl shadow-sm border border-gray-100 text-xs font-bold text-gray-600 hover:bg-gray-50">
                {{ fileName || 'Upload Photo *' }}
                <input type="file" class="hidden" @change="handleFileUpload" accept="image/*" />
              </label>
              <p v-if="validationErrors.picture" class="text-[10px] text-red-500 mt-2 font-bold">{{ validationErrors.picture[0] }}</p>
            </div>

            <div class="grid grid-cols-2 gap-4">
              <input v-model="form.name" type="text" placeholder="First Name *" class="form-input-custom" :class="{'ring-2 ring-red-400': validationErrors.name}" required />
              <input v-model="form.lastname" type="text" placeholder="Last Name *" class="form-input-custom" :class="{'ring-2 ring-red-400': validationErrors.lastname}" required />
              <input v-model="form.surname" type="text" placeholder="SurName *" class="form-input-custom" :class="{'ring-2 ring-red-400': validationErrors.surname}" />
            </div>
            <input v-model="form.email" type="email" placeholder="Email Address *" class="form-input-custom" :class="{'ring-2 ring-red-400': validationErrors.email}" required />
            <input v-model="form.password" type="password" placeholder="Account Password *" class="form-input-custom" :class="{'ring-2 ring-red-400': validationErrors.password}" required />
          </div>

          <div class="space-y-4">
            <h3 class="text-xs font-black text-indigo-500 uppercase tracking-widest ml-1">Academic Profile</h3>
            
            <div>
              <label class="text-[10px] font-black text-gray-400 uppercase ml-2">Career / Engineering *</label>
              <select v-model="form.career_id" class="form-input-custom" :class="{'ring-2 ring-red-400': validationErrors.career_id}" required>
                <option value="" disabled>Select Career</option>
                <option v-for="career in careers" :key="career.id" :value="career.id">
                  {{ career.name }}
                </option>
              </select>
            </div>

            <div class="grid grid-cols-2 gap-4">
              <div>
                <label class="text-[10px] font-black text-gray-400 uppercase ml-2">Semester *</label>
                <select v-model="form.semester" class="form-input-custom" required>
                  <option v-for="n in 10" :key="n" :value="n">{{ n }}° Semester</option>
                </select>
              </div>
              <div>
                <label class="text-[10px] font-black text-gray-400 uppercase ml-2">SAGA Code *</label>
                <input v-model="form.saga_code" type="text" placeholder="e.g. 12345" class="form-input-custom" :class="{'ring-2 ring-red-400': validationErrors.saga_code}" required />
              </div>
            </div>

            <div>
              <label class="text-[10px] font-black text-gray-400 uppercase ml-2">National ID / CI *</label>
              <input v-model="form.id_number" type="text" placeholder="Identity Number" class="form-input-custom" :class="{'ring-2 ring-red-400': validationErrors.id_number}" required />
            </div>
          </div>
        </div>

        <div v-if="Object.keys(validationErrors).length > 0" class="mt-8 p-4 bg-red-50 rounded-2xl text-red-600 text-sm border border-red-100">
          <p class="font-black uppercase text-xs mb-2">Please correct the following:</p>
          <ul class="list-disc list-inside font-medium">
            <li v-for="(errors, field) in validationErrors" :key="field">{{ errors[0] }}</li>
          </ul>
        </div>

        <div class="mt-10 flex gap-4">
          <button type="submit" :disabled="processing" class="flex-1 bg-indigo-600 text-white py-4 rounded-2xl font-bold hover:bg-indigo-700 transition disabled:opacity-50 shadow-lg shadow-indigo-200">
            {{ processing ? 'Processing...' : 'Register Student' }}
          </button>
          <router-link :to="{ name: 'students.index' }" class="px-8 py-4 bg-gray-100 text-gray-500 rounded-2xl font-bold hover:bg-gray-200 transition text-center">
            Cancel
          </router-link>
        </div>
      </form>
    </div>
  </div>
</template>

<script setup>
import { reactive, ref, onMounted } from 'vue';
import { useRouter } from 'vue-router';
import axios from 'axios';
import Swal from 'sweetalert2';

const router = useRouter();
const careers = ref([]);
const processing = ref(false);
const validationErrors = ref({});
const photoPreview = ref(null);
const fileName = ref('');

const form = reactive({
    name: '',
    lastname: '',
    surname: '',
    email: '',
    password: '',
    role: 'student', 
    career_id: '',
    semester: 1,
    saga_code: '',
    id_number: '',
    picture: null
});

/**
 * Carga de carreras
 */
onMounted(async () => {
    try {
        // CORRECCIÓN: Usamos /v1/ porque así está en tu api.php
        const response = await axios.get('/api/v1/careers');
        
        // Laravel Resources suelen devolver { data: [...] }
        careers.value = response.data.data ? response.data.data : response.data;
        
    } catch (e) {
        console.error("Error detallado:", e.response);
        
        // Si es 401, es que Sanctum no reconoce tu sesión
        if (e.response?.status === 401) {
            Swal.fire('Sesión Expirada', 'Por favor, vuelve a loguearte', 'warning');
        } else {
            Swal.fire('Error', 'No se pudieron cargar las carreras (404/500)', 'error');
        }
    }
});

const handleFileUpload = (event) => {
    const file = event.target.files[0];
    if (file) {
        if (!file.type.startsWith('image/')) {
            Swal.fire('Error', 'Debe ser una imagen', 'error');
            return;
        }
        form.picture = file;
        fileName.value = file.name;
        photoPreview.value = URL.createObjectURL(file);
    }
};

const saveStudent = async () => {
    processing.value = true;
    validationErrors.value = {};

    const formData = new FormData();
    
    // Pasamos los datos al FormData
    formData.append('name', form.name);
    formData.append('lastname', form.lastname);
    formData.append('surname', form.surname);
    formData.append('email', form.email);
    formData.append('password', form.password);
    formData.append('role', 'student');
    formData.append('career_id', form.career_id);
    formData.append('semester', form.semester);
    formData.append('saga_code', form.saga_code || '');
    formData.append('id_number', form.id_number || '');
    
    if (form.picture) {
        formData.append('picture', form.picture);
    }

    try {
        // La ruta de tu apiResource 'students' es /api/v1/students
        await axios.post('/api/v1/students', formData, {
            headers: { 
                'Content-Type': 'multipart/form-data',
                'Accept': 'application/json' 
            }
        });
        
        Swal.fire({
            icon: 'success',
            title: '¡Éxito!',
            text: 'Estudiante creado correctamente',
            timer: 2000,
            showConfirmButton: false
        });

        router.push({ name: 'students.index' });
    } catch (e) {
        if (e.response?.status === 422) {
            validationErrors.value = e.response.data.errors;
        } else {
            Swal.fire('Error', 'No se pudo guardar el registro', 'error');
        }
    } finally {
        processing.value = false;
    }
};
</script>
<style scoped>
.form-input-custom {
  @apply w-full border-none bg-gray-50 p-4 rounded-2xl focus:ring-2 focus:ring-indigo-400 font-bold text-gray-700 placeholder-gray-300 transition-all;
}
</style>