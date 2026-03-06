<template>
  <div class="min-h-screen bg-gray-50/50 py-10 px-4">
    <div class="max-w-6xl mx-auto">
      <div class="flex flex-col md:flex-row justify-between items-center mb-8 gap-4">
        <div>
          <h1 class="text-3xl font-black text-gray-900">Administrators</h1>
          <p class="text-gray-500 font-medium">Manage system access and permissions</p>
        </div>
        <router-link 
          :to="{ name: 'admins.create' }" 
          class="bg-indigo-600 hover:bg-indigo-700 text-white px-8 py-4 rounded-2xl font-bold shadow-lg shadow-indigo-100 transition-all active:scale-95"
        >
          Add New Admin
        </router-link>
      </div>

      <div class="mb-6">
        <input 
          v-model="searchQuery" 
          @input="debounceSearch"
          type="text" 
          placeholder="Search by name or email..." 
          class="w-full md:w-96 border-none bg-white shadow-sm rounded-2xl p-4 focus:ring-2 focus:ring-indigo-400 font-medium"
        />
      </div>

      <div class="bg-white rounded-3xl shadow-sm border border-gray-100 overflow-hidden">
        <table class="w-full text-left border-collapse">
          <thead>
            <tr class="bg-gray-50/50 border-b border-gray-100">
              <th class="px-6 py-5 text-xs font-black text-gray-400 uppercase tracking-widest">Administrator</th>
              <th class="px-6 py-5 text-xs font-black text-gray-400 uppercase tracking-widest">Email Address</th>
              <th class="px-6 py-5 text-xs font-black text-gray-400 uppercase tracking-widest text-right">Actions</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-gray-50">
            <tr v-for="admin in admins.data" :key="admin.id" class="hover:bg-gray-50/50 transition-colors">
              <td class="px-6 py-5">
                <div class="flex items-center space-x-4">
                  <div 
                    class="h-12 w-12 flex-shrink-0 cursor-zoom-in" 
                    @click="admin.picture ? openImage(admin.picture) : null"
                  >
                    <img v-if="admin.picture" 
                         :src="'/storage/' + admin.picture" 
                         class="h-12 w-12 rounded-full object-cover border-2 border-white shadow-sm hover:scale-110 transition-transform" />
                    
                    <div v-else class="h-12 w-12 rounded-full bg-indigo-100 flex items-center justify-center border-2 border-white shadow-sm">
                      <span class="text-indigo-600 font-black text-sm">
                        {{ admin.name.charAt(0) }}{{ admin.lastname.charAt(0) }}
                      </span>
                    </div>
                  </div>
                  <span class="font-bold text-gray-900">{{ admin.name }} {{ admin.lastname }}</span>
                </div>
              </td>
              
              <td class="px-6 py-5 text-gray-600 font-medium">{{ admin.email }}</td>
              
              <td class="px-6 py-5 text-right">
                <div class="flex justify-end items-center space-x-2">
                  <router-link 
                    :to="{ name: 'admins.edit', params: { id: admin.id } }" 
                    class="p-2 text-indigo-600 hover:bg-indigo-50 rounded-lg transition-colors font-bold"
                  >
                    Edit
                  </router-link>
                  <button 
                    @click="deleteAdmin(admin.id)" 
                    class="p-2 text-red-600 hover:bg-red-50 rounded-lg transition-colors font-bold"
                  >
                    Delete
                  </button>
                </div>
              </td>
            </tr>
            <tr v-if="admins.data && admins.data.length === 0">
              <td colspan="3" class="px-6 py-20 text-center text-gray-400 font-medium">
                No administrators found.
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>

    <Transition name="fade">
        <div v-if="selectedImage" 
             class="fixed inset-0 z-50 flex items-center justify-center bg-black/80 backdrop-blur-sm p-4"
             @click="closeImage">
            <div class="relative max-w-4xl w-full flex flex-col items-center">
                <button class="absolute -top-12 right-0 text-white hover:text-gray-300 text-4xl font-light">&times;</button>
                <img :src="selectedImage" 
                     class="max-h-[85vh] rounded-3xl shadow-2xl border-4 border-white/10 object-contain" 
                     @click.stop />
            </div>
        </div>
    </Transition>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import axios from 'axios';
import Swal from 'sweetalert2';

const admins = ref({ data: [] });
const searchQuery = ref('');
let debounceTimer = null;

const fetchAdmins = async (page = 1) => {
    try {
        // We call our dynamic UserController with role=admin
        const response = await axios.get('/api/v1/students', {
            params: { 
                role: 'admin', 
                search: searchQuery.value,
                page: page 
            }
        });
        admins.value = response.data;
    } catch (error) {
        console.error("Error fetching admins:", error);
    }
};

const selectedImage = ref(null);

const openImage = (path) => {
    selectedImage.value = '/storage/' + path;
};

const closeImage = () => {
    selectedImage.value = null;
}; 

const debounceSearch = () => {
    clearTimeout(debounceTimer);
    debounceTimer = setTimeout(() => {
        fetchAdmins();
    }, 500);
};

const deleteAdmin = async (id) => {
    const result = await Swal.fire({
        title: 'Are you sure?',
        text: "This administrator will lose all system access!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#4f46e5',
        cancelButtonColor: '#ef4444',
        confirmButtonText: 'Yes, delete them',
        customClass: {
            popup: 'rounded-3xl',
            confirmButton: 'rounded-xl px-6 py-3 font-bold',
            cancelButton: 'rounded-xl px-6 py-3 font-bold'
        }
    });

    if (result.isConfirmed) {
        try {
            await axios.delete(`/api/v1/students/${id}`);
            Swal.fire('Deleted!', 'Administrator has been removed.', 'success');
            fetchAdmins();
        } catch (error) {
            Swal.fire('Error', 'Could not delete user.', 'error');
        }
    }
};

onMounted(() => {
    fetchAdmins();
});
</script>
<style scoped>
.fade-enter-active, .fade-leave-active {
  transition: opacity 0.3s ease;
}
.fade-enter-from, .fade-leave-to {
  opacity: 0;
}
</style>