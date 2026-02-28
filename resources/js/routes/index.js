import { createRouter, createWebHistory } from "vue-router";

import AuthenticatedLayout from "@/layouts/Authenticated.vue";
import Login from "@/components/Auth/Login.vue";

import QuizIndex from "@/components/Quiz/Index.vue";
import QuizCreate from "@/components/Quiz/Create.vue";
import QuizEdit from "@/components/Quiz/Edit.vue";

import QuestionIndex from "@/components/Question/Index.vue";
import QuestionCreate from "@/components/Question/Create.vue";
import QuestionEdit from "@/components/Question/Edit.vue";

import AttenQuiz from "@/components/Attend/AttenQuiz.vue";
import ResultQuiz from "@/components/Attend/Result.vue";

function auth(to, from, next) {
  if (JSON.parse(localStorage.getItem("loggedIn"))) {
    return next(); // allow access
  }
  next("/login"); // redirect if not logged in
}

const routes = [
  {
    path: "/login",
    name: "login",
    component: Login,
  },
  {
    component: AuthenticatedLayout,
    beforeEnter: auth,
    children: [
      // Quiz routes
      {
        path: "/quiz",
        name: "quiz.index",
        component: QuizIndex,
        meta: { title: "Quiz" },
      },
      {
        path: "/quiz/create",
        name: "quiz.create",
        component: QuizCreate,
        meta: { title: "Add New Quiz" },
      },
      {
        path: "/quiz/edit/:id",
        name: "quiz.edit",
        component: QuizEdit,
        meta: { title: "Edit Quiz" },
      },

      // Question routes
      {
        path: "/question",
        name: "question.index",
        component: QuestionIndex,
        meta: { title: "Question" },
      },
      {
        path: "/question/create",
        name: "question.create",
        component: QuestionCreate,
        meta: { title: "Add New Question" },
      },
      {
        path: "/question/edit/:id",
        name: "question.edit",
        component: QuestionEdit,
        meta: { title: "Edit Question" },
      },

      // Students routes
      {
        path: "/students",
        name: "students.index",
        component: () => import("@/components/Students/Index.vue"),
        meta: { title: "Students" },
      },
      {
        path: "/students/create",
        name: "students.create",
        component: () => import("@/components/Students/Create.vue"),
        meta: { title: "Add Student" },
      },
      {
        path: "/students/edit/:id",
        name: "students.edit",
        component: () => import("@/components/Students/Edit.vue"),
        meta: { title: "Edit Student" },
      },

      // Assignments routes
      {
        path: "/assignments",
        name: "assignments.index",
        component: () => import("@/components/Assignments/Index.vue"),
        meta: { title: "Assignments" },
      },
      {
        path: "/assignments/create",
        name: "assignments.create",
        component: () => import("@/components/Assignments/Create.vue"),
        meta: { title: "Add Assignment" },
      },
      {
        path: "/assignments/edit/:id",
        name: "assignments.edit",
        component: () => import("@/components/Assignments/Edit.vue"),
        meta: { title: "Edit Assignment" },
      },

      // TestOP route
      {
        path: "/testop",
        name: "testop.indextest",
        component: () => import("@/components/TestOP/IndexTest.vue"),
        meta: { title: "TestOP Dashboard", requiresAdmin: true },
      },

      // Attend & Result routes
      {
        path: "/attend-quiz/:quizId",
        name: "attend-quiz",
        component: AttenQuiz,
        meta: { title: "Attend Quiz" },
      },
      {
        path: "/result-quiz/:quizId",
        name: "result-quiz",
        component: ResultQuiz,
        meta: { title: "Result Quiz" },
      },
    ],
  },
];

export default createRouter({
  history: createWebHistory(),
  routes,
});

