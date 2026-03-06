import { ref, reactive, inject } from "vue";
import { useRouter } from "vue-router";
import axios from "axios";

// --- IMPORTANTE: El objeto user debe estar FUERA de la función ---
// Esto lo convierte en un estado global que todos los componentes comparten.
const user = reactive({
    name: "",
    lastname: "",
    email: "",
    role: "",
    isAdmin: false,
});

export default function useAuth() {
    const processing = ref(false);
    const validationErrors = ref({});
    const swal = inject("$swal");
    const router = useRouter();

    const loginForm = reactive({
        email: "",
        password: "",
        remember: false,
    });

    const loginUser = (data) => {
        // 1. Normalización del rol
        const roleFromServer = data.role ? data.role.trim().toLowerCase() : 'student';

        // 2. Actualizamos el estado reactivo GLOBAL
        user.name = data.name;
        user.lastname = data.lastname || ""; // Añadido para que no salga undefined
        user.email = data.email;
        user.role = roleFromServer; 
        user.isAdmin = (roleFromServer === 'admin');

        // 3. Persistencia
        localStorage.setItem("loggedIn", JSON.stringify(true));
        localStorage.setItem("user_data", JSON.stringify(data));

        // 4. Redirección
        if (router.currentRoute.value.name === 'login') {
            const destination = user.isAdmin ? "admin.dashboard" : "student.dashboard";
            router.push({ name: destination });
        }
    };

    const submitLogin = async () => {
        if (processing.value) return;
        processing.value = true;
        validationErrors.value = {};

        try {
            // Aseguramos CSRF si usas Sanctum
            await axios.get('/sanctum/csrf-cookie');
            
            const response = await axios.post('/api/v1/login', loginForm);
            const token = response.data.token;
            
            localStorage.setItem('auth_token', token);
            axios.defaults.headers.common['Authorization'] = `Bearer ${token}`;

            // Obtenemos info del usuario desde la ruta correcta v1
            const userResponse = await axios.get('/api/v1/user');
            loginUser(userResponse.data);

        } catch (error) {
            if (error.response?.status === 422) {
                validationErrors.value = error.response.data.errors;
            }
            console.error("Error en el login:", error);
        } finally {
            processing.value = false;
        }
    };

    const logout = async () => {
        try {
            await axios.post("/api/v1/logout");
        } catch (error) {
            console.error("Logout error", error);
        } finally {
            // Limpieza total
            localStorage.clear();
            delete axios.defaults.headers.common['Authorization'];
            
            user.name = "";
            user.lastname = "";
            user.role = "";
            user.isAdmin = false;

            window.location.href = "/login"; // Fuerza limpieza de memoria
        }
    };

    const getUser = async () => {
        const token = localStorage.getItem('auth_token');
        if (!token) return;

        try {
            axios.defaults.headers.common['Authorization'] = `Bearer ${token}`;
            const response = await axios.get("/api/v1/user");
            loginUser(response.data);
        } catch (error) {
            if (error.response?.status === 401) {
                localStorage.clear();
                router.push({ name: "login" });
            }
        }
    };

    return {
        loginForm,
        validationErrors,
        processing,
        submitLogin,
        user, // Ahora este 'user' es el mismo para todos
        logout,
        getUser,
    };
}