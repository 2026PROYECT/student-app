<template>
  <div class="min-h-screen bg-gray-50/50 py-10 px-4">
    <div class="max-w-6xl mx-auto">
      
      <div class="flex flex-col md:flex-row md:items-center justify-between mb-8 gap-4">
        <div>
          <h1 class="text-3xl font-extrabold text-gray-900 tracking-tight">Student Directory</h1>
          <p class="text-gray-500 mt-1">Manage student enrollments and career details.</p>
        </div>
        <router-link :to="{ name: 'students.create' }" 
          class="inline-flex items-center justify-center px-6 py-3 bg-indigo-600 text-white font-bold rounded-2xl shadow-lg shadow-indigo-200 hover:bg-indigo-700 transition-all">
          <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M12 4v16m8-8H4" />
          </svg>
          Add Student
        </router-link>
      </div>
<div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
    <div class="bg-white p-6 rounded-3xl shadow-sm border border-gray-100">
        <p class="text-xs font-bold text-gray-400 uppercase tracking-widest">Total Students</p>
        <p class="text-2xl font-black text-indigo-600">{{ students.total || 0 }}</p>
    </div>
    </div>
      <div class="bg-white p-4 rounded-3xl shadow-sm border border-gray-100 mb-6">
        <div class="relative">
          <span class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
            <svg class="h-5 w-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
            </svg>
          </span>
          <input v-model="search" @input="getStudents(1)" type="text" 
            placeholder="Search by name, email or career..." 
            class="block w-full pl-11 pr-4 py-4 bg-gray-50 border-none rounded-2xl focus:ring-2 focus:ring-indigo-400 transition" />
        </div>
      </div>

      <div class="bg-white rounded-3xl shadow-sm border border-gray-100 overflow-hidden">
        <table class="w-full text-left border-collapse">
          <thead>
            <tr class="bg-gray-50/50 border-b border-gray-100">
              <th class="px-6 py-4 text-xs font-bold text-gray-400 uppercase tracking-widest">Full Name</th>
              <th class="px-6 py-4 text-xs font-bold text-gray-400 uppercase tracking-widest">Contact</th>
              <th class="px-6 py-4 text-xs font-bold text-gray-400 uppercase tracking-widest">Career & Semester</th>
              <th class="px-6 py-4 text-xs font-bold text-gray-400 uppercase tracking-widest text-right">Actions</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-gray-50">
  <template v-if="loading">
    <tr v-for="n in 5" :key="'skeleton-' + n" class="animate-pulse">
      <td class="px-6 py-5">
        <div class="h-4 bg-gray-200 rounded w-3/4 mb-2"></div>
        <div class="h-3 bg-gray-100 rounded w-1/2"></div>
      </td>
      <td class="px-6 py-5">
        <div class="h-4 bg-gray-200 rounded w-full"></div>
      </td>
      <td class="px-6 py-5">
        <div class="h-6 bg-indigo-50 rounded-full w-24"></div>
      </td>
      <td class="px-6 py-5">
        <div class="h-8 bg-gray-100 rounded-xl w-12 ml-auto"></div>
      </td>
    </tr>
  </template>

  <template v-else>
    <tr v-for="user in students.data" :key="user.id" class="hover:bg-indigo-50/30 transition">
     <td class="px-6 py-5">
    <div class="flex items-center space-x-4">
        <div class="h-12 w-12 flex-shrink-0 cursor-zoom-in" 
             @click="user.picture ? openImage(user.picture) : null">
            
            <img v-if="user.picture" 
                 :src="'/storage/' + user.picture" 
                 class="h-12 w-12 rounded-full object-cover border-2 border-white shadow-sm hover:scale-110 transition-transform" />
            
            <div v-else class="h-12 w-12 rounded-full bg-indigo-100 flex items-center justify-center border-2 border-white shadow-sm">
                <span class="text-indigo-600 font-black text-sm uppercase">
                    {{ user.name[0] }}{{ user.lastname[0] }}
                </span>
            </div>
        </div>

        <div>
            <div class="font-bold text-gray-800">{{ user.name }} {{ user.lastname }}</div>
            <div class="text-[10px] text-gray-400 font-black uppercase tracking-wider">
                SAGA: {{ user.student?.saga_code || 'N/A' }}
            </div>
        </div>
    </div>
