<template>
    <div class="flex justify-between p-6 align-items-center">
        <h4 class="text-lg font-bold">Quiz List</h4>
        <router-link
            v-show="user.isAdmin"
            :to="{ name: 'quiz.create' }"
            class="inline-flex items-center px-4 py-2 font-medium text-white rounded-lg bg-amber-400 hover:bg-amber-500"
        >
            Create Quiz
        </router-link>
    </div>

    <div class="p-6 overflow-hidden overflow-x-auto bg-white border-gray-200">
        <div class="min-w-full align-middle">
            <table class="min-w-full border divide-y divide-gray-200">
                <thead>
                    <tr>
                        <th class="px-6 py-3 text-left bg-gray-50">
                            <div class="flex flex-row items-center justify-between cursor-pointer" @click="updateOrdering('quiz_date')">
                                <div class="font-medium text-gray-500 uppercase" :class="{ 'font-bold text-blue-600': orderColumn === 'quiz_date' }">
                                    Quiz Date
                                </div>
                                <span>{{ orderColumn === 'quiz_date' ? (orderDirection === 'asc' ? '↑' : '↓') : '' }}</span>
                            </div>
                        </th>
                        <th class="px-6 py-3 text-left bg-gray-50">
                            <div class="flex flex-row items-center justify-between cursor-pointer" @click="updateOrdering('title')">
                                <div class="font-medium text-gray-500 uppercase" :class="{ 'font-bold text-blue-600': orderColumn === 'title' }">
                                    Quiz Title
                                </div>
                                <span>{{ orderColumn === 'title' ? (orderDirection === 'asc' ? '↑' : '↓') : '' }}</span>
                            </div>
                        </th>
                        <th class="px-6 py-3 text-left bg-gray-50">
                            <div class="flex flex-row items-center justify-between cursor-pointer" @click="updateOrdering('subject_name')">
                                <div class="font-medium text-gray-500 uppercase" :class="{ 'font-bold text-blue-600': orderColumn === 'subject_name' }">
                                    Subject
                                </div>
                                <span>{{ orderColumn === 'subject_name' ? (orderDirection === 'asc' ? '↑' : '↓') : '' }}</span>
                            </div>
                        </th>
                        <th class="px-6 py-3 text-left bg-gray-50 uppercase text-gray-500 font-medium">Total Mark</th>
                        <th class="px-6 py-3 text-left bg-gray-50 uppercase text-gray-500 font-medium">Pass Mark</th>
                        <th class="px-6 py-3 text-left bg-gray-50 uppercase text-gray-500 font-medium">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    <tr v-for="quiz in quizzes?.data" :key="quiz.id">
                        <td class="px-6 py-4 text-sm text-gray-900">{{ quiz.quiz_date }}</td>
                        <td class="px-6 py-4 text-sm text-gray-900">{{ quiz.title }}</td>
                        <td class="px-6 py-4 text-sm text-gray-900">{{ quiz.subject_name }}</td>
                        <td class="px-6 py-4 text-sm text-gray-900">{{ quiz.total_mark }}</td>
                        <td class="px-6 py-4 text-sm text-gray-900">{{ quiz.pass_mark }}</td>
                        <td class="px-6 py-4 text-sm">
                            <div class="flex space-x-2">
                                <router-link :to="{ name: 'attend-quiz', params: { quizId: quiz.id } }" class="px-2 py-1 text-white bg-amber-400 rounded-md text-xs">Attend</router-link>
                                <template v-if="user.isAdmin">
                                    <router-link :to="{ name: 'quiz.edit', params: { id: quiz.id } }" class="px-2 py-1 text-white bg-blue-400 rounded-md text-xs">Edit</router-link>
                                    <button @click="deleteQuiz(quiz.id)" class="px-2 py-1 text-white bg-red-400 rounded-md text-xs">Delete</button>
                                </template>
                            </div>
                        </td>
                    </tr>
                </tbody>
            </table>

            <div v-if="quizzes?.meta" class="mt-4">
                <TailwindPagination
                    :data="quizzes"
                    @pagination-change-page="getQuizzes"
                />
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, onMounted } from "vue";
import { TailwindPagination } from "laravel-vue-pagination";
import useQuiz from "@/composables/quiz"; 
import useAuth from "@/composables/auth";

const orderColumn = ref("quiz_date");
const orderDirection = ref("desc");

const { quizzes, getQuizzes, deleteQuiz } = useQuiz();
const { user, getUser } = useAuth();

const updateOrdering = (column) => {
    orderColumn.value = column;
    orderDirection.value = orderDirection.value === "asc" ? "desc" : "asc";
    getQuizzes(1, orderColumn.value, orderDirection.value);
};

onMounted(async () => {
    await getUser();
    await getQuizzes();
});
</script>