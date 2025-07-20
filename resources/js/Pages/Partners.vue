<template>
  <div>
      <img
  src="/bckg3.jpg"
  alt="Impact Background"
  class="fixed inset-0 w-full h-full object-cover opacity-100 pointer-events-none select-none"
  style="z-index: -1;"
/>
    <div class="max-w-6xl mx-auto">
      <!-- Header -->
      <div class="bg-white/10 backdrop-blur-md rounded-2xl p-8 mb-8 text-center">
        <h1 class="text-4xl font-bold text-gray-800 mb-2 animate-fade-in">
          Our Partner Organizations 
        </h1>
        <p class="text-gray-600 text-lg">
          These partners regularly contribute to food donations
        </p>
      </div>

      <!-- Partners Grid -->
      <section class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        <div
          v-for="partner in approvedPartners"
          :key="partner.id"
          class="bg-white rounded-xl shadow-lg p-6 transform hover:-translate-y-1 hover:shadow-xl transition-all duration-300"
        >
          <!-- Partner Info -->
          <div class="flex items-center justify-between mb-4">
            <div class="text-xl font-bold text-gray-800">{{ partner.name }}</div>

          </div>

          <div class="mb-2 text-gray-600 text-sm">
            <strong>Type:</strong> {{ partner.type }}
          </div>
          <div class="mb-2 text-gray-600 text-sm">
            <strong>Contact:</strong> {{ partner.contact_email }} | {{ partner.contact_phone }}
          </div>
          <div class="mb-2 text-gray-600 text-sm">
            <strong>Address:</strong> {{ partner.address }}
          </div>
          <div class="text-gray-600 text-sm">
            <strong>Total Donations:</strong> {{ partner.donations_count }}
          </div>

          <!-- Details button -->
          <button
            class="mt-4 w-full bg-green-600 hover:bg-green-700 text-white font-medium py-2 px-4 rounded-lg transition-colors duration-200"
            @click="viewPartner(partner)"
          >
            View Details
          </button>
          <!-- Juste après le bouton "View Details" -->
<button
  class="mt-2 w-full bg-blue-600 hover:bg-blue-700 text-white font-medium py-2 px-4 rounded-lg transition-colors duration-200"
  @click="sendRequest(partner.id)"
>
  Send Request
</button>

        </div>
      </section>
    </div>
  </div>
</template>

<script setup>
import { computed } from 'vue'
import { router } from '@inertiajs/vue3'

const props = defineProps({
  partners: Array
})

const sendRequest = (partnerId) => {
  if (confirm('Do you want to send a partnership request to this partner?')) {
    router.post('/partner-requests/send', { partner_id: partnerId }, {
      onSuccess: () => {
        alert('Request sent successfully!');
        router.reload();  // Optionnel, pour rafraîchir la page après envoi
      },
      onError: (error) => {
        console.error('Error:', error);
        alert('Failed to send the request. Please try again.');
      }
    })
  }
}


/**
 * Filtrer uniquement les partenaires approuvés
 */
const approvedPartners = computed(() =>
  props.partners.filter(p => p.status === 'approved')
)

/**
 * Styliser le badge de niveau
 */
const getLevelColor = (level) => {
  switch (level) {
    case 1:
      return 'bg-gray-200 text-gray-800'
    case 2:
      return 'bg-blue-200 text-blue-800'
    case 3:
      return 'bg-purple-200 text-purple-800'
    default:
      return 'bg-gray-200 text-gray-800'
  }
}

/**
 * Action bouton détails
 */
const viewPartner = (partner) => {
  console.log('Selected partner:', partner)
  // ou utiliser router.visit(`/partners/${partner.id}`) plus tard
}
</script>

<style scoped>
@keyframes fade-in {
  from {
    opacity: 0;
    transform: translateY(-20px);
  }
  to {
    opacity: 1;
    transform: translateY(0);
  }
}

.animate-fade-in {
  animation: fade-in 0.6s ease-out;
}
</style>
