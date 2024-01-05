<template>
    <form @submit.prevent="createAlbum" class="max-w-md mx-auto">
        <div class="mb-5">
            <label for="title" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Title</label>
            <input id="title" v-model="newAlbum.title" placeholder="Album Name" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500" required>
        </div>
        <div class="mb-5">
            <label for="description" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Description</label>
            <textarea id="description" v-model="newAlbum.description" placeholder="Description" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"></textarea>
        </div>
        <div class="mb-5">
            <label for="layout" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Layout</label>
            <input id="layout" type="number" v-model="newAlbum.layout" placeholder="" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500">
        </div>            
        <button type="submit" class="text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm w-full sm:w-auto px-5 py-2.5 text-center dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800">Save Album</button>
    </form>
</template>
<script>
import axios from 'axios';
import Pagination from 'vue-pagination-2';
import BreezeAuthenticatedLayout from '@/Layouts/Authenticated.vue'
import { Head } from '@inertiajs/vue3';

export default {
  props: {
    newAlbum: {},
  },
  data() {
    return {
            
    };
  },  
  methods: {    
    async createAlbum() {
      if (this.newAlbum.id) {
          await axios.put(`album/${this.newAlbum.id}`, this.newAlbum);
      } else {
          await axios.post('albums', this.newAlbum);    
      }
      this.newAlbum.title = '';
      this.newAlbum.description = '';
      this.newAlbum.layout = '';
      this.newAlbum.id = '';
      this.$emit("closeModal"); 
    },    
  },
  components: {
    Pagination,
    BreezeAuthenticatedLayout,
    Head
  },
};
</script>