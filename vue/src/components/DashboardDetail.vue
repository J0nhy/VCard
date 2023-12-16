<script setup>
import { ref, watch, computed, inject, onMounted } from "vue";
import { useVcardStore } from "../stores/vcard";
import { useUserStore } from "../stores/user";
import { useToast } from 'vue-toastification';
import { useRouter, onBeforeRouteLeave } from 'vue-router'
import Statistics from './statistics/Statistics.vue'


const serverBaseUrl = inject("serverBaseUrl");

const props = defineProps({
  vcard: {
    type: Object,
    required: true,
  },
});

const emit = defineEmits(["debitar", "depositar", "ativarPiggy"]);

const editingVcard = ref(props.vcard)
const vcardStore = useVcardStore()
const userStore = useUserStore()
const toast = useToast()
const router = useRouter()

watch(
  () => props.vcard,
  (newVcard) => {
    editingVcard.value = newVcard
    console.log("1-Valor de inserting em VcardDetail.vue mudou para:", newVcard);
    //console.log("Valor :", router.currentRoute.value);
  },
  { immediate: true },
  () => props.inserting,
  (newInserting) => {
    console.log("2-Valor de inserting em VcardDetail.vue mudou para:", newInserting);
  }

)


const debitar = () => {
  emit("debitar", editingVcard.value);
}
const depositar = () => {
  emit("depositar", editingVcard.value);
}
const ativarPiggy = () => {
  emit("ativarPiggy", editingVcard.value);
}

const removeElementById = (id) => {

  const element = document.getElementById(id);
  if (element) {
    element.parentNode.removeChild(element);
  }
}

onMounted(() => {
  removeElementById("section0");
  removeElementById("section1");
  removeElementById("section3");
  console.log("valor:" + router.currentRoute.value.params.new_balance);
})


</script>



<template>
  <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2">Dashboard</h1>
  </div>
  <div class="flex">

    <div class="chart">
      <Statistics />
    </div>

    <div class="w-25">
      <br>
      <div v-show="userStore.user && userStore.userType === 'V'">
        <h4 class="mt-3">Saldo Disponível: {{ editingVcard.userBalance }}€</h4>
      </div>
      <hr /><br>
      <div v-show="userStore.user && userStore.userType === 'V'">
        <h4 class="mt-3">{{ editingVcard.piggyBalance != null ? "Saldo Piggy: " + editingVcard.piggyBalance + "€" : "Piggy desativado, pretende ativar ? " }}</h4>
        <button type="button" class="btn btn-primary inline" v-show="editingVcard.piggyBalance == null" @click="ativarPiggy">Ativar</button>
        <div v-show="editingVcard.piggyBalance != null" id="piggy">
          <form class="row g-3 needs-validation" style="margin-top: 1em;">
            <label for="quantidade">Quantidade:</label>
            <input class="form-control" name="valor" id="valor" type="text" v-model="editingVcard.valor" min="0.01" required style="width: 85%" />
            <button type="button" class="btn btn-primary inline" @click="debitar">Debitar</button>
            <button type="button" class="btn btn-primary inline" @click="depositar">Depositar</button>
          </form>
        </div>
      </div>
      <br>
      <hr /><br>
      <div v-show="userStore.user && userStore.userType === 'V' && editingVcard.piggyBalance != null">
        <h4 class="mt-3">Saldo Total: {{ Number(editingVcard.piggyBalance) + Number(editingVcard.userBalance) }}€</h4>
      </div>
    </div>
  </div>
</template>

<style scoped>
#piggy {
  display: flex;
  flex-direction: row;
  justify-content: center;
  align-items: center;
}

.flex {
  display: flex;
}

.inline {
  width: 40%;
  margin: 10% 5% 0% 0%;
  display: inline;
}

.chart {
  width: 70% !important;
}
</style>