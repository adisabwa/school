<template>
  <div id="nilai" class="py-2">
    <el-card class="bg-white/[0.7] mb-2" body-class="max-md:p-3">
      <form-comp
        ref="formFilter"
        :key="formKey"
        :fields="filterFields"
        size="default"
        label-position="left"
        :show-label="showLabel"
        class="max-sm:mt-4"
        form-class="m-0"
        form-item-class="mb-2"
        label-width="150px"
        v-model:form-value="filter"
        :pass-columns="[]"
        :show-submit="false"
        text-submit="Cari"
        error-submit-text="Tidak dapat mengambil data"
        :show-required-text="false"
      >
      </form-comp>
      <ProgresNilai
        ref="progresNilai"
        v-if="filter.id_semester > 0 && filter.id_kamar > 0"
        :role="role"
        :id_semester="filter.id_semester"
        :id_kamar="filter.id_kamar"
        :labelProgress="
          role == 'guru'
            ? user.nama
            : runFunction({
                data: this.filter.id_kamar,
                options: this.filterFields.id_kamar.options,
              })
        "
      />
    </el-card>
  </div>
</template>

<script>
import { mapState } from "pinia";
import { useAuthStore } from "@/config/stores/authStore";
import { useDataStore } from "@/config/stores/dataStore";
import ProgresNilai from "@/modules/pengasuhan/components/ProgresNilai.vue";

export default {
  name: "mapel",
  setup() {
    return {
      runFunction,
      isEmpty,
    };
  },
  components: {
    ProgresNilai
  },
  data: function () {
    return {
      initial: true,
      loading: false,
      filterFields: {
        id_semester: {
          label: "Semester",
          nama_kolom: "id_semester",
          input: "select",
          options: [],
        },
        id_kamar: {
          label: "Kamar",
          nama_kolom: "id_kamar",
          input: "select",
          options: {},
          hidden: false,
        },
      },
      filter: {
        id_semester: "-1",
        id_kamar: "-1",
      },
      // role:'guru',
    };
  },
  watch: {
    "filter.id_semester"(val) {
      if (!isEmpty(val)) {
        if (this.role == "admin")
          return (this.filter.id_kamar = this.filterFields.id_kamar.options[0].value);
        else this.filter.id_kamar = this.user.id_kamar;
      }
    },
  },
  computed: {
    ...mapState(useAuthStore, {
      user: "loggedUser",
      role: "role",
    }),
    ...mapState(useDataStore, {
      storeFilters: "filters",
    }),
    showLabel() {
      return this.$windowWidth.value > 800;
    },
  },
  methods: {
    getInitial: async function () {
      this.initial = true;
      await this.$http.get("data/kamar/options").then((res) => {
        this.initial = false;
        let data = res.data;
        this.filterFields.id_kamar.options = data;
      });
      await this.$http.get("data/semester/options").then((res) => {
        let data = res.data;
        this.filterFields.id_semester.options = data;
        // console.log(this.storeFilters?.id_semester, data[0]?.value)
        this.filter.id_semester = this.storeFilters?.id_semester ?? data[0]?.value;
        // console.log('id_semester', this.filter.id_semester)
        if (this.role != "admin") this.filterFields.id_kamar.hidden = true;
      });
    },
  },
  created: function () {
    this.getInitial();
    // console.log(this.$router);
  },
  mounted() {},
  beforeUnmount() {
    let dataStore = useDataStore();
    Object.entries(this.filter).forEach(([index, val]) =>
      dataStore.setFilter({
        key: index,
        val: val,
      })
    );
    // console.log('change-filter', dataStore.filters)
  },
};
</script>
