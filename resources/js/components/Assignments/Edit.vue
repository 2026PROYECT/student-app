<template>
  <div class="max-w-2xl mx-auto p-8 bg-white rounded-3xl shadow-sm border border-gray-100">
    <!-- Header -->
    <div class="mb-6">
      <h1 class="text-3xl font-extrabold text-gray-900 tracking-tight">Edit Assignment</h1>
      <p class="text-gray-500 text-sm">Update the student or status for this assignment.</p>
    </div>

    <!-- Form -->
    <form @submit.prevent="updateAssignment" class="space-y-6">
      <!-- Student -->
      <div>
        <label class="block text-xs font-bold text-gray-400 uppercase tracking-widest mb-2">Student</label>
        <select v-model="form.student_id"
                class="w-full bg-gray-50 border-none rounded-2xl px-5 py-3 focus:ring-2 focus:ring-indigo-400 transition">
          <option value="">Select a Student</option>
          <option v-for="student in students" :key="student.id" :value="student.id">
            {{ student.name }} {{ student.lastname }}
          </option>
        </select>
        <div v-if="validationErrors?.student_id" class="text-red-500 text-xs mt-1">
          {{ validationErrors.student_id[0] }}
        </div>
      </div>

      <!-- Active -->
      <div class="flex items-center p-4 bg-gray-50 rounded-2xl">
        <input type="checkbox" id="active" v-model="form.active"
               :true-value="1" :false-value="0"
               class="w-5 h-5 text-indigo-600 rounded focus:ring-indigo-400" />
        <label for="active" class="ml-3 text-sm font-medium text-gray-700">Assignment is Active</label>
      </div>

      <!-- Actions -->
      <div class="flex gap-3">
        <button type="submit"
                class="flex-1 py-4 px-6 bg-indigo-600 text-white font-bold rounded-2xl shadow-lg shadow-indigo-200 hover:bg-indigo-700 transition disabled:opacity-50"
                :disabled="isLoading">
          {{ isLoading ? "Updating..." : "Update Assignment" }}
        </button>
        <button @click.prevent="$router.push({ name: 'assignments.index' })"
                class="px-6 py-4 bg-gray-100 text-gray-600 font-bold rounded-2xl hover:bg-gray-200 transition">
          Cancel
        </button>
      </div>
    </form>
  </div>
</template>

<script>
import axios from "axios";

export default {
  data() {
    return {
      form: { student_id: "", active: 1 },
      students: [],
      validationErrors: {},
      isLoading: false
    };
  },
  async mounted() {
    await this.fetchStudents();
    await this.fetchAssignment();
  },
  methods: {
    async fetchStudents() {
      try {
        const res = await axios.get(`/api/users`);
        this.students = (res.data.data || res.data).filter(u => u.role === 'student');
      } catch (error) {
        console.error("Error fetching students:", error);
      }
    },
    async fetchAssignment() {
      try {
        const id = this.$route.params.id;
        const res = await axios.get(`/api/quiz-assignments/${id}`);
        this.form.student_id = res.data.student_id;
        this.form.active = res.data.active;
      } catch (error) {
        console.error("Error fetching assignment:", error);
        alert("Could not load assignment data.");
      }
    },
    async updateAssignment() {
      this.isLoading = true;
      this.validationErrors = {};
      try {
        const id = this.$route.params.id;
        await axios.put(`/api/quiz-assignments/${id}`, this.form);
        this.$router.push({ name: "assignments.index" });
      } catch (error) {
        if (error.response && error.response.status === 422) {
          this.validationErrors = error.response.data.errors;
        } else {
          console.error("Update failed:", error);
        }
      } finally {
        this.isLoading = false;
      }
    }
  }
};
</script>

