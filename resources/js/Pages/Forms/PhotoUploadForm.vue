<template>
    <form ref="form" @submit.prevent="uploadImages">
        <div class="py-12">
            <BreezeValidationErrors class="mb-4" >
                <div v-if="errors.length > 0">
                    <ul>
                        <li v-for="error in errors" :key="error">{{ error[0] }}</li>
                    </ul>
                </div>
            </BreezeValidationErrors>
            <BreezeInput type="file" ref="fileInput" @change="handleFileChange" multiple />
            <BreezeButton>Upload</BreezeButton>
        </div>
    </form>
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
            errors: [],          
        }
    },

    methods: {
        handleFileChange(event) {
            this.images = event.target.files;
        },

        async uploadImages() {
            let formData = new FormData();

            for (let i = 0; i < this.images.length; i++) {
                formData.append('images[]', this.images[i]);
            }
            try {
                await axios.post('/upload', formData, {
                    headers: {
                    'Content-Type': 'multipart/form-data',
                    },
                })
                this.$refs.form.reset();
                this.$emit('loadImages');
            }
            catch(error) {
                this.errors = Object.values(error.response.data.errors);
            }                
        },
    },

}
</script>
