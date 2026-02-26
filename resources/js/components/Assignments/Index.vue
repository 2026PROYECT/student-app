<template>
  <div>
    <div class="flex justify-between items-center mb-4">
      <h1 class="text-2xl font-bold">Quiz Assignments</h1>
      <!-- New Assignment Button -->
      <button
        class="px-4 py-2 bg-indigo-600 text-white rounded hover:bg-indigo-700 transition"
        @click="goToCreate"
      >
        + New Assignment
      </button>
    </div>

    <table class="min-w-full border">
      <thead>
        <tr class="bg-gray-100">
          <th class="p-2 border">Student</th>
          <th class="p-2 border">Quiz</th>
          <th class="p-2 border">Score</th>
          <th class="p-2 border">Finished</th>
          <th class="p-2 border">Actions</th>
        </tr>
      </thead>
      <tbody>
        <tr v-for="assignment in assignments" :key="assignment.id">
          <td class="border p-2">{{ assignment.student.name }}</td>
          <td class="border p-2">{{ assignment.quiz?.title || 'Not yet assigned' }}</td>
          <td class="border p-2">{{ assignment.score ?? '-' }}</td>
          <td class="border p-2">{{ assignment.finished ? 'Yes' : 'No' }}</td>
          <td class="border p-2">
            <button class="btn btn-edit mr-2" @click="editAssignment(assignment)">Edit</button>
            <button class="btn btn-delete" @click="deleteAssignment(assignment.id)">Delete</button>
          </td>
        </tr>
      </tbody>
    </table>
  </div>
</template>

<script>
import axios from "axios";

export default {
  data() {
    return { assignments: [] };
  },
  async mounted() {
    const res = await axios.get("/api/quiz-assignments");
    this.assignments = res.data.data; // paginated response
  },
  methods: {
    goToCreate() {
      this.$router.push({ name: "assignments.create" });
    },
    editAssignment(assignment) {
      this.$router.push({ name: "assignments.edit", params: { id: assignment.id } });
    },
    async deleteAssignment(id) {
      await axios.delete(`/api/quiz-assignments/${id}`);
      this.assignments = this.assignments.filter(a => a.id !== id);
    }
  }
};
</script>