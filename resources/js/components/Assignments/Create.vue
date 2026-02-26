<template>
  <div class="p-6 bg-white rounded-3xl shadow-sm">
    <h1 class="text-2xl font-bold mb-6">Assign Student</h1>

    <form @submit.prevent="createAssignment" class="space-y-4">
      <div>
        <label class="block text-sm font-medium text-gray-700">Student</label>
        <select v-model="form.student_id" class="mt-1 block w-full border-gray-300 rounded-xl p-3 bg-gray-50">
          <option value="">Select a Student</option>
          <option v-for="student in students" :key="student.id" :value="student.id">
            {{ student.name }} {{ student.lastname }}
          </option>
        </select>
      </div>

      <div class="flex items-center">
        <input type="checkbox" v-model="form.active" :true-value="1" :false-value="0" class="h-4 w-4 text-indigo-600 rounded" />
        <label class="ml-2 block text-sm text-gray-900">Mark as Active</label>
      </div>

      <button type="submit" class="w-full py-3 bg-indigo-600 text-white font-bold rounded-xl shadow-lg hover:bg-indigo-700 transition">
        Confirm Assignment
      </button>
    </form>
  </div>
</template>

<script>
import axios from "axios";

export default {
  data() {
    return {
      form: {
        student_id: "", 
        quiz_id: null,  // ✅ Always include the key, even if null
        active: 1,      
      },
      students: [], 
    };
  },
  async mounted() {
    try {
      const res = await axios.get("/api/students");
      // This handles both paginated and non-paginated responses
      this.students = res.data.data || res.data;
    } catch (error) {
      console.error("Error fetching students:", error);
    }
  },
  methods: {
    async createAssignment() {
      try {
        await axios.post("/api/quiz-assignments", this.form);
        this.$router.push({ name: "assignments.index" });
      } catch (error) {
        if (error.response && error.response.status === 422) {
          // This captures the 'unique' duplicate error from Laravel
          const errorMessages = Object.values(error.response.data.errors).flat();
          alert(errorMessages[0] || "This assignment already exists.");
        } else {
          console.error("Error creating assignment:", error);
          alert("A server error occurred. Please check the console.");
        }
      }
    }
  }
};
</script>