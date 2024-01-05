<template>
    <Head title="Dashboard" />
    <BreezeAuthenticatedLayout>
        <template #header>
            <div class="container mx-auto px-4 h-10">
                <h2 class="float-left font-semibold text-xl text-gray-800 leading-tight">
                {{ album?.title }} {{ perPage }}
                </h2>
                <button @click="isModalVisible=!isModalVisible" class="bg-gray-900 text-white py-2 px-4 rounded-full font-bold hover:bg-gray-800 float-right">
                    New Album
                </button>
            </div>            
        </template>
        <div class="bg-gray-900 mx-auto h-screen">
            
            <div class="container mx-auto px-5 py-2 lg:px-32 lg:pt-24">
                <div class="-m-1 flex flex-wrap md:-m-2">
                    <div class="flex flex-wrap"> 
                        <div v-for="(photo, index) in photos" :key="index" class="w-1/2 p-1 md:p-2">
                            
                            <img
                            alt="gallery"
                            class="block w-full max-h-64 rounded-lg object-cover object-center hover:h-full "
                            :src="baseUrl+'/uploads/' + photo.path"/>
                        </div>
                        <!-- <div class="w-1/2 p-1 md:p-2">
                            <img
                            alt="gallery"
                            class="block h-full w-full rounded-lg object-cover object-center"
                            src="https://tecdn.b-cdn.net/img/Photos/Horizontal/Nature/4-col/img%20(72).webp" />
                        </div>
                        <div class="w-full p-1 md:p-2">
                            <img
                            alt="gallery"
                            class="block h-full w-full rounded-lg object-cover object-center"
                            src="https://tecdn.b-cdn.net/img/Photos/Horizontal/Nature/4-col/img%20(73).webp" />
                        </div>
                        </div>
                        <div class="flex w-1/2 flex-wrap">
                            <div class="w-full p-1 md:p-2">
                                <img
                                alt="gallery"
                                class="block h-full w-full rounded-lg object-cover object-center"
                                src="https://tecdn.b-cdn.net/img/Photos/Horizontal/Nature/4-col/img%20(74).webp" />
                            </div>
                            <div class="w-1/2 p-1 md:p-2">
                                <img
                                alt="gallery"
                                class="block h-full w-full rounded-lg object-cover object-center"
                                src="https://tecdn.b-cdn.net/img/Photos/Horizontal/Nature/4-col/img%20(75).webp" />
                            </div>
                            <div class="w-1/2 p-1 md:p-2">
                                <img
                                alt="gallery"
                                class="block h-full w-full rounded-lg object-cover object-center"
                                src="https://tecdn.b-cdn.net/img/Photos/Horizontal/Nature/4-col/img%20(77).webp" />
                            </div> -->
                    </div>
                </div>
                <pagination
      :total="totalItems"
      :per-page="perPage"
      :records="totalItems"
      v-model="currentPage"
      @input="fetchPhotos"
    />
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
                perPage: null,
                currentPage: 1,
            };
        },
        mounted() {
            this.perPage = this.album.layout;
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

                axios.get(window.location.origin+'/'+apiUrl)
                    .then(response => {

                        this.photos = response.data.data;
                        this.totalItems = response.data.total;
                 
                    })
                    .catch(error => {
                        console.error('Error fetching photos:', error);
                    });
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
}

/* Customize pagination item */
.pagination li {
  list-style: none;
  display: inline-block;
  margin: 0 5px;
}

/* Customize pagination link */
.pagination a {
  text-decoration: none;
  padding: 5px 10px;
  border: 1px solid #ddd;
  color: #333;
}

/* Customize active page link */
.pagination li.active a {
  background-color: #007BFF;
  color: #fff;
  border-color: #007BFF;
}
</style>