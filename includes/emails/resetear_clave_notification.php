<!DOCTYPE html>
	<html>
		<head>
			<meta charset="UTF-8">
			<meta name="description" content="Portal deportivo en República Dominicana" />
			<meta http-equiv="X-UA-Compatible" content="IE=edge" />
			<meta name="keywords" content="beisbol, baloncesto, noticias, fútbol" />
			<meta name="viewport" content="width=device-width" scale="1.0" />

			<title>Marcador.do | Confirmar cuenta</title>

			<!--<link rel="stylesheet" type="text/css" href="style2.css" />-->

			<!--[if (gte mso 9)|(IE)]>
   				 <style type="text/css">
       			 table {border-collapse: collapse;}
    			</style>
    		<![endif]-->

    		
		</head>

		<body style="background-color:#e0e0e0; padding:15px; margin:0px;max-width:700px; font-family:calibri">

			<center>
					<!-- tabla de contenido -->
					<!--[if (gte mso 9) | (IE)]>
					<table width="600" align="center">
						<tr>
						<td>
					<![endif]-->
						<table width="auto" style="background-color:#fff; border-spacing:0;" class="outer" align="center" border="0" cellpadding="10px">
							<!-- titulo y logo -->
							<tr style="background-color:#e80303">
								<td class="logo" cellpadding="5px" colspan="3">
									<img src="<?php echo get_templaste_directory_uri(); ?>assets/emails/assets/logo-marcador-blanco.png" width="250px" height="46px" alt="Marcador.do" border="0"/>
									<p style="color:#fff; margin:0px">No seguimos las tendencias, las marcamos.</p>
									<!--contenido aquí-->
								</td>
							</tr>

							<!--barra roja -->
							<tr style="background-color:#636363">
								<td style="color:#fff" colspan="3"><center>CONFIRMACIÓN DE REGISTRO</center></td>
							</tr>

							<!--mensaje @usuario one line -->
							<tr>
								<td colspan="3">
									<table align="left">
										<tr>
											<td colspan="3">
												<p style="margin:0px"> Hola <?php echo $username; ?><br />
												¡Bienvenido a <span style="color:#e80303">marcador.do!</span></p>
											</td>
										</tr>
									</table>
								</td>
							</tr>

							<tr>
								<td colspan="3">Desde ya disfruta de la mejor experiencia digital con nosotros y sobre todo, gracias por formar parte de nuestra gran familia.</td>
							</tr>
							
							
							

							<tr>
								<td colspan="3" align="center"  max-width="600px">
										<a target="_blank" href="<?php echo $link ?>"<button type="button" style="font-size:16px;padding:10px 30px; color:white;background-color:#e80303;border:0px;border-radius:9px;">Empezar</button></a>
										<p style="margin:0px; font-size:12px">¡Vamo'arriba!</p>
								</td>
							</tr>

							<tr>
								<td>
								<!--columna 1-->
									<table>
										<tr>
											<td style="font-size:12px">
												<a href="<?php echo get_permalink(get_page_by_title('Políticas de Privacidad')); ?>">Declaración de privacidad</a>
											</td>
										</tr>
									</table>
								</td>
							<td colspan="2">

								<!--columna 1-->
									<table align="right" cellpadding="3px">
										<tr>
											<td text-aling="right">
												<img src="<?php echo get_templaste_directory_uri();?>assets/emails/assets/emr_texto.png" width="173px" height="18px" alt="Siguenos en las redes sociales" border="0px"/>
											</td><td>
												<a target="_blank" href="https://www.facebook.com/Marcador.do/"><img src="<?php echo get_templaste_directory_uri(); ?>assets/emails/assets/emr_facebook.png" width="20" height="21" alt="facebook" border="0px"/></a>
											</td><td>
												<a target="_blank" href="https://twitter.com/marcador_do"><img src="<?php echo get_templaste_directory_uri(); ?>assets/emails/assets/emr_twitter.png" width="20" height="21" alt="twitter" border="0px"/></a>
											</td><td>
												<a target="_blank" href="https://www.instagram.com/marcador.do/"><img src="<?php echo get_templaste_directory_uri(); ?>assets/emails/assets/emr_instagram.png" width="20" height="21" alt="instagram" border="0px"/></a>
											</td>
										</tr>
									</table>
								</td>
							</tr>

							<tr>
								<td align="center" style="background-color:#e0e0e0" colspan="3">
									<span style="font-size:12px;">Has recibido este email porque está registrado en Marcador.do. Por favor, no contestes directamente a este email. Si tienes cualquier pregunta o sugerencia, por favor visita nuestra página de ayuda. Copyright (c) 2016 Marcador S.R.L. | Calle Rafael Augusto Sánchez #12</span>
								</td>
							</tr>
						</table>
						<!-- [if (gte mso 9) | (IE)]>
						</td>
						</tr>
						</table>
						<![endif]-->			
				</div>
			</center>
		</body>
	</html>