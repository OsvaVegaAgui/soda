<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Restablecer contraseña</title>

  <style>
    body {
      margin: 0;
      padding: 0;
      background: #f3f7f1;
      font-family: "Segoe UI", Roboto, Helvetica, Arial, sans-serif;
      color: #3e4a3c;
    }

    /* Fondo con textura natural */
    .bg-pattern {
      background-color: #fff;
      background-size: cover;
      background-repeat: no-repeat;
      padding: 40px 0;
    }

    .container {
      max-width: 650px;
      margin: auto;
      background: #ffffff;
      border-radius: 14px;
      overflow: hidden;
      box-shadow: 0 12px 35px rgba(0,0,0,0.08);
      border: 1px solid #dce6d5;
    }

    /* Header verde–tierra */
    .header {
      background: linear-gradient(135deg, #2d6a4f, #40916c);
      text-align: center;
      padding: 35px 20px;
      color: #fff;
    }

    .header img {
      width: 80px;
      margin-bottom: 10px;
      border-radius: 14px;
      background: rgba(255,255,255,0.20);
      padding: 8px;
    }

    .header h1 {
      margin: 0;
      font-size: 24px;
      font-weight: 700;
    }

    /* Contenido */
    .content {
      padding: 35px 30px;
      text-align: center;
    }

    .content p {
      font-size: 16px;
      line-height: 1.7;
      color: #4d5a4b;
      margin-bottom: 18px;
    }

    /* Caja aviso */
    .alert-box {
      background: #f1fbf0;
      border-left: 4px solid #52b788;
      padding: 16px 20px;
      border-radius: 10px;
      text-align: left;
      color: #24543b;
      margin-bottom: 26px;
      font-size: 15px;
      box-shadow: 0 5px 12px rgba(76, 119, 90, 0.15);
    }

    /* Botón estilo ganadería/naturaleza */
    .button {
      display: inline-block;
      background: linear-gradient(135deg, #2d6a4f, #52b788);
      color: #fff !important;
      padding: 14px 40px;
      border-radius: 40px;
      font-weight: 600;
      font-size: 15px;
      text-decoration: none;
      box-shadow: 0 6px 16px rgba(52, 101, 72, 0.25);
      transition: opacity 0.2s;
    }

    .button:hover {
      opacity: 0.92;
    }

    /* Footer */
    .footer {
      background: #f7faf6;
      padding: 16px;
      font-size: 13px;
      color: #6f7b6a;
      text-align: center;
      border-top: 1px solid #dce6d5;
    }
  </style>

</head>
<body>

  <div class="bg-pattern">
    <div class="container">

      <!-- HEADER -->
      <div class="header">
        <h2>Soda IACSA</h2>
        <h1>Restablecimiento de contraseña</h1>
      </div>

      <!-- CUERPO -->
      <div class="content">

        <p>Hola 👋,</p>

        <p>
          Hemos recibido una solicitud para restablecer tu contraseña.<br>
          Si no realizaste esta acción, podés ignorar este mensaje.
        </p>

        <div class="alert-box">
          🕒 <strong>Importante:</strong><br>
          El enlace de recuperación tiene una duración de <strong>30 minutos</strong>.
          Una vez cumplido ese tiempo deberás solicitar uno nuevo.
        </div>

        <a href="{{ $url }}" class="button">Restablecer contraseña</a>

        <p style="margin-top:28px; font-size: 14px;">
          Si el botón no funciona, copia y pega este enlace en tu navegador:
        </p>

        <p style="word-break: break-all; font-size: 14px; color:#2d6a4f;">
          {{ $url }}
        </p>

      </div>

      <!-- FOOTER -->
      <div class="footer">
        © {{ date('Y') }} Soda IACSA · Sistema de Recuperación  
      </div>

    </div>
  </div>

</body>
</html>
