<template>
  <div class="min-h-screen flex bg-gray-100">
    <aside class="w-64 bg-white border-r border-gray-200 flex flex-col">
      <div class="px-4 py-4 text-xs font-bold text-gray-400 uppercase tracking-widest border-b bg-gray-50/50">
        {{ user.role === 'admin' ? 'Admin Control Panel' : 'Student Portal' }}
      </div>

      <nav class="flex-1 px-4 py-6 space-y-2">
  
  <template v-if="user.role === 'admin'">
    <router-link :to="{ name: 'quiz.index' }" v-slot="{ isActive }">
      <span :class="[navClass(isActive)]">
        <ClipboardDocumentListIcon class="h-5 w-5" :class="isActive ? 'text-white' : 'text-indigo-600'" />
        <span>Manage Quizzes</span>
      </span>
    </router-link>

    <router-link :to="{ name: 'question.index' }" v-slot="{ isActive }">
      <span :class="[navClass(isActive)]">
        <QuestionMarkCircleIcon class="h-5 w-5" :class="isActive ? 'text-white' : 'text-indigo-600'" />
        <span>Questions</span>
      </span>
    </router-link>

    <router-link :to="{ name: 'students.index' }" v-slot="{ isActive }">
      <span :class="[navClass(isActive)]">
        <UserGroupIcon class="h-5 w-5" :class="isActive ? 'text-white' : 'text-indigo-600'" />
        <span>Students</span>
      </span>
    </router-link>

    <router-link :to="{ name: 'admins.index' }" v-slot="{ isActive }">
      <span :class="[navClass(isActive)]">
        <ShieldCheckIcon class="h-5 w-5" :class="isActive ? 'text-white' : 'text-indigo-600'" />
        <span>Admins</span>
      </span>
    </router-link>

    <router-link :to="{ name: 'assignments.index' }" v-slot="{ isActive }">
      <span :class="[navClass(isActive)]">
        <ClipboardDocumentCheckIcon class="h-5 w-5" :class="isActive ? 'text-white' : 'text-indigo-600'" />
        <span>Assignments</span>
      </span>
    </router-link>
  </template>

  <template v-if="user.role === 'student'">
    <router-link :to="{ name: 'attend.index' }" v-slot="{ isActive }">
      <span :class="[navClass(isActive)]">
        <ClipboardDocumentCheckIcon class="h-5 w-5" :class="isActive ? 'text-white' : 'text-indigo-600'" />
        <span>My Exam Portal</span>
      </span>
    </router-link>
    
    <router-link :to="{ name: 'results.index' }" v-slot="{ isActive }">
      <span :class="[navClass(isActive)]">
        <ClipboardDocumentListIcon class="h-5 w-5" :class="isActive ? 'text-white' : 'text-indigo-600'" />
        <span>My Results</span>
      </span>
    </router-link>
  </template>

</nav>
    </aside>

    <div class="flex-1 flex flex-col">
      <header class="bg-white border-b border-gray-100 px-8 py-4 flex justify-between items-center">
        <h2 class="text-xl font-bold text-gray-800 tracking-tight">
          {{ currentPageTitle }}
        </h2>

        <div class="flex items-center space-x-6">
          <div class="text-right border-r pr-6 border-gray-100">
            <div class="font-bold text-gray-900 leading-none mb-1">{{ user.name }} {{ user.lastname }}</div>
            <div class="text-xs font-medium text-gray-400 uppercase tracking-tighter">{{ user.role }}</div>
          </div>
          <button
            @click="logout"
            class="px-5 py-2 text-xs font-black text-white uppercase bg-red-500 rounded-xl hover:bg-red-600 shadow-lg shadow-red-100 transition-all active:scale-95 disabled:opacity-50"
            :disabled="processing"
          >
            {{ processing ? '...' : 'Log out' }}
          </button>
        </div>
      </header>

      <main class="flex-1 p-8 overflow-y-auto">
        <router-view></router-view>
      </main>
    </div>
  </div>
</template>

<script setup>
import { computed, onMounted } from "vue";
import { useRoute } from "vue-router";
import useAuth from "@/composables/auth";

// Icons
import { 
  ClipboardDocumentListIcon, 
  QuestionMarkCircleIcon, 
  UserGroupIcon, 
  ShieldCheckIcon,
  ClipboardDocumentCheckIcon 
} from "@heroicons/vue/24/outline";

const route = useRoute();
const { user, processing, logout, getUser } = useAuth();

const currentPageTitle = computed(() => route.meta.title || 'Dashboard');

// Helper function for cleaner classes
const navClass = (isActive) => [
  'flex px-4 py-3 text-sm font-bold rounded-2xl transition-all items-center space-x-3',
  isActive 
    ? 'bg-indigo-600 text-white shadow-lg shadow-indigo-100' 
    : 'text-gray-500 hover:bg-gray-50 hover:text-indigo-600'
];

onMounted(() => {
  getUser();
});
</script>