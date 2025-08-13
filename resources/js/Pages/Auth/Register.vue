<template>
  <GuestLayout>
    <Head title="Register" />

    <form @submit.prevent="submit" enctype="multipart/form-data" class="space-y-6">
      <!-- Full Name -->
      <div>
        <InputLabel for="name" value="Full Name" />
        <TextInput
          id="name"
          type="text"
          v-model="form.name"
          required
          autofocus
          autocomplete="name"
          class="mt-1 block w-full"
        />
        <InputError :message="form.errors.name" class="mt-1" />
      </div>

      <!-- Email Address -->
      <div>
        <InputLabel for="email" value="Email Address" />
        <TextInput
          id="email"
          type="email"
          v-model="form.email"
          required
          autocomplete="username"
          class="mt-1 block w-full"
        />
        <InputError :message="form.errors.email" class="mt-1" />
      </div>

      <!-- Select Role -->
      <div>
        <InputLabel for="role" value="Register As" />
        <select
          id="role"
          v-model="form.role"
          class="mt-1 block w-full border-gray-300 rounded-md shadow-sm"
        >
          <option value="receiver">Receiver</option>
          <option value="organization">Organization</option>
          <option value="partner">Partner</option>
        </select>
        <InputError :message="form.errors.role" class="mt-1" />
      </div>

      <!-- Extra fields for Org/Partner -->
      <div v-if="form.role !== 'receiver'" class="space-y-4">
        <!-- Profile Picture -->
        <div>
          <InputLabel for="profile_image" value="Profile Picture" />
          <div class="relative inline-block">
            <input
              id="profile_image"
              type="file"
              accept="image/*"
              @change="handleProfileImage"
              class="absolute inset-0 w-full h-full opacity-0 cursor-pointer"
            />
            <button
              type="button"
              class="px-4 py-2 bg-indigo-500 hover:bg-indigo-600 text-white font-medium rounded-lg shadow"
            >
              Select Profile Picture
            </button>
          </div>
          <p v-if="form.profile_image" class="mt-1 text-sm text-gray-700">
            Selected: {{ form.profile_image.name }}
          </p>
          <InputError :message="form.errors.profile_image" class="mt-1" />
        </div>

        <!-- Common contact fields -->
        <div>
          <InputLabel
            for="org_name"
            :value="form.role === 'organization' ? 'Organization Name' : 'Partner Name'"
          />
          <TextInput
            id="org_name"
            v-model="form.partner_name"
            class="mt-1 block w-full"
          />
          <InputError :message="form.errors.partner_name" class="mt-1" />
        </div>

        <div>
          <InputLabel for="contact_email" value="Contact Email" />
          <TextInput
            id="contact_email"
            v-model="form.contact_email"
            class="mt-1 block w-full"
          />
          <InputError :message="form.errors.contact_email" class="mt-1" />
        </div>

        <div>
          <InputLabel for="contact_phone" value="Contact Phone" />
          <TextInput
            id="contact_phone"
            v-model="form.contact_phone"
            class="mt-1 block w-full"
          />
          <InputError :message="form.errors.contact_phone" class="mt-1" />
        </div>

        <div>
          <InputLabel for="address" value="Address" />
          <TextInput
            id="address"
            v-model="form.address"
            class="mt-1 block w-full"
          />
          <InputError :message="form.errors.address" class="mt-1" />
        </div>

        <div>
          <InputLabel
            for="type"
            :value="form.role === 'organization' ? 'Organization Type' : 'Partner Type'"
          />
          <TextInput id="type" v-model="form.type" class="mt-1 block w-full" />
          <InputError :message="form.errors.type" class="mt-1" />
        </div>

        <!-- Document Upload -->
        <div>
          <InputLabel for="org_document" value="Upload Document" />
          <div class="relative inline-block">
            <input
              id="org_document"
              type="file"
              accept=".pdf,.jpg,.png"
              @change="handleOrgDocument"
              class="absolute inset-0 w-full h-full opacity-0 cursor-pointer"
            />
            <button
              type="button"
              class="px-4 py-2 bg-indigo-500 hover:bg-indigo-600 text-white font-medium rounded-lg shadow"
            >
              Select Document
            </button>
          </div>
          <p v-if="form.org_document" class="mt-1 text-sm text-gray-700">
            Selected: {{ form.org_document.name }}
          </p>
          <InputError :message="form.errors.org_document" class="mt-1" />
        </div>
      </div>

      <!-- Password -->
      <div>
        <InputLabel for="password" value="Password" />
        <TextInput
          id="password"
          type="password"
          v-model="form.password"
          required
          autocomplete="new-password"
          class="mt-1 block w-full"
        />
        <InputError :message="form.errors.password" class="mt-1" />
      </div>

      <!-- Confirm Password -->
      <div>
        <InputLabel for="password_confirmation" value="Confirm Password" />
        <TextInput
          id="password_confirmation"
          type="password"
          v-model="form.password_confirmation"
          required
          autocomplete="new-password"
          class="mt-1 block w-full"
        />
        <InputError :message="form.errors.password_confirmation" class="mt-1" />
      </div>

      <!-- Submit -->
      <div class="flex items-center justify-end space-x-4 mt-6">
        <Link
          href="/login"
          class="text-sm text-gray-600 underline hover:text-gray-900"
        >
          Already registered?
        </Link>

        <PrimaryButton
          :disabled="form.processing"
          class="px-6 py-2"
        >
          {{ form.processing ? 'Registering...' : 'Register' }}
        </PrimaryButton>
      </div>
    </form>
  </GuestLayout>
</template>

<script setup>
import GuestLayout from '@/Layouts/GuestLayout.vue'
import InputError from '@/Components/InputError.vue'
import InputLabel from '@/Components/InputLabel.vue'
import PrimaryButton from '@/Components/PrimaryButton.vue'
import TextInput from '@/Components/TextInput.vue'
import { Head, Link, useForm } from '@inertiajs/vue3'

const form = useForm({
  name: '',
  email: '',
  password: '',
  password_confirmation: '',
  role: 'receiver',
  partner_name: '',
  contact_email: '',
  contact_phone: '',
  address: '',
  type: '',
  profile_image: null,
  org_document: null,
})

function handleProfileImage(e) {
  form.profile_image = e.target.files[0]
}

function handleOrgDocument(e) {
  form.org_document = e.target.files[0]
}

const submit = () => {
  form.post(route('register'), {
    forceFormData: true,
    onFinish: () => form.reset('password', 'password_confirmation'),
  })
}
</script>
