<template>
  <div id="kalender-list" class="pt-1" v-loading="loading">
    <el-card class="bg-white/[0.7] mb-4">
      <form-comp ref="formFilter"
        :key="formKey"
        :fields="filterFields"
        :label-position="labelPosition"
        :form-class="'mt-2 mb-0'"
        label-width="150px"
        v-model:form-value="filter"
        :pass-columns="[]"
        :show-submit="false"
        :show-label="$windowWidth.value > 640"
        text-submit="Cari"
        error-submit-text="Tidak dapat mengambil data"
        :show-required-text="false"
        @on-form-saved="searchData()"
        >
      </form-comp>
    </el-card>
    <table-data v-show="showList"
      ref="tableData" :fields="fields" href="kmi/admin/kaldik"
      @updateData="getSummary()"
      @changedFormValue="({field, value}) => { 
          console.log(field, value)
          if (field == 'tanggal_mulai')
            formValue.tanggal_selesai = value
      }"
      :checked="true"  :pass-columns="['color']"
      v-model:formValue="formValue"
      :params="params">
      <template #menu>
        <el-button type="primary"
          @click="showList = false; this.getSummary()">
          <icons icon="mdi:eye"/> Lihat Kalender
        </el-button>
        <el-button type="primary"
          @click="downloadKaldik()">
          <icons icon="mdi:download"/> Download Kaldik
        </el-button>
      </template>
      <el-table-column label="Warna Latar" width="80" align="center">
        <template #default="scope">
          <div :style="{ backgroundColor: scope.row.color }"
            class="w-6 h-6 rounded-full border border-slate-300 mx-auto">
          </div>
        </template>
      </el-table-column>
    </table-data>
    <el-card v-show="!showList"
      class="bg-white [&_*]:animate [--duration:0.3s]
        before:bg-white before:w-full before:h-full before:absolute before:z-[-2]">
      <el-button type="primary"
        @click="showList = true; $nextTick(() => { $refs.tableData?.getData(); });">
        <icons icon="fa6-solid:list"/> Tampilan List Data Kalender
      </el-button>
      <el-button type="success"
        @click="$refs.tableData.handleActionClick({action:'add'})">
        <icons icon="mdi:plus"/> Tambah Data
      </el-button>
      <el-button type="primary"
        @click="downloadKaldik()">
        <icons icon="mdi:download"/> Download Kaldik
      </el-button>
      <h3 style="text-align: center; margin-bottom: 10px;">
        Kalender Pendidikan Semester {{ ucFirst(kalenders?.semester?.semester) }} 
        Tahun Ajaran {{ kalenders?.semester?.tahun_ajaran }}
      </h3>

      <table class="w-full border-collapse">
        <tbody>
          <template v-for="(monthData, bulanKey) in kalenders?.bulans" :key="bulanKey">
            <tr >
              <td valign="top" style="padding-right: 20px; border: 0px;">
                <table class="kalender border-collapse">
                  <thead>
                    <tr style="background-color: #67C23A;">
                      <th colspan="7">{{ monthIndo(bulanKey) }}</th>
                    </tr>
                    <tr>
                      <td>Sen</td><td>Sel</td><td>Rab</td><td>Kam</td>
                      <td>Jum</td><td>Sab</td><td>Min</td>
                    </tr>
                  </thead>
                  <tbody>
                    <tr v-for="(week, weekIndex) in monthData" :key="weekIndex">
                      <td 
                        v-for="dayIdx in 7" 
                        :key="dayIdx"
                        :id="'date='+week[dayIdx]?.date"
                        class=""
                      >
                        <div class="absolute z-[0] w-full h-full bg-white
                          top-0 left-0 flex flex-col">
                          <div v-for="color in week[dayIdx]?.color" :key="color" :style="{ backgroundColor: color }" 
                            class="w-full h-full cursor-pointer hover:brightness-[1.5]"></div>
                        </div>
                        <div class="date img-kalender">{{ week[dayIdx]?.tanggal || '' }}</div>
                        <template v-if="week[dayIdx]?.shape">
                          <img 
                            v-if="!isEmpty(week[dayIdx].shape)"
                            :src="$baseUrl + 'assets/images/kmi/' + week[dayIdx].shape +'.png'" 
                            class="img-kalender shape"
                          />
                        </template>
                        <div class="absolute z-[999] w-full h-full
                          top-0 left-0 flex flex-col">
                          <div v-for="(color, key) in week[dayIdx]?.color" :key="color"  
                            @mouseover="changeColorKeterangan(bulanKey, week[dayIdx]?.id[key], color)"
                            @mouseleave="changeColorKeterangan(bulanKey, week[dayIdx]?.id[key])"
                            class="w-full h-full cursor-pointer hover:brightness-[1.5]"></div>
                        </div>
                      </td>
                    </tr>
                  </tbody>
                </table>
              </td>

              <td valign="top" style="border: 0px;">
                <table class="keterangan border-collapse" style="width: 100%; font-size: 14px;">
                  <thead>
                    <tr style="background-color: #67C23A;">
                      <th width="160px">Tanggal</th>
                      <th>Keterangan</th>
                    </tr>
                  </thead>
                  <tbody>
                    <tr 
                      v-for="(item, index) in Object.values(kalenders?.keterangan[bulanKey] ?? {})" 
                      @mouseover="hoverKeterangan(item)"
                      @mouseleave="hoverKeterangan(item, false)"
                      class="cursor-pointer"
                      :key="index"
                      :style="{ backgroundColor: !isEmpty(item?.colorHover) ? item?.colorHover : (index % 2 === 0 ? '#d7f9c6' : '') }"
                    >
                      <td>{{ dateIndoRange(item.tanggal_mulai, item.tanggal_selesai) }}</td>
                      <td >
                        <div class="flex items-center">
                          <icons icon="mdi:edit-circle" class="text-[20px] text-blue-600 hover:scale-125 hover:text-blue-400 m-0"
                            @click="$refs.tableData.handleActionClick({action:'edit', id:item.id})"/>
                          <icons icon="mdi:delete-circle" class="text-[20px] text-red-600 hover:scale-125 hover:text-red-400"
                            @click="$refs.tableData.handleActionClick({action:'delete', id:item.id})"/>
                          {{ item.keterangan }}
                        </div>
                      </td>
                    </tr>
                  </tbody>
                </table>
              </td>
            </tr>
            <tr>
              <td colspan="2" class="text-center text-sm text-gray-500">
                <el-divider position="center" class="border-10" v-if="breaks.includes(bulanKey)">
                  <el-button type="text" class="text-red-500" size="small" @click="removePageBreak(bulanKey)">
                    Hapus Page Break
                  </el-button>
                </el-divider>
                <el-divider border-style="dashed" position="center" v-else>
                  <el-button type="info" plain size="small" @click="addPageBreak(bulanKey)">
                    <icons icon="mdi:format-page-break"/> Tambahkan Page Break
                  </el-button>
                </el-divider> 
              </td>
            </tr>
          </template>
        </tbody>
      </table>
    </el-card>
  </div>
