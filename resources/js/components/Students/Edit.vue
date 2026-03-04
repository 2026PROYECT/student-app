<template>
  <div class="min-h-screen bg-gray-50/50 py-10 px-4">
    <div v-if="loading" class="flex flex-col items-center justify-center py-20">
      <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-indigo-600"></div>
      <p class="mt-4 text-gray-500 font-bold">Loading student details...</p>
    </div>

    <div v-else class="max-w-4xl mx-auto">
      <form @submit.prevent="updateStudent" class="bg-white p-8 rounded-3xl shadow-sm border border-gray-100">
        <div class="mb-8">
          <h2 class="text-2xl font-black text-gray-900">Edit Student Profile</h2>
          <p class="text-sm text-gray-500 font-medium">Update academic and personal information</p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
          <div class="space-y-4">
            <h3 class="text-xs font-black text-indigo-500 uppercase tracking-widest ml-1">Personal Info</h3>

            <div class="flex flex-col items-center p-4 bg-gray-50 rounded-2xl border-2 border-dashed border-gray-200">
              <div class="mb-4 relative">
                <img v-if="photoPreview || currentPicture" 
                     :src="photoPreview || '/storage/' + currentPicture"
                     class="w-24 h-24 rounded-full object-cover border-4 border-white shadow-md" />
                <div v-else class="w-24 h-24 rounded-full bg-indigo-100 flex items-center justify-center">
                  <span class="text-indigo-600 font-bold text-xl">{{ form.name[0] }}</span>
                </div>
              </div>
              <label class="cursor-pointer bg-white px-4 py-2 rounded-xl shadow-sm border border-gray-100 text-xs font-bold text-gray-600 hover:bg-gray-50 transition">
                Change Photo
                <input type="file" class="hidden" @change="handleFileUpload" accept="image/*" />
              </label>
            </div>

            <div class="grid grid-cols-2 gap-4">
              <input v-model="form.name" type="text" placeholder="First Name" class="form-input-custom" />
              <input v-model="form.lastname" type="text" placeholder="Last Name" class="form-input-custom" />
            </div>
            <input v-model="form.email" type="email" placeholder="Email" class="form-input-custom" />
          </div>

          <div class="space-y-4">
            <h3 class="text-xs font-black text-indigo-500 uppercase tracking-widest ml-1">Academic Info</h3>
            <div>
              <label class="text-[10px] font-black text-gray-400 uppercase ml-2">Engineering Career</label>
              <select v-model="form.career_id" class="form-input-custom">
                <option value="">Select Career</option>
                <option v-for="c in careers" :key="c.id" :value="c.id">{{ c.name }}</option>
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
                <input v-model="form.saga_code" type="text" placeholder="SAGA" class="form-input-custom" />
              </div>
            </div>

            <div>
              <label class="text-[10px] font-black text-gray-400 uppercase ml-2">Identity Number (CI)</label>
              <input v-model="form.id_number" type="text" placeholder="CI Number" class="form-input-custom" />
            </div>
          </div>
        </div>

        <div class="mt-8 pt-8 border-t border-gray-100">
          <h3 class="text-xs font-black text-indigo-500 uppercase tracking-widest ml-1 mb-4">Security</h3>
          <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div class="relative">
              <input 
                :type="showPassword ? 'text' : 'password'" 
                v-model="form.password" 
                placeholder="New Password (min 8 chars)" 
                class="form-input-custom pr-12"
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
              <p v-if="form.password && form.password.length < 8" class="text-[10px] text-red-500 mt-2 ml-2 font-bold uppercase">Too short!</p>
              <p v-else class="text-[10px] text-gray-400 mt-2 ml-2 italic">Leave blank to keep current password.</p>
            </div>
          </div>
        </div>

        <div class="mt-10 flex gap-4">
          <button type="submit" class="flex-1 bg-indigo-600 text-white py-4 rounded-2xl font-bold hover:bg-indigo-700 transition shadow-lg shadow-indigo-100">
            Update Student Info
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
import { ref, onMounted } from "vue";
import { useRoute, useRouter } from "vue-router";
import axios from "axios";
import Swal from "sweetalert2";

const route = useRoute();
const router = useRouter();
const careers = ref([]);
const loading = ref(true);
const photoPreview = ref(null);
const currentPicture = ref(null);
const showPassword = ref(false);

const form = ref({
  name: "", lastname: "", email: "", career_id: "",
  semester: "", saga_code: "", id_number: "",
  picture: null, password: ""
});

const handleFileUpload = (e) => {
  const file = e.target.files[0];
  if (file) {
    form.value.picture = file;
    photoPreview.value = URL.createObjectURL(file);
  }
};

const fetchData = async () => {
  try {
    const [careerRes, studentRes] = await Promise.all([
      axios.get("/api/careers"),
      axios.get(`/api/students/${route.params.id}`)
    ]);
    careers.value = careerRes.data;
    const data = studentRes.data;
    currentPicture.value = data.picture;
    form.value = {
      name: data.name, lastname: data.lastname, email: data.email,
      career_id: data.student?.career_id || "",
      semester: data.student?.semester || 1,
      saga_code: data.student?.saga_code || "",
      id_number: data.student?.id_number || "",
      picture: null, password: ""
    };
  } catch (error) {
    Swal.fire("Error", "Could not load data", "error");
  } finally {
    loading.value = false;
  }
};

const updateStudent = async () => {
  // Validation check
  if (form.value.password && form.value.password.length < 8) {
    Swal.fire("Wait!", "Password must be 8+ characters", "warning");
    return;
  }

  const data = new FormData();
  data.append("_method", "PUT");
  Object.keys(form.value).forEach(key => {
    if (form.value[key] !== null && form.value[key] !== "") {
        data.append(key, form.value[key]);
    }
  });

  try {
    await axios.post(`/api/students/${route.params.id}`, data, {
      headers: { "Content-Type": "multipart/form-data" }
    });
    Swal.fire("Success", "Student updated!", "success");
    router.push({ name: "students.index" });
  } catch (error) {
    Swal.fire("Error", error.response?.data?.message || "Check inputs", "error");
  }
};

onMounted(fetchData);
</script>

<style scoped>
.form-input-custom {
  @apply w-full border-none bg-gray-50 p-4 rounded-2xl focus:ring-2 focus:ring-indigo-400 font-bold text-gray-700 placeholder-gray-300 transition-all;
}
</style>