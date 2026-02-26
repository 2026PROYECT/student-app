<template>
  <div>
    <h1 class="text-2xl font-bold mb-4">Edit Assignment</h1>

    <form @submit.prevent="updateAssignment" class="space-y-4">
      <!-- Student Selection -->
      <div>
        <label class="block text-sm font-medium text-gray-700">Student</label>
        <select v-model="form.student_id" class="mt-1 block w-full border rounded p-2">
          <option disabled value="">Select a student</option>
          <option v-for="student in students" :key="student.id" :value="student.id">
            {{ student.name }}
          </option>
        </select>
      </div>

      <!-- Submit Button -->
      <button
        type="submit"
        class="px-4 py-2 bg-indigo-600 text-white rounded hover:bg-indigo-700 transition"
      >
        Update Assignment
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
      },
      students: [],
    };
  },
  async mounted() {
    try {
      // Fetch students list
      const resStudents = await axios.get("/api/students");
      this.students = resStudents.data;

      // Fetch current assignment
      const resAssignment = await axios.get(`/api/quiz-assignments/${this.$route.params.id}`);
      this.form.student_id = resAssignment.data.student_id;
    } catch (error) {
      console.error("Error loading data:", error);
    }
  },
  methods: {
    async updateAssignment() {
      try {
        await axios.put(`/api/quiz-assignments/${this.$route.params.id}`, {
          student_id: this.form.student_id,
        });
        this.$router.push({ name: "assignments.index" });
      } catch (error) {
        console.error("Error updating assignment:", error);
      }
    },
  },
};
</script>