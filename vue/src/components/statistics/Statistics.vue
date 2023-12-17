<script setup>
import { onMounted, ref, watch,computed, inject } from 'vue';
import * as Chart from 'chart.js/auto';
import { useUserStore } from '../../stores/user';

const transactions = ref([]);
const vcards = ref([]);
let myPieChart = null;
const totalVcardBalance = ref(0);
const totalTransactionAmount = ref(0);
const userStore = useUserStore();
const averageTransactionsPerMonth = ref(0);
const monthlyExpenses = ref({});
const selectedYear = ref(new Date().getFullYear()); 
let myBarChart = null;
const axios = inject('axios')
const years = computed(() => getUniqueYearsFromTransactions());


const loadTransactions = async () => {
    try {
        if (userStore.user && userStore.userType === 'V') {
            const response = await axios.get("vcards/" + userStore.user.id + "/transactions");
            transactions.value = Array.isArray(response.data.data) ? response.data.data : [];
        }
        if (userStore.user && userStore.userType === 'A') {
            const response = await axios.get("admin/AllTransactions");
            transactions.value = Array.isArray(response.data.data) ? response.data.data : [];
        }
        

        updatePieChart();
        calculateAverageTransactionsPerMonth();
        calculateMonthlyExpenses();
        updateBarChart()
        calculateTotals();
    } catch (error) {
        console.error(error);
    }
};



const loadVcards = async () => {
    try {
        const response = await axios.get("admin/AllVcards");
        //console.log(response);
        vcards.value = Array.isArray(response.data.data) ? response.data.data : [];

    } catch (error) {
        console.error(error);
    }

};
const updatePieChart = () => {
    if (myPieChart) {
        myPieChart.destroy();
    }

    const paymentTypes = {};
    transactions.value.forEach(transaction => {
        const paymentType = transaction.payment_type || 'Unknown';
        paymentTypes[paymentType] = (paymentTypes[paymentType] || 0) + 1;
    });

    const labels = Object.keys(paymentTypes);
    const data = Object.values(paymentTypes);

    const ctxP = document.getElementById("pieChart").getContext('2d');
    myPieChart = new Chart.Chart(ctxP, {
        type: 'pie',
        data: {
            labels: labels,
            datasets: [{
                data: data,
                backgroundColor: ["#F7464A", "#46BFBD", "#FDB45C", "#949FB1", "#4D5360", "#72AD75"],
                hoverBackgroundColor: ["#FF5A5E", "#5AD3D1", "#FFC870", "#A8B3C5", "#616774", "#5d917d"],
            }]
        },
        options: {
            responsive: true
        }
    });
};

const calculateAverageTransactionsPerMonth = () => {
    const transactionMonths = new Set();
    transactions.value.forEach(transaction => {
        const transactionDate = new Date(transaction.date);
        const monthYear = `${transactionDate.getMonth() + 1}-${transactionDate.getFullYear()}`;
        transactionMonths.add(monthYear);
    });

    averageTransactionsPerMonth.value = transactionMonths.size > 0
        ? transactions.value.length / transactionMonths.size
        : 0;
};



const calculateTotals = () => {
    loadVcards();
    totalVcardBalance.value = vcards.value.reduce((total, vcard) => total + parseFloat(vcard.balance), 0);

    const xDaysAgo = new Date();
    xDaysAgo.setDate(xDaysAgo.getDate() - 7);

    const currentDate = new Date();

    totalTransactionAmount.value = transactions.value
        .filter(transaction => {
            const transactionDate = new Date(transaction.date);
            const timeDifference = currentDate - transactionDate;
            const daysDifference = timeDifference / (1000 * 60 * 60 * 24);
            return daysDifference <= 7;
        })
        .reduce((sum, transaction) => {
            return sum + parseFloat(transaction.value);
        }, 0);
};

const calculateMonthlyExpenses = () => {
    transactions.value.forEach(transaction => {
        if (transaction.type === 'D') {
            const transactionDate = new Date(transaction.date);
            const monthYear = `${transactionDate.getMonth() + 1}-${transactionDate.getFullYear()}`;
            monthlyExpenses.value[monthYear] = (monthlyExpenses.value[monthYear] || 0) + parseFloat(transaction.value);
        }
    });
};

