<!-- resources/js/components/AlbumCRUD.vue -->

<template>
    <Head title="Dashboard" />
    <BreezeAuthenticatedLayout>
        <template #header>
            <div class="container mx-auto px-4 h-10">
                <h2 class="float-left font-semibold text-xl text-gray-800 leading-tight">
                Albums 
                </h2>
                <button @click="createAlbum" class="bg-gray-900 text-white py-2 px-4 rounded-full font-bold hover:bg-gray-800 float-right">
                    New Album
                </button>
            </div>            
        </template>
        <div class="bg-gray-900 py-16">
            <div class="container mx-auto px-4">
                <div class="grid grid-cols-3 md:grid-cols-3 gap-8">
                    <div v-for="album in albums" :key="album.id" class="bg-white rounded-lg shadow-lg p-8">
                        <div class="relative overflow-hidden">
                            <img class="object-cover w-full max-h-64" 
                            :src="album.photos[0]?.path? 'uploads/'+album.photos[0]?.path: 'nophoto.jpg'" alt="Product">
                            <div class="absolute inset-0 bg-black opacity-40"></div>
                            <div class="absolute inset-0 flex items-center justify-center">
                                <button @click="viewAlbum(album.id)" class="bg-white text-gray-900 py-2 px-6 rounded-full font-bold hover:bg-gray-300">View Album</button>
                            </div>
                            <div class="inset-0 align-text-bottom">
                                <span>Photos: {{ album.photos.length }}</span>
                            </div>
                        </div>
                        <h3 class="text-xl font-bold text-gray-900 mt-4">{{  album.title }}</h3>
                        <p class="text-gray-500 text-sm mt-2">{{  album.description }}</p>
                        <div class="flex items-center justify-between mt-4">
                            <span class="text-gray-900 font-bold text-lg hover:text-blue-700 cursor-pointer" @click="deleteAlbum(album.id)">Delete</span>
                            <button class="bg-gray-900 text-white py-2 px-4 rounded-full font-bold hover:bg-gray-800" @click="editAlbum(album.id)">Edit</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div v-if="isModalVisible" class="fixed inset-0 overflow-y-auto flex items-center justify-center">
            <div class="bg-black bg-opacity-50 absolute inset-1 w-full" @click="isModalVisible=!isModalVisible"></div>
            <div class="bg-white p-6 rounded shadow-md z-10 w-15">
                <AlbumForm :newAlbum="newAlbum" @closeModal="closeModal"/>
            </div>
        </div>        
    </BreezeAuthenticatedLayout>
</template>
  
  <script>
import axios from 'axios';
import Pagination from 'vue-pagination-2';
import BreezeAuthenticatedLayout from '@/Layouts/Authenticated.vue';
import AlbumForm from './Forms/AlbumForm';
import { Head } from '@inertiajs/vue3';


export default { 
props: {
    albumId: '',
},  
data() {
    return {    
        newAlbum: {},    
        albums: [],
        pagination: {},
        page: 1,
        perPage: 20,
        records: [],
        isModalVisible: false
    };
},
mounted() {
    this.loadAlbums();
},
methods: {    
    createAlbum() {
        this.newAlbum = {
            title: '',
            description: '',
            layout: 3
        };
        this.isModalVisible = true;
    },

    async loadAlbums(page = 1) {
        const response = await axios.get(`albumsList?page=${page}`);
        this.albums = response.data.data;
        this.pagination = response.data;
    },
    
    async editAlbum(albumId) {
        const response = await axios.get(`albumShow/${albumId}`);
        this.newAlbum = response.data;
        this.isModalVisible = true;
    
    },

    async deleteAlbum(albumId) {
        if (confirm('Are you sure you want to delete this album?')) {
            await axios.delete(`album/${albumId}`);
            this.loadAlbums();
        }
    },

    closeModal() {
        this.isModalVisible = false;
        this.newAlbum = {};
        this.loadAlbums();
    },

    viewAlbum(id) {
        window.location = `/albumDashboard/${id}`;
    }
},
components: {
    Pagination,
    BreezeAuthenticatedLayout,
    Head,
    AlbumForm,
},
};
  </script>