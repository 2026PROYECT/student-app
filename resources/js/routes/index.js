import { createRouter, createWebHistory } from "vue-router";
import AuthenticatedLayout from "@/layouts/Authenticated.vue";
import Login from "@/components/Auth/Login.vue";

function auth(to, from, next) {
    const isLoggedIn = JSON.parse(localStorage.getItem("loggedIn"));
    if (!isLoggedIn) return next("/login");
    next();
}
const routes = [
    { path: "/login", name: "login", component: Login },
    {
        path: "/",
        component: AuthenticatedLayout,
        beforeEnter: auth,
        children: [
            { 
                path: '', 
                name: 'home',
                redirect: () => {
                    const userData = JSON.parse(localStorage.getItem("user_data"));
                    return userData?.role === 'admin' ? { name: 'admin.dashboard' } : { name: 'student.dashboard' };
                }
            },
            {
                path: "/admin/dashboard",
                name: "admin.dashboard",
                component: () => import("@/components/Admins/Dashboard.vue"),
            },
            {
                path: "/admin/students/index",
                name: "students.index",
                component: () => import("@/components/Admins/Students/Index.vue"),
            },
            {
                path: "/admin/students/create",
                name: "students.create",
                component: () => import("@/components/Admins/Students/Create.vue"),
            },
            {
                path: '/admin/students/:id/edit', // El :id es dinámico
                name: 'students.edit',            // ESTE NOMBRE DEBE COINCIDIR EXACTAMENTE
                component: () => import('../components/Admins/Students/Edit.vue'),
                props: true // Permite pasar el ID como prop al componente
            },
            {
                path: "/quiz",
                name: "quiz.index",
                component: () => import("@/components/Admins/Quizzes/Index.vue"),
            },
      {
                path: "/question",
                name: "question.index",
                component: () => import("@/components/Admins/Question/Index.vue"),
            },
            {
                path: "/assignments",
                name: "assignments.index",
                component: () => import("@/components/Admins/Assignments/Index.vue"),
            },
            {
                path: "/student/dashboard",
                name: "student.dashboard",
                component: () => import("@/components/Students/Dashboard.vue"),
            },
            {
                path: "/attend",
                name: "attend.index",
                component: () => import("@/components/Students/Attend/Index.vue"),
            },
            {
                path: "/results",
                name: "results.index",
                component: () => import("@/components/Students/Attend/Result.vue"),
            },
        ],
    },
];

export default createRouter({
    history: createWebHistory(),
    routes,
});