<template>
  <div>
    <h1 class="text-2xl font-bold mb-4">Add Student</h1>

    <form @submit.prevent="submit" class="space-y-4">
      <!-- Name -->
      <div>
        <label class="block font-medium">First Name</label>
        <input v-model="form.name" type="text" class="w-full border rounded p-2" />
        <div v-for="err in validationErrors?.name" class="text-red-600 text-sm">{{ err }}</div>
      </div>

      <!-- Lastname -->
      <div>
        <label class="block font-medium">Lastname</label>
        <input v-model="form.lastname" type="text" class="w-full border rounded p-2" />
        <div v-for="err in validationErrors?.lastname" class="text-red-600 text-sm">{{ err }}</div>
      </div>

      <!-- Surname -->
      <div>
        <label class="block font-medium">Surname</label>
        <input v-model="form.surname" type="text" class="w-full border rounded p-2" />
        <div v-for="err in validationErrors?.surname" class="text-red-600 text-sm">{{ err }}</div>
      </div>

      <!-- Email -->
      <div>
        <label class="block font-medium">Email</label>
        <input v-model="form.email" type="email" class="w-full border rounded p-2" />
        <div v-for="err in validationErrors?.email" class="text-red-600 text-sm">{{ err }}</div>
      </div>

      <!-- Password -->
      <div>
        <label class="block font-medium">Password</label>
        <input v-model="form.password" type="password" class="w-full border rounded p-2" />
        <div v-for="err in validationErrors?.password" class="text-red-600 text-sm">{{ err }}</div>
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
        <div v-for="err in validationErrors?.picture" class="text-red-600 text-sm">{{ err }}</div>
      </div>

      <!-- Saga Code -->
      <div>
        <label class="block font-medium">SAGA Code</label>
        <input v-model="form.saga_code" type="text" class="w-full border rounded p-2" />
      </div>

      <!-- ID Number -->
      <div>
        <label class="block font-medium">ID Number</label>
        <input v-model="form.id_number" type="text" class="w-full border rounded p-2" />
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
        type="submit"
        class="px-6 py-2 bg-indigo-600 text-white rounded hover:bg-indigo-700 transition disabled:opacity-50"
        :disabled="isLoading"
    >
        {{ isLoading ? "Saving..." : "Save Student" }}
    </button>
</div>

    </form>
  </div>
</template>

<script>
import { ref, reactive } from "vue";
import axios from "axios";

export default {
  name: "StudentsCreate",
  setup() {
    const isLoading = ref(false);
    const validationErrors = ref({});
    const form = reactive({
      name: "",
      lastname: "",
      surname: "",
      email: "",
      password: "",
      role: "",
      picture: null,
      saga_code: "",
      id_number: "",
      career: "",
      semester: 1,
    });

    const handlePicture = (event) => {
      form.picture = event.target.files[0];
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

        await axios.post("/api/students", formData, {
          headers: { "Content-Type": "multipart/form-data" },
        });

        alert("Student created successfully!");
      } catch (error) {
        if (error.response?.data?.errors) {
          validationErrors.value = error.response.data.errors;
        }
      } finally {
        isLoading.value = false;
      }
    };

    return {
      form,
      isLoading,
      validationErrors,
      handlePicture,
      submit,
    };
  },
};
</script>
