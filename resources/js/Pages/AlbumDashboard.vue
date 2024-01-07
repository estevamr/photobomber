<!-- resources/js/components/AlbumDashboard.vue -->
<template>
    <Head title="Dashboard" />
    <BreezeAuthenticatedLayout>
        <template #header>
            <div class="container mx-auto px-4 h-10">
                <h2 class="float-left font-semibold text-xl text-gray-800 leading-tight">
                {{ album?.title }} 
                </h2>
                <button @click="compileAlbum" class="bg-gray-900 text-white py-2 px-4 rounded-full font-bold hover:bg-gray-800 float-right">
                    Compile
                </button>
                <button @click="isModalOpen=true" class="bg-gray-900 text-white py-2 px-4 rounded-full font-bold hover:bg-gray-800 float-right">
                    Add Photos
                </button>
                
            </div>            
        </template>
        <Loader :loading="loading"/>
        <div class="bg-gray-900 py-16">
            <div class="container mx-auto px-4">              
                <div class="grid grid-cols-3 md:grid-cols-3 gap-8">
                    <div v-for="(photo, index) in photos" :key="index" class="bg-white rounded-lg shadow-lg p-2">
                        <div class="relative overflow-hidden group">
                            <img class="object-cover w-full max-h-64" :src="`${baseUrl}/uploads/${photo.path}`"/>
                            <div class="absolute inset-0 flex items-center justify-center">
                                <button @click="removeFromAlbum(photo.id)" class="bg-white text-gray-900 py-2 px-6 rounded-full font-bold hover:bg-gray-300 hidden group-hover:block">Remove</button>
                            </div>
                        </div>                        
                    </div>
                </div>
                <div class="flex items-center justify-center">
                    <div class="w-1/2 inset-0 flex items-center justify-center">
                        <pagination                            
                            :per-page="perPage"
                            :records="totalItems"
                            v-model="currentPage"
                            @paginate="fetchPhotos"
                        />
                    </div>
                </div>
            </div>
        </div>
    <!-- Modal Dialog -->
    <div v-if="isModalOpen" class="fixed inset-0 flex items-center justify-center bg-gray-800 bg-opacity-75 max-h-screen">
        <div class="bg-white p-8 max-w-2xl mx-auto rounded-md">
            <!-- Modal Content -->
            <PhotoGallery :albumInTheContext="albumId"/>
            <!-- Close Button -->
            <button @click="closeModal" class="mt-4 p-2 bg-blue-500 hover:bg-blue-600 text-white rounded-md">
            Close Modal
            </button>
        </div>
    </div>
    </BreezeAuthenticatedLayout>    
</template>
<script>

import Loader from '@/Components/Loader.vue';
import BreezeAuthenticatedLayout from '@/Layouts/Authenticated.vue';
import { Head } from '@inertiajs/vue3';
import Pagination from 'v-pagination-3';
import PhotoGallery from './PhotoGallery.vue';


export default { 
    props: {
        albumId: '',
        album: {},
    },  
    data() {
        return {  
            baseUrl: window.location.origin,
            photos: [],
            totalItems: 0,
            perPage: 0,
            currentPage: 1,
            loading: false,
            isModalOpen: false,
        };
    },
    mounted() {
        this.perPage = this?.album?.layout;
        this.fetchPhotos();
    },
    
    watch: {
        currentPage() {
            this.fetchPhotos();
        },
    },

    methods: {
        /**
         * Fetches photos associated with a specific album from the server with pagination support.
         */
        async fetchPhotos() {
            const apiUrl = `albumPhotos/${this.albumId}?page=${this.currentPage}&per_page=${this.perPage}`;
            try {
                this.loading = true;
                const response = await axios.get(this.baseUrl+'/'+apiUrl)
                this.photos = response.data.data;
                this.totalItems = response.data.total;
            }
            catch(error){
                console.error('Error fetching photos:', error);
            }
            finally {
                this.loading = false;
            }
        },
        /**
         * Removes a photo from the current album and reloads the photo list.
         *
         * @param {number} photoId - The ID of the photo to remove from the album.
         */
        async removeFromAlbum(photoId) {
            this.loading = true;
            const apiUrl = `/album/remove/${photoId}/${this.albumId}`;
            try {
                const response = await axios.delete(this.baseUrl+apiUrl)
                this.fetchPhotos();
            }
            catch(error){
                console.error('Error while removing photos:', error);
            }
            finally {
                this.loading = false;
            }
        },
        /**
         * Compiles the current album, triggering a compilation process on the server.
         * Displays an alert on error.
         */
        async compileAlbum() {
            try {
                if (this.albumId) {
                    this.loading = true;
                    const compiled = await axios.put(`${this.baseUrl}/compileAlbum`, this.album);
                }
            } catch(error){
                alert("Error while compiling:" + error.response.data.error);
            }
            finally {
                this.loading = false;
            }
            
        },
         /**
         * Closes the modal and fetches the updated photo list.
         */
        closeModal() {
            this.fetchPhotos();
            this.isModalOpen=false;

        }
    },
    components: {
        BreezeAuthenticatedLayout,
        Head,
        Pagination,
        Loader,
        PhotoGallery
    },
}
</script>
<style>
* styles.css */

/* Customize pagination container */
.pagination {
  display: flex;
  justify-content: center;
  margin-top: 20px;
  align-items: center;
}

.pagination .nav {
    width: min-content;
}

/* Customize pagination item */
.VuePagination ul {
    display: flex;
    justify-content: center;
    align-items: center;
    text-align: center;
}

/* Customize pagination item */
.pagination li {
  list-style: none;
  display: inline-block;
  margin: 0 5px;
}



/* Customize pagination link */
.pagination a input {
  text-decoration: none;
  padding: 5px 10px;
  border: 1px solid #ddd;
  color: #fff;
}
.page-link, .VuePagination__count {
    color:#fff;
}
 
/* Customize active page link */
.pagination li.active a input {
  background-color: #007BFF;
  color: #fff;
  border-color: #007BFF;
}
</style>