<template>
    <form @submit.prevent="submit">
        <div class="py-12">
            <BreezeValidationErrors class="mb-4" >
                <div v-if="errors.length > 0">
                    <ul>
                        <li v-for="error in errors" :key="error">{{ error[0] }}</li>
                    </ul>
                </div>
            </BreezeValidationErrors>
            <BreezeInput type="file" ref="fileInput" @change="handleFileChange" multiple />
            <BreezeButton @click="uploadImages">Upload</BreezeButton>
        </div>
    </form>
    <div class="flex items-center justify-center py-4 md:py-8 flex-wrap">
        <button id="all_photos" type="button" @click="albumMenuClick(null)" class="text-blue-700 hover:text-white border border-blue-600 bg-white hover:bg-blue-700 focus:ring-4 focus:outline-none focus:ring-blue-300 rounded-full text-base font-medium px-5 py-2.5 text-center me-3 mb-3 dark:border-blue-500 dark:text-blue-500 dark:hover:text-white dark:hover:bg-blue-500 dark:bg-gray-900 dark:focus:ring-blue-800">All photos</button>
        <button v-if="albumList.length" v-for="album in albumList" :id="album.id" type="button" @click="albumMenuClick(album.id)" class="text-gray-900 border border-white hover:border-gray-200 dark:border-gray-900 dark:bg-gray-900 dark:hover:border-gray-700 bg-white focus:ring-4 focus:outline-none focus:ring-gray-300 rounded-full text-base font-medium px-5 py-2.5 text-center me-3 mb-3 dark:text-white dark:focus:ring-gray-800">{{ album.title }}</button>
    </div>
    <div class="container mx-auto px-4">
        <div class="bg-gray-900 flex-wrap my-8 grid grid-cols-3 md:grid-cols-3 gap-8">
            <div v-for="image in uploadedImages" class="bg-white rounded-lg shadow-lg p-8">
                <img :src="'uploads/' + image.path" alt="Image" class="object-cover w-full max-h-64"  >
                Add to album 
                <select @change="addPhotoToAlbum($event, image.id)">
                    <option v-if="albumList.length" v-for="album in albumList" :value="album.id" 
                    @change="">
                        {{ album.title }}
                    </option>         
                </select>
                <BreezeButton @click="deleteImage(image.id)">Delete</BreezeButton>
            </div>
        </div>
    </div>    
</template>

<script>

import BreezeInput from '@/Components/Input.vue'
import BreezeLabel from '@/Components/Label.vue'
import BreezeDropdown from '@/Components/Dropdown.vue'
import BreezeValidationErrors from '@/Components/ValidationErrors.vue'
import BreezeButton from '@/Components/Button.vue'

export default {
    components: {
        BreezeInput,
        BreezeLabel,
        BreezeValidationErrors,
        BreezeButton,
        BreezeDropdown,
    },
    data() {
        return {
            images: [],
            uploadedImages: [],
            errors: [], 
            albumList: [],
            albumId: 0
        }
    },
    computed: {


    },
    methods: {
        handleFileChange(event) {
            this.images = event.target.files;
        },

        uploadImages() {
            let formData = new FormData();

            for (let i = 0; i < this.images.length; i++) {
                formData.append('images[]', this.images[i]);
            }

            axios.post('/upload', formData, {
                headers: {
                'Content-Type': 'multipart/form-data',
                },
            })
            .then(response => {
                this.loadImages();
            })
            .catch(error => {
                if (error.response.status === 422) {
                        this.errors = Object.values(error.response.data.errors);
                } else {
                        // Handle other errors
                }
            });
        },

        deleteImage(mediaId) {
            if (confirm('Are you sure you want to delete this image?')) {
                axios.delete(`images/${mediaId}`)
                    .then(response => {
                        console.log(response.data.message);
                        this.loadImages();
                    })
                    .catch(error => {
                        console.error('Error deleting image:', error);
                    });
            }
        },

        loadImages() {
            
            const url = this.albumId ? `albumShow/${this.albumId}` : 'images';
            axios.get(url)
                .then(response => {
                    this.uploadedImages = this.albumId ? response.data.photos : response.data;
                })
                .catch(error => {
                    console.error('Error loading images:', error);
                });
        },

        albumMenuClick(id) {
            console.log(id, "oi");
            this.albumId = id;
            this.loadImages();
        },

        loadAlbumList() {
            axios.get('albumsList')
                .then(response => {
                    console.log("tes", response)
                    this.albumList = response.data.data;
                })
                .catch(error => {
                    console.error('Error loading images:', error);
                });
        },
        addPhotoToAlbum(event, photoId) {
            const formData = {
                'albumId': event.target.value,
                'photoId': photoId
            };
            console.log('Entrou', formData);
            axios.post('/addPhotoToAlbum', formData, {
                headers: {
                'Content-Type': 'multipart/form-data',
                },
            });
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