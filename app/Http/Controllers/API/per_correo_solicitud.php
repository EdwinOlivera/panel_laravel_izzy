<?PHP
/*
 * Genera el correo electronico para el siguiente autorizador de la solicitud.
 * @author      Dan Urquía
 * @date        2014-07-18 
 
session_start();
if(!isset($_SESSION['cod_usuario'])){
	header('Location: index.php');
}*/
/*CONEXION CON BASE DE DATOS*/
include_once("../../libs/db_classes/mysql_conn.php");
include_once("../../libs/db_classes/db_permisos.php");
include_once("../../libs/db_classes/db_rrhh.php");
include("../../libs/PHPMailer-master/class.phpmailer.php");
include("../../libs/PHPMailer-master/class.smtp.php");
include("../../libs/PHPMailer-master/PHPMailerAutoload.php");
/*INSTANCIAMIENTOS*/
$DB_RRHH    	 = new db_rrhh();
$DB_PERMISOS	 = new db_permisos();
$cod_solicitud   = $_POST['x1'];
$cod_autorizador = $_POST['x2'];
$PERMISOS  		 = $DB_PERMISOS->get_solicitud_por_codigo($cod_solicitud);
$FECHAS          = $DB_PERMISOS->get_fechas_por_ausencia($cod_solicitud);
$USUARIOS  		 = $DB_RRHH->get_perfil_usuario($cod_autorizador);
$cuerpo = '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
  <head >
    <meta name="viewport" content="width=device-width" style="margin: 0;padding: 0;font-family: &quot;Helvetica Neue&quot;, &quot;Helvetica&quot;, Helvetica, Arial, sans-serif;">
  </head>
			<body bgcolor="#FFFFFF" style="margin: 0;padding: 0;-webkit-font-smoothing: antialiased;-webkit-text-size-adjust: none;height: 100%;width: 100%!important;">			
			<!-- HEADER -->
			<table class="head-wrap" bgcolor="#999999" style="margin: 0;padding: 0;width: 100%;">
				<tr style="margin: 0;padding: 0;">
					<td style="margin: 0;padding: 0;"></td>
					<td class="header container" style="margin: 0 auto!important;padding: 0;display: block!important;max-width: 600px!important;clear: both!important;">						
							<div class="content" style="margin: 0 auto;padding: 15px;font-family: &quot;Helvetica Neue&quot;, &quot;Helvetica&quot;, Helvetica, Arial, sans-serif;max-width: 600px;display: block;">
							<table bgcolor="#999999" style="margin: 0;padding: 0;font-family: &quot;Helvetica Neue&quot;, &quot;Helvetica&quot;, Helvetica, Arial, sans-serif;width: 100%;">
								<tr style="margin: 0;padding: 0;font-family: &quot;Helvetica Neue&quot;, &quot;Helvetica&quot;, Helvetica, Arial, sans-serif;">
									<td style="margin: 0;padding: 0;font-family: &quot;Helvetica Neue&quot;, &quot;Helvetica&quot;, Helvetica, Arial, sans-serif;"><h6 class="collapse" style="margin: 0!important;padding: 0;font-family: &quot;HelveticaNeue-Light&quot;, &quot;Helvetica Neue Light&quot;, &quot;Helvetica Neue&quot;, Helvetica, Arial, &quot;Lucida Grande&quot;, sans-serif;line-height: 1.1;margin-bottom: 15px;color: #444;font-weight: 900;font-size: 14px;text-transform: uppercase;" align="center">DICyP</h6></td>
								</tr>
							</table>
							</div>
							
					</td>
					<td style="margin: 0;padding: 0;font-family: &quot;Helvetica Neue&quot;, &quot;Helvetica&quot;, Helvetica, Arial, sans-serif;"></td>
				</tr>
			</table><!-- /HEADER -->			
			<!-- BODY -->
			<table class="body-wrap" style="margin: 0;padding: 0;font-family: &quot;Helvetica Neue&quot;, &quot;Helvetica&quot;, Helvetica, Arial, sans-serif;width: 100%;">
				<tr style="margin: 0;padding: 0;font-family: &quot;Helvetica Neue&quot;, &quot;Helvetica&quot;, Helvetica, Arial, sans-serif;">
					<td style="margin: 0;padding: 0;font-family: &quot;Helvetica Neue&quot;, &quot;Helvetica&quot;, Helvetica, Arial, sans-serif;"></td>
					<td class="container" bgcolor="#FFFFFF" style="margin: 0 auto!important;padding: 0;font-family: &quot;Helvetica Neue&quot;, &quot;Helvetica&quot;, Helvetica, Arial, sans-serif;display: block!important;max-width: 600px!important;clear: both!important;">
			
						<div class="content" style="margin: 0 auto;padding: 15px;font-family: &quot;Helvetica Neue&quot;, &quot;Helvetica&quot;, Helvetica, Arial, sans-serif;max-width: 600px;display: block;">
						<table style="margin: 0;padding: 0;font-family: &quot;Helvetica Neue&quot;, &quot;Helvetica&quot;, Helvetica, Arial, sans-serif;width: 100%;">
							<tr style="margin: 0;padding: 0;font-family: &quot;Helvetica Neue&quot;, &quot;Helvetica&quot;, Helvetica, Arial, sans-serif;">
								<td style="margin: 0;padding: 0;font-family: &quot;Helvetica Neue&quot;, &quot;Helvetica&quot;, Helvetica, Arial, sans-serif;">
									<h3 style="margin: 0;padding: 0;font-family: &quot;HelveticaNeue-Light&quot;, &quot;Helvetica Neue Light&quot;, &quot;Helvetica Neue&quot;, Helvetica, Arial, &quot;Lucida Grande&quot;, sans-serif;line-height: 1.1;margin-bottom: 15px;color: #000;font-weight: 500;font-size: 27px;">Hola, '.$USUARIOS[0]['primer_nombre'].' '.$USUARIOS[0]['primer_apellido'].'</h3>
									<p class="lead" style="margin: 0;padding: 0;font-family: &quot;Helvetica Neue&quot;, &quot;Helvetica&quot;, Helvetica, Arial, sans-serif;margin-bottom: 10px;font-weight: normal;font-size: 17px;line-height: 1.6;">Solicitud de permiso <strong style="margin: 0;padding: 0;font-family: &quot;Helvetica Neue&quot;, &quot;Helvetica&quot;, Helvetica, Arial, sans-serif;">Nro. '.$PERMISOS[0]['cod_solicitud'].'</strong> por parte de <strong style="margin: 0;padding: 0;font-family: &quot;Helvetica Neue&quot;, &quot;Helvetica&quot;, Helvetica, Arial, sans-serif;">'.$PERMISOS[0]['nombre'].'</strong></p>				
															
									<!-- social & contact -->
									<table class="social" width="100%" style="margin: 0;padding: 0;background-color: #ebebeb;width: 100%;">
										<tr style="margin: 0;padding: 0;">
											<td style="margin: 0;padding: 0;">
												
												<!-- column 1 -->
												<table align="center" class="column" style="margin: 0;padding: 0;width: 280px;float: left;min-width: 279px;">';
												
            if($PERMISOS[0]['cod_tipo_permiso'] != 1){ 
										$cuerpo .= '<tr style="margin: 0;padding: 0;">
														<td style="margin: 0;padding: 15px;">															
															<h5 style="margin: 0;padding: 0;font-family: &quot;HelveticaNeue-Light&quot;, &quot;Helvetica Neue Light&quot;, &quot;Helvetica Neue&quot;, Helvetica, Arial, &quot;Lucida Grande&quot;, sans-serif;line-height: 1.1;margin-bottom: 15px;color: #000;font-weight: 900;font-size: 17px;">Horas de salida:</h5>
																<table>	
																	<tr> <td style="margin: 0;padding: 3px 7px; text-align: center; width:100%"> <div><p><a href="#" class="soc-btn tw" style="margin: 0;font-family: &quot;Helvetica Neue&quot;, &quot;Helvetica&quot;, Helvetica, Arial, sans-serif;color: #FFF;font-size: 12px;margin-bottom: 10px;text-decoration: none;font-weight: bold;display: block;text-align: center;background-color: #1daced;"><strong style="margin: 0;padding: 0;font-family: &quot;Helvetica Neue&quot;, &quot;Helvetica&quot;, Helvetica, Arial, sans-serif;">Fecha</strong> '.utf8_encode($PERMISOS[0]['fecha_permiso']).'</a> </p></div></td> </tr>
																	<tr> <td style="margin: 0;padding: 3px 7px; text-align: center; width:100%"> <div><p><a href="#" class="soc-btn tw" style="margin: 0;font-family: &quot;Helvetica Neue&quot;, &quot;Helvetica&quot;, Helvetica, Arial, sans-serif;color: #FFF;font-size: 12px;margin-bottom: 10px;text-decoration: none;font-weight: bold;display: block;text-align: center;background-color: #1daced;"><strong style="margin: 0;padding: 0;font-family: &quot;Helvetica Neue&quot;, &quot;Helvetica&quot;, Helvetica, Arial, sans-serif;">Salida</strong> '. utf8_encode($PERMISOS[0]['hora_salida']).'</a> </p></div></td> </tr>
																	<tr> <td style="margin: 0;padding: 3px 7px; text-align: center; width:100%"> <div><p><a href="#" class="soc-btn tw" style="margin: 0;font-family: &quot;Helvetica Neue&quot;, &quot;Helvetica&quot;, Helvetica, Arial, sans-serif;color: #FFF;font-size: 12px;margin-bottom: 10px;text-decoration: none;font-weight: bold;display: block;text-align: center;background-color: #1daced;"><strong style="margin: 0;padding: 0;font-family: &quot;Helvetica Neue&quot;, &quot;Helvetica&quot;, Helvetica, Arial, sans-serif;">Regreso</strong> '. utf8_encode($PERMISOS[0]['hora_regreso']).'</a> </p></div></td> </tr>';
                          
            } else {
            $cuerpo .= '<tr style="margin: 0;padding: 0;">
														<td style="margin: 0;padding: 15px;">															
															<h5 style="margin: 0;padding: 0;font-family: &quot;HelveticaNeue-Light&quot;, &quot;Helvetica Neue Light&quot;, &quot;Helvetica Neue&quot;, Helvetica, Arial, &quot;Lucida Grande&quot;, sans-serif;line-height: 1.1;margin-bottom: 15px;color: #000;font-weight: 900;font-size: 17px;">Fechas a tomar:</h5>
																<table>';	
          
							
							if(count($FECHAS) > 0){
								$dias = 0;
								foreach($FECHAS as $FECHA){
									if ($FECHA['cod_tipo_jornada'] == 1){
										$dias += 1;
									} else {
										$dias += 0.5;
									}
									$cuerpo .= '<tr> <td style="margin: 0;padding: 3px 7px; text-align: center; width:100%"> <div><p><a href="#" class="soc-btn tw" style="margin: 0;font-family: &quot;Helvetica Neue&quot;, &quot;Helvetica&quot;, Helvetica, Arial, sans-serif;color: #FFF;font-size: 12px;margin-bottom: 10px;text-decoration: none;font-weight: bold;display: block;text-align: center;background-color: #1daced;"><strong style="margin: 0;padding: 0;font-family: &quot;Helvetica Neue&quot;, &quot;Helvetica&quot;, Helvetica, Arial, sans-serif;">'.utf8_encode($FECHA['fecha']).'</strong> | '.utf8_encode($FECHA['tipo_jornada']).'</a> </p></div></td> </tr>';
								}
							} else { //Si no hay registros informará al usuario
								$cuerpo .= '<tr> <td style="margin: 0;padding: 3px 7px; text-align: center; width:100%"> <div><p><a href="#" class="soc-btn tw" style="margin: 0;font-family: &quot;Helvetica Neue&quot;, &quot;Helvetica&quot;, Helvetica, Arial, sans-serif;color: #FFF;font-size: 12px;margin-bottom: 10px;text-decoration: none;font-weight: bold;display: block;text-align: center;background-color: #1daced;"><strong style="margin: 0;padding: 0;font-family: &quot;Helvetica Neue&quot;, &quot;Helvetica&quot;, Helvetica, Arial, sans-serif;">No hay fechas disponibles</strong></a> </p></div></td> </tr>';
							}
			} 
									$cuerpo .= '							</table>
														</td>
													</tr>
												</table>
												<!-- column 2 -->
												<table align="left" class="column" style="margin: 0;padding: 0;font-family: &quot;Helvetica Neue&quot;, &quot;Helvetica&quot;, Helvetica, Arial, sans-serif;width: 280px;float: left;min-width: 279px;">
													<tr style="margin: 0;padding: 0;font-family: &quot;Helvetica Neue&quot;, &quot;Helvetica&quot;, Helvetica, Arial, sans-serif;">
														<td style="margin: 0;padding: 15px;font-family: &quot;Helvetica Neue&quot;, &quot;Helvetica&quot;, Helvetica, Arial, sans-serif;">				
																						
															<h5 class="" style="margin: 0;padding: 0;font-family: &quot;HelveticaNeue-Light&quot;, &quot;Helvetica Neue Light&quot;, &quot;Helvetica Neue&quot;, Helvetica, Arial, &quot;Lucida Grande&quot;, sans-serif;line-height: 1.1;margin-bottom: 15px;color: #000;font-weight: 900;font-size: 17px;">Informaci&oacute;n:</h5>		 										
															<p style="margin: 0;padding: 0;font-family: &quot;Helvetica Neue&quot;, &quot;Helvetica&quot;, Helvetica, Arial, sans-serif;margin-bottom: 10px;font-weight: normal;font-size: 14px;line-height: 1.6;">Motivo: <strong style="margin: 0;padding: 0;font-family: &quot;Helvetica Neue&quot;, &quot;Helvetica&quot;, Helvetica, Arial, sans-serif;">'.$PERMISOS[0]['tipo_motivo'].'</strong></br> 
															   Origen: <strong style="margin: 0;padding: 0;font-family: &quot;Helvetica Neue&quot;, &quot;Helvetica&quot;, Helvetica, Arial, sans-serif;">'.$PERMISOS[0]['origen_motivo'].'</strong> </br> 
															   Descripci&oacute;n:
															<strong>'.$PERMISOS[0]['motivo'].'</strong></p>
							
														</td>
													</tr>
												</table><!-- /column 2 -->
												
												<span class="clear" style="margin: 0;padding: 0;font-family: &quot;Helvetica Neue&quot;, &quot;Helvetica&quot;, Helvetica, Arial, sans-serif;display: block;clear: both;"></span>	
												
											</td>
										</tr>
									</table><!-- /social & contact -->
									
								</td>
							</tr>
						</table>
						</div><!-- /content -->
												
					</td>
					<td style="margin: 0;padding: 0;font-family: &quot;Helvetica Neue&quot;, &quot;Helvetica&quot;, Helvetica, Arial, sans-serif;">
					</td>
				</tr>
				<tr style="margin: 0;padding: 0;font-family: &quot;Helvetica Neue&quot;, &quot;Helvetica&quot;, Helvetica, Arial, sans-serif;">
					<td colspan="2" align="center" style="margin: 0;padding: 0;font-family: &quot;Helvetica Neue&quot;, &quot;Helvetica&quot;, Helvetica, Arial, sans-serif;">
					<!-- Callout Panel -->
					<p class="callout" style="margin: 0;padding: 15px;font-family: &quot;Helvetica Neue&quot;, &quot;Helvetica&quot;, Helvetica, Arial, sans-serif;margin-bottom: 15px;font-weight: normal;font-size: 14px;line-height: 1.6;background-color: #ECF8FF;">
					Puede ingresar al Sistema desde aqu&iacute;: <a href="http://app.dicyp.unah.edu.hn" style="margin: 0;padding: 0;font-family: &quot;Helvetica Neue&quot;, &quot;Helvetica&quot;, Helvetica, Arial, sans-serif;color: #2BA6CB;font-weight: bold;">Sistema Web &raquo;</a>
					</p><!-- /Callout Panel -->	        
					</td>
				</tr>
			</table><!-- /BODY -->
			
			<!-- FOOTER -->
			<table class="footer-wrap" style="margin: 0;padding: 0;font-family: &quot;Helvetica Neue&quot;, &quot;Helvetica&quot;, Helvetica, Arial, sans-serif;width: 100%;clear: both!important;">
				<tr style="margin: 0;padding: 0;font-family: &quot;Helvetica Neue&quot;, &quot;Helvetica&quot;, Helvetica, Arial, sans-serif;">
					<td style="margin: 0;padding: 0;font-family: &quot;Helvetica Neue&quot;, &quot;Helvetica&quot;, Helvetica, Arial, sans-serif;"></td>
					<td class="container" style="margin: 0 auto!important;padding: 0;font-family: &quot;Helvetica Neue&quot;, &quot;Helvetica&quot;, Helvetica, Arial, sans-serif;display: block!important;max-width: 600px!important;clear: both!important;">
						
							<!-- content -->
							<div class="content" style="margin: 0 auto;padding: 15px;font-family: &quot;Helvetica Neue&quot;, &quot;Helvetica&quot;, Helvetica, Arial, sans-serif;max-width: 600px;display: block;">
							<table style="margin: 0;padding: 0;font-family: &quot;Helvetica Neue&quot;, &quot;Helvetica&quot;, Helvetica, Arial, sans-serif;width: 100%;">
							<tr style="margin: 0;padding: 0;font-family: &quot;Helvetica Neue&quot;, &quot;Helvetica&quot;, Helvetica, Arial, sans-serif;">
								<td align="center" style="margin: 0;padding: 0;font-family: &quot;Helvetica Neue&quot;, &quot;Helvetica&quot;, Helvetica, Arial, sans-serif;">
									<p style="margin: 0;padding: 0;font-family: &quot;Helvetica Neue&quot;, &quot;Helvetica&quot;, Helvetica, Arial, sans-serif;margin-bottom: 10px;font-weight: normal;font-size: 14px;line-height: 1.6;">
										<a href="https://www.unah.edu.hn/" style="margin: 0;padding: 0;font-family: &quot;Helvetica Neue&quot;, &quot;Helvetica&quot;, Helvetica, Arial, sans-serif;color: #2BA6CB;">UNAH</a> |
										<a href="http://dicyp.unah.edu.hn" style="margin: 0;padding: 0;font-family: &quot;Helvetica Neue&quot;, &quot;Helvetica&quot;, Helvetica, Arial, sans-serif;color: #2BA6CB;">DICyP</a>
									</p>
								</td>
							</tr>
						</table>
							</div><!-- /content -->
							
					</td>
					<td style="margin: 0;padding: 0;font-family: &quot;Helvetica Neue&quot;, &quot;Helvetica&quot;, Helvetica, Arial, sans-serif;"></td>
				</tr>
			</table><!-- /FOOTER -->
			
			</body>
