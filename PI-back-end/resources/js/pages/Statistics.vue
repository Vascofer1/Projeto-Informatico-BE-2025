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


Chart.register(BarController, BarElement, CategoryScale, LinearScale, Title, Tooltip, Legend, PieController, ArcElement);

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
  const neutralTextColor = '#9CA3AF';
  const backgroundColors = ['#3b82f6', '#10b981', '#f59e0b', '#ef4444', '#8b5cf6', '#ec4899'];

  const total = data.reduce((acc, val) => acc + val, 0);

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
          callbacks: {
            label: (ctx) => {
              const value = ctx.parsed;
              const percentage = ((value / total) * 100).toFixed(1);
              return `${ctx.label}: ${value} (${percentage}%)`;
            }
          },
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
    <div class="px-6 py-10 bg-gray-100 dark:bg-gray-900 min-h-screen space-y-12">
      
      <!-- Filtro -->
      <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div>
          <label class="block mb-1 text-sm font-semibold text-gray-700 dark:text-gray-300">Filter by period</label>
          <select @change="onFilterChange" :value="props.selectedFilter"
            class="rounded-md border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 text-sm font-medium text-gray-800 dark:text-white px-3 py-2 shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
            <option value="year">Last Year</option>
            <option value="6months">Last 6 Months</option>
          </select>
        </div>
      </div>

      <!-- Top Eventos -->
      <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-lg p-6">
        <h2 class="text-xl font-semibold text-gray-800 dark:text-white mb-4">Top 5 Events by Confirmed Participants</h2>
        <ul class="divide-y divide-gray-200 dark:divide-gray-700">
          <li
            v-for="index in 5"
            :key="index"
            class="py-3 flex items-start gap-3 text-gray-700 dark:text-gray-300"
          >
            <span class="text-2xl w-6">{{ medalEmojis[index - 1] }}</span>
            <div class="flex-1">
              <template v-if="props.topEvents[index - 1]">
                <p class="font-semibold">
                  {{ props.topEvents[index - 1].name }}
                </p>
                <p class="text-sm text-gray-600 dark:text-gray-400">
                  {{ props.topEvents[index - 1].confirmed }} confirmed ({{ props.topEvents[index - 1].percentage }}%)
                </p>
              </template>
              <template v-else>
                <p class="italic text-gray-500">No event</p>
              </template>
            </div>
          </li>
        </ul>
      </div>

      <!-- Gráficos Mensais -->
      <div class="grid lg:grid-cols-2 gap-6">
        <div class="bg-white dark:bg-gray-800 p-6 rounded-2xl shadow-lg">
          <h2 class="text-lg font-semibold text-gray-800 dark:text-white mb-3">Events per Month</h2>
          <canvas id="eventsChart" class="w-full h-96"></canvas>
        </div>

        <div class="bg-white dark:bg-gray-800 p-6 rounded-2xl shadow-lg">
          <h2 class="text-lg font-semibold text-gray-800 dark:text-white mb-3">Participants per Month</h2>
          <canvas id="participantsChart" class="w-full h-96"></canvas>
        </div>
      </div>

      <!-- Gráficos por Categoria e Tipo -->
      <div class="grid lg:grid-cols-2 gap-6">
        <div class="bg-white dark:bg-gray-800 p-6 rounded-2xl shadow-lg flex flex-col items-center">
          <h2 class="text-lg font-semibold text-gray-800 dark:text-white mb-4">Events by Category</h2>
            <canvas id="categoryChart" class="w-48 h-48"></canvas>
        </div>

        <div class="bg-white dark:bg-gray-800 p-6 rounded-2xl shadow-lg flex flex-col items-center">
          <h2 class="text-lg font-semibold text-gray-800 dark:text-white mb-4">Events by Type</h2>
          <canvas id="typeChart" class="w-48 h-48"></canvas>
        </div>
      </div>
    </div>
  </AppLayout>
</template>
