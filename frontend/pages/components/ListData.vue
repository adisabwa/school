<template>
	<div 
    ref="scrollContainer"
		v-infinite-scroll="loadingData"
		class="min-h-[200px] max-h-[50vh] overflow-y-auto px-6 "
		:infinite-scroll-disabled="disabledScroll"
		infinite-scroll-delay="1000"
		infinite-scroll-distance="10">
		<template v-for="(s, key) in listData">
			<div v-if="s?.tanggal?.slice(0, 7) != listData[key - 1]?.tanggal?.slice(0,7)" 
        class="bg-slate-100
        text-slate-400 text-[15px]
        p-2 py-1 mb-3">{{ monthIndo(s.tanggal) }}</div>
      <div :class="[`h-fit px-5 py-3 mb-3
        rounded-[15px]
        bg-[var(--bg-color)] border border-solid border-[var(--border-color)]
        text-[var(--text-color)]
        flex items-center justify-between
        relative overflow-hidden
        cursor-pointer`,
        boxClass]"
				draggable="true"
				@click="(event) => editData(event, key)"
				v-click-outside="() => addClass('#listData'+key, 'translate-x-[125px]')">
        <div class="leading-[1.5] w-full">
          <div v-if="$slots.subtitle" class="font-semibold text-[13px] opacity-70">
            <slot name="subtitle" :data="s" />
          </div>
          <div v-if="$slots.title" class="font-bold text-[17px]">
            <slot name="title" :data="s"/>
          </div>
          <div v-if="$slots.content" class="content-data-list font-semibold text-[13px] opacity-70">
            <slot name="content" :data="s" />
          </div>
          <div v-if="showName" class="content-data-list font-normal italic text-[14px] 
            mt-1 opacity-70">
            {{ s.nama }}
          </div>
        </div>
				<div :id="'listData'+key"
					class="absolute bg-[var(--bg-button-color)] right-0 translate-x-[125px]
					px-4
					animate
					h-full w-fit flex items-center" 
					v-if="['user','super-admin'].includes(user.role)">
					<el-button
						class="rounded-full h-[40px] w-[40px]
						border border-solid border-[var(--border-color)]
						bg-[var(--button-color)]"
						@click="$emit('editData',s)">
						<icons icon="mdi:edit" class="m-0 text-[20px] text-[var(--text-color)]" />
					</el-button>
					<el-button
						class="rounded-full h-[40px] w-[40px]
						border border-solid border-[var(--border-color)]
						bg-red-200"
						@click="deleteData(s.id)">
						<icons icon="mdi:delete" class="m-0 text-[20px] text-red-500" />
					</el-button>
				</div>
      </div>
		</template>
		<p v-if="loadingScroll" class="my-0 text-center text-[13px]">Menggambil Data...</p>
		<p v-if="noMoreScrolling" class="my-0 text-center text-[13px]">Data Selesai</p>
	</div>
</template>

<script>
import { mapActions, mapState } from 'pinia';

export default {
  name: "ListData",
	emits:['editData','deleteData'],
  props:{
    idAnggota:{
      type:[String, Number],
      default:null,
    },
    href:{
      type:[String, Number],
      default:'',
    },
    hrefDelete:{
      type:[String, Number],
      default:'',
    },
    boxClass:{type:String,default:''},
    groupBy:{type:Array, default:[]},
    datas:{type:[Array, Object], default:[]}
  },
  computed: {
    
  },
  data: function() {
    return {
			listData:[],
			loadingScroll:true,
			noMoreScrolling:false,
			limit:5,
			offset:null,
      showName:false,
		}
	},
  watch:{
    idAnggota(val){
      // console.log('id_anggota', val)
      setTimeout(this.getData(),300)
    },
    disabledScroll(val){
      // console.log('disable', val)
    }
  },
	computed:{
		disabledScroll(){
      // console.log(this.noMoreScrolling, this.loadingScroll)
			return this.noMoreScrolling || this.loadingScroll
		},
    ...mapState(useAuthStore,{
      user:'loggedUser',
    })
	},
	methods:{
    ...mapActions(useDataStore,{
      delete:'deleteData'
    }),
    getData(reset = true){
      // console.log(this.href, reset, this.idAnggota)
      console.log(this.datas)
      if (!this.isEmpty(this.datas)) {
        this.listData = this.datas
        this.noMoreScrolling = true
        this.loadingScroll = false
        return;
      }
      this.loadingScroll = true
      if (reset) {
        this.offset = 0
        this.limit = 5
        this.noMoreScrolling = false
        this.listData = []
      }
      this.$http.get(this.href, {
          params: {
            in:{
              id_anggota: this.idAnggota.split(','),
            },
            order:['tanggal desc','nama','id desc'],
            limit:this.limit,
            offset:this.offset,
            grouping:this.groupBy,
          }
        }).then(result => {
          var res = result.data;
          this.listData = [...this.listData, ...res]
          // console.log(this.listData)
          this.showName = this.idAnggota.split(',').length > 1
          this.loadingScroll = false
          // console.log('no-more', res.length < this.limit)
          if (res.length < this.limit) {
            this.noMoreScrolling = true;
          } else {
            this.offset += this.limit;
          }
          // this.$nextTick(() => {
          //   const el = this.$refs.scrollContainer;
          //   if (
          //     el &&
          //     el.scrollHeight <= el.clientHeight &&
          //     !this.noMoreScrolling
          //   ) {
          //     // console.log('scroll one more')
          //     this.getData(false); // Load more if not scrollable
          //   }
          // });
          // // console.log(this.listData)
        });
    },
    loadingData(){
      // console.log('loading')
      this.loadingScroll = true
      this.getData(false)
    },
    editData(event, key){
      const isInsideParent = jquery('.content-data-list').toArray().some(function(parent) {
        return jquery.contains(parent, event.target);
      });
      if (isInsideParent) {
        return; // Ignore clicks on the excluded element
      }
      this.removeClass('#listData'+key, 'translate-x-[125px]')
    },
    deleteData(id){
      this.delete({
        href:this.hrefDelete,
        id:id,
      }).then( res => {
        this.getData(true);
        this.$emit('deleteData', id)
      })
    }
	},
	mounted(){
    this.getData(true)
	}
}
</script>