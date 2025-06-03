<template>
  <div id="group-admin" class="pt-16 translate-y-[-20px]">
    <el-card class="relative overflow-hidden
        bg-gradient-to-tr from-white/[0.8] from-40% to-teal-200/[0.7] rounded-[10px]
      z-[0]
        font-montserrat
      mb-3 p-0" 
      header-class="relative px-4 pt-6 pb-2 text-[15px] font-montserrat font-bold text-left"
      body-class="py-2 px-0">
      <template #header>
        <div class="relative text-center">
          {{ isEmpty(data.nama_group) ? 'Kelompok' : 'Kel.' }} {{ data.nama_group }}
        </div>
      </template>
      <template v-if="isEmpty(data.id)" >
        <div class="text-center h-20 w-[200px]
          flex items-center mx-auto">
          Anda belum memiliki grup
        </div>
      </template>
      <template v-else>
        <table class="w-full
          [&_td]:align-top text-[13px] px-6">
          <tbody>
            <tr class="font-bold">
              <td >Mentor</td>
              <td width="20" class="text-center">:</td>
              <td v-if="!isEmpty(data.anggota)">{{ data.anggota[0].nama }}</td>
            </tr>
            <tr class="font-bold">
              <td >Anggota</td>
              <td width="20" class="text-center">:</td>
              <td class="font-normal">
                <ol class="text-[13px] pl-4 m-0">
                  <template v-for="(i, key) in data.anggota">
                    <li class="pl-1"
                      v-if="key > 0">{{ i.nama }}</li>
                  </template>
                </ol>
              </td>
            </tr>
            <tr class="font-bold">
              <td >Aktivitas</td>
              <td width="20" class="text-center">:</td>
              <td class="text-right">
                <el-button class="[&_*]:text-[11px] h-fit py-1 active:scale-90
                  bg-teal-700 text-white"
                  @click="showAdd = true"
                  >
                  <icons icon="mdi:plus"/>Tambah Data
                </el-button>
              </td>
            </tr>
          </tbody>
          </table>
        <div 
            v-infinite-scroll="loadingData"
            :infinite-scroll-disabled="noMoreScrolling"
            infinite-scroll-delay="1000"
            infinite-scroll-distance="10"
            class="min-h-[200px] max-h-[50vh] overflow-auto px-6 mt-3">
          <div v-for="data in listActivity"
            class="relative py-3 px-4 pb-3 bg-white/[0.9] rounded-[15px] mb-3
            border border-solid border-teal-700/[0.5]
            flex gap-x-2">
            <div class="w-full text-[13px]">
              <div class="font-bold italic">{{ dateDayIndo(data.tanggal) }}</div>
              <el-divider class="my-1 
                border-0 border-b border-solid border-teal-700/[0.5]"/>
              <div :class="[`text-[12px] 
                inline-block overflow-hidden`,
                data.show ? '' : 'max-h-[35px]']">{{ data.kegiatan }}</div>
              <div class="text-teal-700 text-[10px] float-right"
                @click="data.show = !data.show">Show All</div>
            </div>
          </div>
          <p v-if="loadingScroll" class="my-0 text-center text-[13px]">Menggambil Data...</p>
          <p v-if="noMoreScrolling" class="my-0 text-center text-[13px]">Data Selesai</p>
        </div>
      </template>
    </el-card>
    <el-dialog v-model="showAdd" draggable
      :append-to-body="true"
      class="w-fit max-w-[80%] py-3
        bg-gradient-to-tr from-white from-50% to-teal-100"
      header-class="font-bold text-[16px]"
      body-class="text-[14px]">
      <template #header>
        <div>Data Aktifitas</div>
      </template>
      <form-comp ref="formActiviity"
        class="[&_*]:rounded-[15px]"
        :key="'form-activity-'+formKey"
        :fields="fields" 
        v-model:id="dataId"
        v-model:form-value="formValue" 
        href="data/group/activity/store"
        href-get="data/group/activity/get"
        :show-columns="['tanggal','kegiatan']"
        @saved="submittedData" 
        @error="saving=false"
        size="large"
        :show-submit="false"
        label-position="top"
        :show-required-text="false">
      </form-comp>  
      <template #footer>
        <div class="dialog-footer">
          <el-button @click="showAdd = false">Batal</el-button>
          <el-button type="primary" @click="$refs.formActiviity.submitForm()"
            class="bg-teal-700">
            Simpan
          </el-button>
        </div>
      </template>
    </el-dialog>
  </div>
</template>

<script>
  import Form from '@/components/Form.vue'

export default {
  name:'group-user',
  components:{
    'form-comp' : Form,
  },
  data: () => {
    return {
      loading:true,
      showAdd: false,
      formKey:1,
      dataId:-1,
      fields:{
        tanggal:'',
        kegiatan:'',
      },
      data:{},
      listActivity:[],
      formValue:{},
      loadingScroll:true,
      noMoreScrolling:false,
      limit:5,
      offset:null,
    }
  },
  watch:{
    showAdd(){
      this.formKey = this.formKey + 1
    }
  },
  methods: {
      getInitial: async function() {
        this.loading = true;
        // await this.$http.get('/infaq/shadaqah/get_last')
        //   .then(result => {
        //     var res = result.data;
        //     this.lastData = this.fillAndAddObjectValue(this.lastData, res)
        //   });
        
        await this.getData();
        await this.$http.get('/kolom/preparation?table=mu_group_activity&grouping=0&input=0')
          .then(result => {
            var res = result.data;
            this.dataId = -1
            this.fields = this.fillAndAddObjectValue(this.fields, res)
            this.fields.tanggal.default = this.dateNow()
            this.fields.id_group.default = this.data.id
            this.loading = false
          });
        // this.datas = [];
        // await this.getData();
      },
    async getData() {
      this.loading = true;
      this.$http.get('/data/group',{
        params: {
          where: {
            id_anggota:useAuthStore()?.loggedUser?.id_anggota
          }
        }
      })
          .then(result => {
            var res = result.data;
            // console.log(res)
            this.data = res[0] ?? {}
            this.loading = false
          });
    },
      submittedData(){
        this.loading = false;
        this.showAdd = false;
        this.limit = this.offset
        this.offset = 0
        this.getDataActivity();
      },
    async getDataActivity(reset = true) {
      this.loading = true;
      this.$http.get('/data/group/activity', {
        params:{
          where:{ id_group : this.data?.id_group, },
          order:['tanggal desc'],
          limit:this.limit,
          offset:this.offset,
        }
      })
          .then(result => {
            var res = result.data;
            res = res.map(d => {
              d.show = false
              return d
            })
            // console.log(res)
            this.listActivity = reset ? res : [...this.listActivity, ...res]
            this.loading = false
            this.loadingScroll = false
            if (this.isEmpty(res)) {
              this.noMoreScrolling = true
            } else {
              this.offset += 5
            }
          });
    },
    async loadingData(){
      this.loadingScroll = true
      await this.getDataActivity(false)
    },
  },
  created: function() {
    this.getInitial()
  },
}
</script>