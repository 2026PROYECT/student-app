<template>
    <div class="min-h-screen bg-gray-50/50 py-10 px-4">
        <div v-if="question.id" class="max-w-4xl mx-auto">
            
            <div class="flex items-center justify-between mb-8">
                <div>
                    <h1 class="text-3xl font-extrabold text-gray-900 tracking-tight">Update Question</h1>
                    <p class="text-gray-500 mt-1 text-sm">Modify the question details below.</p>
                </div>
                <button @click.prevent="goBack" class="flex items-center text-sm font-medium text-indigo-600 bg-indigo-50 px-4 py-2 rounded-2xl hover:bg-indigo-100 transition">
                    ← Back to Page {{ route.query.page || 1 }}
                </button>
            </div>

            <form @submit.prevent="updateQuestion(route.params.id, question)" class="space-y-6">
                
                <div class="bg-white p-8 rounded-3xl shadow-sm border border-gray-100 grid grid-cols-1 md:grid-cols-2 gap-8">
                    <div>
                        <label class="block text-xs font-bold text-gray-400 uppercase tracking-widest mb-3 ml-1">Select Quiz</label>
                        <select v-model="question.quiz_id" class="w-full bg-gray-50 border-none rounded-2xl px-5 py-4 focus:ring-2 focus:ring-indigo-400 transition">
                            <option value="">Choose a Quiz</option>
                            <option v-for="quiz in quizzes.data" :key="quiz.id" :value="quiz.id">{{ quiz.title }}</option>
                        </select>
                        <p v-if="validationErrors.quiz_id" class="text-red-500 text-xs mt-2 ml-1">{{ validationErrors.quiz_id[0] }}</p>
                    </div>

               <div class="col-span-1">
    <label class="block text-xs font-bold text-gray-400 uppercase tracking-widest mb-2 ml-1">Area</label>
    <select v-model="question.area" 
            class="w-full bg-gray-50 border-none rounded-2xl px-4 py-3 focus:ring-2 focus:ring-indigo-400 transition-all"
            :class="{'ring-2 ring-red-400': validationErrors.area}">
        <option value="" disabled>-- Choose Area --</option>
        <option value="L">Left (L)</option>
        <option value="R">Right (R)</option>
    </select>
    
    <p v-if="validationErrors.area" class="text-red-500 text-xs mt-1 ml-1">
        {{ validationErrors.area[0] }}
    </p>
</div>
                </div>

                <div class="bg-white p-8 rounded-3xl shadow-sm border border-gray-100 space-y-6">
    <div class="flex items-center mb-2">
        <label class="block text-xs font-bold text-gray-400 uppercase tracking-widest ml-1">Question Variations</label>
        <span class="ml-2 px-2 py-0.5 bg-gray-100 text-gray-500 text-[10px] rounded-full uppercase font-bold">Text Only</span>
    </div>

    <div class="relative">
        <span class="absolute -left-4 top-4 flex items-center justify-center w-8 h-8 bg-indigo-600 text-white rounded-full text-xs font-bold shadow-lg shadow-indigo-100">1</span>
        <textarea v-model="question.question1" rows="2" placeholder="Primary Question Text (Required)" 
            class="w-full bg-gray-50 border-none rounded-2xl px-8 py-4 focus:ring-2 focus:ring-indigo-400 transition"
            :class="{'ring-2 ring-red-400': validationErrors.question1}"></textarea>
        <p v-if="validationErrors.question1" class="text-red-500 text-[10px] mt-1 ml-4">{{ validationErrors.question1[0] }}</p>
    </div>

    <div class="relative">
        <span class="absolute -left-4 top-4 flex items-center justify-center w-8 h-8 bg-gray-200 text-gray-500 rounded-full text-xs font-bold">2</span>
        <textarea v-model="question.question2" rows="2" placeholder="Second Variation (Optional)" 
            class="w-full bg-gray-50 border-none rounded-2xl px-8 py-4 focus:ring-2 focus:ring-indigo-400 transition"></textarea>
    </div>

    <div class="relative">
        <span class="absolute -left-4 top-4 flex items-center justify-center w-8 h-8 bg-gray-200 text-gray-500 rounded-full text-xs font-bold">3</span>
        <textarea v-model="question.question3" rows="2" placeholder="Third Variation (Optional)" 
            class="w-full bg-gray-50 border-none rounded-2xl px-8 py-4 focus:ring-2 focus:ring-indigo-400 transition"></textarea>
    </div>
