<template>
  <div class="min-h-screen bg-gray-50/50 py-10 px-4">
    <div class="max-w-4xl mx-auto">
      
      <!-- Header -->
      <div class="flex items-center justify-between mb-8">
        <div>
          <h1 class="text-3xl font-extrabold text-gray-900 tracking-tight">Add Student</h1>
          <p class="text-gray-500 mt-1 text-sm">Fill in the details below to create a new student record.</p>
        </div>
        <router-link :to="{ name: 'students.index' }"
          class="inline-flex items-center justify-center px-6 py-3 bg-indigo-600 text-white font-bold rounded-2xl shadow-lg shadow-indigo-200 hover:bg-indigo-700 transition-all">
          ← Back to Directory
        </router-link>
      </div>

      <!-- Form -->
      <form @submit.prevent="submit" class="space-y-6">
        
        <!-- Card: Basic Info -->
        <div class="bg-white p-8 rounded-3xl shadow-sm border border-gray-100 space-y-6">
          <div>
            <label class="block text-xs font-bold text-gray-400 uppercase tracking-widest mb-2">First Name</label>
            <input v-model="form.name" type="text"
                   class="w-full bg-gray-50 border-none rounded-2xl px-5 py-3 focus:ring-2 focus:ring-indigo-400 transition" />
            <p v-for="err in validationErrors?.name" class="text-red-500 text-xs mt-1">{{ err }}</p>
          </div>

          <div>
            <label class="block text-xs font-bold text-gray-400 uppercase tracking-widest mb-2">Lastname</label>
            <input v-model="form.lastname" type="text"
                   class="w-full bg-gray-50 border-none rounded-2xl px-5 py-3 focus:ring-2 focus:ring-indigo-400 transition" />
            <p v-for="err in validationErrors?.lastname" class="text-red-500 text-xs mt-1">{{ err }}</p>
          </div>

          <div>
            <label class="block text-xs font-bold text-gray-400 uppercase tracking-widest mb-2">Surname</label>
            <input v-model="form.surname" type="text"
                   class="w-full bg-gray-50 border-none rounded-2xl px-5 py-3 focus:ring-2 focus:ring-indigo-400 transition" />
            <p v-for="err in validationErrors?.surname" class="text-red-500 text-xs mt-1">{{ err }}</p>
          </div>

          <div>
            <label class="block text-xs font-bold text-gray-400 uppercase tracking-widest mb-2">Email</label>
            <input v-model="form.email" type="email"
                   class="w-full bg-gray-50 border-none rounded-2xl px-5 py-3 focus:ring-2 focus:ring-indigo-400 transition" />
            <p v-for="err in validationErrors?.email" class="text-red-500 text-xs mt-1">{{ err }}</p>
          </div>

          <div>
            <label class="block text-xs font-bold text-gray-400 uppercase tracking-widest mb-2">Password</label>
            <input v-model="form.password" type="password"
                   class="w-full bg-gray-50 border-none rounded-2xl px-5 py-3 focus:ring-2 focus:ring-indigo-400 transition" />
            <p v-for="err in validationErrors?.password" class="text-red-500 text-xs mt-1">{{ err }}</p>
          </div>

          <div>
            <label class="block text-xs font-bold text-gray-400 uppercase tracking-widest mb-2">Role</label>
            <select v-model="form.role"
                    class="w-full bg-gray-50 border-none rounded-2xl px-5 py-3 focus:ring-2 focus:ring-indigo-400 transition">
              <option disabled value="">-- Select Role --</option>
              <option value="admin">Admin</option>
              <option value="student">Student</option>
            </select>
            <p v-for="err in validationErrors?.role" class="text-red-500 text-xs mt-1">{{ err }}</p>
          </div>
        </div>

        <!-- Card: Picture -->
        <div class="bg-white p-8 rounded-3xl shadow-sm border border-gray-100">
          <label class="block text-xs font-bold text-gray-400 uppercase tracking-widest mb-4">Picture</label>
          <input type="file" accept="image/*" @change="handlePicture"
                 class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4
                        file:rounded-full file:border-0 file:text-sm file:font-semibold
                        file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100" />
          <p v-for="err in validationErrors?.picture" class="text-red-500 text-xs mt-1">{{ err }}</p>

          <!-- Preview -->
          <div v-if="previewUrl" class="mt-4">
            <img :src="previewUrl" alt="Preview"
                 class="h-24 w-24 object-cover rounded-xl border border-gray-100 shadow-sm" />
          </div>
        </div>

        <!-- Card: Additional Info -->
        <div class="bg-white p-8 rounded-3xl shadow-sm border border-gray-100 space-y-6">
          <div>
            <label class="block text-xs font-bold text-gray-400 uppercase tracking-widest mb-2">SAGA Code</label>
            <input v-model="form.saga_code" type="text"
                   class="w-full bg-gray-50 border-none rounded-2xl px-5 py-3 focus:ring-2 focus:ring-indigo-400 transition" />
          </div>

          <div>
            <label class="block text-xs font-bold text-gray-400 uppercase tracking-widest mb-2">ID Number</label>
            <input v-model="form.id_number" type="text"
                   class="w-full bg-gray-50 border-none rounded-2xl px-5 py-3 focus:ring-2 focus:ring-indigo-400 transition" />
          </div>

          <div>
            <label class="block text-xs font-bold text-gray-400 uppercase tracking-widest mb-2">Career</label>
            <select v-model="form.career_id"
                    class="w-full bg-gray-50 border-none rounded-2xl px-5 py-3 focus:ring-2 focus:ring-indigo-400 transition">
              <option disabled value="">-- Select Career --</option>
              <option v-for="career in careers" :key="career.id_career" :value="career.id_career">
                {{ career.career }}
              </option>
            </select>
            <p v-for="err in validationErrors?.career_id" class="text-red-500 text-xs mt-1">{{ err }}</p>
          </div>

          <div>
            <label class="block text-xs font-bold text-gray-400 uppercase tracking-widest mb-2">Semester</label>
            <select v-model="form.semester"
                    class="w-full bg-gray-50 border-none rounded-2xl px-5 py-3 focus:ring-2 focus:ring-indigo-400 transition">
              <option disabled value="">-- Select Semester --</option>
              <option v-for="n in 10" :key="n" :value="n">{{ n }}</option>
            </select>
            <p v-for="err in validationErrors?.semester" class="text-red-500 text-xs mt-1">{{ err }}</p>
          </div>
        </div>

        <!-- Submit -->
        <button type="submit" :disabled="isLoading"
                class="group relative w-full flex justify-center py-5 px-4 border border-transparent
                       text-lg font-bold rounded-3xl text-white bg-indigo-600 hover:bg-indigo-700
                       focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500
                       transition-all shadow-xl shadow-indigo-200 disabled:opacity-50">
          {{ isLoading ? "Saving..." : "Save Student" }}
        </button>
      </form>
    </div>
  </div>
