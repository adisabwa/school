<template>
	<div class="flex pt-8">
    <el-card class="w-full rounded-2xl font-montserrat
      bg-gradient-to-tl from-white/[0.8] from-30% to-teal-50/[0.5]"
      header-class="pt-8 pb-2 text-center font-bold"
      body-class="px-4 pt-4">
      <template #header>
        Aktivitas Hari Ini
      </template>
      <el-card
        class="relative overflow-hidden
        bg-gradient-to-tl from-white/[0.8] from-30% to-lime-200/[0.4] rounded-[10px]
        z-[0] text-[13px]
        mb-6 p-0" 
        header-class="pb-2"
        body-class="pt-3 px-4">
        <template #header>
          <div class="z-[10] text-gray-500
            text-[15px] font-semibold">Tadarus : </div>
        </template>
          <ol class="pl-4 m-0 leading-[1.6] text-[13px]">
            <li v-for="s in quranBaca"
              class="pl-2 mb-1">
              <div class="">
                {{ s.nama_surat_mulai }} ({{ s.surat_mulai }}) : {{ s.ayat_mulai }}
                <span v-if="s.surat_mulai == s.surat_selesai">
                 - 
                </span>
                <span v-else>
                 - {{ s.nama_surat_selesai }} ({{ s.surat_selesai }}) : 
                </span> {{ s.ayat_selesai }}
                <span class="font-bold"> &nbsp; [ {{ s.total_ayat }} ayat ]</span>
              </div>
            </li>
          </ol>
          <div v-if="isEmpty(quranBaca)"
            class="text-center">
          - Belum ada data -
          </div>
        <img :src="menu.quranBaca.image" height="90px" width="90px"
            class="absolute z-[-1] bottom-[10px] right-[-10px]
              opacity-[0.4]"/>
      </el-card>
      <el-card
        class="relative overflow-hidden
        bg-gradient-to-tl from-white/[0.8] from-30% to-orange-100/[0.4] rounded-[10px]
        z-[0]  text-[13px]
        mb-6 p-0" 
        header-class="pb-2"
        body-class="pt-3 px-4">
        <template #header>
          <div class="z-[10] text-gray-500
            text-[15px] font-semibold">Setoran Hafalan : </div>
        </template>
          <ol class="pl-4 m-0 leading-[1.6] text-[13px]">
            <li v-for="s in quranHafal"
              class="pl-2 mb-1">
              <div class="">
                {{ s.nama_surat_mulai }} ({{ s.surat_mulai }}) : {{ s.ayat_mulai }}
                <span v-if="s.surat_mulai == s.surat_selesai">
                 - 
                </span>
                <span v-else>
                 - {{ s.nama_surat_selesai }} ({{ s.surat_selesai }}) : 
                </span> {{ s.ayat_selesai }}
                <span class="font-bold"> &nbsp; [ {{ s.total_ayat }} ayat ]</span>
              </div>
            </li>
          </ol>
          <div v-if="isEmpty(quranHafal)"
            class="text-center">
          - Belum ada data -
          </div>
        <img :src="menu.quranHafal.image" height="90px" width="90px"
            class="absolute z-[0] bottom-[10px] right-[-10px]
              opacity-[0.4]"/>
      </el-card>
      <el-card
        class="relative overflow-hidden
        bg-gradient-to-tl from-white/[0.8] from-30% to-sky-100/[0.4] rounded-[10px]
        z-[0]  text-[13px]
        mb-6 p-0" 
        header-class="pb-2"
        body-class="pt-3 px-4">
        <template #header>
          <div class="z-[10] text-gray-500
            text-[15px] font-semibold">Baca Tarjamah : </div>
        </template>
          <ol class="pl-4 m-0 leading-[1.6] text-[13px]">
            <li v-for="s in quranTarjamah"
              class="pl-2 mb-1">
              <div class="">
                {{ s.nama_surat_mulai }} ({{ s.surat_mulai }}) : {{ s.ayat_mulai }}
                <span v-if="s.surat_mulai == s.surat_selesai">
                 - 
                </span>
                <span v-else>
                 - {{ s.nama_surat_selesai }} ({{ s.surat_selesai }}) : 
                </span> {{ s.ayat_selesai }}
                <span class="font-bold"> &nbsp; [ {{ s.total_ayat }} ayat ]</span>
              </div>
            </li>
          </ol>
          <div v-if="isEmpty(quranTarjamah)"
            class="text-center">
          - Belum ada data -
          </div>
        <img :src="menu.quranTarjamah.image" height="90px" width="90px"
            class="absolute z-[0] bottom-[10px] right-[-10px]
              opacity-[0.4]"/>
      </el-card>
      <el-card
        class="relative overflow-hidden
        bg-gradient-to-tl from-white/[0.8] from-30% to-purple-100/[0.4] rounded-[10px]
        z-[0]  text-[13px]
        mb-6 p-0" 
        header-class="pb-3"
        body-class="pt-4 px-4">
        <template #header>
          <div class="z-[10] text-gray-500
            text-[15px] font-semibold">Sholat Wajib  {{ (
								(sholatWajib.shubuh > 0 ? 2 : 0) + (sholatWajib.dhuhur > 0 ? 4 : 0) + (sholatWajib.asar > 0 ? 4 : 0) + (sholatWajib.maghrib > 0 ? 3 : 0) + (sholatWajib.isya > 0 ? 4 : 0) 
							) }} Raka'at : </div>
        </template>
          <div class="grid grid-cols-2 gap-x-3 gap-y-4
            w-full font-roboto">
            <template v-for="i in ['shubuh','dhuhur','asar','maghrib','isya']">
              <div :class="[statusWajib(sholatWajib[i]),
                  `text-center 
                  rounded-lg relative
                  flex items-start justify-center
                  p-2`]">
                <icons :icon="sholatWajib[i] >= 25 ? 'mdi:check-circle-outline' : 'mdi:cross-circle-outline'"
                  class="mt-2"/> 
                <div class="mr-2">
                  <div class="font-bold">
                    {{ ucFirst(i) }}
                  </div>
                  <div class="text-[12px]">{{ getLabelWajib(sholatWajib[i]) }}</div>
                  <star :count="getCount(sholatWajib[i])" width="13px"/>
                </div>
              </div>
            </template>
          </div>
        <img :src="menu.sholatWajib.image" height="90px" width="90px"
            class="absolute z-[-1] bottom-[10px] right-[-10px]
              opacity-[0.4]"/>
      </el-card>
      <el-card
        class="relative overflow-hidden
        bg-gradient-to-tl from-white/[0.8] from-30% to-rose-100/[0.4] rounded-[10px]
        z-[0]  text-[13px]
        mb-6 p-0" 
        header-class="pb-3"
        body-class="pt-4 px-4">
        <template #header>
          <div class="z-[10] text-gray-500
            text-[15px] font-semibold">Sholat Sunnah : </div>
        </template>
        <div class="">
          <div v-if="!isEmpty(sholatSunnah.daftar_sholat)"
            class="w-full font-roboto ">
            <ul class="pl-[0px] italic mt-0 mb-1
              grid grid-flow-row grid-cols-2 auto-rows-[minmax(40px,_auto)]
              gap-x-2 gap-y-3">
              <div class="text-center
                bg-rose-50 
                  border border-solid border-rose-200
                  shadow-md shadow-rose-900/[0.2]
                  w-fit h-fit rounded-lg py-4 px-5
                  mx-auto 
                  col-start-2 row-start-1 row-span-3">
                <div class="text-4xl">{{ sholatSunnah.total_rakaat }}</div>
                <div>Raka'at</div>
                <star class="mt-2 *:m-[-2px]" :count="getCountSunnah(sholatSunnah.total_rakaat)" width="20px"/>
              </div>
							<li v-for="(j) in (sholatSunnah.daftar_sholat ?? '').split('/').map(n => n.split('-'))"
								class="ml-2 pl-2 [&::marker]:content-['-']">
								<div class="text-[12px] leading-[1.3]">
                  Sholat {{ j[0] }}</div>
                <div class="text-[10px] mt-[2px]">{{ j[1] }} Raka'at</div>
							</li>
						</ul>
          </div>
          <div v-else
            class="text-center">
          - Belum ada data -
          </div>
        </div>
        <img :src="menu.sholatSunnah.image" height="90px" width="90px"
            class="absolute z-[-1] bottom-[10px] right-[-10px]
              opacity-[0.4]"/>
      </el-card>
      <el-card
        class="relative overflow-hidden
        bg-gradient-to-tl from-white/[0.8] from-30% to-teal-100/[0.4] rounded-[10px]
        z-[0]  text-[13px]
        mb-6 p-0" 
        header-class="pb-3"
        body-class="pt-4 px-4">
        <template #header>
          <div class="z-[10] text-gray-500
            text-[15px] font-semibold">Infaq Shadaqah : </div>
        </template>
        <div class="">
          <div class="w-full font-roboto
            grid grid-cols-2 gap-x-3 gap-y-4">
            <div v-for="i in infaqShadaqah"
              class="h-fit px-3 py-3
              rounded-[15px]
              bg-cyan-50/[0.8] border border-solid border-cyan-200
              text-cyan-900">
              <div class="leading-[1.5]">
                <div class="font-bold text-[15px]"> 
                  {{ i.tipe == '0' ? 'Tanpa Nominal' : toIDR(i.jumlah) }}
                </div>
                <div class="font-semibold text-[11px] opacity-70">{{ i.keterangan }}</div>
              </div>
            </div>
          </div>
          <div v-if="isEmpty(infaqShadaqah)"
            class="text-center">
          - Belum ada data -
          </div>
        </div>
        <img :src="menu.infaqShadaqah.image" height="90px" width="90px"
            class="absolute z-[-1] bottom-[10px] right-[-10px]
              opacity-[0.4]"/>
      </el-card>
    </el-card>
  </div>
