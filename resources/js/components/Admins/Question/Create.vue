<template>
  <div class="min-h-screen bg-gray-50/50 py-10 px-4">
    <div class="max-w-3xl mx-auto">
      
      <div class="flex items-center justify-between mb-8">
        <div>
          <h1 class="text-2xl font-bold text-gray-800">Add New Question</h1>
          <p class="text-gray-500 text-sm">Fill in the details to expand your quiz database.</p>
        </div>
        <div class="h-12 w-12 bg-indigo-100 rounded-2xl flex items-center justify-center text-indigo-600">
          <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
            
          </svg>
        </div>
      </div>

      <form @submit.prevent="storeQuestion(question)" class="space-y-6">
        
        <div class="bg-white p-6 rounded-3xl shadow-sm border border-gray-100 grid grid-cols-1 md:grid-cols-2 gap-6">
          <div class="col-span-1">
            <label class="block text-xs font-bold text-gray-400 uppercase tracking-widest mb-2 ml-1">Select Quiz</label>
            <select v-model="question.quiz_id" class="w-full bg-gray-50 border-none rounded-2xl px-4 py-3 focus:ring-2 focus:ring-indigo-400 transition-all">
              <option disabled value="">-- Choose Quiz --</option>
              <option v-for="quiz in quizzes.data" :key="quiz.id" :value="quiz.id">{{ quiz.title }}</option>
            </select>
            <p v-if="validationErrors?.quiz_id" class="mt-1 text-xs text-red-500">{{ validationErrors.quiz_id[0] }}</p>
          </div>

          <div class="col-span-1">
            <label class="block text-xs font-bold text-gray-400 uppercase tracking-widest mb-2 ml-1">Area</label>
            <select v-model="question.area" class="w-full bg-gray-50 border-none rounded-2xl px-4 py-3 focus:ring-2 focus:ring-indigo-400 transition-all">
              <option disabled value="">-- Choose Area --</option>
              <option value="L">Left (L)</option>
              <option value="R">Right (R)</option>
            </select>
          </div>
        </div>

        <div class="bg-white p-6 rounded-3xl shadow-sm border border-gray-100 space-y-4">
          <label class="block text-xs font-bold text-gray-400 uppercase tracking-widest mb-2 ml-1">Question Content</label>
          <div v-for="i in [1, 2, 3]" :key="i">
            <textarea 
              v-model="question['question' + i]" 
              :placeholder="'Question Variation ' + i + '...'"
              class="w-full bg-gray-50 border-none rounded-2xl px-4 py-3 focus:ring-2 focus:ring-indigo-400 transition-all mb-2"
              rows="2"
            ></textarea>
            <p v-if="validationErrors?.['question' + i]" class="text-xs text-red-500 mb-2">{{ validationErrors['question' + i][0] }}</p>
          </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
          <div class="bg-white p-6 rounded-3xl shadow-sm border border-gray-100">
            <label class="block text-xs font-bold text-gray-400 uppercase tracking-widest mb-4 ml-1">Visual Aid</label>
            <div class="flex flex-col items-center justify-center border-2 border-dashed border-gray-200 rounded-2xl p-4 hover:border-indigo-300 transition-colors relative">
              <input type="file" accept="image/*" @change="handlePicture" class="absolute inset-0 opacity-0 cursor-pointer" />
              <div v-if="!picturePreview" class="text-center">
                <div class="text-gray-400 mb-2">📸 Click to upload image</div>
              </div>
              <img v-else :src="picturePreview" class="max-h-32 rounded-lg shadow-sm" />
            </div>
          </div>

          <div class="bg-white p-6 rounded-3xl shadow-sm border border-gray-100">
            <label class="block text-xs font-bold text-gray-400 uppercase tracking-widest mb-4 ml-1">Audio Aid</label>
            <div class="flex flex-col items-center justify-center border-2 border-dashed border-gray-200 rounded-2xl p-4 hover:border-indigo-300 transition-colors relative">
              <input type="file" accept="audio/*" @change="handleSound" class="absolute inset-0 opacity-0 cursor-pointer" />
              <div v-if="!soundPreview" class="text-center">
                <div class="text-gray-400 mb-2">🎵 Click to upload sound</div>
              </div>
              <audio v-else :src="soundPreview" controls class="w-full h-8"></audio>
            </div>
          </div>
        </div>

        <div class="bg-white p-6 rounded-3xl shadow-sm border border-gray-100">
          <label class="block text-xs font-bold text-gray-400 uppercase tracking-widest mb-4 ml-1">Multiple Choice Options</label>
          <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div v-for="opt in ['a', 'b', 'c', 'd']" :key="opt">
              <div class="flex items-center bg-gray-50 rounded-2xl px-4 py-2 focus-within:ring-2 focus-within:ring-indigo-400">
                <span class="text-indigo-500 font-bold uppercase mr-3">{{ opt }}</span>
                <input v-model="question['option_' + opt]" type="text" :placeholder="'Option ' + opt.toUpperCase()" class="bg-transparent border-none w-full focus:ring-0" />
              </div>
            </div>
          </div>

          <div class="mt-8 pt-6 border-t border-gray-50">
            <label class="block text-xs font-bold text-gray-400 uppercase tracking-widest mb-2 ml-1">Correct Answer</label>
            <div class="flex gap-4">
              <button 
                v-for="ans in ['A', 'B', 'C', 'D']" 
                :key="ans"
                type="button"
                @click="question.right_answer = ans"
                :class="question.right_answer === ans ? 'bg-indigo-600 text-white shadow-indigo-200' : 'bg-gray-100 text-gray-500'"
                class="flex-1 py-3 rounded-2xl font-bold transition-all shadow-lg"
              >
                {{ ans }}
              </button>
            </div>
          </div>
        </div>

        <button
          type="submit"
          :disabled="isLoading"
          class="w-full py-4 bg-indigo-600 hover:bg-indigo-700 text-white font-bold rounded-2xl shadow-xl shadow-indigo-100 transition-all hover:-translate-y-1 disabled:opacity-50"
        >
          {{ isLoading ? 'Saving to Database...' : 'Save Question' }}
        </button>

      </form>
    </div>
  </div>
</template>

<script setup>
import { reactive, ref, onMounted } from 'vue';
import useQuiz from '@/composables/quiz';
import useQuestion from '@/composables/question';

const { quizzes, getQuizzes } = useQuiz();
const { storeQuestion, validationErrors, isLoading } = useQuestion();

const question = reactive({
  quiz_id: '',
  question1: '',
  question2: '',
  question3: '',
  picture: null,
  sound: null,
  option_a: '',
  option_b: '',
  option_c: '',
  option_d: '',
  right_answer: '',
  area: '',
});

const picturePreview = ref(null);
const soundPreview = ref(null);

const handlePicture = (e) => {
  const file = e.target.files[0];
  question.picture = file;
  picturePreview.value = file ? URL.createObjectURL(file) : null;
};

const handleSound = (e) => {
  const file = e.target.files[0];
  question.sound = file;
  soundPreview.value = file ? URL.createObjectURL(file) : null;
};

onMounted(() => {
  getQuizzes();
});
</script>