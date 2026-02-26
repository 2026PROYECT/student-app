import { ref } from "vue";
import axios from "axios";
import { useRouter } from "vue-router";
import Swal from "sweetalert2";

export default function useQuestion() {
  const router = useRouter();
  const questions = ref({ data: [] });
  const question = ref({}); // ✅ Added for single question
  const validationErrors = ref({});
  const isLoading = ref(false);

  // 1. Fetch all questions
  const getQuestions = async (page = 1, orderColumn = "created_at", orderDirection = "desc") => {
    try {
      const response = await axios.get("/api/questions", {
        params: { page, order_column: orderColumn, order_direction: orderDirection },
      });
      questions.value = response.data.data; 
    } catch (error) {
      console.error("Error fetching questions:", error);
    }
  };

  // 2. Fetch a single question for editing
  const getQuestion = async (id) => {
    try {
      const response = await axios.get(`/api/questions/${id}`);
      question.value = response.data.data;
    } catch (error) {
      console.error("Error fetching single question:", error);
    }
  };

  // 3. Store a new question
  const storeQuestion = async (questionData) => {
    isLoading.value = true;
    validationErrors.value = {};

    try {
      const formData = new FormData();
      Object.keys(questionData).forEach(key => {
        if (questionData[key] !== null) formData.append(key, questionData[key]);
      });

      await axios.post("/api/questions", formData, {
        headers: { "Content-Type": "multipart/form-data" },
      });

      await Swal.fire({ icon: "success", title: "Saved!", showConfirmButton: false, timer: 1500 });
      router.push({ name: "question.index" });
    } catch (error) {
      if (error.response?.status === 422) validationErrors.value = error.response.data.errors;
    } finally {
      isLoading.value = false;
    }
  };

  const updateQuestion = async (id, questionData) => {
    isLoading.value = true;
    validationErrors.value = {};

    try {
        const formData = new FormData();
        
        // Loop through all keys
        for (const key in questionData) {
            const value = questionData[key];

            // Only append files if they are actually File objects (newly selected)
            if (key === 'picture' || key === 'sound') {
                if (value instanceof File) {
                    formData.append(key, value);
                }
                // If it's a string, we skip it so Laravel doesn't try to validate a string as an image
            } else if (value !== null && value !== undefined) {
                formData.append(key, value);
            }
        }

        // Method Spoofing for Laravel
        formData.append("_method", "PUT");

        await axios.post(`/api/questions/${id}`, formData, {
            headers: { "Content-Type": "multipart/form-data" },
        });

        await Swal.fire({ icon: "success", title: "Updated!", timer: 1500, showConfirmButton: false });
        router.push({ name: "question.index" });

    } catch (error) {
        if (error.response?.status === 422) {
            validationErrors.value = error.response.data.errors;
            // 💡 Log this to see exactly which field failed
            console.log("Validation Failed:", validationErrors.value);
        }
    } finally {
        isLoading.value = false;
    }
};
  // 5. Delete a question
  const deleteQuestion = async (id) => {
    try {
      await axios.delete(`/api/questions/${id}`);
      await getQuestions();
    } catch (error) {
      console.error("Error deleting question:", error);
    }
  };

  return {
    questions,
    question, // ✅ Added
    getQuestions,
    getQuestion, // ✅ Added
    storeQuestion,
    updateQuestion, // ✅ Added
    deleteQuestion,
    validationErrors,
    isLoading,
  };
}