<style style="margin: 0;padding: 0;font-family: &quot;Helvetica Neue&quot;, &quot;Helvetica&quot;, Helvetica, Arial, sans-serif;">
			/* ------------------------------------- GLOBAL ------------------------------------- */* { margin:0;padding:0;}* { font-family: "Helvetica Neue", "Helvetica", Helvetica, Arial, sans-serif; }img { max-width: 100%; }.collapse {margin:0;padding:0;}body {-webkit-font-smoothing:antialiased; -webkit-text-size-adjust:none; width: 100%!important; height: 100%;}/* ------------------------------------- ELEMENTS ------------------------------------- */a { color: #2BA6CB;}.btn {text-decoration:none;color: #FFF;background-color: #666;padding:10px 16px;font-weight:bold;margin-right:10px;text-align:center;cursor:pointer;display: inline-block;}p.callout {padding:15px;background-color:#ECF8FF;margin-bottom: 15px;}.callout a {font-weight:bold;color: #2BA6CB;}table.social {/* padding:15px; */background-color: #ebebeb;}.social .soc-btn {padding: 3px 7px;font-size:12px;margin-bottom:10px;text-decoration:none;color: #FFF;font-weight:bold;display:block;text-align:center;}a.fb { background-color: #3B5998!important; }a.tw { background-color: #1daced!important; }a.gp { background-color: #DB4A39!important; }a.ms { background-color: #000!important; }.sidebar .soc-btn { display:block;width:100%;}/* ------------------------------------- HEADER ------------------------------------- */table.head-wrap { width: 100%;}.header.container table td.logo { padding: 15px; }.header.container table td.label { padding: 15px; padding-left:0px;}/* ------------------------------------- BODY ------------------------------------- */table.body-wrap { width: 100%;}/* ------------------------------------- FOOTER ------------------------------------- */table.footer-wrap { width: 100%;clear:both!important;}.footer-wrap .container td.content p { border-top: 1px solid rgb(215,215,215); padding-top:15px;}.footer-wrap .container td.content p {font-size:10px;font-weight: bold;}/* ------------------------------------- TYPOGRAPHY ------------------------------------- */h1,h2,h3,h4,h5,h6 {font-family: "HelveticaNeue-Light", "Helvetica Neue Light", "Helvetica Neue", Helvetica, Arial, "Lucida Grande", sans-serif; line-height: 1.1; margin-bottom:15px; color:#000;}h1 small, h2 small, h3 small, h4 small, h5 small, h6 small { font-size: 60%; color: #6f6f6f; line-height: 0; text-transform: none; }h1 { font-weight:200; font-size: 44px;}h2 { font-weight:200; font-size: 37px;}h3 { font-weight:500; font-size: 27px;}h4 { font-weight:500; font-size: 23px;}h5 { font-weight:900; font-size: 17px;}h6 { font-weight:900; font-size: 14px; text-transform: uppercase; color:#444;}.collapse { margin:0!important;}p, ul { margin-bottom: 10px; font-weight: normal; font-size:14px; line-height:1.6;}p.lead { font-size:17px; }p.last { margin-bottom:0px;}ul li {margin-left:5px;list-style-position: inside;}/* ------------------------------------- SIDEBAR ------------------------------------- */ul.sidebar {background:#ebebeb;display:block;list-style-type: none;}ul.sidebar li { display: block; margin:0;}ul.sidebar li a {text-decoration:none;color: #666;padding:10px 16px;/* font-weight:bold; */margin-right:10px;/* text-align:center; */cursor:pointer;border-bottom: 1px solid #777777;border-top: 1px solid #FFFFFF;display:block;margin:0;}ul.sidebar li a.last { border-bottom-width:0px;}ul.sidebar li a h1,ul.sidebar li a h2,ul.sidebar li a h3,ul.sidebar li a h4,ul.sidebar li a h5,ul.sidebar li a h6,ul.sidebar li a p { margin-bottom:0!important;}/* --------------------------------------------------- RESPONSIVENESSNuke it from orbit. Its the only way to be sure. ------------------------------------------------------ *//* Set a max-width, and make it display as block so it will automatically stretch to that width, but will also shrink down on a phone or something */.container {display:block!important;max-width:600px!important;margin:0 auto!important; /* makes it centered */clear:both!important;}/* This should also be a block element, so that it will fill 100% of the .container */.content {padding:15px;max-width:600px;margin:0 auto;display:block; }/* Lets make sure tables in the content area are 100% wide */.content table { width: 100%; }/* Odds and ends */.column {width: 300px;float:left;}.column tr td { padding: 15px; }.column-wrap { padding:0!important; margin:0 auto; max-width:600px!important;}.column table { width:100%;}.social .column {width: 280px;min-width: 279px;float:left;}/* Be sure to place a .clear element after each set of columns, just to be safe */.clear { display: block; clear: both; }/* ------------------------------------------- PHONEFor clients that support media queries.Nothing fancy. -------------------------------------------- */@media only screen and (max-width: 600px) {a[class="btn"] { display:block!important; margin-bottom:10px!important; background-image:none!important; margin-right:0!important;}div[class="column"] { width: auto!important; float:none!important;}table.social div[class="column"] {width:auto!important;}}
			</style>			
			</html>';
												
/*
 * ENVIO DE CORREO AL AUTORIZADOR
 */
$mail = new PHPMailer();
$mail->CharSet  = 'UTF-8';
$mail->Encoding = 'quoted-printable';
$mail->isSMTP();                                      // Set mailer to use SMTP
$mail->SMTPAuth = true;                               // Enable SMTP authentication
$mail->SMTPSecure = 'ssl'; 
$mail->Host = 'smtp.gmail.com';  // Specify main and backup SMTP servers
$mail->Port = 465;  
$mail->Username = 'cientificayposgrado.unah.inpos@gmail.com';                 // SMTP username
$mail->Password = 'INPOS.C0rre0.UNAH';                           // SMTP password
                         // Enable encryption, 'ssl' also accepted

$mail->From = 'cientificayposgrado.unah.inpos@gmail.com';
$mail->FromName = 'Sistema Web DICyP';
$mail->addAddress($USUARIOS[0]['email'],$USUARIOS[0]['primer_nombre'].' '.$USUARIOS[0]['primer_apellido']);     // Add a recipient

$mail->isHTML(true);                                  // Set email format to HTML

$mail->Subject = 'Solicitud de permiso Nro. '.$PERMISOS[0]['cod_solicitud'].' de parte de '.utf8_encode($PERMISOS[0]['nombre']);
$mail->Body    = $cuerpo;
$mail->AltBody = 'Este correo no puede verse en su Gestor de Correos, favor ingresar al Sistema Web a realizar las acciones.';


if(!$mail->send()) {
    echo '1|No pudo enviarse el correo electrónico. ';
    echo 'Mailer Error: ' . $mail->ErrorInfo;
} else {
    echo '0|Mensaje ha sido enviado a '.utf8_encode($USUARIOS[0]['primer_nombre'].' '.$USUARIOS[0]['primer_apellido']);
}
?>
