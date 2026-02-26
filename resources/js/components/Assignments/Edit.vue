<template>
  <div class="max-w-2xl mx-auto p-6 bg-white rounded-3xl shadow-sm border border-gray-100">
    <div class="mb-6">
      <h1 class="text-2xl font-bold text-gray-800">Edit Assignment</h1>
      <p class="text-gray-400 text-sm">Update the status or student for this assignment.</p>
    </div>

    <form @submit.prevent="updateAssignment" class="space-y-6">
      <div>
        <label class="block text-sm font-bold text-gray-700 mb-2">Student</label>
        <select 
          v-model="form.student_id" 
          class="w-full p-3 bg-gray-50 border border-gray-200 rounded-xl focus:ring-2 focus:ring-indigo-500 outline-none transition"
        >
          <option value="">Select a Student</option>
          <option v-for="student in students" :key="student.id" :value="student.id">
            {{ student.name }} {{ student.lastname }}
          </option>
        </select>
      </div>

      <div class="flex items-center p-4 bg-gray-50 rounded-xl">
        <input 
          type="checkbox" 
          id="active"
          v-model="form.active" 
          :true-value="1" 
          :false-value="0"
          class="w-5 h-5 text-indigo-600 rounded focus:ring-indigo-500"
        />
        <label for="active" class="ml-3 font-medium text-gray-700">Assignment is Active</label>
      </div>

      <div class="flex gap-3">
        <button 
          type="submit" 
          class="flex-1 py-3 bg-indigo-600 text-white font-bold rounded-xl hover:bg-indigo-700 shadow-lg shadow-indigo-200 transition"
        >
          Update Assignment
        </button>
        <button 
          @click.prevent="$router.push({ name: 'assignments.index' })"
          class="px-6 py-3 bg-gray-100 text-gray-600 font-bold rounded-xl hover:bg-gray-200 transition"
        >
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
      form: {
        student_id: "",
        quiz_id: null,
        active: 1
      },
      students: []
    };
  },
  async mounted() {
    await this.fetchStudents();
    await this.fetchAssignment();
  },
  methods: {
    async fetchStudents() {
      try {
        const res = await axios.get("/api/students");
        this.students = res.data.data || res.data;
      } catch (error) {
        console.error("Error fetching students:", error);
      }
    },
    async fetchAssignment() {
      try {
        // Get the ID from the URL (route params)
        const id = this.$route.params.id;
        const res = await axios.get(`/api/quiz-assignments/${id}`);
        
        // Fill the form with existing data
        this.form.student_id = res.data.student_id;
        this.form.quiz_id = res.data.quiz_id;
        this.form.active = res.data.active;
      } catch (error) {
        console.error("Error fetching assignment:", error);
        alert("Could not load assignment data.");
      }
    },
    async updateAssignment() {
      try {
        const id = this.$route.params.id;
        // Use PUT to update the record
        await axios.put(`/api/quiz-assignments/${id}`, this.form);
        
        this.$router.push({ name: "assignments.index" });
      } catch (error) {
        if (error.response && error.response.status === 422) {
          const errorMessages = Object.values(error.response.data.errors).flat();
          alert(errorMessages[0]);
        } else {
          console.error("Update failed:", error);
        }
      }
    }
  }
};
</script>