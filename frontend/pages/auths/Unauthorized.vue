<template>
	<div class="h-full flex items-center align-middle justify-center">
		<div class="text-center mt-20 mx-10 p-6 bg-white rounded-lg shadow-lg px-[10vw] py-[5vh]">
			<icons icon="solar:danger-bold" class="text-yellow-500 text-[100px] mb-4" />
			<div class="text-3xl font-bold mb-2">Unauthorized Access</div>
			<div class="text-gray-700 mb-7">You do not have permission to view this page.</div>
			<el-button @click="$router.go(-1)" size="large" type="primary">
				<icons icon="mdi:arrow-left"/>Go Back
			</el-button>
		</div>
	</div>
</template>