</template>
  
<script>
    
import { nextTick, reactive } from 'vue';
import { mapActions, mapState } from 'pinia';
import { filterFields } from 'element-plus/es/components/form/src/utils.mjs';
import { useAuthStore } from '@/config/stores/authStore'
import { id } from 'element-plus/es/locale/index.mjs';
  
  export default {
    name: "kalender-list",
    setup() {
      const { getDataFromStorage } = useStorage()
      return {
        isEmpty, dateIndoRange, monthIndo, ucFirst,
        getDataFromStorage,
      }
    },
    props:{
      type:'',
      showCreate:{
        type:Boolean,
        default: true,
      },
      showSearch:{
        type:Boolean,
        default: true,
      },
    },
    components: {
      
    },
    data: function() {
      return {
        showList:false,
        loading:false,
        data:{},
        fields:[],
        state: reactive({
          passColumns : [],
          showColumns : [],
        }),
        filterFields: {
          id_semester:{
            label:'Semester',
            nama_kolom:'id_semester',
            input:'select',
            options:[],
          },
          id_unit:{
            label:'Unit',
            nama_kolom:'id_unit',
            input:'select',
            options:[],
          },
        },
        filter:{
          id_semester: '',
          id_unit: '',
        },
        params:{
          where:[],
        },
        formValue:{},
        kalenders:{},
        breaks:[],
      };
    },
    provide() {
      return {
        sharedState: this.state
      }
    },
    watch: {
      
        'filter.id_semester' (val) {
          // console.log('id',val)
          this.formValue.id_semester = val
          this.searchData()
      },
        'filter.id_unit' (val) {
          // console.log('id',val)
          this.formValue.id_unit = val
          this.searchData()
        },
    },
    computed: {
      ...mapState(useAuthStore,{
        user:'loggedUser'
      }),
      labelPosition(){
        return this.$windowWidth.value < 800 ? 'top' : 'left'
      },
    },
    methods: {
      addPageBreak(bulanKey){
        setTimeout(() => {
          this.saveToStorage('kaldik_breaks_' + this.filter.id_semester, bulanKey)
          this.getPageBreaks()
        }, 300);
      },  
      removePageBreak(bulanKey){
        setTimeout(() => {
          this.removeFromStorage('kaldik_breaks_' + this.filter.id_semester, bulanKey)
          this.getPageBreaks()
        }, 300);
      },  
      searchData(){
        this.params.where = Object.fromEntries(
          Object.entries(this.filter).filter(([key, value]) => value)
        )
        this.getSummary()
        this.getPageBreaks()
      },
      getPageBreaks(){
        this.breaks = this.getDataFromStorage('kaldik_breaks_' + this.filter.id_semester) || []
      },
      getInitial: async function() {
          this.loading = true;
          await this.$http.get('/kolom/preparation?table=' + this.$prefixTable + 'aka_kaldik&grouping=0&input=0')
            .then(result => {
              var res = result.data;
              this.fields = res
              this.loading = false
              this.$nextTick(() => {
                this.$refs.tableData?.getData()
                this.getSummary()
              })
            });
          await this.$http.get('data/semester/options')
            .then(result => {
              let res = result.data
              this.filterFields.id_semester.options = res
              this.filter.id_semester = res.length > 0 ? res[0].value : ''
            })
          await this.$http.get('data/unit/options')
            .then(result => {
              let res = result.data
              this.filterFields.id_unit.options = res
              this.filterFields.id_unit.options.splice(0, 0, {value:'', label:'Semua Unit'})
              this.filter.id_unit = res.length > 0 ? res[0].value : ''
            })
        },
      getSummary(){
        let where = {}
        if (this.filter.id_unit > 0)
          where.id_unit = this.filter.id_unit
        if (this.filter.id_semester > 0)
          where.id_semester = this.filter.id_semester

        this.$http.get('kmi/admin/kaldik/download_kalender', {
          params: {
            where: where,
            data: 1,
          }
        }
        )
          .then(result => {
            let res = result.data
            this.kalenders = res
          })
      },
      changeColorKeterangan(bulanKey, id, color = null){
        console.log(this.kalenders.keterangan[bulanKey], id)
        console.log(this.kalenders.keterangan[bulanKey][id])
        this.kalenders.keterangan[bulanKey][id].colorHover = color
      },
      hoverKeterangan(item, isHover = true){
        item.colorHover = isHover ? item.color : '' 
        let dates = getDateRanges(item.tanggal_mulai, item.tanggal_selesai)
        dates.forEach(date => {
          if (isHover){
            addClass('#date-' + date, 'brightness-[1.5] shadow-lg shadow-orange-400')
          } else {
            removeClass('#date-' + date, 'brightness-[1.5] shadow-lg shadow-orange-400')
          }
        })
      },
      downloadKaldik(){
        let where = {}
        if (this.filter.id_unit > 0)
          where.id_unit = this.filter.id_unit
        if (this.filter.id_semester > 0)
          where.id_semester = this.filter.id_semester

        let url = this.$baseUrl + 'kmi/admin/kaldik/download_kalender?'
        url += objectToQueryParams({
          'where': where,
          'page-breaks': this.breaks,
        })
        window.open(url, '_blank')
      },
      // getColor(colors, angle = 0) {
      //   if (!colors || colors.length === 0) return 'white';
      //   if (colors.length === 1) return colors[0];

      //   const stepSize = 100 / colors.length;
        
      //   // Map through colors to create "Color Pct%, Color Pct%" strings
      //   const stops = colors.map((color, index) => {
      //     const start = index * stepSize;
      //     const end = (index + 1) * stepSize;
      //     return `${color} ${start}%, ${color} ${end}%`;
      //   });

      //   return `linear-gradient(${angle}, ${stops.join(', ')})`;
      // },
    },
    created: function() {
      this.getInitial();
    }
  }
  </script>

<style scoped>
  .kalender td {
    width: 35px;
    height: 25px;
    text-align: center;
    vertical-align: middle;
    position: relative;
    border: 1px solid #cccccc;
  }
  .keterangan td {
    padding: 3px 6px;
    border: 1px solid #cccccc;
  }
  .img-kalender {
    width: 30px;
    height: 25px;
    position: absolute;
    left: 50%;
    top: 50%;
    transform: translateY(-50%) translateX(-50%);
  }
  .date {
    z-index: 2;
  }
  .icon {
    width: 100%;
    height: 100%;
    position: absolute;
    left: 50%;
    top: 50%;
    transform: translateY(-50%) translateX(-50%);
    z-index: 1;
    color:#337413;
  }
  .img-kalender {
    pointer-events: none; /* Mouse ignores this element completely */
  }
</style>
  