<template>
  <div>
    <div class="flex flex-col md:flex-row justify-between items-center mb-6 gap-4">
      <h1 class="text-2xl font-bold text-gray-800">Quiz Assignments</h1>
      
      <div class="flex w-full md:w-auto gap-3">
        <div class="relative flex-1">
          <input 
            v-model="searchQuery" 
            @input="onSearch"
            type="text" 
            placeholder="Search student..." 
            class="pl-10 pr-4 py-2.5 border border-gray-200 rounded-xl focus:ring-2 focus:ring-indigo-500 outline-none w-full md:w-64 text-sm"
          />
          <span class="absolute left-3 top-3 text-gray-400">
            🔍
          </span>
        </div>

        <button
          class="px-5 py-2.5 bg-indigo-600 text-white font-semibold rounded-xl shadow-md hover:bg-indigo-700 transition-all flex items-center whitespace-nowrap"
          @click="goToCreate"
        >
          <span class="mr-2">+</span> New Assignment
        </button>
      </div>
    </div>

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
          <tr v-for="assignment in assignments" :key="assignment.id" class="hover:bg-gray-50/50 transition">
            <td class="px-6 py-4">
              <div class="font-bold text-gray-800">
                {{ assignment.student?.name }} {{ assignment.student?.lastname }}
              </div>
              <div class="text-xs text-gray-400">{{ assignment.student?.surname || '' }}</div>
            </td>

            <td class="px-6 py-4">
              <span 
                :class="assignment.active ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700'"
                class="px-3 py-1 rounded-full text-xs font-bold"
              >
                {{ assignment.active ? 'Active' : 'Inactive' }}
              </span>
            </td>

            <td class="px-6 py-4 text-sm text-gray-600">
              {{ formatDate(assignment.created_at) }}
            </td>

            <td class="px-6 py-4 text-sm">
              <div class="font-medium">{{ assignment.score ?? 'N/A' }} pts</div>
              <div class="text-xs text-gray-400">{{ assignment.finished ? '✅ Finished' : '⏳ Pending' }}</div>
            </td>

            <td class="px-6 py-4 text-right space-x-2">
              <button @click="editAssignment(assignment)" class="text-indigo-600 hover:text-indigo-900 font-bold text-sm">Edit</button>
              <button @click="deleteAssignment(assignment.id)" class="text-red-600 hover:text-red-900 font-bold text-sm">Delete</button>
            </td>
          </tr>
          <tr v-if="assignments.length === 0">
            <td colspan="5" class="px-6 py-10 text-center text-gray-400 italic">No assignments found.</td>
          </tr>
        </tbody>
      </table>

      <div v-if="meta.last_page > 1" class="bg-gray-50 px-6 py-4 border-t border-gray-100 flex justify-between items-center">
        <div class="text-sm text-gray-500">
          Showing page {{ meta.current_page }} of {{ meta.last_page }}
        </div>
        <div class="flex gap-2">
          <button 
            @click="getAssignments(meta.current_page - 1)"
            :disabled="meta.current_page === 1"
            class="px-4 py-2 bg-white border border-gray-200 rounded-lg text-sm font-medium disabled:opacity-50 hover:bg-gray-50"
          >
            Previous
          </button>
          <button 
            @click="getAssignments(meta.current_page + 1)"
            :disabled="meta.current_page === meta.last_page"
            class="px-4 py-2 bg-white border border-gray-200 rounded-lg text-sm font-medium disabled:opacity-50 hover:bg-gray-50"
          >
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
      meta: {
        current_page: 1,
        last_page: 1
      }
    };
  },
  async mounted() {
    this.getAssignments();
  },
  methods: {
    async getAssignments(page = 1) {
      try {
        const res = await axios.get("/api/quiz-assignments", {
          params: {
            page: page,
            search: this.searchQuery
          }
        });
        
        // Laravel's paginate() returns data in .data.data
        this.assignments = res.data.data;
        
        // Save the metadata for pagination buttons
        this.meta = {
          current_page: res.data.current_page,
          last_page: res.data.last_page
        };
      } catch (error) {
        console.error("Error fetching assignments:", error);
      }
    },

    onSearch() {
      // Debounce: Wait 500ms after typing stops before searching
      clearTimeout(this.searchTimeout);
      this.searchTimeout = setTimeout(() => {
        this.getAssignments(1); // Always reset to page 1 on new search
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