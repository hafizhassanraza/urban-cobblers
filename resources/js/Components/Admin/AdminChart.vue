<script setup>
import { computed } from 'vue';
import {
    Chart as ChartJS,
    CategoryScale,
    LinearScale,
    PointElement,
    LineElement,
    BarElement,
    ArcElement,
    Title,
    Tooltip,
    Legend,
    Filler,
} from 'chart.js';
import { Line, Bar, Doughnut } from 'vue-chartjs';

ChartJS.register(
    CategoryScale,
    LinearScale,
    PointElement,
    LineElement,
    BarElement,
    ArcElement,
    Title,
    Tooltip,
    Legend,
    Filler,
);

const props = defineProps({
    type: {
        type: String,
        required: true,
        validator: (value) => ['line', 'bar', 'doughnut'].includes(value),
    },
    labels: {
        type: Array,
        default: () => [],
    },
    datasets: {
        type: Array,
        default: () => [],
    },
    height: {
        type: Number,
        default: 280,
    },
});

const chartComponent = computed(() => ({
    line: Line,
    bar: Bar,
    doughnut: Doughnut,
}[props.type]));

const chartData = computed(() => ({
    labels: props.labels,
    datasets: props.datasets,
}));

const chartOptions = computed(() => {
    if (props.type === 'doughnut') {
        return {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'bottom',
                },
            },
        };
    }

    return {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: {
                position: 'top',
            },
        },
        scales: {
            y: {
                beginAtZero: true,
                grid: {
                    color: 'rgba(17, 17, 17, 0.06)',
                },
            },
            x: {
                grid: {
                    display: false,
                },
            },
        },
    };
});
</script>

<template>
    <div :style="{ height: `${height}px` }">
        <component :is="chartComponent" :data="chartData" :options="chartOptions" />
    </div>
</template>
