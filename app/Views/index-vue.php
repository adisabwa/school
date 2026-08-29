<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="utf-8">
  	<meta http-equiv="X-UA-Compatible" content="IE=edge">
  	<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">
	<meta name="author" content="Adi Sabwa">
	<meta name="website" content="codev-app.my.id">
	<!-- Site title -->
	<title>Sistem Informasi Sekolah</title>
	<link rel="icon" href="<?= base_url('assets/images/favicon.ico') ?>">
	<?php if (env('CI_ENVIRONMENT') === 'production'): ?>
		<link rel="manifest" href="<?= base_url() ?>assets/vue/manifest.webmanifest">
	<?php endif; ?>
	<script async src="https://docs.opencv.org/4.x/opencv.js"></script>
	<style>
		.loader-wrapper {
			position: fixed;
			top: 0;
			left: 0;
			width: 100%;
			height: 100%;
			display: flex;
			align-items: center;
			justify-content: center;
			background-color: #fff;
			z-index: -1;
		}
		.loader {
			--d:45px;
			width: 4px;
			height: 4px;
			border-radius: 50%;
			color: #5bb6e3;
			box-shadow: 
				calc(1*var(--d))      calc(0*var(--d))     0 0,
				calc(0.707*var(--d))  calc(0.707*var(--d)) 0 1px,
				calc(0*var(--d))      calc(1*var(--d))     0 2px,
				calc(-0.707*var(--d)) calc(0.707*var(--d)) 0 3px,
				calc(-1*var(--d))     calc(0*var(--d))     0 4px,
				calc(-0.707*var(--d)) calc(-0.707*var(--d))0 5px,
				calc(0*var(--d))      calc(-1*var(--d))    0 6px;
			animation: l27 1s infinite steps(8);
		}
		@keyframes l27 {
			100% {transform: rotate(1turn)}
		}
	</style>
</head>
<body>
	<div class="loader-wrapper">
		<div class="loader">
		</div>
			<img src="<?= base_url('assets/images/logo-kecil.png') ?>" alt="Logo" width="50"
				style="position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%);">
	</div>

	<div id="app">
		
	</div>
	
	<div id="navbar-app">
		
	</div>	
	
    <!-- <?= vite('new-frontend/'.$app.'/main.js') ?> -->
    <!-- <?= vite('new-frontend/navbar/main.js') ?> -->
    <?= vite('frontend/main.js') ?>
</body>
</html>