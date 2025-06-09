<script setup lang="ts">
import { ref, reactive, computed } from 'vue'
import AppLayout from '@/layouts/AppLayout.vue'
import type { BreadcrumbItem } from '@/types'
import { router } from '@inertiajs/vue3'
import axios from 'axios'
import { Plus } from 'lucide-vue-next'

interface Event {
  id: number
  name: string
  type: string
  category: string
  location: string
  start_date: string
  start_time: string
  end_date: string
  end_time: string
  description: string
  status: string
  image?: string
}

const props = defineProps<{
  event: Event
  questions: {
    id: number
    name: string
    description: string
    options: string[]
    category: string | null
    mandatory?: boolean
    predefined?: boolean
  }[]
}>()

const formSelections = reactive<{ [key: number]: { included: boolean; mandatory: boolean } }>({})

props.questions.forEach((q) => {
  formSelections[q.id] = {
    included: false,
    mandatory: false,
  }
})

const suggestedQuestions = computed(() =>
  props.questions.filter((q) => q.predefined && !formSelections[q.id].included)
)

const selectedQuestions = computed(() =>
  props.questions.filter((q) => formSelections[q.id].included)
)

const breadcrumbs: BreadcrumbItem[] = [
  {
    title: `Events / ${props.event.name} / Suggested Questions`,
    href: `${props.event.id}/Statistics`,
  },
]

const showCreateCard = ref(false)

const newQuestion = reactive({
  name: '',
  description: '',
  optionsText: '',
})

const submitNewQuestion = async () => {
  if (!newQuestion.name.trim()) {
    alert('Please write a name for the question.')
    return
  }

  let options: string[] = []

  try {
    const trimmed = newQuestion.optionsText.trim()

    if (trimmed.startsWith('[') && trimmed.endsWith(']')) {
      options = JSON.parse(trimmed)
      if (!Array.isArray(options) || options.some(opt => typeof opt !== 'string')) throw new Error()
    } else {
      options = trimmed
        .split('\n')
        .map(opt => opt.trim())
        .filter(opt => opt)
    }

    if (options.length < 2) {
      alert('The question must have at least 2 options.')
      return
    }

  } catch {
    alert('Invalid options format. Please provide an option per line.')
    return
  }

  try {
    const response = await axios.post(`/events/${props.event.id}/questions`, {
      name: newQuestion.name,
      description: newQuestion.description,
      options,
    })

    props.questions.push(response.data)
    formSelections[response.data.id] = { included: true, mandatory: false }

    newQuestion.name = ''
    newQuestion.description = ''
    newQuestion.optionsText = ''
    showCreateCard.value = false
  } catch (err) {
    alert('Erro ao adicionar pergunta.')
    console.error(err)
  }
}

const selectedQuestion = ref<typeof props.questions[0] | null>(null)

const openQuestionDetails = (question: typeof props.questions[0]) => {
  selectedQuestion.value = question
}

const closeQuestionDetails = () => {
  selectedQuestion.value = null
}

const formProcessing = ref(false)

const submitForm = () => {
  formProcessing.value = true
  const selected = Object.entries(formSelections)
    .filter(([_, val]) => val.included)
    .map(([id, val]) => ({
      question_id: Number(id),
      mandatory: val.mandatory,
    }))

  router.post(`/events/${props.event.id}/form`, {
    questions: selected,
  }, {
    onFinish: () => formProcessing.value = false,
  })
}
</script>

