<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Recuperar Contraseña</title>
  <style>
    body {
      margin: 0;
      padding: 0;
      background: #f5f7fa;
      font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
      display: flex;
      justify-content: center;
      align-items: center;
      min-height: 100vh;
    }

    .container {
      background: rgb(57, 42, 119);
      padding: 40px 30px;
      box-shadow: 0 0 20px rgba(0, 0, 0, 0.2);
      border-radius: 12px;
      width: 350px;
      text-align: center;
      color: #fff;
    }

    .container h1 {
      margin-bottom: 10px;
      color: #fff;
    }

    .container img {
      width: 120px;
      margin: 15px 0;
    }

    .container h4 {
      margin-bottom: 25px;
      color: #d78c49;
      font-weight: normal;
    }

    .container input {
      width: 100%;
      padding: 12px;
      margin: 10px 0;
      border: none;
      border-radius: 6px;
      font-size: 1rem;
      background: #fff;
      color: #333;
    }

    .container input::placeholder {
      color: #999;
    }

    .container button {
      width: 100%;
      padding: 12px;
      background-color: #d78c49;
      color: white;
      border: none;
      border-radius: 6px;
      font-size: 1rem;
      cursor: pointer;
      transition: background-color 0.3s;
    }

    .container button:hover {
      background-color: #c47b3e;
    }

    .container a {
      margin-top: 15px;
      display: block;
      font-size: 0.9rem;
      color: #d78c49;
      text-decoration: none;
    }

    .container a:hover {
      text-decoration: underline;
    }
  </style>
</head>
<body>
  <input type="hidden" id="data" value="<?php echo $_GET['data'] ?>">
  <input type="hidden" id="data2" value="<?php echo $_GET['data2'] ?>">

  <div class="container">
    <h1>Restablecer Contraseña</h1>
    <img src="https://png.pngtree.com/png-clipart/20220509/original/pngtree-beauty-logo-png-image_7690272.png" alt="Logo">
    <h4>SCOSMETIC GYANE</h4>
    <form id="frm_reset_password">
      <input type="password" name="password" placeholder="Nueva contraseña" required>
      <input type="password" name="confirm_password" placeholder="Confirmar contraseña" required>
      <button type="submit">Actualizar</button>
    </form>
  </div>

  <script>
    const base_url = '<?php echo BASE_URL; ?>';
    const base_url_server = '<?php echo BASE_URL_SERVER; ?>';
  </script>
  <script src="<?php echo BASE_URL; ?>src/view/js/principal.js"></script>
  <script src="<?php echo BASE_URL ?>src/view/pp/plugins/sweetalert2/sweetalert2.min.js"></script>
</body>
</html>
