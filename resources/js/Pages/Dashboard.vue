<template>
  <AppLayout>
    <template #header>
      <div class="flex items-center justify-between">
        <div>
          <h1 class="text-2xl font-bold text-gray-800">Dashboard</h1>
          <p class="text-sm text-gray-500 mt-1">
            Ringkasan performa penjualan periode
            <span class="font-medium text-gray-700">{{ startDate }}</span>
            &ndash;
            <span class="font-medium text-gray-700">{{ endDate }}</span>
          </p>
        </div>
      </div>
    </template>

    <!-- FILTER BAR -->
    <v-card class="filter-card mb-6" elevation="0">
      <v-row align="center" class="ma-0">
        <v-col cols="12" md="3">
          <v-menu v-model="startMenu" :close-on-content-click="false">
            <template v-slot:activator="{ props }">
              <v-btn v-bind="props" class="date-trigger">
                <v-icon size="18" class="mr-2 text-indigo-500" icon="mdi-calendar-start" />
                <div class="text-left">
                  <div class="text-[11px] uppercase tracking-wide text-gray-400 leading-none mb-1">Dari</div>
                  <div class="text-sm font-medium text-gray-800">{{ startDate }}</div>
                </div>
              </v-btn>
            </template>
            <v-date-picker v-model="startDate" @update:model-value="startMenu = false" />
          </v-menu>
        </v-col>

        <v-col cols="12" md="3">
          <v-menu v-model="endMenu" :close-on-content-click="false">
            <template v-slot:activator="{ props }">
              <v-btn v-bind="props" class="date-trigger">
                <v-icon size="18" class="mr-2 text-indigo-500" icon="mdi-calendar-end" />
                <div class="text-left">
                  <div class="text-[11px] uppercase tracking-wide text-gray-400 leading-none mb-1">Sampai</div>
                  <div class="text-sm font-medium text-gray-800">{{ endDate }}</div>
                </div>
              </v-btn>
            </template>
            <v-date-picker v-model="endDate" @update:model-value="endMenu = false" />
          </v-menu>
        </v-col>

        <v-col cols="12" md="4">
          <div class="flex gap-2 flex-wrap">
            <v-chip
              v-for="range in quickRanges"
              :key="range.label"
              size="small"
              variant="flat"
              :class="['quick-chip', activeRange === range.label ? 'quick-chip-active' : '']"
              @click="applyQuickRange(range)"
            >
              {{ range.label }}
            </v-chip>
          </div>
        </v-col>

        <v-col cols="12" md="2">
          <v-btn
            block
            color="primary"
            class="apply-btn"
            :loading="isFiltering"
            @click="applyFilters"
          >
            <v-icon left size="18" class="mr-1" icon="mdi-filter-variant" />
            Terapkan
          </v-btn>
        </v-col>
      </v-row>
    </v-card>

    <!-- STAT WIDGETS -->
    <v-row class="mb-6">
      <v-col cols="12" md="4" v-for="stat in statCards" :key="stat.key">
        <v-card class="stat-card" :style="{ '--accent': stat.accent, '--accent-soft': stat.accentSoft }" elevation="0">
          <div class="stat-glow"></div>
          <div class="flex items-center relative z-10">
            <div class="stat-icon">
              <v-icon size="28" color="white" :icon="stat.icon" />
            </div>
            <div class="ml-2 py-4">
              <div class="text-sm text-gray-500 font-medium">{{ stat.label }}</div>
              <div class="text-3xl font-bold text-gray-800 mt-1 tabular-nums">
                <span v-if="stat.prefix">{{ stat.prefix }} </span>{{ formatNumber(stat.animatedValue) }}
              </div>
            </div>
          </div>
        </v-card>
      </v-col>
    </v-row>

    <!-- CHARTS -->
    <v-row>
      <v-col cols="12" md="6">
        <v-card class="chart-card" elevation="0">
          <div class="flex items-center justify-between mb-4">
            <div>
              <h2 class="text-lg font-bold text-gray-800">Penjualan Per Bulan</h2>
              <p class="text-xs text-gray-400 mt-0.5">Total nilai penjualan tiap bulan pada periode terpilih</p>
            </div>
            <v-icon size="20" class="text-indigo-400" icon="mdi-chart-bar" />
          </div>

          <div v-if="hasBulanData" style="height: 300px;">
            <Bar :data="chartDataPerBulan" :options="barChartOptions" />
          </div>
          <div v-else class="empty-state">
            <v-icon size="40" class="text-gray-300 mb-2" icon="mdi-chart-box-outline" />
            <p class="text-sm text-gray-400">Belum ada data penjualan pada periode ini</p>
          </div>
        </v-card>
      </v-col>

      <v-col cols="12" md="6">
        <v-card class="chart-card" elevation="0">
          <div class="flex items-center justify-between mb-4">
            <div>
              <h2 class="text-lg font-bold text-gray-800">Penjualan Per Item</h2>
              <p class="text-xs text-gray-400 mt-0.5">Kontribusi qty terjual tiap item</p>
            </div>
            <v-icon size="20" class="text-indigo-400" icon="mdi-chart-donut" />
          </div>

          <div v-if="hasItemData" style="height: 300px;">
            <Doughnut :data="chartDataPerItem" :options="doughnutChartOptions" />
          </div>
          <div v-else class="empty-state">
            <v-icon size="40" class="text-gray-300 mb-2" icon="mdi-package-variant-closed" />
            <p class="text-sm text-gray-400">Belum ada item terjual pada periode ini</p>
          </div>
        </v-card>
      </v-col>
    </v-row>
  </AppLayout>
