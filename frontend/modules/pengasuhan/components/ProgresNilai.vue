<template>
  <div id="nilai" class="py-2">
    <div v-loading="loading">
      <div class="p-3 border border-solid border-slate-200 shadow-md">
        <h2 class="text-lg font-bold m-0 mb-1">Total Progress Pengisian Nilai {{ labelProgress }}</h2>
        <table class="w-full">
          <tbody>
            <tr v-for="u in percentage">
              <td width="100" class="font-bold" nowrap>{{ u.label }} </td>
              <td width="20" class="text-center">:</td>
              <td>
                <el-progress :percentage="u.percentage"
                  class="w-full"
                  :stroke-width="18"
                  :show-text="true"
                  :status="u.percentage < 100 ? 'warning' : ''"
                  striped
                  >
                  {{ u.percentage }} % 
                </el-progress>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
      <div class="p-3 border border-solid border-slate-200 shadow-md text-md">
        <div class="font-bold m-0 text-[16px]">
          Progress Pengisian Nilai Pengasuhan {{ labelProgress }}
        </div>
        <div :class="['grid gap-x-5 ',
          percentagePengasuhan.length > 1 ? 'grid-cols-1 md:grid-cols-2' : 'grid-cols-1']">
          <div
            v-for="(kamar, key) in percentagePengasuhan"
            :class="['my-1 mb-3 p-2 border border-solid border-slate-200']"
          >
            <table class="w-full text-sm">
              <tbody>
                <tr>
                  <td class="font-bold text-[15px]">
                    <div class="mb-2 bg-[var(--color-main-50)] px-2 py-1">
                      <el-button
                        v-if="role == 'admin' || role == 'wamar'"
                        link
                        type="primary"
                        size="small"
                        class="p-0 mr-1"
                        @click="
                          id_kamar = kamar.id_kamar;
                          loading = true;
                          $router.push({ name: 'admin-pengasuhan' });
                        "
                      >
                        <icons v-if="role == 'guru'" icon="bxs:edit" class="m-0" />
                        <icons v-else icon="mdi:eye" class="m-0" />
                      </el-button>
                      {{ role == "guru" ? "Kamar " + kamar.kamar + " - " : "" }}
                      {{ kamar.kamar }} | {{ kamar.nama_wamar }}
                    </div>
                  </td>
                </tr>
                <tr
                  v-for="u in kamar.nilai"
                >
                  <td class="px-2 pb-3">
                    <div class="font-bold pb-1">{{ u.kategori }}</div>
                    <el-progress
                      :percentage="u.presentase_nilai"
                      class="w-full *:text-sm"
                      :stroke-width="18"
                      :show-text="true"
                      :status="u.presentase_nilai < 100 ? 'warning' : ''"
                      striped
                    >
                      {{ u.presentase_nilai }} %
                    </el-progress>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import { mapState } from "pinia";

export default {
  name: "mapel",
  emits: ["update:id_semester", "update:id_kelas", "update:id_kamar"],
  props: {
    role: {
      type: String,
      default: "admin",
    },
    id_semester: {
      type: String,
      default: "",
    },
    id_kelas: {
      type: String,
      default: "",
    },
    id_kamar: {
      type: String,
      default: "",
    },
    labelProgress: {
      type: String,
      default: "Pengasuhan",
    },
  },
  setup() {
    return {
      runFunction,
      isEmpty,
    };
  },
  components: {},
  data: function () {
    return {
      initial: true,
      loading: false,
      params: {
        where: [],
      },
      percentage:[],
      percentagePengasuhan: [],
      // role:'guru',
    };
  },
  watch: {
    id_semester(val){
      this.getData()
    },
    id_kelas(val){
      this.getData()
    },
    id_kamar(val){
      this.getData()
    },
  },
  computed: {
  },
  methods: {
    async getData() {
      this.percentagePengasuhan = [];
      this.percentage = [];
      await this.$http
        .get("pengasuhan/nilai/get_progres", {
          params: {
            id_semester: this.id_semester,
            id_kamar: this.id_kamar,
            id_kelas: this.id_kelas,
          },
        })
        .then((result) => {
          this.loading = false;
          let res = result.data;
          this.percentagePengasuhan = res;
          res.forEach((kamar) => {
            this.percentage.push({
              label: kamar.kamar,
              percentage: kamar.presentase_pengasuhan,
            });
          });
          console.log("percentage", this.percentage);
        });
    },
  },
  mounted() {
    this.getData();
  },
};
</script>
