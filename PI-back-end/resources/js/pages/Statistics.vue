<script setup lang="ts">
import { onMounted } from 'vue'
import {
  Chart,
  BarController,
  BarElement,
  CategoryScale,
  LinearScale,
  Title,
  Tooltip,
  Legend,
  PieController,
  ArcElement
} from 'chart.js'
import AppLayout from '@/layouts/AppLayout.vue'
import { BreadcrumbItem } from '@/types/BreadcrumbItem'

Chart.register(BarController, BarElement, CategoryScale, LinearScale, Title, Tooltip, Legend, PieController, ArcElement)

const props = defineProps<{
  eventsPerMonth: Record<string, number>;
  participantsPerMonth: Record<string, number>;
  categoryDistribution: Record<string, number>;
  typeDistribution: Record<string, number>;
  selectedFilter: string;
  topEvents: {
    name: string;
    confirmed: number;
    percentage: number;
  }[];
}>();

const drawBarChart = (canvasId: string, labels: string[], data: number[], label: string, color: string) => {
  const ctx = document.getElementById(canvasId) as HTMLCanvasElement;
  // Force a neutral color that works in both light and dark mode
  const neutralTextColor = '#9CA3AF'; // Slightly darker gray for better contrast

  new Chart(ctx, {
    type: 'bar',
    data: {
      labels,
      datasets: [{
        label,
        data,
        backgroundColor: color,
        borderRadius: 6,
        barThickness: 30
      }]
    },
    options: {
      responsive: true,
      plugins: {
        legend: {
          labels: {
            color: neutralTextColor,
            font: {
              weight: 'bold'
            }
          }
        },
        tooltip: {
          callbacks: {
            label: (ctx) => `${ctx.parsed.y} ${label.toLowerCase()}`
          },
          bodyColor: neutralTextColor,
          backgroundColor: 'rgba(17, 24, 39, 0.9)',
          titleColor: neutralTextColor,
          padding: 10
        },
        title: {
          display: false,
          color: neutralTextColor
        }
      },
      scales: {
        x: {
          grid: {
            color: 'rgba(209, 213, 219, 0.2)'
          },
          ticks: { 
            color: neutralTextColor,
            font: {
              weight: 'bold'
            }
          }
        },
        y: {
          beginAtZero: true,
          grid: {
            color: 'rgba(209, 213, 219, 0.2)'
          },
          ticks: {
            color: neutralTextColor,
            stepSize: 1,
            font: {
              weight: 'bold'
            }
          }
        }
      }
    }
  });
};

const drawPieChart = (canvasId: string, labels: string[], data: number[]) => {
  const ctx = document.getElementById(canvasId) as HTMLCanvasElement;
  // Force a neutral color that works in both light and dark mode
  const neutralTextColor = '#9CA3AF';
  const backgroundColors = ['#3b82f6', '#10b981', '#f59e0b', '#ef4444', '#8b5cf6', '#ec4899'];

  new Chart(ctx, {
    type: 'pie',
    data: {
      labels,
      datasets: [{
        data,
        backgroundColor: backgroundColors.slice(0, labels.length),
        borderWidth: 1
      }]
    },
    options: {
      plugins: {
        legend: {
          position: 'bottom',
          labels: {
            color: neutralTextColor,
            font: {
              size: 12,
              weight: 'bold'
            },
            padding: 15,
            usePointStyle: true
          }
        },
        tooltip: {
          bodyColor: neutralTextColor,
          backgroundColor: 'rgba(17, 24, 39, 0.9)',
          titleColor: neutralTextColor,
          padding: 10
        }
      }
    }
  });
};

const onFilterChange = (event: Event) => {
  const filter = (event.target as HTMLSelectElement).value;
  window.location.href = `/statistics?filter=${filter}`;
};

