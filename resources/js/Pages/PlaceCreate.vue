<template>
  <div class="min-h-screen bg-gradient-to-br from-slate-50 to-green-50 py-8 px-4">
    <Header/>
    <div class="max-w-3xl mx-auto">
      <!-- Header -->
      <div class="text-center mb-8">
        <h1 class="text-3xl font-bold text-slate-800 mb-2">Add New Place</h1>
        <p class="text-slate-600">Share a beautiful location with the community</p>
      </div>

      <!-- Form Card -->
      <div class="bg-white rounded-2xl shadow-lg border border-slate-100 overflow-hidden">
        <form @submit.prevent="submitForm" novalidate class="p-8">
          <!-- Form Grid -->
          <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <!-- Left Column -->
            <div class="space-y-6">
              <!-- Title -->
              <div class="form-group">
                <label class="block text-sm font-semibold text-slate-700 mb-2">
                  Title <span class="text-red-500">*</span>
                </label>
                <input 
                  v-model="form.title" 
                  class="w-full border border-slate-200 rounded-xl px-4 py-3 focus:ring-2 focus:ring-green-500 focus:border-transparent transition-all duration-200 placeholder:text-slate-400" 
                  placeholder="Enter place title"
                  required 
                />
                <p v-if="form.errors.title" class="text-red-500 text-sm mt-2 flex items-center gap-1">
                  <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                  </svg>
                  {{ form.errors.title }}
                </p>
              </div>

              <!-- Address -->
              <div class="form-group">
                <label class="block text-sm font-semibold text-slate-700 mb-2">
                  Address <span class="text-red-500">*</span>
                </label>
                <input 
                  v-model="form.address" 
                  class="w-full border border-slate-200 rounded-xl px-4 py-3 focus:ring-2 focus:ring-green-500 focus:border-transparent transition-all duration-200 placeholder:text-slate-400" 
                  placeholder="Enter full address"
                  required 
                />
              </div>

              <!-- Contact Info -->
              <div class="form-group">
                <label class="block text-sm font-semibold text-slate-700 mb-2">
                  Contact Info <span class="text-red-500">*</span>
                </label>
                <input 
                  v-model="form.contact_info" 
                  class="w-full border border-slate-200 rounded-xl px-4 py-3 focus:ring-2 focus:ring-green-500 focus:border-transparent transition-all duration-200 placeholder:text-slate-400" 
                  placeholder="Phone, email, etc."
                  required 
                />
              </div>

              <!-- Availability -->
              <div class="form-group">
                <label class="block text-sm font-semibold text-slate-700 mb-2">
                  Available From <span class="text-red-500">*</span>
                </label>
                <input 
                  type="date" 
                  v-model="form.availability" 
                  class="w-full border border-slate-200 rounded-xl px-4 py-3 focus:ring-2 focus:ring-green-500 focus:border-transparent transition-all duration-200" 
                  required 
                />
              </div>
            </div>

            <!-- Right Column -->
            <div class="space-y-6">
              <!-- Description -->
              <div class="form-group">
                <label class="block text-sm font-semibold text-slate-700 mb-2">
                  Description <span class="text-red-500">*</span>
                </label>
                <textarea 
                  v-model="form.description" 
                  rows="4"
                  class="w-full border border-slate-200 rounded-xl px-4 py-3 focus:ring-2 focus:ring-green-500 focus:border-transparent transition-all duration-200 placeholder:text-slate-400 resize-none" 
                  placeholder="Describe this beautiful place..."
                  required
                ></textarea>
                <p v-if="form.errors.description" class="text-red-500 text-sm mt-2 flex items-center gap-1">
                  <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                  </svg>
                  {{ form.errors.description }}
                </p>
              </div>

              <!-- Google Maps -->
              <div class="form-group">
                <label class="block text-sm font-semibold text-slate-700 mb-2">
                  Google Maps Link
                  <span class="text-slate-500 font-normal">(optional)</span>
                </label>
                <input 
                  v-model="form.google_maps_link" 
                  class="w-full border border-slate-200 rounded-xl px-4 py-3 focus:ring-2 focus:ring-green-500 focus:border-transparent transition-all duration-200 placeholder:text-slate-400" 
                  placeholder="https://maps.google.com/..."
                />
              </div>
            </div>
          </div>

          <!-- Photos Section -->
          <div class="mt-8 pt-6 border-t border-slate-100">
            <label class="block text-sm font-semibold text-slate-700 mb-4">
              Photos
              <span class="text-slate-500 font-normal">(Upload multiple images)</span>
            </label>
            
            <!-- Upload Area -->
            <div 
              class="border-2 border-dashed border-slate-200 rounded-xl p-8 text-center hover:border-green-300 transition-colors duration-200 cursor-pointer"
              @click="$refs.fileInput.click()"
              @dragover.prevent="dragover = true"
              @dragleave.prevent="dragover = false"
              @drop.prevent="handleDrop"
              :class="{ 'border-green-300 bg-green-50': dragover }"
            >
              <input
                ref="fileInput"
                type="file"
                multiple
                accept="image/*"
                @change="handleFiles"
                class="hidden"
              />
              
              <div class="space-y-3">
                <div class="w-16 h-16 bg-slate-100 rounded-full flex items-center justify-center mx-auto">
                  <svg class="w-8 h-8 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                  </svg>
                </div>
                <div>
                  <p class="text-slate-600 font-medium">Click to upload images</p>
                  <p class="text-slate-500 text-sm">or drag and drop files here</p>
                  <p class="text-slate-400 text-xs mt-1">PNG, JPG, WebP up to 10MB each</p>
                </div>
              </div>
            </div>

            <!-- Image Previews -->
            <div v-if="previews.length" class="mt-6">
              <h4 class="text-sm font-semibold text-slate-700 mb-3">Selected Images ({{ previews.length }})</h4>
              <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-4">
                <div 
                  v-for="(preview, index) in previews" 
                  :key="index"
                  class="relative group"
                >
                  <img 
                    :src="preview" 
                    class="w-full h-24 object-cover rounded-lg border border-slate-200" 
                    :alt="`Preview ${index + 1}`"
                  />
                  <button
                    type="button"
                    @click="removeImage(index)"
                    class="absolute -top-2 -right-2 w-6 h-6 bg-red-500 text-white rounded-full flex items-center justify-center text-xs hover:bg-red-600 transition-colors opacity-0 group-hover:opacity-100"
                  >
                    ×
                  </button>
                </div>
              </div>
            </div>

            <p v-if="form.errors['photos[]'] || form.errors.photos" class="text-red-500 text-sm mt-3 flex items-center gap-1">
              <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
              </svg>
              {{ form.errors['photos[]'] || form.errors.photos }}
            </p>
          </div>

          <!-- Progress Bar -->
          <div v-if="progress > 0" class="mt-6">
            <div class="flex justify-between items-center mb-2">
              <span class="text-sm font-medium text-slate-700">Uploading...</span>
              <span class="text-sm text-slate-600">{{ progress }}%</span>
            </div>
            <div class="w-full bg-slate-200 rounded-full h-2">
              <div 
                class="h-2 rounded-full bg-gradient-to-r from-green-500 to-green-600 transition-all duration-300" 
                :style="{ width: progress + '%' }"
              ></div>
            </div>
          </div>

          <!-- Actions -->
          <div class="flex gap-4 mt-8 pt-6 border-t border-slate-100">
            <button 
              type="button"
              @click="router.visit('/places')"
              class="px-6 py-3 border-2 border-slate-200 text-slate-600 rounded-xl font-semibold hover:border-slate-300 hover:bg-slate-50 transition-all duration-200"
            >
              Cancel
            </button>
            
            <button 
              type="submit" 
              :disabled="form.processing"
              class="flex-1 bg-gradient-to-r from-green-600 to-green-700 hover:from-green-700 hover:to-green-800 disabled:from-slate-400 disabled:to-slate-500 text-white font-semibold py-3 px-6 rounded-xl transition-all duration-200 shadow-sm hover:shadow-md transform hover:-translate-y-0.5 disabled:transform-none disabled:shadow-none flex items-center justify-center gap-2"
            >
              <svg v-if="form.processing" class="w-5 h-5 animate-spin" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
              </svg>
              <span>{{ form.processing ? 'Creating Place...' : 'Create Place' }}</span>
            </button>
          </div>
        </form>
      </div>
    </div>

    <!-- Success Toast (will be shown via JS) -->
    <div id="success-toast" class="fixed top-4 right-4 transform translate-x-full transition-transform duration-300 z-50">
      <div class="bg-green-600 text-white px-6 py-4 rounded-xl shadow-lg flex items-center gap-3">
        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
          <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
        </svg>
        <span class="font-medium">Place created successfully!</span>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue'
