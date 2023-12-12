<script setup>
import { onMounted, ref, watch } from 'vue';
import * as Chart from 'chart.js/auto';
import axios from 'axios';

const transactions = ref({ data: [] });
const vcards = ref({ data: [] });
let myPieChart = null;
const totalVcardBalance = ref(0);
const totalTransactionAmount = ref(0);

const loadTransactions = async () => {
    try {
        const response = await axios.get("http://laravel.test/api/transactions/900000001");
        transactions.value = response.data || { data: [] };

        // Log the type of transactions.value
        console.log('Type of transactions.value:', typeof transactions.value);

        // Call the function to update the pie chart with the new data
        updatePieChart();
        calculateTotals();
    } catch (error) {
        console.error(error);
    }
};

const loadVcards = async () => {
    try {
        const response = await axios.get("http://laravel.test/api/users");
        vcards.value = response.data || { data: [] };
        console.log('Vcards:', vcards.value);

        calculateTotals();
    } catch (error) {
        console.error(error);
    }
};

const updatePieChart = () => {
    if (myPieChart) {
        myPieChart.destroy();
    }

    // Extract payment types and count occurrences
    const paymentTypes = {};
    transactions.value.data.forEach(transaction => {
        const paymentType = transaction.payment_type || 'Unknown';
        paymentTypes[paymentType] = (paymentTypes[paymentType] || 0) + 1;
    });

    // Extract labels and data for the chart
    const labels = Object.keys(paymentTypes);
    const data = Object.values(paymentTypes);

    // Create the pie chart
    const ctxP = document.getElementById("pieChart").getContext('2d');
    myPieChart = new Chart.Chart(ctxP, {
        type: 'pie',
        data: {
            labels: labels,
            datasets: [{
                data: data,
                backgroundColor: ["#F7464A", "#46BFBD", "#FDB45C", "#949FB1", "#4D5360"],
                hoverBackgroundColor: ["#FF5A5E", "#5AD3D1", "#FFC870", "#A8B3C5", "#616774"],
            }]
        },
        options: {
            responsive: true
        }
    });
};

const calculateTotals = () => {
    // Calculate totalVcardBalance
    
    totalVcardBalance.value = vcards.value.data.reduce((total, vcard) => total + parseFloat(vcard.balance), 0);

    // Calculate totalTransactionAmount
    const xDaysAgo = new Date();
    xDaysAgo.setDate(xDaysAgo.getDate() - 7);

    
    console.log('xDaysAgo:', xDaysAgo);

    const currentDate = new Date();

  totalTransactionAmount.value = transactions.value.data
    .filter(transaction => {
      const transactionDate = new Date(transaction.date);
      const timeDifference = currentDate - transactionDate;
      const daysDifference = timeDifference / (1000 * 60 * 60 * 24);
      return daysDifference <= 7;
    })
    .reduce((sum, transaction) => {
      return sum + parseFloat(transaction.value);
    }, 0);

    console.log('Total Transaction Amount in the last 7 days:', totalTransactionAmount.value);
};


onMounted(async () => {
    await Promise.all([loadTransactions(), loadVcards()]);
});

watch(transactions, () => {
    // Update the pie chart and totals whenever transactions change
    updatePieChart();
    calculateTotals();
});

watch(vcards, () => {
    // Recalculate the totalVcardBalance whenever vcards change
    calculateTotals();
});
</script>

<template>
    <div>
        <h3 class="mt-5 mb-3">Estatísticas</h3>
        <hr>
        <h5 class="mt-5 mb-3">Tipos de pagamento</h5>
        <canvas id="pieChart" style="max-width: 600px; max-height: 400px; margin-top: 20px;"></canvas>

        <h5 class="mt-5 mb-3">Totais</h5>
        <p>Total dos saldos dos vcards: {{ totalVcardBalance }}</p>
        <p>Total das transações nos últimos 7 dias: {{ totalTransactionAmount }}</p>
    </div>
</template>
