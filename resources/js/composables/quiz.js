import { useRouter } from "vue-router";
import axios from "axios";

export default function useQuiz() {
    const quizzes = ref({ data: [] });
    const quiz = ref({});
    const results = ref({});
    const validationErrors = ref({});
    const isLoading = ref(false);
    const router = useRouter();
    const swal = inject("$swal");

    const getQuizzes = async (
        page = 1,
        order_column = "quiz_date",
        order_direction = "desc"
    ) => {
        isLoading.value = true;
        try {
            const response = await axios.get("/api/v1/quizzes", {
                params: { page, order_column, order_direction }
            });
            quizzes.value = response.data;
        } catch (error) {
            console.error("Error fetching quizzes:", error);
        } finally {
            isLoading.value = false;
        }
    };

    const deleteQuiz = async (id) => {
        swal({
            title: "Are you sure?",
            text: "You won't be able to revert this!",
            icon: "warning",
            showCancelButton: true,
            confirmButtonText: "Yes, delete it!",
            confirmButtonColor: "#ef4444",
        }).then((result) => {
            if (result.isConfirmed) {
                axios.delete("/api/v1/quizzes/" + id)
                    .then(() => {
                        getQuizzes();
                        swal({ icon: "success", title: "Deleted!" });
                    });
            }
        });
    };

    return {
        quizzes,
        quiz,
        results,
        validationErrors,
        isLoading,
        getQuizzes,
        deleteQuiz,
    };
}