onMounted(() => {
  // Add custom CSS to help with text visibility
  const style = document.createElement('style');
  style.textContent = `
    .chart-js-legend text {
      fill: #D1D5DB !important;
    }
    canvas {
      font-family: ui-sans-serif, system-ui, sans-serif;
      font-weight: bold;
    }
  `;
  document.head.appendChild(style);
  
  // Initial chart draw with delay to ensure DOM is ready
  setTimeout(() => {
    drawBarChart('eventsChart', Object.keys(props.eventsPerMonth), Object.values(props.eventsPerMonth), 'Events', '#3b82f6');
    drawBarChart('participantsChart', Object.keys(props.participantsPerMonth), Object.values(props.participantsPerMonth), 'Participants', '#10b981');
    drawPieChart('categoryChart', Object.keys(props.categoryDistribution), Object.values(props.categoryDistribution));
    drawPieChart('typeChart', Object.keys(props.typeDistribution), Object.values(props.typeDistribution));
  }, 0);
});

const breadcrumbs: BreadcrumbItem[] = [
  {
    title: 'Statistics',
    href: '/statistics',
  },
];

const medalEmojis = ['🥇', '🥈', '🥉', '4️⃣', '5️⃣']
</script>

<template>
  <AppLayout :breadcrumbs="breadcrumbs">
    <div class="p-8 space-y-12 bg-gray-100 dark:bg-gray-900 min-h-screen transition-colors">
      <div class="mb-6">
        <label class="block mb-2 text-sm font-medium text-gray-700 dark:text-gray-300">Filter by:</label>
        <select @change="onFilterChange" :value="props.selectedFilter"
          class="rounded-md shadow-sm border-gray-300 dark:border-gray-600 text-black dark:bg-gray-800 dark:text-white">
          <option value="year">Last Year</option>
          <option value="6months">Last 6 Months</option>
        </select>
      </div>
      <!-- Average and Top Events -->
      <div class="w-full bg-white dark:bg-gray-800 p-6 rounded-xl shadow-md">
        <h3 class="text-lg font-semibold text-gray-800 dark:text-white mb-4">Top 5 Events by Confirmed Participants</h3>
        <ul class="space-y-2">
          <li
            v-for="index in 5" 
            :key="index"
            class="flex items-center space-x-3 text-gray-700 dark:text-gray-300"
          >
            <span class="text-xl">{{ medalEmojis[index - 1] }}</span>
            <div>
              <span v-if="props.topEvents[index - 1]">
  <strong>{{ props.topEvents[index - 1].name }}</strong> —
  {{ props.topEvents[index - 1].confirmed }} confirmed
  ({{ props.topEvents[index - 1].percentage }}%)
</span>
              <span v-else class="italic text-gray-500">No event</span>
            </div>
          </li>
        </ul>
      </div>
      <!-- Events per Month -->
      <div class="w-full bg-white dark:bg-gray-800 p-6 rounded-xl shadow-md">
        <h2 class="text-xl font-semibold text-gray-800 dark:text-white mb-4">Events per Month</h2>
        <canvas id="eventsChart" class="w-full h-96"></canvas>
      </div>

      <!-- Participants per Month -->
      <div class="w-full bg-white dark:bg-gray-800 p-6 rounded-xl shadow-md">
        <h2 class="text-xl font-semibold text-gray-800 dark:text-white mb-4">Participants per Month</h2>
        <canvas id="participantsChart" class="w-full h-96"></canvas>
      </div>

      <!-- Category and Type Distribution (Side by side) -->
      <div class="w-full bg-white dark:bg-gray-800 p-6 rounded-xl shadow-md grid grid-cols-1 md:grid-cols-2 gap-6">
        <!-- Category Distribution -->
        <div class="flex flex-col items-center">
          <h2 class="text-xl font-semibold text-gray-800 dark:text-white mb-4 text-center">Events by Category</h2>
          <canvas id="categoryChart" class="w-64 h-64"></canvas>
        </div>

        <!-- Type Distribution -->
        <div class="flex flex-col items-center">
          <h2 class="text-xl font-semibold text-gray-800 dark:text-white mb-4 text-center">Events by Type</h2>
          <canvas id="typeChart" class="w-64 h-64"></canvas>
        </div>
      </div>
    </div>
  </AppLayout>
</template>