</td>
      <td class="px-6 py-5 text-sm text-gray-600">{{ user.email }}</td>
      <td class="px-6 py-5">
        <span v-if="user.student?.career" class="px-3 py-1 bg-indigo-50 text-indigo-700 rounded-full text-xs font-bold mr-2">
          {{ user.student.career.name }}
        </span>
        <span class="text-xs text-gray-400">Sem. {{ user.student?.semester || 'N/A' }}</span>
      </td>
      <td class="px-6 py-5 text-right space-x-2">
        <router-link :to="{ name: 'students.edit', params: { id: user.id } }" 
          class="inline-flex p-2 text-indigo-600 hover:bg-indigo-100 rounded-xl transition">
          Edit
        </router-link>
        <button @click="confirmDelete(user.id)" 
          class="inline-flex p-2 text-red-600 hover:bg-red-50 rounded-xl transition">
          Delete
        </button>
      </td>
    </tr>

    <tr v-if="students.data.length === 0">
      <td colspan="4" class="px-6 py-10 text-center text-gray-400">No students found.</td>
    </tr>
  </template>
</tbody>
        </table>
      </div>

      <div class="mt-8 flex justify-center">
          <TailwindPagination :data="students" @pagination-change-page="getStudents" />
      </div>
    </div>
  </div>
  <Transition name="fade">
        <div v-if="selectedImage" 
             class="fixed inset-0 z-50 flex items-center justify-center bg-black/80 backdrop-blur-sm p-4"
             @click="closeImage">
            <div class="relative max-w-4xl w-full flex flex-col items-center">
                <button class="absolute -top-12 right-0 text-white hover:text-gray-300 text-4xl font-light">&times;</button>
                <img :src="selectedImage" 
                     class="max-h-[85vh] rounded-3xl shadow-2xl border-4 border-white/10 object-contain" 
                     @click.stop />
            </div>
        </div>
    </Transition>
</template>

<script setup>
import { ref, onMounted, watch } from "vue"; // Added watch
import axios from "axios";
import Swal from "sweetalert2";
import { TailwindPagination } from 'laravel-vue-pagination';
import debounce from 'lodash/debounce'; // Optional but recommended

const search = ref("");
const loading = ref(true); // Track loading state
const students = ref({ data: [], total: 0 });

const selectedImage = ref(null);

const openImage = (path) => {
    selectedImage.value = '/storage/' + path;
};  

const closeImage = () => {
    selectedImage.value = null;
};
/**
 * Fetch students from the API
 */
const getStudents = async (page = 1) => {
    loading.value = true;
    try {
        const response = await axios.get(`/api/students`, {
    params: { page, search: search.value, role: 'student' }
});
        // This now expects the paginated response from UserController@index
        students.value = response.data; 
    } catch (error) {
        console.error("Fetch Error:", error);
        Swal.fire('Error', 'Could not load student data', 'error');
    } finally {
        loading.value = false;
    }
};



/**
 * Debounced search: Waits 300ms after user stops typing 
 * to prevent excessive API calls.
 */
const performSearch = debounce(() => {
    getStudents(1);
}, 300);

/**
 * Delete a student (and their linked user account)
 */
const confirmDelete = async (id) => {
    const result = await Swal.fire({
        title: 'Are you sure?',
        text: "This will delete the user account and their student profile.",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#4f46e5',
        cancelButtonColor: '#ef4444',
        confirmButtonText: 'Yes, delete everything!'
    });

    if (result.isConfirmed) {
        try {
            await axios.delete(`/api/students/${id}`);
            // Refresh list
            getStudents(students.value.current_page);
            Swal.fire('Deleted!', 'Student record removed.', 'success');
        } catch (error) {
            Swal.fire('Error', 'Could not delete student.', 'error');
        }
    }
};

// Initial Load
onMounted(() => getStudents());

// Watch the search ref and trigger the debounced search
watch(search, () => {
    performSearch();
});
</script>
<style scoped>
.fade-enter-active, .fade-leave-active {
  transition: opacity 0.3s ease;
}
.fade-enter-from, .fade-leave-to {
  opacity: 0;
}
</style>