<template>



  <div>
    <h1 class="text-2xl font-bold mb-4">Students</h1>

    <!-- Add Student button -->
    <router-link
      :to="{ name: 'students.create' }"
      class="btn btn-primary"
    >
      Add Student
    </router-link>

    <!-- Search bar -->
    <div class="mt-4">
      <input
        v-model="search"
        @input="loadStudents(1)"
        type="text"
        placeholder="Search by name or email..."
        class="w-full border rounded p-2"
      />
    </div>

    <!-- Students list -->
    <table class="mt-6 w-full border-collapse border border-gray-300">
      <thead>
        <tr class="bg-gray-100">
          <th class="border border-gray-300 px-4 py-2">Name</th>
          <th class="border border-gray-300 px-4 py-2">Email</th>
          <th class="border border-gray-300 px-4 py-2">Career</th>
          <th class="border border-gray-300 px-4 py-2">Semester</th>
          <th class="border border-gray-300 px-4 py-2">Actions</th>
        </tr>
      </thead>
      <tbody>
        <tr v-for="student in students" :key="student.id">
          <td class="border border-gray-300 px-4 py-2">
            {{ student.name }} {{ student.lastname }} {{ student.surname }}
          </td>
          <td class="border border-gray-300 px-4 py-2">{{ student.email }}</td>
          <td class="border border-gray-300 px-4 py-2">{{ student.career }}</td>
          <td class="border border-gray-300 px-4 py-2">{{ student.semester }}</td>
          <td class="border border-gray-300 px-4 py-2 space-x-2">
            <router-link
              :to="{ name: 'students.edit', params: { id: student.id } }"
              class="btn btn-edit"
            >
              Edit
            </router-link>

            <button
              @click="deleteStudent(student.id)"
              class="btn btn-delete"
            >
              Delete
            </button>
          </td>
        </tr>
      </tbody>
    </table>

    <!-- Pagination -->
    <div v-if="pagination.links.length" class="flex justify-center mt-4 space-x-2">
      <button
        v-for="link in pagination.links"
        :key="link.label"
        :disabled="!link.url"
        @click="link.url ? loadStudents(getPageNumber(link.url)) : null"
        :class="[
          'btn',
          link.active ? 'btn-primary font-bold' : 'btn-primary opacity-70',
          !link.url ? 'btn-disabled' : ''
        ]"
        v-html="link.label"
      />
    </div>
  </div>
</template>

<script>
import axios from "axios";

export default {
  name: "StudentsIndex",
  data() {
    return {
      students: [],
      pagination: {
        links: [],
        current_page: 1,
        last_page: 1,
      },
      search: "",
    };
  },
  async mounted() {
    this.loadStudents();
  },
  methods: {
    async loadStudents(page = 1) {
      const response = await axios.get(`/api/students?page=${page}&search=${this.search}`);

      this.students = response.data.data;

      // Normalize links to always have { url, label, active }
      this.pagination = {
        current_page: response.data.current_page,
        last_page: response.data.last_page,
        links: (response.data.links || []).map(link => ({
          url: link.url || null,
          label: this.decodeHtml(link.label),
          active: link.active || false,
        })),
      };
    },
    getPageNumber(url) {
      if (!url) return this.pagination.current_page;
      try {
        const params = new URL(url).searchParams;
        return params.get("page");
      } catch {
        return this.pagination.current_page;
      }
    },
    decodeHtml(html) {
      const txt = document.createElement("textarea");
      txt.innerHTML = html;
      return txt.value;
    },
    async deleteStudent(id) {
      if (!confirm("Are you sure you want to delete this student?")) return;
      await axios.delete(`/api/students/${id}`);
      this.students = this.students.filter((s) => s.id !== id);
    },
  },
};
</script>