const updateBarChart = () => {
    if (myBarChart) {
        myBarChart.destroy();
    }

    const ctxB = document.getElementById('barChart');
    myBarChart = new Chart.Chart(ctxB, {
        type: 'bar',
        data: {
            labels: getMonthsOfYear(),
            datasets: [
                {
                    label: 'Number of Transactions per Month',
                    data: getTransactionsPerMonth(),
                    backgroundColor: 'rgba(75, 192, 192, 0.2)',
                    borderColor: 'rgba(75, 192, 192, 1)',
                    borderWidth: 1,
                },
            ],
        },
        options: {
            scales: {
                x: {
                    beginAtZero: true,
                    title: {
                        display: true,
                        text: 'Months',
                    },
                },
                y: {
                    beginAtZero: true,
                    title: {
                        display: true,
                        text: 'Number of Transactions',
                    },
                },
            },
            plugins: {
                legend: {
                    display: true,
                    position: 'top',
                },
            },
        },
    });
};

const getMonthsOfYear = () => {
    const months = [];
    for (let i = 1; i <= 12; i++) {
        months.push(`${i}-${selectedYear.value}`);
    }
    return months;
};

const getTransactionsPerMonth = () => {
    const transactionsPerMonth = Array(12).fill(0);

    transactions.value.forEach(transaction => {
        const transactionDate = new Date(transaction.date);
        if (transactionDate.getFullYear() === selectedYear.value) {
            transactionsPerMonth[transactionDate.getMonth()] += 1;
        }
    });

    return transactionsPerMonth;
};

const getUniqueYearsFromTransactions = () => {
  const uniqueYears = new Set();
  transactions.value.forEach(transaction => {
    const transactionDate = new Date(transaction.date);
    const year = transactionDate.getFullYear();
    uniqueYears.add(year);
  });
  return Array.from(uniqueYears);
};



onMounted(async () => {
    await loadTransactions();
    await loadVcards();
    await updatePieChart();
    await calculateMonthlyExpenses();
    await updateBarChart();
    await calculateTotals();
});

watch(transactions, () => {
    
});

watch(vcards, () => {
    //
});

watch(selectedYear, () => {
    //updateBarChart();
});
</script>

<template>
    <div class="container mt-5">
        <div id="section0">
            <h3 class="mb-3">Estatísticas</h3>
            <hr>
        </div>
  
      <!-- Totals Section -->
      <div class="row" id="section1">
      <div class="col-md-6">
        <div class="card border-info">
          <div class="card-header bg-info text-white">
            <h5 class="mb-0">Estatísticas das ultimas vinte mil transacoes</h5>
          </div>
          <div class="card-body">
            <ul class="list-group list-group-flush">
              <li class="list-group-item d-flex justify-content-between align-items-center" v-if="userStore.userType === 'A'">
                Total dos saldos dos vcards
                <span class="badge badge-primary badge-pill text-dark">{{ totalVcardBalance.toFixed(2) }}€</span>
              </li>
              <li class="list-group-item d-flex justify-content-between align-items-center">
                Total das transações nos últimos 7 dias
                <span class="badge badge-primary badge-pill text-dark">{{ totalTransactionAmount }}</span>
              </li>
              <li class="list-group-item d-flex justify-content-between align-items-center">
                Média de transações por mês
                <span class="badge badge-primary badge-pill text-dark">{{ averageTransactionsPerMonth.toFixed(2) }}</span>
              </li>
            </ul>
          </div>
        </div>
      </div>
    </div>
  
      <!-- Charts Section -->
      <div class="row mt-4">
        <!-- Pie Chart -->
        <div class="col-md-6" id="section2">
          <h5 class="mb-3">Tipos de pagamento</h5>
          <canvas id="pieChart" style="max-width: 100%; max-height: 400px;"></canvas>
        </div>
  
        <!-- Bar Chart -->
        <div class="col-md-6" id="section3">
          <h5 class="mb-3">Despesas mensais</h5>
          <div v-if="years.length > 0" class="mb-3">
            <label for="year">Selecionar Ano:</label>
            <select id="year" v-model="selectedYear" class="form-control">
              <option v-for="year in years" :key="year" :value="year">{{ year }}</option>
            </select>
          </div>
          <canvas id="barChart" style="max-width: 100%; max-height: 400px;"></canvas>
        </div>
      </div>
    </div>
  </template>
  
  <style scoped>
  /* Styling for Totals Section */
  .row > .col-md-6 {
    margin-bottom: 20px;
  }
  
  /* Styling for Charts Section */
  .row > .col-md-6 > h5 {
    margin-top: 10px;
  }

  
  /* Add more styles as needed */
  </style>
  
  
  
