<template>
  <div class="min-h-screen bg-gray-50/50 py-10 px-4">
    <div class="max-w-6xl mx-auto">
      <div class="flex flex-col md:flex-row md:items-center justify-between mb-8 gap-4">
        <div>
          <h1 class="text-3xl font-extrabold text-gray-900 tracking-tight">Active Exam List</h1>
          <p class="text-gray-500 mt-1">Showing students currently assigned for a random quiz.</p>
        </div>
        <router-link :to="{ name: 'assignments.create' }" 
          class="inline-flex items-center justify-center px-6 py-3 bg-indigo-600 text-white font-bold rounded-2xl shadow-lg shadow-indigo-200 hover:bg-indigo-700 transition-all">
          + Add Student to List
        </router-link>
      </div>

      <div class="mb-6">
        <input 
          v-model="searchQuery"
          type="text" 
          placeholder="Filter active assignments..." 
          class="w-full px-6 py-4 bg-white border-none rounded-3xl shadow-sm focus:ring-2 focus:ring-indigo-400 font-bold text-gray-700 transition-all"
        />
      </div>

      <div class="bg-white rounded-3xl shadow-sm border border-gray-100 overflow-hidden">
        <table class="w-full text-left border-collapse">
          <thead>
            <tr class="bg-gray-50/50 border-b border-gray-100">
              <th class="px-6 py-4 text-xs font-bold text-gray-400 uppercase tracking-widest">Student</th>
              <th class="px-6 py-4 text-xs font-bold text-gray-400 uppercase tracking-widest text-center">Status</th>
              <th class="px-6 py-4 text-xs font-bold text-gray-400 uppercase tracking-widest text-right">Actions</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-gray-50">
            <tr v-for="item in filteredAssignments" :key="item.id" class="hover:bg-indigo-50/30 transition">
              <td class="px-6 py-5">
                <div class="font-bold text-gray-800">{{ item.student?.name }} {{ item.student?.lastname }}</div>
                <div class="text-[10px] text-gray-400 font-medium">{{ item.student?.email }}</div>
              </td>
              <td class="px-6 py-5 text-center">
                <span :class="item.active ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700'" 
                      class="px-3 py-1 rounded-full text-[10px] font-black uppercase">
                  {{ item.active ? 'Active' : 'Disabled' }}
                </span>
              </td>
              <td class="px-6 py-5 text-right space-x-3">
                <router-link :to="{ name: 'assignments.edit', params: { id: item.id } }" 
                  class="text-indigo-600 font-bold text-sm hover:underline">Edit Status</router-link>
                <button @click="deleteAssignment(item.id)" class="text-red-400 font-bold text-sm hover:underline">Remove</button>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue';
import axios from 'axios';
import Swal from 'sweetalert2';

const assignments = ref([]);
const searchQuery = ref('');

const filteredAssignments = computed(() => {
  if (!searchQuery.value) return assignments.value;
  const q = searchQuery.value.toLowerCase();
  return assignments.value.filter(item => 
    item.student?.name?.toLowerCase().includes(q) || 
    item.student?.lastname?.toLowerCase().includes(q)
  );
});

const fetchAssignments = async () => {
  const res = await axios.get('/api/quiz-assignments');
  assignments.value = res.data.data?.data || res.data.data || res.data;
};

const deleteAssignment = async (id) => {
  const result = await Swal.fire({
    title: 'Remove from list?',
    text: "This student will no longer be eligible for the random quiz.",
    icon: 'warning',
    showCancelButton: true,
    confirmButtonText: 'Yes, remove'
  });

  if (result.isConfirmed) {
    await axios.delete(`/api/quiz-assignments/${id}`);
    fetchAssignments();
  }
};

onMounted(fetchAssignments);
</script>