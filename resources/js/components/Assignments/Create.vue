<template>
  <div class="p-8 bg-white rounded-3xl shadow-sm border border-gray-100">
    <h1 class="text-2xl font-extrabold text-gray-900 mb-6">Assign Student</h1>

    <form @submit.prevent="createAssignment" class="space-y-6">
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
      <div class="flex items-center">
        <input type="checkbox" v-model="form.active" :true-value="1" :false-value="0"
               class="h-4 w-4 text-indigo-600 rounded focus:ring-indigo-400" />
        <label class="ml-2 block text-sm text-gray-900">Mark as Active</label>
      </div>

      <!-- Submit -->
      <button type="submit"
              class="w-full py-4 px-6 bg-indigo-600 text-white font-bold rounded-2xl shadow-lg shadow-indigo-200 hover:bg-indigo-700 transition disabled:opacity-50"
              :disabled="isLoading">
        {{ isLoading ? "Saving..." : "Confirm Assignment" }}
      </button>
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
    try {
      const res = await axios.get("/api/students");
      this.students = res.data.data || res.data;
    } catch (error) {
      console.error("Error fetching students:", error);
    }
  },
  methods: {
    async createAssignment() {
      this.isLoading = true;
      this.validationErrors = {};
      try {
        await axios.post("/api/quiz-assignments", this.form);
        this.$router.push({ name: "assignments.index" });
      } catch (error) {
        if (error.response && error.response.status === 422) {
          this.validationErrors = error.response.data.errors;
        } else {
          console.error("Error creating assignment:", error);
          alert("A server error occurred. Please check the console.");
        }
      } finally {
        this.isLoading = false;
      }
    }
  }
};
</script>
