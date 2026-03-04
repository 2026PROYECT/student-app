No need to apologize! Building the Student Create form is actually the most complex part of your user management because it handles data for two different tables (users and students) and a file upload all at once.

Since we are using your 10 Engineering Careers from the seeder, this form will dynamically pull those from the database.

The Complete StudentCreate.vue
Fragmento de código
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
            
            <div class="flex flex-col items-center p-4 bg-gray-50 rounded-2xl border-2 border-dashed border-gray-200">
              <div v-if="photoPreview" class="mb-4">
                <img :src="photoPreview" class="w-24 h-24 rounded-full object-cover border-4 border-white shadow-md" />
              </div>
              <label class="cursor-pointer bg-white px-4 py-2 rounded-xl shadow-sm border border-gray-100 text-xs font-bold text-gray-600 hover:bg-gray-50">
                {{ fileName || 'Upload Photo' }}
                <input type="file" class="hidden" @change="handleFileUpload" accept="image/*" />
              </label>
            </div>

            <div class="grid grid-cols-2 gap-4">
              <input v-model="form.name" type="text" placeholder="First Name" class="form-input-custom" required />
              <input v-model="form.lastname" type="text" placeholder="Last Name" class="form-input-custom" required />
            </div>
            <input v-model="form.email" type="email" placeholder="Email Address" class="form-input-custom" required />
            <input v-model="form.password" type="password" placeholder="Account Password" class="form-input-custom" required />
          </div>

          <div class="space-y-4">
            <h3 class="text-xs font-black text-indigo-500 uppercase tracking-widest ml-1">Academic Profile</h3>
            
            <div>
              <label class="text-[10px] font-black text-gray-400 uppercase ml-2">Career / Engineering</label>
              <select v-model="form.career_id" class="form-input-custom" required>
                <option value="" disabled>Select Career</option>
                <option v-for="career in careers" :key="career.id" :value="career.id">
                  {{ career.name }}
                </option>
              </select>
            </div>

            <div class="grid grid-cols-2 gap-4">
              <div>
                <label class="text-[10px] font-black text-gray-400 uppercase ml-2">Semester</label>
                <select v-model="form.semester" class="form-input-custom">
                  <option v-for="n in 10" :key="n" :value="n">{{ n }}° Semester</option>
                </select>
              </div>
              <div>
                <label class="text-[10px] font-black text-gray-400 uppercase ml-2">SAGA Code</label>
                <input v-model="form.saga_code" type="text" placeholder="e.g. 12345" class="form-input-custom" />
              </div>
            </div>

            <div>
              <label class="text-[10px] font-black text-gray-400 uppercase ml-2">National ID / CI</label>
              <input v-model="form.id_number" type="text" placeholder="Identity Number" class="form-input-custom" />
            </div>
          </div>
        </div>

        <div v-if="Object.keys(validationErrors).length > 0" class="mt-8 p-4 bg-red-50 rounded-2xl text-red-600 text-sm">
          <ul class="list-disc list-inside">
            <li v-for="(errors, field) in validationErrors" :key="field">{{ errors[0] }}</li>
          </ul>
        </div>

        <div class="mt-10 flex gap-4">
          <button type="submit" :disabled="processing" class="flex-1 bg-indigo-600 text-white py-4 rounded-2xl font-bold hover:bg-indigo-700 transition disabled:opacity-50">
            {{ processing ? 'Processing...' : 'Register Student' }}
          </button>
          <router-link :to="{ name: 'students.index' }" class="px-8 py-4 bg-gray-100 text-gray-500 rounded-2xl font-bold hover:bg-gray-200 transition">
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
  email: '',
  password: '',
  role: 'student', // Must be 'student'
  career_id: '',
  semester: 1,
  saga_code: '',
  id_number: '',
  picture: null
});

onMounted(async () => {
  try {
    const response = await axios.get('/api/careers');
    careers.value = response.data;
  } catch (e) {
    console.error("Could not load careers");
  }
});

const handleFileUpload = (event) => {
  const file = event.target.files[0];
  if (file) {
    form.picture = file;
    fileName.value = file.name;
    photoPreview.value = URL.createObjectURL(file);
  }
};

const saveStudent = async () => {
  processing.value = true;
  validationErrors.value = {};

  const formData = new FormData();
  // Append everything to FormData
  Object.keys(form).forEach(key => {
    if (form[key] !== null) formData.append(key, form[key]);
  });

  try {
    await axios.post('/api/students', formData, {
      headers: { 'Content-Type': 'multipart/form-data' }
    });
    
    Swal.fire('Success', 'Student registered successfully', 'success');
    router.push({ name: 'students.index' });
  } catch (e) {
    if (e.response?.data?.errors) {
      validationErrors.value = e.response.data.errors;
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