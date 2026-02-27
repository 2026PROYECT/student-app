<template>
  <div>
    <!-- Header -->
    <div class="flex flex-col md:flex-row justify-between items-center mb-8 gap-4">
      <h1 class="text-3xl font-extrabold text-gray-900 tracking-tight">Quiz Assignments</h1>
      
      <div class="flex w-full md:w-auto gap-3">
        <!-- Search -->
        <div class="relative flex-1">
          <input v-model="searchQuery" @input="onSearch" type="text"
                 placeholder="Search student..."
                 class="pl-10 pr-4 py-3 bg-gray-50 border-none rounded-2xl focus:ring-2 focus:ring-indigo-400 outline-none w-full md:w-64 text-sm transition" />
          <span class="absolute left-3 top-3 text-gray-400">🔍</span>
        </div>

        <!-- New Assignment -->
        <button @click="goToCreate"
                class="px-6 py-3 bg-indigo-600 text-white font-bold rounded-2xl shadow-lg shadow-indigo-200 hover:bg-indigo-700 transition-all flex items-center whitespace-nowrap">
          <span class="mr-2">+</span> New Assignment
        </button>
      </div>
    </div>

    <!-- Table Card -->
    <div class="bg-white rounded-3xl shadow-sm border border-gray-100 overflow-hidden">
      <table class="w-full text-left border-collapse">
        <thead>
          <tr class="bg-gray-50 border-b border-gray-100">
            <th class="px-6 py-4 text-xs font-bold text-gray-400 uppercase tracking-widest">Student Details</th>
            <th class="px-6 py-4 text-xs font-bold text-gray-400 uppercase tracking-widest">Status</th>
            <th class="px-6 py-4 text-xs font-bold text-gray-400 uppercase tracking-widest">Assigned Date</th>
            <th class="px-6 py-4 text-xs font-bold text-gray-400 uppercase tracking-widest">Score/Finished</th>
            <th class="px-6 py-4 text-xs font-bold text-gray-400 uppercase tracking-widest text-right">Actions</th>
          </tr>
        </thead>
        <tbody class="divide-y divide-gray-50">
          <tr v-for="assignment in assignments" :key="assignment.id" class="hover:bg-indigo-50/30 transition">
            <!-- Student -->
            <td class="px-6 py-5">
              <div class="font-bold text-gray-800">
                {{ assignment.student?.name }} {{ assignment.student?.lastname }}
              </div>
              <div class="text-xs text-gray-400">{{ assignment.student?.surname || '' }}</div>
            </td>

            <!-- Status -->
            <td class="px-6 py-5">
              <span :class="assignment.active ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700'"
                    class="px-3 py-1 rounded-full text-xs font-bold">
                {{ assignment.active ? 'Active' : 'Inactive' }}
              </span>
            </td>

            <!-- Date -->
            <td class="px-6 py-5 text-sm text-gray-600">
              {{ formatDate(assignment.created_at) }}
            </td>

            <!-- Score/Finished -->
            <td class="px-6 py-5 text-sm">
              <div class="font-medium">{{ assignment.score ?? 'N/A' }} pts</div>
              <div class="text-xs text-gray-400">{{ assignment.finished ? '✅ Finished' : '⏳ Pending' }}</div>
            </td>

            <!-- Actions -->
            <td class="px-6 py-5 text-right space-x-2">
              <button @click="editAssignment(assignment)"
                      class="inline-flex px-3 py-1 text-indigo-600 bg-indigo-50 hover:bg-indigo-100 rounded-xl text-xs font-bold transition">
                Edit
              </button>
              <button @click="deleteAssignment(assignment.id)"
                      class="inline-flex px-3 py-1 text-red-600 bg-red-50 hover:bg-red-100 rounded-xl text-xs font-bold transition">
                Delete
              </button>
            </td>
          </tr>

          <!-- Empty State -->
          <tr v-if="assignments.length === 0">
            <td colspan="5" class="px-6 py-10 text-center text-gray-400 italic">No assignments found.</td>
          </tr>
        </tbody>
      </table>

      <!-- Pagination -->
      <div v-if="meta.last_page > 1"
           class="bg-gray-50 px-6 py-4 border-t border-gray-100 flex justify-between items-center">
        <div class="text-sm text-gray-500">
          Showing page {{ meta.current_page }} of {{ meta.last_page }}
        </div>
        <div class="flex gap-2">
          <button @click="getAssignments(meta.current_page - 1)"
                  :disabled="meta.current_page === 1"
                  class="px-4 py-2 bg-indigo-50 text-indigo-700 rounded-xl text-sm font-bold disabled:opacity-50 hover:bg-indigo-100 transition">
            Previous
          </button>
          <button @click="getAssignments(meta.current_page + 1)"
                  :disabled="meta.current_page === meta.last_page"
                  class="px-4 py-2 bg-indigo-50 text-indigo-700 rounded-xl text-sm font-bold disabled:opacity-50 hover:bg-indigo-100 transition">
            Next
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import axios from "axios";
import Swal from "sweetalert2";

export default {
  data() {
    return {
      assignments: [],
      searchQuery: "",
      searchTimeout: null,
      meta: { current_page: 1, last_page: 1 }
    };
  },
  async mounted() {
    this.getAssignments();
  },
  methods: {
    async getAssignments(page = 1) {
      try {
        const res = await axios.get("/api/quiz-assignments", {
          params: { page, search: this.searchQuery }
        });
        this.assignments = res.data.data;
        this.meta = {
          current_page: res.data.current_page,
          last_page: res.data.last_page
        };
      } catch (error) {
        console.error("Error fetching assignments:", error);
      }
    },
    onSearch() {
      clearTimeout(this.searchTimeout);
      this.searchTimeout = setTimeout(() => {
        this.getAssignments(1);
      }, 500);
    },
    goToCreate() {
      this.$router.push({ name: "assignments.create" });
    },
    editAssignment(assignment) {
      this.$router.push({ name: "assignments.edit", params: { id: assignment.id } });
    },
    async deleteAssignment(id) {
      const result = await Swal.fire({
        title: 'Delete Assignment?',
        text: "This action cannot be undone.",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#4f46e5',
        confirmButtonText: 'Yes, delete it!'
      });
      if (result.isConfirmed) {
        try {
          await axios.delete(`/api/quiz-assignments/${id}`);
          this.getAssignments(this.meta.current_page);
          Swal.fire('Deleted!', 'Record removed.', 'success');
        } catch (e) {
          Swal.fire('Error', 'Could not delete record.', 'error');
        }
      }
    },
    formatDate(dateString) {
      if (!dateString) return "-";
      return new Date(dateString).toLocaleDateString();
    }
  }
};
</script>