</template>

<script setup>
  import { setStatusColor as statusWajib, options as optionsWajib, getLabel as getLabelWajib, getCount, getCountSunnah} from '@/helpers/sholat.js'
</script>

<script>
import { mapState } from 'pinia';
import { topMenu } from '@/helpers/menus.js'

export default {
  name: "quran",
  components: {
  },
  data: function() {
    return {
      idAnggota:'',
      menu:topMenu,
      tanggal:null,
      quranBaca:{},
      quranHafal:{},
      quranTarjamah:{},
      sholatWajib:{},
      sholatSunnah:{
        daftar_sholat:'',
      },
      infaqShadaqah:{},
      fields:[
        {href:'quran/baca', data:'quranBaca'},
        {href:'quran/hafal', data:'quranHafal'},
        {href:'quran/tarjamah', data:'quranTarjamah'},
        {href:'sholat/wajib/get_where', data:'sholatWajib'},
        {href:'sholat/sunnah/get_where', data:'sholatSunnah'},
        {href:'infaq/shadaqah', data:'infaqShadaqah'},
      ]
    };
  },
  watch: {
   
  },  
  computed: {
    ...mapState(useAuthStore, {
      user: 'loggedUser',
    }),
    
  },
  methods: {
    getInitial(){
      let vm = this
      let form = window.jsonToFormData({
        where:{
          id_anggota: vm.idAnggota,
          tanggal: vm.tanggal
        },
      })
      this.fields.forEach(f => {
        this.$http.post(f.href, form)
        .then(result => {
          var res = result.data;
          vm[f.data] = res
        });
      })
    }
  },
  created: function() {
    this.idAnggota = this.user.id_anggota
    // this.idAnggota = '24'
    this.tanggal = this.dateNow()
    // this.tanggal = '2025-05-09'
    this.getInitial()
  },
  mounted: function() {
    
  },
}
</script>