<?php
session_start();
require dirname(__DIR__, 2) . "/vendor/autoload.php";

use Portal\Users;

$perfil = -1;
$error=false;

if (isset($_SESSION['user'])) {
    //ERROR ERROR ERROR ERROR ERROR
    //cambiar la llamada al metodo hay que tratarlo de forma distinta, comprobar con el de paco 
    $perfil = (new Users)->setUsername($_SESSION['user'])->getPerfil();

}
if ($perfil == 0) {
    //header("Location:index.php");
}

if(isset($_POST['registrar'])){
    $username=trim($_POST['username']);
    $email=trim($_POST['email']);
    $password=trim($_POST['password']);
    $img=$_POST['img'];
    $perfil=$_POST['perfil'];

    //comprobamos que los campos no estén vacíos
    if($username==""){
        $error=true;
        $_SESSION['username']="no puedes introdcir el campo username vacio";
    }
    if ($email=""){
        $error=true;
        $_SESSION['email']="no puedes introducir el campo email vacio";
    }
    if ($password=""){
        $error=true;
        $_SESSION['password']="no puedes introducir el campo password vacio";
    }

    //comprobamos que no hay errores al usar valores ya existentes en la base de datos
    $userprev=(new Users)->readAll()->username;
    $emailprev=(new Users)->readAll()->email;
    
    if(in_array($username,$userprev)){
        $error=true;
        $_SESSION['username']='El username elegido ya existe en la base de datos';
    }
    if(in_array($email,$emailprev)){
        $error=true;
        $_SESSION['email']="El email elegido ya existe en la base de datos";
    }

    //comprobamos el campo img
    //falta hacer la funcion para comprobar los tipos mime
    //es lo de is imagen

    if (!isset($_FILES['img']['tmp_name'])){
        //no se ha elegido imagen asi que le asignamos una dependiendo de si se ha creado admin o user
        //cada uno tiene una por defecto

        if ($perfil==1){
            $img=(new Users)->setImg("../img/users/admin.jpg");

        }elseif($perfil==0){
            $img=(new Users)->setImg("../img/users/users.png");
        }

    }

    //vemos si ha habido errores
    if ($error==false){
        //no hay errores, guardamos los datos
        (new Users)->setUsername($username)
        //la pass hay que codificarla pero no recuerdo como
        ->setPassword($password)
        ->setEmail($email)
        ->setPerfil($perfil)
        //hay que retocar el setter de img de la clase usuario para que se guarde ennuestra carpeta de imagenes
        ->setImg($img)
        ->create();
        $_SESSION['correcto']="Usuario guardado con exito";

    }else{
        //hay errores, pintamos la pagina y los mostramos
    }


}else{

?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!-- BootStrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-1BmE4kWBq78iYhFldvKuhfTAU6auU8tT94WrHftjDbrCEXSU1oBoqyl2QvZ6jIW3" crossorigin="anonymous">
    <!-- FONTAWESOME -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta2/css/all.min.css" integrity="sha512-YWzhKL2whUzgiheMoBFwW8CKV4qpHQAEuvilg9FAn5VJUDwKZZxkJNuGM4XkWuk94WCrrwslk8yWNGmY1EduTA==" crossorigin="anonymous" referrerpolicy="no-referrer" />

    <title>Registrar Usuario</title>





</head>

<body style="background-color:silver">
    <h5 class="text-center mt-4">Registrar Usuario</h5>
    <div class="container mt-2">
<?php
        if(isset($_SESSION['correcto'])){
            echo <<<TXT
            <div class="alert alert-success" role="alert" value='{$_SESSION["correcto"]}'>
            
            </div>
        TXT;
        }

?>


        <div class="bg-success p-4 text-white rounded shadow-lg m-auto" style="width:35rem">
            <form name="cautor" action="<?php echo $_SERVER['PHP_SELF'] ?>" method='POST' enctype="multipart/form-data">

                <div class="mb-3">
                    <label for="n" class="form-label">Nombre Usuario</label>
                    <input type="text" class="form-control" id="n" placeholder="Username" name="username" required>
                <?php
                if (isset($_SESSION['username'])){
                    echo <<<TXT
            <div class="alert alert-danger" role="alert" value='{$_SESSION["username"]}'>
            
            </div>
        TXT;
                }

                ?>
                
                </div>
                <div class="mb-3">
                    <label for="a" class="form-label">Email</label>
                    <input type="email" class="form-control" id="a" placeholder="Correo" name="email" required>
                
                    <?php
                if (isset($_SESSION['email'])){
                    echo <<<TXT
            <div class="alert alert-danger" role="alert" value='{$_SESSION["email"]}'>
            
            </div>
        TXT;
                }

                ?>
                
                </div>

                

                <div class="mb-3">
                    <label for="p" class="form-label">Password</label>
                    <input type="password" class="form-control" id="p" placeholder="Contraseña" name="password" required>
                
                    <?php
                if (isset($_SESSION['password'])){
                    echo <<<TXT
            <div class="alert alert-danger" role="alert" value='{$_SESSION["password"]}'>
            
            </div>
        TXT;
                }

                ?>
                
                </div>

                <div class="mb-3">
                    <label for="f" class="form-label">Imagen</label>
                    <input class="form-control" type="file" id="f" name="img">
                    <?php
                if (isset($_SESSION['img'])){
                    echo <<<TXT
            <div class="alert alert-danger" role="alert" value='{$_SESSION["img"]}'>
            
            </div>
        TXT;
                }

                ?>

                </div>

                <?php

                if ($perfil == 1) {
                    //NOTA
                    //EL echo <<<TXT y el codigo html debe estar al mismo nivel tal y como esta ahora,
                    //el cierre debe estar al mismo nivel o más atrás,no después sino da fallo
                    echo <<< TXT
                    <div class="mb-3">
                    <label for="p" class="form-label">Perfil</label>
                    <select class="form-control" name="perfil">
                    <option value="1">Administrador</option>
                    <option value="0" selected>Usuario</option>
                TXT;
                }
                ?>

                <div>
                    <button type='submit' name="registrar" class="btn btn-info"><i class="fas fa-save"></i> Registrar</button>
                    <button type="reset" class="btn btn-warning"><i class="fas fa-broom"></i> Limpiar</button>
                </div>

            </form>
        </div>

    </div>
</body>

</html><?php } ?>