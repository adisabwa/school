<template>
  <div id="group-admin" class="pt-12 translate-y-[-20px]">
    <el-card class="relative overflow-hidden
        bg-gradient-to-tr from-white/[0.8] from-40% to-teal-200/[0.7] rounded-[10px]
      z-[0]
        font-montserrat
      mb-3 p-0" 
      header-class="relative px-4 pt-6 pb-2 text-[15px] font-montserrat font-bold text-left"
      body-class="py-2 px-0">
      <template #header>
        <div class="relative">
          <el-button class="absolute right-2 top-1/2 -translate-y-1/2
            rounded-full p-0
            h-[25px] w-[25px]
            flex items-center justify-center
            font-montserrat
            bg-teal-700
            text-white
            active:scale-90"
            @click="showAdd = true;
              getInitial()">
            <icons icon="mdi:plus" class="m-0"/>
          </el-button>
          Data Kelompok
        </div>
      </template>
      
      <ListData ref="listGroup"
        class="[--text-color:theme(colors.teal.900)]
          [--bg-color:theme(colors.teal.50)]
          [--border-color:theme(colors.teal.400)]
          [--bg-button-color:theme(colors.teal.100)]
          [--button-color:theme(colors.teal.200)]
          font-sans max-h-screen
        "
        :datas="datas"
        hrefDelete="data/group/delete"
        box-class="px-2 mb-4"
        @edit-data="(({id}) => {
          dataId = id
          showAdd = true
        })"
        @delete-data="submittedData">
        <template #title="{ data }">
          <div class="text-[16px] mb-1">
            Kel. {{ data.nama_group }}
          </div>
          <el-divider class="m-0"/>
        </template>
        <template #content="{ data }">
          <div class="text-[13px] font-semibold
            flex items-center justify-between
            py-1 pb-2
            "
            @click="data.show = !data.show">
            <div>
              Daftar Anggota <br/>
              <span class="italic"> ( {{data.anggota.filter(r => r.type == 'mentor').length }} Mentor & 
              {{ data.anggota.filter(r => r.type == 'anggota').length }} Anggota )</span>
            </div>
            <icons :icon="data.show ? 'fe:arrow-down' : 'fe:arrow-up'" 
              class="ml-1 text-[12px]"/>
          </div>
          <div :class="['animate bg-white px-2  overflow-hidden',
            data.show ? 'max-h-screen pt-1 pb-2' : 'max-h-0 p-0']">
            <div class="italic">Mentor :</div>
            <ol class="text-[12px] italic pl-4 m-0">
              <template v-for="(i, key) in data.anggota.filter(r => r.type == 'mentor')">
                <li class="pl-1">{{ i.nama }}</li>
              </template>
            </ol>
            <div class="mt-1 italic">Anggota :</div>
            <ol class="text-[12px] italic pl-4 m-0">
              <template v-for="(i, key) in data.anggota.filter(r => r.type == 'anggota')">
                <li class="pl-1">{{ i.nama }}</li>
              </template>
            </ol>
          </div>
        </template>
      </ListData>
    </el-card>
    
    <el-dialog v-model="showAdd" draggable
      :append-to-body="true"
      class="w-fit max-w-[80%] py-3
        bg-gradient-to-tr from-white from-50% to-teal-100"
      header-class="font-bold text-[16px]"
      body-class="text-[14px]">
      <template #header>
        <div>Data Shadaqah</div>
      </template>
      <form-comp ref="formGroup"
        class="[&_*]:rounded-[15px]"
        :key="'form-group-'+formKey"
        :fields="fields" 
        v-model:id="dataId"
        href="data/group/store"
        href-get="data/group/get"
        @saved="submittedData" 
        :pass-columns="['mu_group_anggota']"
        @error="saving=false"
        size="large"
        :show-submit="false"
        label-position="top"
        :show-required-text="false">
        <template #default="{ form, errors, fields }">
          <template v-for="tipe in ['mentor', 'anggota']">
            <el-form-item
              :label="`Nama ${ucFirst(tipe)}`"
              :prop="`mu_group_anggota.${tipe}`">
              <floating-select
                class="w-full"
                :data-input="form['mu_group_anggota']?.filter(d => d.type == tipe)?.map(data => data.id_anggota)"
                :options="fields['mu_group_anggota']?.fields?.id_anggota?.options"
                :placeholder="`Pilih ${ucFirst(tipe)}`"
                :clearable="true"
                :filterable="true"
                :multiple="true"
                @change="(ids) => {
                  form['mu_group_anggota'] = form['mu_group_anggota']?.filter(i => i?.type == undefined || i.type != tipe)                
                  if (Array.isArray(ids) == false) {
                    return
                  }
                  ids.forEach((id) => {
                    let index = form['mu_group_anggota'].findIndex(i => i.id_anggota == id)
                    if (index > 0) {
                      form['mu_group_anggota'][index].type = tipe
                    } else {
                      form['mu_group_anggota'].push({
                        id_anggota: id,
                        type: tipe,
                      })
                    }
                  })
                }"/>
              <ol class="text-[15px] italic pl-6 m-0 leading-[1.5] mt-2">
                <template v-for="(i, key) in form['mu_group_anggota']">
                  <li v-if="i?.type == tipe"
                    class="pl-1">
                    <div>
                      {{ runFunction(null, i?.id_anggota, fields['mu_group_anggota']?.fields?.id_anggota?.options) }}
                    </div>
                    <div v-if="errors['mu_group_anggota']?.[key]?.id_anggota"
                      class="text-red-500 text-[12px]">
                      {{ errors['mu_group_anggota']?.[key]?.id_anggota }}
                    </div>
                  </li>
                </template>
              </ol>
            </el-form-item>
          </template>
        </template>
      </form-comp>  
      <template #footer>
        <div class="dialog-footer">
          <el-button @click="showAdd = false">Batal</el-button>
          <el-button type="primary" @click="$refs.formGroup.submitForm()"
            class="bg-teal-700">
            Simpan
          </el-button>
        </div>
      </template>
    </el-dialog>
  </div>
</template>

<script>
import ListData from '@/pages/components/ListData.vue';
import { error } from 'jquery';
import { isEmpty } from 'lodash';

export default {
  name:'group-admin',
  components:{
    ListData,
  },
  data: () => {
    return {
      show:true,
      showAdd: false,
      formKey:0,
      fields:{},
      datas:{},
      dataId:-1,
    }
  },
  methods: {
    getData() {
      this.loading = true;
      this.$http.get('/data/group')
          .then(result => {
            var res = result.data;
            this.datas = res
            this.loading = false
            this.$nextTick(() => {
              this.$refs.listGroup.getData(true)
            });
          });
    },
    getInitial: async function() {
        this.loading = true;
        
        await this.$http.get('/kolom/preparation?table=mu_group&grouping=0&input=0')
          .then(result => {
            var res = result.data;
            this.dataId = -1
            this.fields = this.fillAndAddObjectValue(this.fields, res)
            // console.log(this.fields)
            this.formKey++
            this.loading = false
          });
      },
    submittedData(){
      this.saving = false
      this.showAdd = false
      this.getData()
    },
  },
  created: function() {
    this.getData()
    this.getInitial()
  },
  mounted(){
  }
}
</script>