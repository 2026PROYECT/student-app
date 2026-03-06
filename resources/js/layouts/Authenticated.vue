<template>
  <div class="flex min-h-screen bg-gray-100">
    <aside class="w-64 bg-indigo-900 text-white flex flex-col shadow-xl">
      <div class="p-6 border-b border-indigo-800">
        <h2 class="text-2xl font-black tracking-tighter uppercase">Exam Pro</h2>
        <p class="text-xs text-indigo-300 font-bold mt-1">
            {{ user.role === 'admin' ? 'Panel de Administración' : 'Panel de Estudiante' }}
        </p>
      </div>

      <nav class="flex-1 px-4 space-y-2 mt-4">
        
        <template v-if="user.role === 'admin'">
          <router-link :to="{ name: 'admin.dashboard' }" class="nav-link" active-class="active">
            <span>🏠</span> Dashboard
          </router-link>
          <router-link :to="{ name: 'students.index' }" class="nav-link" active-class="active">
            <span>👥</span> Estudiantes
          </router-link>
          <router-link :to="{ name: 'quiz.index' }" class="nav-link" active-class="active">
            <span>📝</span> Exámenes (Quizzes)
          </router-link>
          <router-link :to="{ name: 'question.index' }" class="nav-link" active-class="active">
            <span>❓</span> Preguntas
          </router-link>
          <router-link :to="{ name: 'assignments.index' }" class="nav-link" active-class="active">
            <span>🔑</span> Autorizaciones
          </router-link>
        </template>

        <template v-else>
          <router-link :to="{ name: 'student.dashboard' }" class="nav-link" active-class="active">
            <span>🏠</span> Dashboard
          </router-link>
          <router-link :to="{ name: 'attend.index' }" class="nav-link" active-class="active">
            <span>✍️</span> Dar Examen
          </router-link>
          <router-link :to="{ name: 'results.index' }" class="nav-link" active-class="active">
            <span>📊</span> Mis Notas
          </router-link>
        </template>

      </nav>

      <div class="p-4 border-t border-indigo-800">
        <button @click="logout" class="logout-btn">
          <span>🚪</span> Cerrar Sesión
        </button>
      </div>
    </aside>

    <main class="flex-1 p-8 overflow-y-auto">
        <router-view v-slot="{ Component }">
            <component :is="Component" v-if="Component" />
        </router-view>
    </main>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import useAuth from "@/composables/auth";

const { logout } = useAuth();
// Obtenemos el usuario del localStorage
const user = ref(JSON.parse(localStorage.getItem('user_data')) || {});

// Por si el usuario cambia o se refresca la página
onMounted(() => {
    if (!user.value.role) {
        const stored = localStorage.getItem('user_data');
        if (stored) user.value = JSON.parse(stored);
    }
});
</script>

<style scoped>
.nav-link {
    display: flex;
    align-items: center;
    padding: 0.75rem 1rem;
    border-radius: 0.75rem;
    color: #e0e7ff;
    transition: all 0.2s;
}
.nav-link:hover { background-color: #3730a3; }
.nav-link.active { background-color: #4338ca; font-weight: bold; color: white; }
.nav-link span { margin-right: 0.75rem; }

.logout-btn {
    display: flex;
    width: 100%;
    padding: 0.75rem;
    color: #fca5a5;
    border-radius: 0.75rem;
    transition: background 0.2s;
}
.logout-btn:hover { background-color: rgba(127, 29, 29, 0.4); }
</style>