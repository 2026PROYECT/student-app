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
        email: "",
        isAdmin: false,
    });

 const submitLogin = async () => {
  if (processing.value) return;

  processing.value = true;
  validationErrors.value = {};

  try {
    await axios.get("/sanctum/csrf-cookie"); // CSRF cookie
    await axios.post("/login", loginForm);   // login
    const response = await axios.get("/api/user"); // fetch user
    loginUser(response.data);                // store user
  } catch (error) {
    if (error.response?.data) {
      validationErrors.value = error.response.data.errors;
    }
  } finally {
    processing.value = false;
  }
};

    const loginUser = (data) => {
  user.name = data.name;
  user.email = data.email;
  user.isAdmin = data.is_admin;
  localStorage.setItem("loggedIn", JSON.stringify(true));
  router.push({ name: "quiz.index" });
};


    const logout = async () => {
        if (processing.value) return;

        processing.value = true;

        try {
            await axios.post("/logout");
            router.push({ name: "login" });
        } catch (error) {
            swal({
                icon: "error",
                title: error.response.status,
                text: error.response.statusText,
            });
        } finally {
            processing.value = false;
        }
    };

    const getUser = async () => {
  try {
    const response = await axios.get("/api/user");
    loginUser(response.data);
  } catch (error) {
    console.error("Unauthorized:", error.response?.status);
  }
};

return {
  loginForm,
  validationErrors,
  processing,
  submitLogin,
  user,
  logout,
  getUser, // <-- add back here
};


}

