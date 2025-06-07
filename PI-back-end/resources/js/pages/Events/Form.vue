<script setup lang="ts">
import { ref, computed, onMounted, reactive } from 'vue'
import { usePage, router } from '@inertiajs/vue3'


const formSubmitted = ref(false)

const page = usePage()
const props = defineProps({
  event: {
    type: Object,
    required: true,
  },
  questions: {
    type: Array as () => {
      id: number
      name: string
      description: string
      options: string[]
      mandatory: boolean
      event_question_id: number
    }[],
    required: true,
  },
})

const answers = reactive<{ [key: number]: string }>({})

const categoryBackground = computed(() => {
  let url = "";

  if (props.event.custom_background) {
    url = `/storage/${props.event.custom_background}`;
  } else {
    switch (props.event.category) {
      case 'Sports':
        url = '/images/bg-sport.jpg';
        break;
      case 'Health':
        url = '/images/bg-health.jpg';
        break;
      case 'Technology':
        url = '/images/bg-tech.jpg';
        break;
      default:
        return { backgroundColor: '#bfdbfe' }; // equivalente a bg-blue-100
    }
  }

  return {
    backgroundImage: `url('${url}')`,
    backgroundSize: 'cover',
    backgroundPosition: 'center'
  };
});



const eventHasStarted = computed(() => {
  const eventDate = new Date(`${props.event.start_date}T${props.event.start_time}`)
  const now = new Date()
  return now >= eventDate
})

const submitForm = () => {
  const missing = props.questions.filter(q => q.mandatory && !answers[q.id])
  if (missing.length > 0) {
    alert('Please fill in all mandatory questions before submitting.')
    return
  }

  const payload = props.questions.map((q) => ({
    event_question_id: q.event_question_id,
    answer: answers[q.id] ?? null,
  }))

  router.post(`/events/${props.event.id}/submit-form`, {
    responses: payload,
  }, {
    onSuccess: () => {
      formSubmitted.value = true

      Object.keys(answers).forEach(key => {
    answers[parseInt(key)] = ''
  })
  
      setTimeout(() => {
        formSubmitted.value = false
      }, 4000) // desaparece após 4 segundos
    },
    onError: (err) => {
      console.error('Erro ao submeter:', err)
    }
  })
}
</script>

<template>
  <div
  class="min-h-screen p-4 sm:p-6"
  :style="categoryBackground"
>
    <div class="max-w-5xl mx-auto bg-white/80 backdrop-blur-md shadow-2xl rounded-2xl p-6 sm:p-10">
      <h1 class="text-2xl sm:text-3xl text-center  text-gray-800">
        Participation Form for <span class="text-3xl sm:text-4xl font-bold">{{ props.event.name }}</span>
      </h1>
      <br>
      <p class="text-center text-md sm:text-lg text-gray-700 mt-2 mb-6">
        The responses are anonymous!! Please help us get to know the participants of our participants better.
      </p>

      <div
    v-if="formSubmitted"
    class="text-center bg-green-100 border border-green-300 text-green-800 rounded-lg py-3 px-6 mb-6 shadow-md"
  >
    ✅ Form submitted successfully! Thank you for your participation.
  </div>
      <form v-if="!eventHasStarted" @submit.prevent="submitForm" class="space-y-10">
        <div v-for="q in props.questions" :key="q.id"
          class="bg-white/90 rounded-xl shadow-sm p-4 sm:p-6 border border-gray-200">
          <h3 class="text-xl font-semibold text-gray-800">{{ q.name }}</h3>
          <p class="text-sm text-gray-600 mb-3">{{ q.description }}</p>

          <p v-if="q.mandatory" class="text-red-600 text-sm font-medium mb-2">* Mandatory</p>
          <p v-else class="text-gray-500 text-sm mb-2">Optional</p>

          <div>
            <div v-if="q.options.length" class="flex flex-col gap-2">
              <label v-for="opt in q.options" :key="opt" class="text-sm text-gray-800 flex items-center gap-2">
                <input type="radio" :name="'question-' + q.id" :value="opt" v-model="answers[q.id]"
                  class="accent-blue-600" />
                {{ opt }}
              </label>
            </div>
            <div v-else>
              <input type="text" v-model="answers[q.id]"
                class="w-full mt-1 p-3 rounded-lg border border-gray-300 focus:outline-none focus:ring-2 focus:ring-blue-500 text-gray-900"
                :placeholder="q.mandatory ? 'Resposta obrigatória' : 'Resposta opcional'" />
            </div>
          </div>
        </div>

        <div class="flex justify-center mt-8">
          <button type="submit"
            class="bg-blue-600 text-white text-lg font-semibold py-3 px-8 rounded-lg shadow hover:bg-blue-700 transition">
            Submit Form
          </button>
        </div>
        
      </form>
      

      <p v-else class="text-center text-red-600 text-xl font-bold mt-12">
        Sorry, you can no longer submit the form.
      </p>
    </div>
  </div>
</template>