</template>

<script setup>
import { ref, computed, watch, onBeforeUnmount } from 'vue'
import { router } from '@inertiajs/vue3'
import AppLayout from '@/Layouts/AppLayout.vue'
import { Bar, Doughnut } from 'vue-chartjs'
import {
  Chart as ChartJS,
  Title,
  Tooltip,
  Legend,
  BarElement,
  CategoryScale,
  LinearScale,
  ArcElement
} from 'chart.js'

ChartJS.register(Title, Tooltip, Legend, BarElement, CategoryScale, LinearScale, ArcElement)

const props = defineProps({
  widgets: Object,
  charts: Object,
  filters: Object
})

const startMenu = ref(false)
const endMenu = ref(false)
const startDate = ref(props.filters.start_date)
const endDate = ref(props.filters.end_date)
const isFiltering = ref(false)
const activeRange = ref(null)

const formatNumber = (num) => new Intl.NumberFormat('id-ID').format(Math.round(num) || 0)

/* ---------- Quick date ranges ---------- */
const toISODate = (d) => d.toISOString().slice(0, 10)

const quickRanges = [
  { label: 'Hari ini', days: 0 },
  { label: '7 hari', days: 7 },
  { label: '30 hari', days: 30 },
  { label: 'Bulan ini', monthToDate: true }
]

const applyQuickRange = (range) => {
  activeRange.value = range.label
  const today = new Date()
  if (range.monthToDate) {
    startDate.value = toISODate(new Date(today.getFullYear(), today.getMonth(), 1))
  } else {
    const from = new Date(today)
    from.setDate(from.getDate() - range.days)
    startDate.value = toISODate(from)
  }
  endDate.value = toISODate(today)
  applyFilters()
}

/* ---------- Animated counters for stat widgets ---------- */
const useAnimatedValue = (targetRef) => {
  const displayValue = ref(0)
  let rafId = null

  const animate = (from, to) => {
    if (rafId) cancelAnimationFrame(rafId)
    const duration = 600
    const start = performance.now()

    const step = (now) => {
      const progress = Math.min((now - start) / duration, 1)
      const eased = 1 - Math.pow(1 - progress, 3)
      displayValue.value = from + (to - from) * eased
      if (progress < 1) {
        rafId = requestAnimationFrame(step)
      } else {
        displayValue.value = to
      }
    }
    rafId = requestAnimationFrame(step)
  }

  watch(targetRef, (newVal, oldVal) => {
    animate(oldVal || 0, newVal || 0)
  }, { immediate: true })

  onBeforeUnmount(() => {
    if (rafId) cancelAnimationFrame(rafId)
  })

  return displayValue
}

