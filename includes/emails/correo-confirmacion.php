<!DOCTYPE html>
	<html>
		<head>
			<meta charset="UTF-8">
			<meta name="description" content="Portal deportivo en República Dominicana">
			<meta name="keywords" content="beisbol, baloncesto, noticias, fútbol">
			<meta name="viewport" content="width=device-width" scale="1.0">

			<title>Marcador.do | Confirmar cuenta</title>

			<link rel="stylesheet" type="text/css" href="<?php echo get_template_directory_uri(); ?>/assets/emails/style.css" />
		</head>

		<body>
			
				<div class="container">
					<div class="header">
						<div class="logo">
							<img src="<?php echo get_template_directory_uri(); ?>/assets/emails/assets/logo-marcador-blanco.png"/>
							<p>No seguimos las tendencias, las marcamos.</p>
						</div>

						<div class="header-titler">Confirmación de cuenta</div>

					</div>
					<div class="grupo1">
						<p class="texto">
							<span class="texto-user">Hola <?php echo $username; ?></span>
							<br />
							¡Confirmamos tu inscripción en <a style="font-weight:bold" target="_blank" href="<?php echo $link; ?>">marcador.do</a>!</p>

						<p>Desde ya disfruta de la mejor experiencia digital con nosotros y sobre todo, gracias por ser parte de nuestra gran familia.</p>

						<div class="btn_principal">
							<a href="<?php echo $link; ?>" class="button" target="_blank">Empezar</a>
							<p>¡Vamo'arriba!</p>
						</div>
					</div>


					<div class="container-link">
						<a href="#">Declaración de privacidad</a>
					</div>

					<div class="container-redes">
						<ul>
							<li class="texto_redes"><img src="<?php echo get_template_directory_uri(); ?>/assets/emails/assets/logo-marcador-rojo.png" /></li>
							<li><a target="_blank" href="https://www.facebook.com/Marcador.do/"><img src="<?php echo get_template_directory_uri(); ?>/assets/emails/assets/emr_facebook.png" /></a>
							<li><a target="_blank" href="https://twitter.com/marcador_do"><img src="<?php echo get_template_directory_uri(); ?>/assets/emails/assets/emr_twitter.png" /></a>
							<li><a target="_blank" href="https://www.instagram.com/marcador.do/"><img src="<?php echo get_template_directory_uri(); ?>/assets/emails/assets/emr_instagram.png" /></a>
						</ul>
				</div>
			</div>
			<p class="footer-text">Has recibido este email porque está registrado en Marcador.do.
			Por favor, no contestes directamente a este email.Si tienes cualquier pregunta o sugerencia, por favor visita nuestra página de ayuda.
			Copyright (c) 2016 Marcador S.R.L. | Calle Rafael Augusto Sánchez #12</p>

		</body>
	</html>