import { useForm } from '@inertiajs/vue3'
import { router } from '@inertiajs/vue3'
import Header from '@/Components/Header.vue'

const form = useForm({
  title: '',
  description: '',
  address: '',
  google_maps_link: '',
  contact_info: '',
  availability: '',
  photos: []
})

const previews = ref([])
const progress = ref(0)
const dragover = ref(false)

function handleFiles(e) {
  const files = Array.from(e.target.files || [])
  processFiles(files)
}

function handleDrop(e) {
  dragover.value = false
  const files = Array.from(e.dataTransfer.files || [])
  processFiles(files)
}

function processFiles(files) {
  const maxSize = 10 * 1024 * 1024 // 10MB max per file
  const validFiles = []
  
  for (const file of files) {
    if (!file.type.startsWith('image/')) {
      showToast('Please select only image files', 'error')
      continue
    }
    if (file.size > maxSize) {
      showToast(`File ${file.name} exceeds 10MB limit`, 'error')
      continue
    }
    validFiles.push(file)
  }

  if (validFiles.length === 0) return

  form.photos = [...form.photos, ...validFiles]

  // Generate previews
  validFiles.forEach(file => {
    const reader = new FileReader()
    reader.onload = (ev) => previews.value.push(ev.target.result)
    reader.readAsDataURL(file)
  })
}

