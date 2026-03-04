import { createRouter, createWebHistory } from "vue-router";
import AuthenticatedLayout from "@/layouts/Authenticated.vue";
import Login from "@/components/Auth/Login.vue";

function auth(to, from, next) {
    const isLoggedIn = JSON.parse(localStorage.getItem("loggedIn"));
    const userData = JSON.parse(localStorage.getItem("user_data"));

    if (!isLoggedIn) return next("/login");

    // Role Protection: If route requires admin and user isn't one, redirect to Student Portal
    if (to.meta.role === 'admin' && userData?.role !== 'admin') {
        return next({ name: "attend.index" }); 
    }

    next();
}

const routes = [
    { path: "/login", name: "login", component: Login },
    {
        path: "/",
        component: AuthenticatedLayout,
        beforeEnter: auth,
        children: [
            // Redirect based on role
            { 
                path: '', 
                redirect: to => {
                    const userData = JSON.parse(localStorage.getItem("user_data"));
                    return userData?.role === 'admin' ? { name: 'assignments.index' } : { name: 'attend.index' };
                }
            },

            // --- ADMIN MANAGEMENT ---
            {
                path: "/admins",
                name: "admins.index",
                component: () => import("@/components/Admins/AdminIndex.vue"),
                meta: { title: "Admins", role: 'admin' },
            },
            {
                path: "/admins/create",
                name: "admins.create",
                component: () => import("@/components/Admins/AdminCreate.vue"),
                meta: { title: "Add Admin", role: 'admin' },
            },
            {
                path: "/admins/edit/:id",
                name: "admins.edit",
                component: () => import("@/components/Admins/AdminEdit.vue"),
                meta: { title: "Edit Admin", role: 'admin' },
            },

            // --- STUDENT MANAGEMENT (Admin Only) ---
            {
                path: "/students",
                name: "students.index",
                component: () => import("@/components/Students/Index.vue"),
                meta: { title: "Students", role: 'admin' },
            },
            {
                path: "/students/create",                
                name: "students.create",
                component: () => import("@/components/Students/Create.vue"),
                meta: { title: "Add Student", role: 'admin' },
            },
            {
                path: "/students/edit/:id",
                name: "students.edit",
                component: () => import("@/components/Students/Edit.vue"),
                meta: { title: "Edit Student", role: 'admin' },
            },

            // --- QUIZ MANAGEMENT ---
            {
                path: "/quiz",
                name: "quiz.index",
                component: () => import("@/components/Quiz/Index.vue"),
                meta: { title: "Quizzes", role: 'admin' },
            },
            {
                path: "/quiz/create",
                name: "quiz.create",
                component: () => import("@/components/Quiz/Create.vue"),
                meta: { title: "Add Quiz", role: 'admin' },
            },
            {
                path: "/quiz/edit/:id",
                name: "quiz.edit",
                component: () => import("@/components/Quiz/Edit.vue"),
                meta: { title: "Edit Quiz", role: 'admin' },
            },

            // --- QUESTIONS ---
            {
                path: "/question",
                name: "question.index",
                component: () => import("@/components/Question/Index.vue"),
                meta: { title: "Questions", role: 'admin' },
            },
            {
                path: "/question/create",
                name: "question.create",
                component: () => import("@/components/Question/Create.vue"),
                meta: { title: "Add Question", role: 'admin' },
            },
            {
                path: "/question/edit/:id",
                name: "question.edit",
                component: () => import("@/components/Question/Edit.vue"),
                meta: { title: "Edit Question", role: 'admin' },
            },

            // --- STUDENT EXAM PORTAL (Attend) ---
            {
                path: '/attend',
                name: 'attend.index',
                component: () => import('@/components/Attend/Index.vue'),
                meta: { title: 'Exam Portal', role: 'student' }
            },
            {
                path: '/attend/quiz/:id',
                name: 'attenquiz.show',
                component: () => import('@/components/Attend/AttenQuiz.vue'),
                props: true,
                meta: { title: 'Examination in Progress', role: 'student' }
            },
            {
    path: '/results',
    name: 'results.index', // This matches the name in your sidebar
    component: () => import('@/components/Attend/Result.vue'), // Points to your Result file
    meta: { title: 'My Results', role: 'student' }
},

            // --- ASSIGNMENTS (The Authorization List) ---
            {
                path: "/assignments",
                name: "assignments.index",
                component: () => import("@/components/Assignments/Index.vue"),
                meta: { title: "Assignments", role: 'admin' },
            },
            {
                path: '/assignments/create',
                name: 'assignments.create',
                component: () => import('@/components/Assignments/Create.vue'),
                meta: { title: "New Assignment", role: 'admin' },
            },
            {
                path: '/assignments/:id/edit',
                name: 'assignments.edit',
                component: () => import('@/components/Assignments/Edit.vue'),
                meta: { title: "Edit Assignment", role: 'admin' },
            }
        ],
    },
];

export default createRouter({
    history: createWebHistory(),
    routes,
});