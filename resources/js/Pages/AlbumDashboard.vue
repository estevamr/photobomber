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
            </div>            
        </template>
        <div class="bg-gray-900 py-16">
            <div class="container mx-auto px-4">              
                <div class="grid grid-cols-3 md:grid-cols-3 gap-8">
                    <div v-for="(photo, index) in photos" :key="index" class="bg-white rounded-lg shadow-lg p-2">
                        <div class="relative overflow-hidden group">
                            <img class="object-cover w-full max-h-64" :src="baseUrl+'/uploads/' + photo.path"/>
                            <div class="absolute inset-0 flex items-center justify-center">
                                <button @click="removeFromAlbum(photo.id)" class="bg-white text-gray-900 py-2 px-6 rounded-full font-bold hover:bg-gray-300 hidden group-hover:block">Remove</button>
                            </div>
                        </div>                        
                    </div>
                </div>
                <div class="flex items-center justify-center">
                    <pagination class="w-1/2 inset-0 flex items-center justify-center"
                        :total="totalItems"
                        :per-page="perPage"
                        :records="totalItems"
                        v-model="currentPage"
                        @input="fetchPhotos"
                    />
                </div>
            </div>
        </div>
        
    </BreezeAuthenticatedLayout>    
</template>
<script>

    import BreezeAuthenticatedLayout from '@/Layouts/Authenticated.vue';
    import { Head } from '@inertiajs/vue3';
    import Pagination from 'v-pagination-3';

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

            fetchPhotos() {
                const apiUrl = `albumPhotos/${this.albumId}?page=${this.currentPage}&per_page=${this.perPage}`;

                axios.get(this.baseUrl+'/'+apiUrl)
                    .then(response => {

                        this.photos = response.data.data;
                        this.totalItems = response.data.total;
                 
                    })
                    .catch(error => {
                        console.error('Error fetching photos:', error);
                    });
            },

            removeFromAlbum(photoId) {
                
                const apiUrl = `/album/remove/${photoId}/${this.albumId}`;

                axios.delete(this.baseUrl+apiUrl)
                    .then(response => {

                        this.fetchPhotos();
                
                    })
                    .catch(error => {
                        console.error('Error while removing photos:', error);
                    });
            },
            async compileAlbum() {
                try {
                    if (this.albumId) {
                        const compiled = await axios.put(`${this.baseUrl}/compileAlbum`, this.album);
                    }
                } catch(error){
                    alert("Error while compiling:" + error.response.data.error);
                }
                
            },
        },
        components: {
            BreezeAuthenticatedLayout,
            Head,
            Pagination
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