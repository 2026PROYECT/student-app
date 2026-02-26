<template>
  <div>
    <h1 class="text-2xl font-bold mb-4">Add Assignment</h1>

    <form @submit.prevent="createAssignment" class="space-y-4">
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

      <!-- Finished Flag -->
      <div>
        <label class="inline-flex items-center">
          <input type="checkbox" v-model="form.finished" class="mr-2" />
          Finished
        </label>
      </div>

      <!-- Submit Button -->
      <button
        type="submit"
        class="px-4 py-2 bg-indigo-600 text-white rounded hover:bg-indigo-700 transition"
      >
        Save Assignment
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
        finished: false,
        score: null, // leave null until quiz is taken
      },
      students: [],
    };
  },
  async mounted() {
    try {
      const res = await axios.get("/api/students");
      this.students = res.data; // should be an array of student users
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
        console.error("Error creating assignment:", error);
      }
    },
  },
};
</script>
