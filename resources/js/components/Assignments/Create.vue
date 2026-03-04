<template>
  <div class="min-h-screen bg-gray-50/50 py-10 px-4">
    <div class="max-w-2xl mx-auto">
      <form @submit.prevent="saveAssignment" class="bg-white p-8 rounded-3xl shadow-sm border border-gray-100">
        <div class="mb-8">
          <h2 class="text-2xl font-black text-gray-900">Activate Student</h2>
          <p class="text-sm text-gray-500 font-medium">Find a student to authorize them for an exam session.</p>
        </div>

        <div class="space-y-6">
          <div>
            <label class="block text-xs font-black text-gray-400 uppercase mb-2 ml-1">Search Roster</label>
            <div class="relative mb-3">
              <span class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                <svg class="h-4 w-4 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                </svg>
              </span>
              <input 
                v-model="studentSearch" 
                type="text" 
                placeholder="Type name or email..." 
                class="form-input-custom pl-11"
              />
            </div>
            
            <div class="max-h-60 overflow-y-auto rounded-2xl border border-gray-100 bg-gray-50/30 p-2 custom-scrollbar">
              <div v-if="filteredStudents.length === 0" class="p-8 text-center">
                <p class="text-xs text-gray-400 font-bold uppercase tracking-widest">No students found</p>
              </div>
              
              <div 
                v-for="s in filteredStudents" 
                :key="s.id" 
                @click="form.student_id = s.id"
                :class="form.student_id === s.id ? 'bg-indigo-600 text-white shadow-md' : 'hover:bg-indigo-50 text-gray-700'"
                class="p-4 rounded-xl cursor-pointer transition-all mb-1 flex justify-between items-center group"
              >
                <div>
                  <div class="font-bold leading-tight">{{ s.name }} {{ s.lastname }}</div>
                  <div class="text-[10px] opacity-70 font-medium">{{ s.email }}</div>
                </div>
                <div v-if="form.student_id === s.id" class="animate-pulse">
                  <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                  </svg>
                </div>
              </div>
            </div>
          </div>

          <div class="flex items-center justify-between p-6 bg-indigo-50/50 rounded-3xl border border-indigo-100">
            <div>
              <label class="block text-xs font-black text-indigo-400 uppercase mb-1 tracking-widest">Initial Status</label>
              <p class="text-sm font-bold text-gray-700">
                {{ form.active ? 'Available for exam' : 'Inactive / Locked' }}
              </p>
            </div>
            <button 
              type="button"
              @click="form.active = form.active ? 0 : 1"
              :class="form.active ? 'bg-green-500 shadow-green-100' : 'bg-gray-300 shadow-none'"
              class="relative inline-flex h-6 w-11 items-center rounded-full transition-all duration-300 shadow-lg"
            >
              <span :class="form.active ? 'translate-x-6' : 'translate-x-1'" class="inline-block h-4 w-4 transform rounded-full bg-white transition-all shadow-sm" />
            </button>
          </div>
        </div>

        <div class="mt-10 flex gap-4">
          <button 
            type="submit" 
            :disabled="!form.student_id || processing" 
            class="flex-1 bg-indigo-600 text-white py-4 rounded-2xl font-bold hover:bg-indigo-700 shadow-lg shadow-indigo-100 transition disabled:opacity-50 active:scale-95"
          >
            {{ processing ? 'Activating...' : 'Confirm Activation' }}
          </button>
          <router-link :to="{ name: 'assignments.index' }" class="px-8 py-4 bg-gray-100 text-gray-500 rounded-2xl font-bold hover:bg-gray-200 transition">
            Cancel
          </router-link>
        </div>
      </form>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue';
import { useRouter } from 'vue-router';
import axios from 'axios';
import Swal from 'sweetalert2';

const router = useRouter();
const students = ref([]);
const studentSearch = ref('');
const processing = ref(false);

const form = ref({
  student_id: '',
  active: 1
  // quiz_id is no longer here
});

const filteredStudents = computed(() => {
  if (!studentSearch.value) return students.value;
  const q = studentSearch.value.toLowerCase();
  return students.value.filter(s => 
    s.name?.toLowerCase().includes(q) || 
    s.lastname?.toLowerCase().includes(q) ||
    s.email?.toLowerCase().includes(q)
  );
});

onMounted(async () => {
  try {
    const res = await axios.get('/api/students');
    // Using defensive logic to handle ResponseSuccess or plain arrays
    students.value = res.data.data?.data || res.data.data || res.data;
  } catch (e) {
    Swal.fire('Error', 'Could not load student list', 'error');
  }
});

const saveAssignment = async () => {
  processing.value = true;
  try {
    await axios.post('/api/quiz-assignments', form.value);
    
    Swal.fire({
      icon: 'success',
      title: 'Success!',
      text: 'Student has been added to the active list.',
      timer: 2000,
      showConfirmButton: false
    });
    
    router.push({ name: 'assignments.index' });
  } catch (e) {
    // If backend returns a 422 (Unique constraint), show the error
    if (e.response?.status === 422) {
      Swal.fire('Already Exists', 'This student is already on the active list.', 'warning');
    } else {
      Swal.fire('Database Error', 'Check if the quiz_id column was successfully removed.', 'error');
    }
  } finally {
    processing.value = false;
  }
};
</script>

<style scoped>
.form-input-custom {
  @apply w-full border-none bg-gray-50 p-4 rounded-2xl focus:ring-2 focus:ring-indigo-400 font-bold text-gray-700 transition-all;
}
.custom-scrollbar::-webkit-scrollbar {
  width: 4px;
}
.custom-scrollbar::-webkit-scrollbar-thumb {
  background: #e2e8f0;
  border-radius: 10px;
}
</style>