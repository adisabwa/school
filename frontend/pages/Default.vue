
<template>
	<div class="[--color-border:theme(colors.orange.400)]">
  	<psb/>
		<div class="translate-y-[-40px]">
			<div id="bottom" class="bg-cover bg-top bg-repeat
				h-[60px] min-w-[600px] w-full
				filter hue-rotate-[349deg] brightness-[.9]"
				:style="`background-image:url('${$baseUrl}assets/images/bottom.png')`"></div>
				<div class="relative h-auto w-full bg-[#f3b76f] pt-10 pb-20
					bg-scroll bg-repeat bg-top
					bg-[url('https://darularqampatean.com/wp-content/uploads/2021/10/bg-vectors.png')]">
					<div class="max-w-[80%] mx-auto
						text-3xl text-center font-bold text-emerald-800">Applikasi PPM Darul Arqam</div>
					<div class="max-w-[80%] mx-auto mt-10">
						<el-input v-model="keyword" placeholder="Cari Aplikasi" 
							class="w-full *:text-[18px]"
							:input-style="{
								padding:'20px 5px',
							}">
							<template #prefix>
								<icons icon="mdi:search"
									class="text-[25px] px-1" />
							</template>
						</el-input>
					</div>
					<div class="grid grid-cols-[repeat(auto-fit,_minmax(200px,_230px))] gap-[calc(30px)] justify-center
						max-w-[80%] mx-auto mt-10">
						<template v-for="(menu, ind) in Object.values(topMenu)?.filter?.(d => {
							let q = keyword?.toLowerCase()
							if (q) 
								return d.label.toLowerCase().includes(q)
							else
								return true
						})">
							<div class="grid-item group/menu
							[--duration:0.5s]">
								<div class="relative animate pointer duration-500 group/app
									hover:scale-90 " 
									@click="$router.push({name:menu.route})">
								<div class="relative h-[150px] bg-transparent
									rounded-full animate
									flex items-center justify-center">
									<div class="m-auto h-[150px] w-[150px] rounded-[10px] 
										bg-contain bg-no-repeat
										overflow-hidden
										flex items-center justify-center align-middle"
										:style="{maskImage:`url(${menu.image})`,
										maskSize:'contain',
										maskRepeat:'no-repeat',
										backgroundImage:`url(${menu.image})`}">
										<icons class="text-[#f3b76f] text-[100px]" :icon="menu.icon" />
										<div class="absolute w-[90px] h-[200%] rotate-[-25deg] top-[-50%]
											bg-gradient-to-r from-transparent via-white/[0.5] to-transparent
											animate-[fly-in-absolute_5s_infinite_ease-in-out] [--from-left:-70%] [--left:350%] "
											:style="{animationDelay: ind + 's !important'}"/>
									</div>
								</div>
								<div class="animate
									w-full p-2 rounded-[10px]
									bg-gradient-to-l from-orange-300 from-[50%] to-orange-500 to-[50%]
									bg-[length:200%_200%] bg-right-bottom 
									group-hover/menu:bg-left-top
									text-center mt-3 text-xl text-emerald-800
									group-hover/menu:text-white
									font-bold">{{ menu.label }}
								</div>
							</div>
						</div>
					</template>
				</div>
			</div>
			<div class="absolute bg-[#f3b76f] h-[100px] w-full" />
		</div>
	</div>
</template>

<script>
import Psb from '@/pages/psb/Start.vue'
import { topMenu } from '@/helpers/menus.js'

export default {
  name: "default",
  components: {
    Psb,
  },
  data: function() {
    return {
		topMenu:topMenu,
		keyword:'',
    };
  },
	
}
</script>