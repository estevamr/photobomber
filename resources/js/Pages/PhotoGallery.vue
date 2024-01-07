<template>
   <Loader :loading="loading"/>
    <div>
        <PhotoUploadForm @loadImages="loadImages" />
    </div>
    <div class="relative">
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
            <!-- Loop through the photos and display them -->
            <div v-for="photo in photos" :key="photo.id" class="relative group">
                <img :src="`uploads/${photo.path}`" alt="Photo" class="rounded-md w-full h-48 object-cover transition-transform transform group-hover:scale-110">
                <!-- Menu -->
                <div  class="absolute top-0 left-0 right-0 bottom-0 bg-black bg-opacity-50 hidden group-hover:flex flex-col items-center justify-center">
                    <a  @click="openPanel(photo.id)" class="cursor-pointer text-white font-bold">Add to album</a>
                    <a  @click="deleteImage(photo.id)" class="cursor-pointer text-white font-bold">Delete</a>
                </div>
            </div>
        </div>
    </div>

    <div class="flex items-center justify-center">
        <div class="w-1/2 inset-0 flex items-center justify-center">
            <Pagination                            
                :per-page="perPage"
                :records="totalItems"
                v-model="currentPage"
                @paginate="loadImages"
            />
        </div>
    </div>
    <!-- Side panel -->
    <div v-if="showSideTab" class="fixed top-0 right-0 bottom-0 w-1/4 bg-gray-200 p-4">
        <div class="flex justify-end">
            <button @click="showSideTab=false" class="text-red">
            <svg fill="none" stroke="black" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                viewBox="0 0 24 24" class="w-6 h-6">
                <path d="M6 18L18 6M6 6l12 12"></path>
            </svg>
            </button>
        </div>
        <h2 class="text-xl font-bold mb-4">Please select an album</h2>
        <ul class="max-w-md space-y-1 text-gray-500 list-disc list-inside dark:text-gray-400 cursor-pointer">
            <li v-for="album in albumList" @click="addPhotoToAlbum(album.id)">{{ album.title }}</li>
        </ul>
    </div>
</template>

<script>


import BreezeButton from '@/Components/Button.vue'
import PhotoUploadForm from './Forms/PhotoUploadForm.vue'
import Loader from '@/Components/Loader.vue';
import Pagination from 'v-pagination-3';


export default {
    components: {
        PhotoUploadForm,
        BreezeButton,
        Loader,
        Pagination
    },
    data() {
        return {
           
            albumList: [],
            albumId: 0,
            loading: false,

            photos: [],
            totalItems: 0,
            perPage: 6,
            currentPage: 1,
            showSideTab: false,
            photoId: null,
          
        }
    },

    methods: {
        openPanel(id) {
            this.photoId = id;
            this.showSideTab = true;
        },
        
        async deleteImage(mediaId) {
            if (confirm('Are you sure you want to delete this image?')) {
                try {
                    this.loading = true;
                    const response = await axios.delete(`images/${mediaId}`);
                    this.loadImages();
                }
                catch(error) {
                    alert('Error deleting image:', error);
                }
                finally {
                    this.loading = false;
                }
                
            }
        },

        async loadImages() {
            const url = `images?page=${this.currentPage}&per_page=${this.perPage}`;
            try {
                this.loading = true;
                const response = await axios.get(url);
                this.totalItems = response.data.total;
                this.photos = response.data.data;
                
            }
            catch(error) {
                console.error('Error loading images:', error);
            }
            finally {
                this.loading = false;
            }
        },

        albumMenuClick(id) {
            this.albumId = id;
            this.loadImages();
        },

        async loadAlbumList() {
            try {
                this.loading = true;
                const response = await axios.get('albumsList');
                this.albumList = response.data.data;
      
            }
            catch(error) {
                console.error('Error loading images:', error);
            }
            finally {
                this.loading = false;
            }     
        },
        async addPhotoToAlbum(albumId) {
            if (albumId) {
                const formData = {
                    'albumId':albumId,
                    'photoId': this.photoId
                };
                try {
                    this.loading = true;
                    await axios.post('/addPhotoToAlbum', formData, {
                        headers: {
                            'Content-Type': 'multipart/form-data',
                        },
                    });
                }
                catch(error) {
                    alert(error.response.data.error);
                    console.error('Error while adding photos: ', error);
                }
                finally {
                    this.loading = false;
                    this.showSideTab = false;
                }    
            }                        
        }
    },
    created() {
        this.loadImages();
        this.loadAlbumList();
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
  color: #111827;
}
.page-link, .VuePagination__count {
    color:#111827;
}
 
/* Customize active page link */
.pagination li.active a input {
  background-color: #007BFF;
  color: #111827;
  border-color: #007BFF;
}
</style>