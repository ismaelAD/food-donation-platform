<!-- resources/js/Pages/FoodRequests/Show.vue -->
<template>
  <div class="min-h-screen bg-gray-50">
    <div class="max-w-4xl mx-auto py-8 px-4 space-y-6">
      
      <!-- Back Button -->
      <button @click="goBack"
        class="inline-flex items-center text-gray-600 hover:text-gray-900 transition-colors duration-200 group">
        <svg class="w-5 h-5 mr-2 transform group-hover:-translate-x-1 transition-transform duration-200" 
             fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
        </svg>
        Back to requests
      </button>

      <!-- Header Card -->
      <div class="bg-white shadow-sm rounded-xl border border-gray-200 p-8 space-y-6">
        <div class="space-y-4">
          <h1 class="text-3xl font-bold text-gray-900">{{ request.title }}</h1>
          <p class="text-gray-600 text-lg leading-relaxed">{{ request.description }}</p>
        </div>

        <!-- Progress Bars Section -->
        <div class="grid md:grid-cols-2 gap-6 pt-6 border-t border-gray-100">
          
          <!-- Food Progress -->
          <div v-if="request.quantity" class="space-y-3">
            <div class="flex justify-between items-center">
              <h3 class="text-sm font-semibold text-gray-700 uppercase tracking-wide">Food Needed</h3>
              <span class="text-sm text-gray-500">{{ totalFoodDonated }} / {{ request.quantity }} portions</span>
            </div>
            <div class="w-full bg-gray-200 rounded-full h-3">
              <div 
                :style="{ width: foodProgressPercent + '%' }" 
                class="h-3 bg-gradient-to-r from-blue-500 to-blue-600 rounded-full transition-all duration-300"
              ></div>
            </div>
            <p class="text-sm text-gray-600">
              <span class="font-medium">{{ Math.max(0, request.quantity - totalFoodDonated) }}</span> portions remaining
            </p>
          </div>

          <!-- Money Progress -->
          <div v-if="goalAmount > 0" class="space-y-3">
            <div class="flex justify-between items-center">
              <h3 class="text-sm font-semibold text-gray-700 uppercase tracking-wide">Funding Goal</h3>
              <span class="text-sm text-gray-500">€{{ totalMoneyCollected.toFixed(2) }} / €{{ goalAmount.toFixed(2) }}</span>
            </div>
            <div class="w-full bg-gray-200 rounded-full h-3">
              <div 
                :style="{ width: moneyProgressPercent + '%' }" 
                class="h-3 bg-gradient-to-r from-emerald-500 to-emerald-600 rounded-full transition-all duration-300"
              ></div>
            </div>
            <p class="text-sm text-gray-600">
              <span class="font-medium">€{{ remainingAmount.toFixed(2) }}</span> remaining
            </p>
          </div>

        </div>
      </div>

      <!-- Responses Section -->
      <div class="bg-white shadow-sm rounded-xl border border-gray-200 p-8">
        <h2 class="text-2xl font-bold text-gray-900 mb-6">Community Responses</h2>
        
        <div v-if="request.food_request_donations.length || request.contributions.length" class="space-y-4">
          <!-- Food Donations -->
          <div v-for="f in request.food_request_donations" :key="'fd' + f.id"
               class="flex items-center justify-between p-4 rounded-lg border border-gray-100 hover:border-gray-200 transition-colors">
            <div class="flex items-center space-x-4">
              <div class="w-10 h-10 bg-blue-100 rounded-full flex items-center justify-center">
                <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                </svg>
              </div>
              <div>
                <p class="font-semibold text-gray-900">{{ f.user.name }}</p>
                <p class="text-sm text-gray-600">{{ f.quantity }} servings available until {{ formatDate(f.available_until) }}</p>
              </div>
            </div>
            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-700">
              Food Donation
            </span>
          </div>

          <!-- Money Contributions -->
          <div v-for="c in request.contributions" :key="'c' + c.id"
               class="flex items-center justify-between p-4 rounded-lg border border-gray-100 hover:border-gray-200 transition-colors">
            <div class="flex items-center space-x-4">
              <div class="w-10 h-10 bg-emerald-100 rounded-full flex items-center justify-center">
                <svg class="w-5 h-5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"/>
                </svg>
              </div>
              <div>
                <p class="font-semibold text-gray-900">{{ c.partner.name }}</p>
                <p class="text-sm text-gray-600">€{{ parseFloat(c.amount).toFixed(2) }} contribution</p>
              </div>
            </div>
            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-emerald-100 text-emerald-700">
              Financial Support
            </span>
          </div>
        </div>

        <div v-else class="text-center py-12">
          <svg class="mx-auto w-12 h-12 text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
          </svg>
          <p class="text-gray-500 text-lg">No responses yet</p>
          <p class="text-gray-400 text-sm mt-1">Be the first to help out!</p>
        </div>
      </div>

      <!-- Action Forms -->
      <div class="grid md:grid-cols-2 gap-6">
        
        <!-- Food Offer Form -->
        <div class="bg-white shadow-sm rounded-xl border border-gray-200 p-6">
          <h3 class="text-xl font-bold text-gray-900 mb-4">Offer Food</h3>
          <form @submit.prevent="respond" class="space-y-4">
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-2">Number of Servings</label>
              <input 
                v-model.number="offer.quantity" 
                type="number" 
                min="1"
                required 
                class="w-full px-3 py-2 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors"
                placeholder="e.g., 10"
              />
            </div>
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-2">Available Until</label>
              <input 
                v-model="offer.available_until" 
                type="datetime-local" 
                required 
                class="w-full px-3 py-2 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors"
              />
            </div>
            <button 
              type="submit" 
              :disabled="offer.processing"
              class="w-full bg-blue-600 hover:bg-blue-700 disabled:opacity-50 disabled:cursor-not-allowed text-white font-medium px-4 py-2 rounded-lg transition-colors duration-200"
            >
              {{ offer.processing ? 'Sending...' : 'Send Food Offer' }}
            </button>
          </form>
        </div>

           <!-- Money Contribution Section -->
  <div v-if="goalAmount > 0" class="bg-white shadow-sm rounded-xl border border-gray-200 p-6">
    <h3 class="text-xl font-bold text-gray-900 mb-4">Contribute Money</h3>
    
    <!-- Étape 1 : montant only -->
    <div v-if="!showPaymentDetails">
      <form @submit.prevent="preparePayment" class="space-y-4">
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-2">Amount (€)</label>
          <input 
            v-model.number="payment.amount" 
            type="number"
            min="1" 
            step="0.01"
            required
            class="w-full px-3 py-2 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 transition-colors"
            placeholder="e.g., 25.00"
          />
        </div>
        <button 
          type="submit"
          class="w-full bg-emerald-600 hover:bg-emerald-700 text-white font-medium px-4 py-2 rounded-lg transition-colors duration-200"
        >
          Contribute Now
        </button>
      </form>
    </div>

    <!-- Étape 2 : détails de paiement -->
    <div v-else class="space-y-4 mt-4">
      <h4 class="text-md font-semibold text-gray-700">Payment Details</h4>
      <form @submit.prevent="contribute" class="space-y-4">
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-2">Cardholder Name</label>
          <input 
            v-model="payment.cardName"
            type="text" 
            required
            class="w-full px-3 py-2 border rounded shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
            placeholder="John Doe"
          />
        </div>
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-2">Card Number</label>
          <input 
            v-model="payment.cardNumber"
            type="text" 
            required
            pattern="\d{4}\s\d{4}\s\d{4}\s\d{4}"
            class="w-full px-3 py-2 border rounded shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
            placeholder="4242 4242 4242 4242"
          />
        </div>
        <div class="flex gap-3">
          <div class="flex-1">
            <label class="block text-sm font-medium text-gray-700 mb-2">Expiry (MM/YY)</label>
            <input 
              v-model="payment.expiry"
              type="text" 
              required
              pattern="(0[1-9]|1[0-2])\/\d{2}"
              class="w-full px-3 py-2 border rounded shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
              placeholder="MM/YY"
            />
          </div>
          <div class="flex-1">
            <label class="block text-sm font-medium text-gray-700 mb-2">CVC</label>
            <input 
              v-model="payment.cvc"
              type="text" 
              required
              pattern="\d{3}"
              class="w-full px-3 py-2 border rounded shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
              placeholder="123"
            />
          </div>
        </div>
        <button 
          type="submit"
          :disabled="payment.processing"
          class="w-full bg-blue-600 hover:bg-blue-700 text-white font-medium px-4 py-2 rounded-lg transition-colors duration-200 disabled:opacity-50"
        >
          {{ payment.processing ? 'Processing...' : 'Send Payment' }}
        </button>
        <button 
          type="button"
          @click="cancelPayment"
          class="w-full text-center text-gray-600 mt-2 hover:underline"
        >
          Cancel
        </button>
      </form>
    </div>
  </div>
        

      </div>
    </div>
  </div>