</div>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="bg-white p-8 rounded-3xl shadow-sm border border-gray-100">
                        <label class="block text-xs font-bold text-gray-400 uppercase tracking-widest mb-4 ml-1">Question Image</label>
                        
                        <div v-if="question.picture && typeof question.picture === 'string'" class="mb-4">
                            <img :src="'/storage/' + question.picture" class="h-32 w-full object-cover rounded-2xl border border-gray-100">
                            <p class="text-[10px] text-gray-400 mt-2 text-center uppercase tracking-tighter">Current Image</p>
                        </div>

                        <input type="file" @change="e => question.picture = e.target.files[0]" class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100">
                    </div>

                    <div class="bg-white p-8 rounded-3xl shadow-sm border border-gray-100">
                        <label class="block text-xs font-bold text-gray-400 uppercase tracking-widest mb-4 ml-1">Audio Clip</label>
                        
                        <div v-if="question.sound && typeof question.sound === 'string'" class="mb-4">
                            <audio controls :src="'/storage/' + question.sound" class="w-full h-10"></audio>
                            <p class="text-[10px] text-gray-400 mt-2 text-center uppercase tracking-tighter">Current Audio</p>
                        </div>

                        <input type="file" @change="e => question.sound = e.target.files[0]" class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100">
                    </div>
                </div>

                <div class="bg-white p-8 rounded-3xl shadow-sm border border-gray-100">
                    <label class="block text-xs font-bold text-gray-400 uppercase tracking-widest mb-6 ml-1">Answer Configuration</label>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-8">
                        <div v-for="opt in ['a', 'b', 'c', 'd']" :key="opt">
                            <input v-model="question['option_' + opt]" type="text" :placeholder="'Option ' + opt.toUpperCase()" class="w-full bg-gray-50 border-none rounded-2xl px-5 py-4 focus:ring-2 focus:ring-indigo-400 transition">
                        </div>
                    </div>
                    
                    <div>
                        <label class="block text-xs font-bold text-indigo-600 uppercase tracking-widest mb-3 ml-1">Correct Letter</label>
                        <select v-model="question.right_answer" class="w-full bg-indigo-50 border-none rounded-2xl px-5 py-4 focus:ring-2 focus:ring-indigo-400 font-bold text-indigo-700">
                            <option value="A">A</option>
                            <option value="B">B</option>
                            <option value="C">C</option>
                            <option value="D">D</option>
                        </select>
                    </div>
                </div>

                <button :disabled="isLoading" class="group relative w-full flex justify-center py-5 px-4 border border-transparent text-lg font-bold rounded-3xl text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-all shadow-xl shadow-indigo-200 disabled:opacity-50">
                    <span v-if="isLoading">Updating Question...</span>
                    <span v-else>Apply Changes</span>
                </button>
            </form>
        </div>

        <div v-else class="flex flex-col justify-center items-center h-96">
            <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-indigo-600 mb-4"></div>
            <p class="text-gray-400 font-medium">Fetching question data...</p>
        </div>
    </div>
</template>

<script setup>
import { onMounted } from 'vue';
import { useRoute, useRouter } from 'vue-router';
import useQuiz from '@/composables/quiz';
import useQuestion from '@/composables/question';

const route = useRoute();
const router = useRouter();

const { quizzes, getQuizzes } = useQuiz();
const { question, getQuestion, updateQuestion, isLoading, validationErrors } = useQuestion();

onMounted(async () => {
    await getQuizzes();
    await getQuestion(route.params.id);
});

const goBack = () => {
    router.push({ name: 'question.index', query: { page: route.query.page || 1 } });
};
</script>