<template>
	<div >
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
					<div class="grid grid-cols-[repeat(auto-fit,_minmax(200px,_230px))] gap-[calc(30px)] justify-center
						max-w-[80%] mx-auto mt-10">
						<template v-for="(menu, ind) in Object.values(topMenu)">
							<div class="grid-item">
								<div class="relative animate pointer duration-500 group/app
									hover:scale-90 " 
									@click="$router.push({name:menu.route})">
                  <div class="h-[200px] w-[200px] bg-transparent
                    rounded-full animate
                    bg-[conic-gradient(theme(colors.orange.400)_0%,_#ddd_0%)]
                    hover:bg-[conic-gradient(theme(colors.orange.400)_75%,_#ddd_0%)]
                    [mask-image:circle]
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
									<div class="text-center mt-3 text-xl text-emerald-800
										font-bold">{{ menu.label }}</div>
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
    };
  },
	
}
</script>