const jumlahTransaksi = computed(() => props.widgets.jumlahTransaksi || 0)
const jumlahPenjualan = computed(() => props.widgets.jumlahPenjualan || 0)
const jumlahQty = computed(() => props.widgets.jumlahQtyItemTerjual || 0)

const animatedTransaksi = useAnimatedValue(jumlahTransaksi)
const animatedPenjualan = useAnimatedValue(jumlahPenjualan)
const animatedQty = useAnimatedValue(jumlahQty)

const statCards = computed(() => [
  {
    key: 'transaksi',
    label: 'Jumlah Transaksi',
    icon: 'mdi-cash-register',
    accent: '#4F46E5',
    accentSoft: 'rgba(79, 70, 229, 0.12)',
    animatedValue: animatedTransaksi.value
  },
  {
    key: 'penjualan',
    label: 'Jumlah Penjualan',
    icon: 'mdi-cash-multiple',
    accent: '#10B981',
    accentSoft: 'rgba(16, 185, 129, 0.12)',
    prefix: 'Rp',
    animatedValue: animatedPenjualan.value
  },
  {
    key: 'qty',
    label: 'Jumlah Qty Terjual',
    icon: 'mdi-package-variant',
    accent: '#F59E0B',
    accentSoft: 'rgba(245, 158, 11, 0.12)',
    animatedValue: animatedQty.value
  }
])

/* ---------- Chart data ---------- */
const chartDataPerBulan = ref({ labels: [], datasets: [] })
const chartDataPerItem = ref({ labels: [], datasets: [] })

const hasBulanData = computed(() => (props.charts.penjualanPerBulan || []).length > 0)
const hasItemData = computed(() => (props.charts.penjualanPerItem || []).length > 0)

const itemPalette = [
  '#6366F1', '#10B981', '#F59E0B', '#EF4444', '#0EA5E9', '#A855F7', '#EC4899', '#84CC16'
]

const updateChartData = () => {
  const bulanData = props.charts.penjualanPerBulan || []
  chartDataPerBulan.value = {
    labels: bulanData.map((item) => item.bulan),
    datasets: [
      {
        label: 'Penjualan',
        data: bulanData.map((item) => item.total),
        backgroundColor: 'rgba(79, 70, 229, 0.85)',
        hoverBackgroundColor: 'rgba(79, 70, 229, 1)',
        borderRadius: 6,
        maxBarThickness: 42
      }
    ]
  }

  const itemData = props.charts.penjualanPerItem || []
  chartDataPerItem.value = {
    labels: itemData.map((item) => item.item?.nama || 'Unknown'),
    datasets: [
      {
        label: 'Qty',
        data: itemData.map((item) => item.total_qty),
        backgroundColor: itemPalette,
        borderWidth: 2,
        borderColor: '#ffffff',
        hoverOffset: 8
      }
    ]
  }
}

watch(() => props.charts, updateChartData, { deep: true, immediate: true })

const barChartOptions = {
  responsive: true,
  maintainAspectRatio: false,
  animation: { duration: 500, easing: 'easeOutQuart' },
  plugins: {
    legend: { display: false },
    tooltip: {
      backgroundColor: '#1f2937',
      padding: 10,
      cornerRadius: 8,
      displayColors: false
    }
  },
  scales: {
    x: { grid: { display: false } },
    y: { grid: { color: '#F1F5F9' }, ticks: { callback: (v) => new Intl.NumberFormat('id-ID', { notation: 'compact' }).format(v) } }
  }
}

const doughnutChartOptions = {
  responsive: true,
  maintainAspectRatio: false,
  cutout: '65%',
  animation: { duration: 500, easing: 'easeOutQuart' },
  plugins: {
    legend: { position: 'bottom', labels: { usePointStyle: true, boxWidth: 8, padding: 16 } },
    tooltip: {
      backgroundColor: '#1f2937',
      padding: 10,
      cornerRadius: 8
    }
  }
}

/* ---------- Filter action ---------- */
const applyFilters = () => {
  startMenu.value = false
  endMenu.value = false
  isFiltering.value = true
  router.visit('/', {
    data: {
      start_date: startDate.value,
      end_date: endDate.value
    },
    onFinish: () => {
      isFiltering.value = false
    }
  })
}
</script>