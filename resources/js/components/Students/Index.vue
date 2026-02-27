<template>
  <div class="min-h-screen bg-gray-50/50 py-10 px-4">
    <div class="max-w-6xl mx-auto">
      
      <!-- Header -->
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

      <!-- Stats Card -->
      <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
        <div class="bg-white p-6 rounded-3xl shadow-sm border border-gray-100">
          <p class="text-xs font-bold text-gray-400 uppercase tracking-widest">Total Students</p>
          <p class="text-2xl font-black text-indigo-600">{{ students.total || 0 }}</p>
        </div>
      </div>

      <!-- Search Card -->
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

      <!-- Table Card -->
      <div class="bg-white rounded-3xl shadow-sm border border-gray-100 overflow-hidden">
        <table class="w-full text-left border-collapse">
          <thead>
            <tr class="bg-gray-50/50 border-b border-gray-100">
              <th class="px-6 py-4 text-xs font-bold text-gray-400 uppercase tracking-widest">Full Name</th>
              <th class="px-6 py-4 text-xs font-bold text-gray-400 uppercase tracking-widest">Contact</th>
              <th class="px-6 py-4 text-xs font-bold text-gray-400 uppercase tracking-widest">Career & Semester</th>
              <th class="px-6 py-4 text-xs font-bold text-gray-400 uppercase tracking-widest">Picture</th>
              <th class="px-6 py-4 text-xs font-bold text-gray-400 uppercase tracking-widest text-right">Actions</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-gray-50">
            <tr v-for="student in students.data" :key="student.id" class="hover:bg-indigo-50/30 transition">
              <td class="px-6 py-5">
                <div class="font-bold text-gray-800">{{ student.name }} {{ student.lastname }}</div>
                <div class="text-xs text-gray-400">{{ student.surname || '' }}</div>
              </td>

              <td class="px-6 py-5 text-sm text-gray-600">
                {{ student.email }}
              </td>

              <td class="px-6 py-5">
                <span class="px-3 py-1 bg-indigo-50 text-indigo-700 rounded-full text-xs font-bold mr-2">
                  {{ student.career }}
                </span>
                <span class="text-xs text-gray-400">Sem. {{ student.semester }}</span>
              </td>

              <!-- Picture thumbnail -->
              <td class="px-6 py-5">
                <div class="h-10 w-10 flex-shrink-0 overflow-hidden rounded-xl border border-gray-100 shadow-sm">
                  <img v-if="student.picture"
                       :src="'/storage/' + student.picture"
                       @click="showImage('/storage/' + student.picture, student.name + ' ' + student.lastname)"
                       class="h-10 w-10 object-cover cursor-pointer hover:scale-110 transition-transform"
                       alt="Student Image" />
                </div>
              </td>

              <!-- Actions -->
              <td class="px-6 py-5 text-right space-x-2">
                <router-link :to="{ name: 'students.edit', params: { id: student.id } }"
                  class="inline-flex p-2 text-indigo-600 hover:bg-indigo-100 rounded-xl transition">
                  Edit
                </router-link>
                <button @click="confirmDelete(student.id)"
                  class="inline-flex p-2 text-red-600 hover:bg-red-50 rounded-xl transition">
                  Delete
                </button>
              </td>
            </tr>

            <tr v-if="students.data && students.data.length === 0">
              <td colspan="5" class="px-6 py-10 text-center text-gray-400">No students found.</td>
            </tr>
          </tbody>
        </table>
      </div>

      <!-- Pagination -->
      <div class="mt-8 flex justify-center">
        <TailwindPagination :data="students" @pagination-change-page="getStudents" />
      </div>
    </div>
  </div>
</template>


<script setup>
import { ref, onMounted } from "vue";
import axios from "axios";
import Swal from "sweetalert2";
import { TailwindPagination } from 'laravel-vue-pagination';


const search = ref("");

const students = ref({ data: [], total: 0 }); // Initialize with empty structure

const getStudents = async (page = 1) => {
    try {
        const response = await axios.get(`/api/students`, {
            params: { page, search: search.value }
        });
        // ✅ Save the WHOLE object (contains total, per_page, data, etc.)
        students.value = response.data; 
    } catch (error) {
        console.error("Error:", error);
    }
};

const showImage = (fullUrl, fullName) => {
    Swal.fire({
        title: fullName,
        imageUrl: fullUrl, // This will now receive '/storage/pictures/...'
        imageWidth: 400,
        imageHeight: 400,
        imageAlt: 'Student Profile',
        showConfirmButton: false,
        showCloseButton: true,
        customClass: {
            popup: 'rounded-[3rem]', 
            image: 'rounded-2xl object-cover'
        }
    });
};

const confirmDelete = async (id) => {
    const result = await Swal.fire({
        title: 'Delete Student?',
        text: "This action cannot be undone.",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#4f46e5',
        cancelButtonColor: '#ef4444',
        confirmButtonText: 'Yes, delete!'
    });

    if (result.isConfirmed) {
        await axios.delete(`/api/students/${id}`);
        getStudents();
        Swal.fire('Deleted!', 'Student record removed.', 'success');
    }
};

onMounted(() => getStudents());
</script>