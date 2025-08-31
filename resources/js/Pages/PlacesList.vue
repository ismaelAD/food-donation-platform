<template>
  <div class="min-h-screen bg-gradient-to-br from-slate-50 to-green-50 py-8 px-4">
    <Header/>
    <div class="max-w-7xl mx-auto">
      <!-- Header -->
      <div class="text-center mb-12">
        <h1 class="text-4xl font-bold text-slate-800 mb-2">Available Places</h1>
        <p class="text-slate-600 text-lg">Discover and book amazing locations</p>
      </div>

      <!-- Grid -->
      <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
        <div 
          v-for="place in places" 
          :key="place.id" 
          class="group bg-white rounded-2xl shadow-sm hover:shadow-xl transition-all duration-300 overflow-hidden border border-slate-100 hover:border-green-200"
        >
          <!-- Images Carousel -->
          <div class="relative h-48 bg-slate-100 overflow-hidden">
            <div 
              v-if="place.photos && place.photos.length > 0" 
              class="flex gap-1 h-full"
            >
              <img
                v-for="(photo, index) in place.photos.slice(0, 3)"
                :key="photo.id"
                :src="photo.url"
                :class="[
                  'object-cover transition-transform duration-300 group-hover:scale-105',
                  place.photos.length === 1 ? 'w-full' : 
                  place.photos.length === 2 ? 'w-1/2' : 
                  index === 0 ? 'w-1/2' : 'w-1/4'
                ]"
                :alt="`${place.title} - Photo ${index + 1}`"
                loading="lazy"
              />
              <div 
                v-if="place.photos.length > 3"
                class="w-1/4 bg-slate-900/70 flex items-center justify-center text-white font-semibold"
              >
                +{{ place.photos.length - 3 }}
              </div>
            </div>
            <div v-else class="h-full flex items-center justify-center bg-gradient-to-br from-slate-100 to-slate-200">
              <svg class="w-16 h-16 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
              </svg>
            </div>
          </div>

          <!-- Content -->
          <div class="p-6">
            <!-- Title -->
            <h3 class="font-bold text-xl text-slate-800 mb-3 group-hover:text-green-700 transition-colors">
              {{ place.title }}
            </h3>
            
            <!-- Description -->
            <p class="text-slate-600 text-sm leading-relaxed mb-4 line-clamp-3">
              {{ place.description }}
            </p>

            <!-- Info Grid -->
            <div class="space-y-3 mb-6">
              <div class="flex items-center gap-3">
                <div class="w-2 h-2 bg-green-500 rounded-full"></div>
                <span class="text-slate-700 text-sm font-medium">Available:</span>
                <span class="text-slate-600 text-sm">{{ place.availability }}</span>
              </div>
              
              <div class="flex items-center gap-3">
                <div class="w-2 h-2 bg-slate-400 rounded-full"></div>
                <span class="text-slate-700 text-sm font-medium">Contact:</span>
                <span class="text-slate-600 text-sm">{{ place.contact_info }}</span>
              </div>
            </div>

            <!-- Actions -->
            <div class="flex gap-3">

              
              <a 
                v-if="place.google_maps_link" 
                :href="place.google_maps_link" 
                target="_blank" 
                class="flex items-center justify-center px-4 py-3 border-2 border-slate-200 hover:border-green-300 text-slate-600 hover:text-green-700 rounded-xl transition-all duration-200 hover:bg-green-50"
              >
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                </svg>
              </a>
            </div>
          </div>
        </div>
      </div>

      <!-- Empty State -->
      <div v-if="!places || places.length === 0" class="text-center py-16">
        <div class="w-24 h-24 bg-slate-100 rounded-full flex items-center justify-center mx-auto mb-4">
          <svg class="w-12 h-12 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
          </svg>
        </div>
        <h3 class="text-xl font-semibold text-slate-700 mb-2">No places available</h3>
        <p class="text-slate-500">Check back later for new locations.</p>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue'
import { router } from '@inertiajs/vue3'
import Header from '@/Components/Header.vue'


const props = defineProps({
  places: Array
})

function requestUse(placeId) {
  router.post(`/places/${placeId}/request`, {}, {
    onSuccess: () => {
      // Toast notification plus élégante
      const toast = document.createElement('div')
      toast.className = 'fixed top-4 right-4 bg-green-600 text-white px-6 py-3 rounded-lg shadow-lg z-50 transform transition-all duration-300'
      toast.textContent = 'Request sent successfully!'
      document.body.appendChild(toast)
      
      setTimeout(() => {
        toast.style.transform = 'translateX(100%)'
        setTimeout(() => toast.remove(), 300)
      }, 3000)
    }
  })
}
</script>

<style scoped>
.line-clamp-3 {
  display: -webkit-box;
  -webkit-line-clamp: 3;
  -webkit-box-orient: vertical;
  overflow: hidden;
}
</style>