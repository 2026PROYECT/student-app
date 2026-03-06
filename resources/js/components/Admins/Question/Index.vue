<template>
  <div class="flex justify-between p-6 align-items-center">
    <h4 class="text-lg font-bold">Question List</h4>
    <router-link
      :to="{ name: 'question.create' }"
      active-class="border-b-2 border-indigo-400"
      class="inline-flex items-center px-4 py-2 font-medium leading-5 text-white transition duration-150 ease-in-out rounded-lg bg-amber-400 focus:outline-none focus:border-indigo-700"
    >
      Create Question
    </router-link>
  </div>

  <div class="p-6 overflow-hidden overflow-x-auto bg-white border-gray-200">
    <div class="min-w-full align-middle">
      <table class="min-w-full border divide-y divide-gray-200">
        <thead>
          <tr>
            <th class="px-6 py-3 text-left bg-gray-50">Quiz Name</th>
            <th class="px-6 py-3 text-left bg-gray-50">Question</th>
            <th class="px-6 py-3 text-left bg-gray-50">Correct Answer</th>
            <th class="px-6 py-3 text-left bg-gray-50">Options</th>
            <th class="px-6 py-3 text-left bg-gray-50">Created At</th>
            <th class="px-6 py-3 text-left bg-gray-50">Actions</th>
          </tr>
        </thead>
        <tbody class="bg-white divide-y divide-gray-200 divide-solid">
          <tr v-for="question in questions?.data" :key="question?.id">
            <td class="px-6 py-4 text-sm leading-5 text-gray-900 whitespace-no-wrap">
              {{ question.quiz?.title }}
            </td>
            <td class="px-6 py-4 text-sm leading-5 text-gray-900 whitespace-no-wrap">
              {{ question.question1 }}
            </td>
            <td class="px-6 py-4 text-sm leading-5 text-gray-900 whitespace-no-wrap">
              {{ question.right_answer }}
            </td>
            <td class="px-6 py-4 text-sm leading-5 text-gray-900 whitespace-no-wrap">
              A: {{ question.option_a }} |
              B: {{ question.option_b }} |
              C: {{ question.option_c }} |
              D: {{ question.option_d }}
            </td>
            <td class="px-6 py-4 text-sm leading-5 text-gray-900 whitespace-no-wrap">
              {{ question.created_at }}
            </td>
            <td class="px-6 py-4 text-sm leading-5 text-gray-900 whitespace-no-wrap">
<router-link
  :to="{ 
    name: 'question.edit', 
    params: { id: question.id }, 
    query: { page: questions.meta?.current_page } 
  }"
  class="text-indigo-600 hover:text-indigo-900"
>
  Edit
</router-link>
              <a
                href="#"
                @click.prevent="deleteQuestion(question.id)"
                class="ml-2 text-red-600"
              >
                Delete
              </a>
            </td>
          </tr>
        </tbody>
      </table>

      <TailwindPagination
        :data="questions"
        @pagination-change-page="getQuestions"
        class="mt-4"
      />
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from "vue";
import { TailwindPagination } from "laravel-vue-pagination";
import useQuestion from "@/composables/question";

const { questions, getQuestions, deleteQuestion } = useQuestion();

onMounted(() => {
  getQuestions();
});
</script>