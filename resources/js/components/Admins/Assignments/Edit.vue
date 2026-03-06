Since the quiz_id is gone, the Edit.vue is likely crashing because it's still trying to "load" or "save" the quiz relationship. We need to strip it down so it only manages the active status and the student_id.

Here is the clean, updated Edit.vue to match your new database structure.

The Updated Assignments/Edit.vue
Fragmento de código
<template>
  <div class="min-h-screen bg-gray-50/50 py-10 px-4">
    <div class="max-w-2xl mx-auto">
      <div v-if="loading" class="text-center py-20">
        <div class="animate-spin inline-block w-8 h-8 border-4 border-indigo-600 border-t-transparent rounded-full mb-4"></div>
        <p class="text-gray-400 font-bold uppercase tracking-widest text-xs">Loading Status...</p>
      </div>

      <form v-else @submit.prevent="updateAssignment" class="bg-white p-8 rounded-3xl shadow-sm border border-gray-100">
        <div class="mb-8">
          <h2 class="text-2xl font-black text-gray-900">Manage Access</h2>
          <p class="text-sm text-gray-500 font-medium">Control if this student can start their random exam.</p>
        </div>

        <div class="space-y-6">
          <div class="p-6 bg-gray-50 rounded-3xl border border-gray-100">
            <div class="flex justify-between items-center">
              <div>
                <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1">Student</label>
                <div class="text-xl font-extrabold text-gray-800">{{ form.student?.name }} {{ form.student?.lastname }}</div>
                <div class="text-sm text-gray-500 font-medium">{{ form.student?.email }}</div>
              </div>
              <div class="text-right">
                <span class="px-3 py-1 bg-indigo-100 text-indigo-700 rounded-full text-[10px] font-black uppercase">
                  {{ form.student?.subject_name || 'General' }}
                </span>
              </div>
            </div>
          </div>

          <div class="flex items-center justify-between p-6 bg-white rounded-3xl border-2 border-gray-100 transition-all" 
               :class="form.active ? 'border-green-100 bg-green-50/20' : 'border-red-50 bg-red-50/10'">
            <div>
              <h3 class="font-bold text-gray-800">Status: {{ form.active ? 'Authorized' : 'Disabled' }}</h3>
              <p class="text-xs text-gray-500">
                {{ form.active ? 'The student can see and start a quiz.' : 'The student is blocked from entering.' }}
              </p>
            </div>
            <button 
              type="button"
              @click="form.active = form.active ? 0 : 1"
              :class="form.active ? 'bg-green-500 shadow-green-100' : 'bg-gray-300 shadow-none'"
              class="relative inline-flex h-6 w-11 items-center rounded-full transition-all duration-300"
            >
              <span :class="form.active ? 'translate-x-6' : 'translate-x-1'" class="inline-block h-4 w-4 transform rounded-full bg-white transition-all shadow-sm" />
            </button>
          </div>
        </div>

        <div class="mt-10 flex gap-4">
          <button type="submit" :disabled="processing" class="flex-1 bg-indigo-600 text-white py-4 rounded-2xl font-bold hover:bg-indigo-700 shadow-lg shadow-indigo-100 transition">
            {{ processing ? 'Updating...' : 'Save Changes' }}
          </button>
          <router-link :to="{ name: 'assignments.index' }" class="px-8 py-4 bg-gray-100 text-gray-500 rounded-2xl font-bold">
            Back
          </router-link>
        </div>
      </form>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import { useRoute, useRouter } from 'vue-router';
import axios from 'axios';
import Swal from 'sweetalert2';

const route = useRoute();
const router = useRouter();
const loading = ref(true);
const processing = ref(false);

const form = ref({
  id: null,
  student_id: null,
  active: 0,
  student: null // To store nested data
});

const fetchAssignment = async () => {
  try {
    const res = await axios.get(`/api/quiz-assignments/${route.params.id}`);
    const data = res.data.data || res.data;
    
    form.value.id = data.id;
    form.value.student_id = data.student_id;
    form.value.active = data.active;
    form.value.student = data.student;
  } catch (e) {
    Swal.fire('Error', 'Assignment not found', 'error');
    router.push({ name: 'assignments.index' });
  } finally {
    loading.value = false;
  }
};

const updateAssignment = async () => {
  processing.value = true;
  try {
    // ⚠️ We only send active and student_id. NO quiz_id!
    await axios.put(`/api/quiz-assignments/${route.params.id}`, {
      student_id: form.value.student_id,
      active: form.value.active
    });

    Swal.fire({
      icon: 'success',
      title: 'Updated',
      toast: true,
      position: 'top-end',
      timer: 2000,
      showConfirmButton: false
    });
    router.push({ name: 'assignments.index' });
  } catch (e) {
    Swal.fire('Error', 'Failed to update record', 'error');
  } finally {
    processing.value = false;
  }
};

onMounted(fetchAssignment);
</script>