</template>
<script>
import { ref, reactive, onMounted } from "vue";
import { useRouter } from "vue-router"; // 1. Add this import
import axios from "axios";

export default {
  name: "StudentsCreate",
  setup() {
    const router = useRouter(); // 2. Initialize the router instance
    const isLoading = ref(false);
    const validationErrors = ref({});
    const careers = ref([]);
    const previewUrl = ref(null);

    const form = reactive({
      name: "",
      lastname: "",
      surname: "",
      email: "",
      password: "",
      role: "",
      picture: null,
      saga_code: "",
      id_number: "",
      career_id: "",
      semester: 1,
    });

    const handlePicture = (event) => {
      const file = event.target.files[0];
      form.picture = file;
      if (file) {
        // Clean up memory if a preview already existed
        if (previewUrl.value) URL.revokeObjectURL(previewUrl.value);
        previewUrl.value = URL.createObjectURL(file);
      }
    };

    const submit = async () => {
      if (isLoading.value) return;
      isLoading.value = true;
      validationErrors.value = {};

      try {
        const formData = new FormData();
        Object.keys(form).forEach((key) => {
          formData.append(key, form[key]);
        });

        await axios.post("/api/users", formData, {
          headers: { "Content-Type": "multipart/form-data" },
        });

        // 3. This is the redirect logic
        // If your route is defined as { name: 'students.index', path: '/students' ... }
        router.push({ name: 'students.index' }); 
        
        // OR if you don't use names, use the path:
        // router.push('/students');

      } catch (error) {
        if (error.response?.data?.errors) {
          validationErrors.value = error.response.data.errors;
        }
      } finally {
        isLoading.value = false;
      }
    };

    onMounted(async () => {
      try {
        const { data } = await axios.get("/api/careers");
        careers.value = data;
      } catch (e) {
        console.error("Failed to load careers");
      }
    });

    return {
      form,
      isLoading,
      validationErrors,
      handlePicture,
      submit,
      careers,
      previewUrl,
    };
  },
};
</script>
