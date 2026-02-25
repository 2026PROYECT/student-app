<template>
  <div>
    <h1 class="text-2xl font-bold mb-4">Edit Student</h1>

    <form @submit.prevent="submit" class="space-y-4">
      <!-- Name -->
      <div>
        <label class="block font-medium">First Name</label>
        <input v-model="form.name" type="text" class="w-full border rounded p-2" />
        <div v-for="err in validationErrors?.name" class="text-red-600 text-sm">{{ err }}</div>
      </div>

      <!-- Email -->
      <div>
        <label class="block font-medium">Email</label>
        <input v-model="form.email" type="email" class="w-full border rounded p-2" />
        <div v-for="err in validationErrors?.email" class="text-red-600 text-sm">{{ err }}</div>
      </div>

      <!-- Role -->
      <div>
        <label class="block font-medium">Role</label>
        <select v-model="form.role" class="w-full border rounded p-2">
          <option disabled value="">-- Select Role --</option>
          <option value="admin">Admin</option>
          <option value="student">Student</option>
        </select>
        <div v-for="err in validationErrors?.role" class="text-red-600 text-sm">{{ err }}</div>
      </div>

      <!-- Picture -->
      <div>
        <label class="block font-medium">Picture</label>
        <input type="file" accept="image/*" @change="handlePicture" />
        <div v-if="picturePreview" class="mt-2">
          <img :src="picturePreview" class="w-32 h-32 object-cover rounded" />
        </div>
        <div v-for="err in validationErrors?.picture" class="text-red-600 text-sm">{{ err }}</div>
      </div>

      <!-- Career -->
      <div>
        <label class="block font-medium">Career</label>
        <input v-model="form.career" type="text" class="w-full border rounded p-2" />
      </div>

      <!-- Semester -->
      <div>
        <label class="block font-medium">Semester</label>
        <input v-model="form.semester" type="number" min="1" class="w-full border rounded p-2" />
      </div>

      <!-- Submit -->
      <div class="flex justify-center">
        <button
          :disabled="isLoading"
          class="btn btn-primary"
          :class="{ 'btn-disabled': isLoading }"
        >
          <span v-if="isLoading">Updating...</span>
          <span v-else>Update</span>
        </button>
      </div>
    </form>
  </div>
</template>

<script>
import { ref, reactive, onMounted } from "vue";
import axios from "axios";
import { useRoute } from "vue-router";

export default {
  name: "StudentsEdit",
  setup() {
    const route = useRoute();
    const isLoading = ref(false);
    const validationErrors = ref({});
    const picturePreview = ref(null);

    const form = reactive({
      id: null,
      name: "",
      email: "",
      role: "",
      picture: null,
      career: "",
      semester: 1,
    });

    const handlePicture = (event) => {
      const file = event.target.files[0];
      form.picture = file;
      picturePreview.value = URL.createObjectURL(file);
    };

    const loadStudent = async () => {
      const response = await axios.get(`/api/students/${route.params.id}`);
      Object.assign(form, response.data);
      form.id = response.data.id;
      if (response.data.picture) {
        picturePreview.value = `/storage/${response.data.picture}`;
      }
    };

    const submit = async () => {
      if (isLoading.value) return;
      isLoading.value = true;
      validationErrors.value = {};

      try {
        const formData = new FormData();
        Object.keys(form).forEach((key) => {
          formData.append(key, form[key]);
        });

        await axios.post(`/api/students/${form.id}?_method=PUT`, formData, {
          headers: { "Content-Type": "multipart/form-data" },
        });

        alert("Student updated successfully!");
      } catch (error) {
        if (error.response?.data?.errors) {
          validationErrors.value = error.response.data.errors;
        }
      } finally {
        isLoading.value = false;
      }
    };

    onMounted(loadStudent);

    return {
      form,
      isLoading,
      validationErrors,
      picturePreview,
      handlePicture,
      submit,
    };
  },
};
</script>