<template>
  <AppLayout :breadcrumbs="breadcrumbs">
    <div class="p-10 bg-gray-100 dark:bg-gray-900 min-h-screen flex justify-center">
      <div class="max-w-2xl w-full space-y-8">

        <!-- Suggested Questions -->
        <div class="bg-white dark:bg-gray-800 p-6 rounded-xl shadow">
          <h2 class="text-lg font-semibold text-gray-800 dark:text-gray-100 mb-4">Suggested Questions</h2>

          <div v-for="question in suggestedQuestions" :key="question.id"
            class="bg-gray-50 dark:bg-gray-900 rounded-lg shadow-sm p-4 mb-3 flex justify-between items-center">
            <div @click="openQuestionDetails(question)" class="cursor-pointer">
              <h3 class="text-sm font-semibold text-gray-800 dark:text-gray-100 hover:underline">{{ question.name }}</h3>
              <p class="text-xs text-gray-500 dark:text-gray-400">{{ question.description }}</p>
            </div>
            <label class="flex items-center text-xs text-gray-700 dark:text-gray-300">
              <input type="checkbox" v-model="formSelections[question.id].included" />
              <span class="ml-1">Include</span>
            </label>
          </div>
        </div>

        <!-- Selected Questions -->
        <div class="bg-white dark:bg-gray-800 p-6 rounded-xl shadow">
          <div class="flex justify-between items-center mb-4">
            <h2 class="text-lg font-semibold text-gray-800 dark:text-gray-100">Selected Questions</h2>
            <button
              @click="showCreateCard = true"
              class="bg-orange-600 dark:bg-blue-500 text-white px-3 py-1.5 rounded-lg shadow hover:bg-orange-700 dark:hover:bg-blue-600 transition flex items-center gap-1"
            >
              <Plus class="w-4 h-4" /> Add Question
            </button>
          </div>

          <div v-if="selectedQuestions.length === 0" class="text-gray-500 dark:text-gray-400 text-sm italic">
            No questions selected.
          </div>

          <div v-for="question in selectedQuestions" :key="question.id"
            class="bg-gray-50 dark:bg-gray-900 rounded-lg shadow-sm p-4 mb-3 flex justify-between items-center">
            <div @click="openQuestionDetails(question)" class="cursor-pointer">
              <h3 class="text-sm font-semibold text-gray-800 dark:text-gray-100 hover:underline">{{ question.name }}</h3>
              <p class="text-xs text-gray-500 dark:text-gray-400">{{ question.description }}</p>
            </div>
            <div class="flex flex-col items-end gap-1">
              <label class="flex items-center text-xs text-gray-700 dark:text-gray-300">
                <input type="checkbox" v-model="formSelections[question.id].included" />
                <span class="ml-1">Remove</span>
              </label>

              <label class="flex items-center text-xs text-gray-700 dark:text-gray-300">
                <input type="checkbox" v-model="formSelections[question.id].mandatory" />
                <span class="ml-1">Required</span>
              </label>
            </div>
          </div>
        </div>

        <!-- Confirm Button -->
        <button
          class="w-full bg-orange-600 dark:bg-blue-600 text-white py-2 rounded hover:bg-orange-700 dark:bg-blue-700"
          @click="submitForm"
          :disabled="formProcessing"
        >
          {{ formProcessing ? 'Saving...' : 'Confirm' }}
        </button>
      </div>
    </div>

    <!-- Create Question Modal -->
    <transition name="fade">
      <div v-if="showCreateCard" class="fixed inset-0 z-50 flex items-center justify-center bg-black/40">
        <div class="bg-white dark:bg-gray-900 w-full max-w-md p-6 rounded-2xl shadow-xl relative">
          <button @click="showCreateCard = false"
            class="absolute top-2 right-2 text-gray-500 dark:text-gray-400 hover:text-red-500 text-xl">&times;</button>

          <h2 class="text-xl font-bold mb-4 text-gray-800 dark:text-gray-100">New Question</h2>

          <input v-model="newQuestion.name" placeholder="Question Name"
            class="w-full p-2 border border-gray-300 dark:border-gray-700 rounded-lg mb-2 text-gray-800 dark:text-gray-100 bg-white dark:bg-gray-800" />

          <input v-model="newQuestion.description" placeholder="Description"
            class="w-full p-2 border border-gray-300 dark:border-gray-700 rounded-lg mb-2 text-gray-800 dark:text-gray-100 bg-white dark:bg-gray-800" />

          <textarea v-model="newQuestion.optionsText" placeholder="Options (one per line)"
            class="w-full p-2 border border-gray-300 dark:border-gray-700 rounded-lg mb-4 text-gray-800 dark:text-gray-100 bg-white dark:bg-gray-800"></textarea>

          <button @click="submitNewQuestion"
            class="w-full bg-orange-500 dark:bg-blue-500 text-white py-2 rounded-lg hover:bg-orange-600 dark:hover:bg-blue-600 transition">
            Add Question
          </button>
        </div>
      </div>
    </transition>

    <!-- Question Details Modal -->
    <transition name="fade">
      <div v-if="selectedQuestion" class="fixed inset-0 z-50 flex items-center justify-center bg-black/40">
        <div class="bg-white dark:bg-gray-900 w-full max-w-md p-6 rounded-2xl shadow-xl relative">
          <button @click="closeQuestionDetails"
            class="absolute top-2 right-2 text-gray-500 dark:text-gray-400 hover:text-red-500 text-xl">&times;</button>

          <h2 class="mb-4 text-3xl font-black text-gray-700 dark:text-gray-200">{{ selectedQuestion.name }}</h2>

          <p class="mb-4 text-base font-medium text-gray-700 dark:text-gray-300">
            Description:
            <span class="text-lg font-semibold text-gray-700 dark:text-gray-100">{{ selectedQuestion.description }}</span>
          </p>

          <div v-if="selectedQuestion.options.length">
            <p class="text-sm font-medium text-gray-700 dark:text-gray-200 mb-1">Options:</p>
            <ul class="list-disc list-inside text-sm text-gray-700 dark:text-gray-300">
              <li v-for="opt in selectedQuestion.options" :key="opt">{{ opt }}</li>
            </ul>
          </div>
        </div>
      </div>
    </transition>
  </AppLayout>
</template>

<style scoped>
.fade-enter-active, .fade-leave-active {
  transition: opacity 0.3s ease;
}
.fade-enter-from, .fade-leave-to {
  opacity: 0;
}
</style>
