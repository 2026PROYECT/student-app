import { ref, reactive, inject } from "vue";
import { useRouter } from "vue-router";
import axios from "axios";

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

    const user = reactive({
        name: "",
        lastname: "", // Added to match your user profile
        email: "",
        role: "",     // Added for role-based logic
        isAdmin: false,
    });

    const loginUser = (data) => {
        // 1. Update the reactive user object
        user.name = data.name;
        user.lastname = data.lastname;
        user.email = data.email;
        user.role = data.role;
        user.isAdmin = data.role === 'admin' || data.is_admin === true;

        // 2. Persist to localStorage for Route Guards
        localStorage.setItem("loggedIn", JSON.stringify(true));
        localStorage.setItem("user_data", JSON.stringify(data));

        // 3. Role-Based Redirect
        // Only redirect if we are currently on the login page
        if (router.currentRoute.value.name === 'login') {
            if (user.isAdmin) {
                router.push({ name: "assignments.index" });
            } else {
                router.push({ name: "attend.index" });
            }
        }
    };

    const submitLogin = async () => {
        if (processing.value) return;

        processing.value = true;
        validationErrors.value = {};

        try {
            await axios.get("/sanctum/csrf-cookie");
            await axios.post("/login", loginForm);
            const response = await axios.get("/api/user");
            loginUser(response.data);
        } catch (error) {
            if (error.response?.data?.errors) {
                validationErrors.value = error.response.data.errors;
            } else if (error.response?.status === 401) {
                swal({
                    icon: "error",
                    title: "Access Denied",
                    text: "Invalid email or password.",
                });
            }
        } finally {
            processing.value = false;
        }
    };

    const logout = async () => {
        if (processing.value) return;
        processing.value = true;

        try {
            await axios.post("/logout");
            // Clean up localStorage
            localStorage.removeItem("loggedIn");
            localStorage.removeItem("user_data");
            
            // Reset reactive state
            user.name = "";
            user.role = "";
            user.isAdmin = false;

            router.push({ name: "login" });
        } catch (error) {
            console.error("Logout failed", error);
        } finally {
            processing.value = false;
        }
    };

    const getUser = async () => {
        try {
            const response = await axios.get("/api/user");
            loginUser(response.data);
        } catch (error) {
            if (error.response?.status === 401) {
                localStorage.removeItem("loggedIn");
                localStorage.removeItem("user_data");
            }
        }
    };

    return {
        loginForm,
        validationErrors,
        processing,
        submitLogin,
        user,
        logout,
        getUser,
    };
}