function removeImage(index) {
  previews.value.splice(index, 1)
  const newFiles = Array.from(form.photos)
  newFiles.splice(index, 1)
  form.photos = newFiles
}

function showToast(message, type = 'success') {
  const toast = document.createElement('div')
  const bgColor = type === 'error' ? 'bg-red-600' : 'bg-green-600'
  const icon = type === 'error' ? 
    '<path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>' :
    '<path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>'
  
  toast.className = `fixed top-4 right-4 ${bgColor} text-white px-6 py-4 rounded-xl shadow-lg z-50 transform transition-all duration-300 flex items-center gap-3`
  toast.innerHTML = `
    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">${icon}</svg>
    <span class="font-medium">${message}</span>
  `
  
  document.body.appendChild(toast)
  
  setTimeout(() => toast.style.transform = 'translateX(0)', 100)
  setTimeout(() => {
    toast.style.transform = 'translateX(100%)'
    setTimeout(() => toast.remove(), 300)
  }, 3000)
}

function submitForm() {
  // Client-side validation
  if (!form.title?.trim() || !form.description?.trim() || !form.address?.trim() || !form.contact_info?.trim()) {
    showToast('Please fill all required fields', 'error')
    return
  }

  form.post('/places', {
    forceFormData: true,
    onProgress: (event) => {
      if (event.lengthComputable) {
        progress.value = Math.round((event.loaded / event.total) * 100)
      }
    },
    onSuccess: () => {
      showToast('Place created successfully!')
      setTimeout(() => router.visit('/places'), 1500)
    },
    onError: (errors) => {
      console.error('Upload errors', errors)
      showToast('Please check the form for errors', 'error')
    },
    onFinish: () => {
      progress.value = 0
    }
  })
}
</script>

<style scoped>
.form-group input:focus,
.form-group textarea:focus {
  box-shadow: 0 0 0 3px rgba(34, 197, 94, 0.1);
}

/* Animations pour les previews */
.grid > div {
  animation: fadeInUp 0.3s ease-out forwards;
}

@keyframes fadeInUp {
  from {
    opacity: 0;
    transform: translateY(10px);
  }
  to {
    opacity: 1;
    transform: translateY(0);
  }
}
</style>