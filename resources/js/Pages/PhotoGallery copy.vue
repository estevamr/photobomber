<template>
   <Loader :loading="loading"/>
    <div>
        <PhotoUploadForm @loadImages="loadImages" />
    </div>
    <!-- <div class="flex items-center justify-center py-4 md:py-8 flex-wrap">
        <button id="all_photos" type="button" @click="albumMenuClick(null)" class="text-blue-700 hover:text-white border border-blue-600 bg-white hover:bg-blue-700 focus:ring-4 focus:outline-none focus:ring-blue-300 rounded-full text-base font-medium px-5 py-2.5 text-center me-3 mb-3 dark:border-blue-500 dark:text-blue-500 dark:hover:text-white dark:hover:bg-blue-500 dark:bg-gray-900 dark:focus:ring-blue-800">All photos</button>
        <button v-if="albumList.length" v-for="album in albumList" :id="album.id" type="button" @click="albumMenuClick(album.id)" class="text-gray-900 border border-white hover:border-gray-200 dark:border-gray-900 dark:bg-gray-900 dark:hover:border-gray-700 bg-white focus:ring-4 focus:outline-none focus:ring-gray-300 rounded-full text-base font-medium px-5 py-2.5 text-center me-3 mb-3 dark:text-white dark:focus:ring-gray-800">{{ album.title }}</button>
    </div> -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
        <!-- Loop through the photos and display them -->
        <div v-for="image in uploadedImages" :key="image.id">
            <img :src="'uploads/' + image.path" alt="Photo" class="rounded-md w-full h-48 object-cover">
        </div>
    </div>
    <!-- <div class="container mx-auto px-4">
        <div class="bg-gray-900 flex-wrap my-8 grid grid-cols-3 md:grid-cols-3 gap-8">
            <div v-for="image in uploadedImages" class="bg-white rounded-lg shadow-lg p-8">
                <img :src="'uploads/' + image.path" alt="Image" class="object-cover w-full max-h-64"  >
                Add to album 
                <select @change="addPhotoToAlbum($event, image.id)">
                    <option value="">Choose an album</option>
                    <option v-if="albumList.length" v-for="album in albumList" :value="album.id">
                        {{ album.title }}
                    </option>         
                </select>
                <BreezeButton @click="deleteImage(image.id)">Delete</BreezeButton>
            </div>
        </div>
    </div>     -->
</template>

<script>


import BreezeButton from '@/Components/Button.vue'
import PhotoUploadForm from './Forms/PhotoUploadForm.vue'
import Loader from '@/Components/Loader.vue';


export default {
    components: {
        PhotoUploadForm,
        BreezeButton,
        Loader
    },
    data() {
        return {
            images: [],
            uploadedImages: [],
            errors: [], 
            albumList: [],
            albumId: 0,
            loading: false,
        }
    },

    methods: {
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
            const url = this.albumId ? `albumShow/${this.albumId}` : 'images';
            try {
                this.loading = true;
                const response = await axios.get(url);
                this.uploadedImages = this.albumId ? response.data.photos : response.data;
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
        async addPhotoToAlbum(event, photoId) {
            if (event.target.value) {
                const formData = {
                    'albumId': event.target.value,
                    'photoId': photoId
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
                    console.error('Error while adding photos: ', error);
                }
                finally {
                    this.loading = false;
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
.row {
  display: flex;
  flex-wrap: wrap;
  padding: 0 4px;
}

/* Create two equal columns that sits next to each other */
.column {
  flex: 50%;
  padding: 0 4px;
}

.column img {
  margin-top: 8px;
  vertical-align: middle;
}
</style>