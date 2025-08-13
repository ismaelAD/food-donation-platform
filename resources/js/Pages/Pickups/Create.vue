<template>
  <Header/>
  <div class="min-h-screen bg-gradient-to-br from-blue-50 to-indigo-50 p-6">
    <div class="max-w-lg mx-auto bg-white shadow-xl rounded-2xl overflow-hidden">
      <!-- Header avec bouton retour -->
      <div class="bg-gradient-to-r from-indigo-500 to-blue-600 p-6 text-white">
        <div class="flex items-center mb-2">
          <button 
            @click="goBack" 
            class="mr-4 p-2 rounded-full hover:bg-white/20 transition-colors duration-200 flex items-center justify-center"
            aria-label="Retour à la page précédente"
          >
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
            </svg>
          </button>
          <h1 class="text-2xl font-bold">Schedule Pickup</h1>
        </div>
        <p class="text-blue-100 text-sm">Organize your food donation pickup</p>
      </div>

      <div class="p-6">
        <!-- Information sur le don avec design amélioré -->
        <div class="mb-6 p-4 bg-gradient-to-r from-blue-50 to-indigo-50 rounded-xl border-l-4 border-indigo-500">
          <h2 class="font-semibold text-gray-800 mb-3 flex items-center">
            <svg class="w-5 h-5 mr-2 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            Donation Details
          </h2>
          <div class="space-y-2">
            <div class="flex items-center">
              <span class="font-medium text-gray-700 min-w-20">Category:</span>
              <span class="ml-2 px-3 py-1 bg-green-100 text-green-800 rounded-full text-sm font-medium">
                {{ donation.category }}
              </span>
              <span class="ml-2 px-2 py-1 bg-blue-100 text-blue-800 rounded-full text-sm">
                {{ donation.quantity }} portions
              </span>
            </div>
            <div class="flex items-center">
              <span class="font-medium text-gray-700 min-w-20">Donor:</span>
              <span class="ml-2 text-gray-900">{{ donation.user.name }}</span>
            </div>
          </div>
        </div>

        <!-- Message d'erreur pour les receivers -->
        <div v-if="showQuantityError" class="mb-6 p-4 bg-red-50 border-l-4 border-red-400 rounded-xl">
          <div class="flex items-center">
            <svg class="w-5 h-5 text-red-400 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            <p class="text-red-700 font-medium">
              Quantity limit exceeded
            </p>
          </div>
          <p class="text-red-600 text-sm mt-2">
            As a recipient, you can only collect donations of 5 portions maximum.
          </p>
        </div>

        <!-- Formulaire amélioré -->
        <form @submit.prevent="submit" class="space-y-6">
          <div class="space-y-2">
            <label class="block text-gray-700 font-medium flex items-center">
              <svg class="w-4 h-4 mr-2 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3a2 2 0 012-2h4a2 2 0 012 2v4m-6 4v10a2 2 0 002 2h4a2 2 0 002-2V11m-6 0h6"/>
              </svg>
              Pickup Date & Time
            </label>
            <input 
              type="datetime-local" 
              v-model="form.scheduled_at"
              class="w-full border-2 border-gray-200 rounded-xl p-3 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 transition-all duration-200" 
              required 
              :min="minDateTime"
            />
          </div>

          <div class="space-y-2">
            <label class="block text-gray-700 font-medium flex items-center">
              <svg class="w-4 h-4 mr-2 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
              </svg>
              Notes (optional)
            </label>
            <textarea 
              v-model="form.notes" 
              rows="3"
              placeholder="Add additional information..."
              class="w-full border-2 border-gray-200 rounded-xl p-3 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 transition-all duration-200 resize-none"
            ></textarea>
          </div>

          <div class="flex gap-3 pt-4">
            <button 
              type="button"
              @click="goBack"
              class="flex-1 bg-gray-100 hover:bg-gray-200 text-gray-700 py-3 rounded-xl font-medium transition-all duration-200 flex items-center justify-center"
            >
              <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
              </svg>
              Cancel
            </button>
            
            <button 
              type="submit"
              :disabled="isSubmitDisabled"
              :class="[
                'flex-1 py-3 rounded-xl font-medium transition-all duration-200 flex items-center justify-center',
                isSubmitDisabled 
                  ? 'bg-gray-300 text-gray-500 cursor-not-allowed' 
                  : 'bg-gradient-to-r from-green-500 to-green-600 hover:from-green-600 hover:to-green-700 text-white shadow-lg hover:shadow-xl transform hover:scale-[1.02]'
              ]"
            >
              <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
              </svg>
              {{ form.processing ? 'Saving...' : 'Save Pickup' }}
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>
</template>

<script setup>
import { useForm } from '@inertiajs/vue3'
import { computed, onMounted } from 'vue'
import Header from '@/Components/Header.vue'


const props = defineProps({
  donation: Object,
  user: Object, // Ajout de l'utilisateur connecté pour vérifier son rôle
  auth: Object // Alternative si l'utilisateur est dans auth
})

const form = useForm({
  donation_id: props.donation.id,
  scheduled_at: '',
  notes: ''
})

// Vérifier si l'utilisateur est un receiver et si la quantité dépasse 5
const currentUser = computed(() => props.user || props.auth?.user)
const isReceiver = computed(() => currentUser.value?.role === 'receiver')
const showQuantityError = computed(() => 
  isReceiver.value && props.donation.quantity > 5
)

// Désactiver le bouton de soumission si receiver avec quantité > 5
const isSubmitDisabled = computed(() => 
  form.processing || showQuantityError.value
)

// Date minimum (maintenant)
const minDateTime = computed(() => {
  const now = new Date()
  now.setMinutes(now.getMinutes() - now.getTimezoneOffset())
  return now.toISOString().slice(0, 16)
})

// Fonction pour revenir à la page précédente
const goBack = () => {
  if (window.history.length > 1) {
    window.history.back()
  } else {
    // Fallback si pas d'historique
    window.location.href = '/donations'
  }
}

// Soumission du formulaire avec vérification
const submit = () => {
  if (showQuantityError.value) {
    return // Empêcher la soumission
  }
  form.post('/pickups')
}

// Définir une date par défaut (dans 2 heures)
onMounted(() => {
  const defaultDate = new Date()
  defaultDate.setHours(defaultDate.getHours() + 2)
  defaultDate.setMinutes(0, 0, 0)
  form.scheduled_at = defaultDate.toISOString().slice(0, 16)
})
</script>