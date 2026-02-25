<template>
  <div class="min-h-screen flex bg-gray-100">
    <!-- Sidebar -->
    <aside class="w-64 bg-white border-r border-gray-200 flex flex-col">
      <!-- Section Title -->
      <div class="px-4 py-2 text-xs font-bold text-gray-500 uppercase tracking-wide border-b">
        {{ user.isAdmin ? 'Admin Area' : 'Student Area' }}
      </div>

      <!-- Navigation Menu -->
      <nav class="flex-1 px-4 py-6 space-y-4">
        <!-- Quiz link -->
        <router-link :to="{ name: 'quiz.index' }" v-slot="{ isActive }">
          <span
            :class="[
              'flex px-4 py-2 text-sm font-medium rounded transition items-center space-x-2',
              isActive ? 'bg-indigo-600 text-white' : 'text-gray-900 hover:bg-gray-100'
            ]"
          >
            <ClipboardDocumentListIcon class="h-5 w-5 text-indigo-600" />
            <span>Quiz</span>
          </span>
        </router-link>

        <!-- Question link (only for admin) -->
        <router-link v-show="user.isAdmin" :to="{ name: 'question.index' }" v-slot="{ isActive }">
          <span
            :class="[
              'flex px-4 py-2 text-sm font-medium rounded transition items-center space-x-2',
              isActive ? 'bg-indigo-600 text-white' : 'text-gray-900 hover:bg-gray-100'
            ]"
          >
            <QuestionMarkCircleIcon class="h-5 w-5 text-indigo-600" />
            <span>Question</span>
          </span>
        </router-link>
        <!-- Students link (only for admin) -->
<router-link v-show="user.isAdmin" :to="{ name: 'students.index' }" v-slot="{ isActive }">
  <span
    :class="[
      'flex px-4 py-2 text-sm font-medium rounded transition items-center space-x-2',
      isActive ? 'bg-indigo-600 text-white' : 'text-gray-900 hover:bg-gray-100'
    ]"
  >
    <UserIcon class="h-5 w-5 text-indigo-600" />
    <span>Students</span>
  </span>
</router-link>


      </nav>
    </aside>

    <!-- Main Content -->
    <div class="flex-1 flex flex-col">
      <!-- Top Bar -->
      <header class="bg-white shadow px-6 py-4 flex justify-between items-center">
        <!-- Dynamic Page Title -->
        <h2 class="text-xl font-semibold text-gray-800">
          {{ currentPageTitle }}
        </h2>

        <!-- Login Info -->
        <div class="flex items-center">
          <div class="text-right">
            <div class="font-semibold text-gray-900">{{ user.name }}</div>
            <div class="text-sm text-gray-500">{{ user.email }}</div>
          </div>
          <button
            @click="logout"
            type="button"
            class="ml-4 px-4 py-2 text-xs font-semibold text-white uppercase bg-gray-800 rounded hover:bg-gray-700 transition"
            :class="{ 'opacity-25': processing }"
            :disabled="processing"
          >
            Log out
          </button>
        </div>
      </header>

      <!-- Page Content -->
      <main class="flex-1 p-6 overflow-y-auto">
        <router-view></router-view>
      </main>
    </div>
  </div>
</template>

<script setup>
import { computed, onMounted } from "vue";
import { useRoute } from "vue-router";
import useAuth from "@/composables/auth";

// Correct Heroicons imports (v2)
import { ClipboardDocumentListIcon, QuestionMarkCircleIcon } from "@heroicons/vue/24/outline";
import { UserIcon } from "@heroicons/vue/24/outline";

const route = useRoute();
const { user, processing, logout, getUser } = useAuth();

const currentPageTitle = computed(() => route.meta.title);

onMounted(() => {
  getUser();
});
</script>