</template>

<script setup>
import { ref,computed } from 'vue'
import { useForm, router } from '@inertiajs/vue3'

const props = defineProps({ request: Object })
const showPaymentDetails = ref(false)
const payment = useForm({
  amount: null,
  cardName: '',
  cardNumber: '',
  expiry: '',
  cvc: '',
  processing: false,
})

function preparePayment() {
  if (!payment.amount || payment.amount <= 0) return
  showPaymentDetails.value = true
}

function contribute() {
  // validation front supplémentaire
  if (!payment.cardName || !payment.cardNumber || !payment.expiry || !payment.cvc) {
    alert('Please fill in all payment fields correctly.')
    return
  }
  payment.processing = true
  payment.post(`/food-requests/${props.request.id}/contribute`, {
    preserveScroll: true,
    onFinish: () => payment.processing = false,
    onSuccess: () => {
      payment.reset()
      showPaymentDetails.value = false
    },
  })
}

function cancelPayment() {
  showPaymentDetails.value = false
}

// Food offer form
const offer = useForm({ quantity: null, available_until: null })
function respond() {
  offer.post(`/food-requests/${props.request.id}/respond`, {
    preserveScroll: true,
    onSuccess: () => offer.reset(),
  })
}

// Money contribution form


// Food progress calculations
const totalFoodDonated = computed(() =>
  props.request.food_request_donations.reduce((sum, d) => sum + Number(d.quantity), 0)
)
const neededFood = computed(() => props.request.quantity || 0)
const foodProgressPercent = computed(() =>
  neededFood.value > 0 ? Math.min(100, (totalFoodDonated.value / neededFood.value) * 100) : 0
)

// Money progress calculations
const totalMoneyCollected = computed(() =>
  props.request.contributions.reduce((sum, c) => sum + Number(c.amount), 0)
)
const goalAmount = computed(() => Number(props.request.target_amount) || 0)
const remainingAmount = computed(() => Math.max(0, goalAmount.value - totalMoneyCollected.value))
const moneyProgressPercent = computed(() =>
  goalAmount.value > 0 ? Math.min(100, (totalMoneyCollected.value / goalAmount.value) * 100) : 0
)

// Navigation
function goBack() {
  // Check if there's a previous page in history
  if (window.history.length > 1) {
    window.history.back()
  } else {
    // Fallback to food requests index
    router.visit('/food-requests')
  }
}

// Date formatting
function formatDate(dt) {
  return new Date(dt).toLocaleString('en-GB', {
    day: 'numeric',
    month: 'short',
    year: 'numeric',
    hour: '2-digit',
    minute: '2-digit'
  })
}
</script>