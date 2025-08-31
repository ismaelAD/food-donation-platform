<template>
  <Header/>
<nav class="bg-white border-b border-gray-200 sticky top-0 z-20 shadow-sm">
  <div class="max-w-7xl mx-auto px-4 py-3 flex items-center justify-between">
    
    <div class="flex items-center space-x-3">
      <img src="/logo2.webp" alt="Logo" class="w-10 h-10 rounded-full object-contain" />
      <span class="text-xl font-semibold text-gray-800">FoodShare</span>
    </div>

    <button @click="goBack"
            class="flex items-center text-sm text-gray-600 hover:text-emerald-600 transition">
      <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
      </svg>
      Back
    </button>

  </div>
</nav>
        <img
  src="/bckg5.jpg"
  alt="Impact Background"
  class="fixed inset-0 w-full h-full object-cover opacity-50 pointer-events-none select-none"
  style="z-index: -1;"
/>
  <div class="max-w-4xl mx-auto py-10 px-4">
    <h1 class="text-3xl font-bold mb-6">All Food Requests</h1>
    <div v-if="requests.length" class="space-y-4">
      <div
        v-for="r in requests"
        :key="r.id"
        class="p-6 bg-white rounded-xl shadow hover:shadow-lg transition"
      >
        <h2 class="text-xl font-semibold">{{ r.title }}</h2>
        <p class="text-gray-600 mb-2">{{ r.description }}</p>
        <p class="text-sm text-gray-500">
          Needed before: {{ formatDate(r.needed_before) }}
        </p>
        <Link
          :href="`/food-requests/${r.id}`"
          class="mt-3 inline-block text-emerald-600 hover:underline"
        >
          View Request
        </Link>
      </div>
    </div>
    <p v-else class="text-gray-500">No food requests at the moment.</p>
  </div>
</template>

<script setup>
import { Link } from '@inertiajs/vue3'
import Header from '@/Components/Header.vue'


const props = defineProps({ requests: [Array, Object] })


function goBack() {
  if (window.history.length > 1) {
    window.history.back()
  } else {
    window.location.href = '/' // fallback
  }
}

const formatDate = (dt) => {
  if (!dt) return ''
  return new Date(dt).toLocaleString('en-GB', {
    day: 'numeric', month: 'short', year: 'numeric',
    hour: '2-digit', minute: '2-digit'
  })
}
</script>
