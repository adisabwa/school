<template>
	<div id="account-page">
		<el-card class="mx-2 bg-white pt-[50px] pb-10">
			<div class="relative">
				<div class="cursor-pointer
          h-[120px] w-[120px] mx-auto mb-6
          rounded-full overflow-hidden relative
					flex items-center justify-center
					border-3 border-solid border-teal-700"
					@click="showEdit = true;showColumns=['photo']">
          <div v-if="!isEmpty(viewValue.photo)"
            class="w-full h-full bg-cover bg-top"
            :style="`background-image:url('${viewValue.photo}')`"
            />
					<icons v-else 
            icon="mdi:user" class="mr-0 text-teal-700 text-[100px]"/>
				</div>
				<el-divider class="w-full [&>*]:w-[max-content] my-4">
					<div class="text-xl text-center font-bold">Profil Anggota</div>
				</el-divider>
				<div class="mt-6 flex items-center justify-center gap-2">
					<el-button class="mt-1 flex items-center
						w-full rounded-[10px] mx-0 bg-teal-700 
						text-teal-100 font-bold"
						@click="showEdit = true;
              showColumns=[];
              getInitial();">
						<icons icon="mdi:edit"/>
						Data Profil
					</el-button>
					<el-button class="mt-1 flex items-center
						w-full rounded-[10px] mx-0 bg-teal-700 
						text-teal-100 font-bold"
						@click="showEdit = true;
              showColumns = ['password','passwordconf'];
              fields = {
                password: {input:'password',nama_kolom:'password',default:'',label:'Password Baru'},
                passwordconf: {input:'password',nama_kolom:'passwordconf',default:'',label:'Konfirmasi Password Baru'}
              };">
						<icons icon="mdi:edit"/>
						Kata Sandi
					</el-button>
				</div>
				<form-comp v-if="showEdit"
					ref="formRegistrasi"
					class="mt-3 px-1"
					:key="'form-registrasi-'+formKey"
					:fields="fields" 
					:id="dataId"
					v-model:form-value="formValue"
					:show-columns="showColumns"
					href="data/anggota/store"
					href-get="data/anggota/get"
					@saved="saving = false; showEdit = false;
					authStore.resetAccount(); getInitial()"  
					@error="saving=false"
          :pass-columns="['password','passwordconf','role']"
					size="large"
					submit-text="Simpan Data"
					label-position="top"
					:show-required-text="false"
				></form-comp>  
				<template v-else>
					<view-table
						ref="viewAccount"
						class="mt-2 w-full"
						:fields="fields"
						label-position="top"
            v-model:form-value="viewValue"
						label-width="80px"
            :pass-columns="['photo','password','passwordconf','role']"
						href-get="/data/anggota/get_where"
						:keyword="dataId"
						:search-columns="['id']"
						v-model:empty="empty"
						v-model:loading="loading"
						v-model:id="dataId"
						/>
				</template>
			</div>
		</el-card>
	</div>
</template>

<script setup>
   
</script>

<script>

import { mapState } from 'pinia';
import ViewTable from '../../components/ViewTable.vue';
import Form from '../../components/Form.vue';
import { unset } from 'lodash';
const authStore = useAuthStore()

export default {
	name: 'account-page',
	components:{
		ViewTable,
		'form-comp' : Form,
	},
	data: function() {
		return {
      formKey:1,
			saving: false,
			showEdit: false,
			showColumns:[],
			fields:{},
			empty:false,
			loading:false,
			dataId:-1,
      formValue:{},
      viewValue:{},
	  authStore:authStore,
		};
	},
	computed:{
	},
  watch: {
    fields:{
      deep:true,
      handler(val){
        this.formKey++;
      }
    }
  },
	methods: {
		getInitial(){
      this.fields = {}
			this.$http.get('/kolom/preparation?table=mu_anggota&grouping=0&input=0')
        .then(result => {
          var res = result.data;
          delete res.password
          delete res.passwordconf
          this.fields = res
          this.saving = false
          // console.log(this.fields)
        });
		}
		
	},
	created() {
		this.dataId = this.authStore.loggedUser.id_anggota;
		this.getInitial()
  }
}
</script>

<style lang="sass" scoped>
.page
	width: 50%
.icon 
	font-size: 80px
	height: 80px
	width: 80px
	margin-bottom: 10px
.table-event-detail
	td 
		padding: 10px 10px
		height: 30px
		vertical-align: top
		&:last-child
			min-width: 30px

</style>