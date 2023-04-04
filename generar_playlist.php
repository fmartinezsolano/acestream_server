<?php
include('connect.php');

header("Content-type: application/octet");
header("Content-Disposition: attachment; filename=playlist_temp.m3u8");


if(isset($_GET['user']) && isset($_GET['password']))
{
    $user = $_GET['user'];
    $password = md5($_GET['password']);


    $sql = "INSERT INTO log(user, ip) VALUES (?, ?)";
    $sentencia = $link->prepare($sql);
    $sentencia->bind_param('ss', $user, $_SERVER['REMOTE_ADDR']); 
    $sentencia->execute();
    $sentencia->close();
    

    $sql = "SELECT puerto FROM usuarios WHERE user = ? AND password = ?";

    $sentencia = $link->prepare($sql);
    $sentencia->bind_param('ss', $user, $password); 
    $sentencia->execute();
    $sentencia->bind_result($puerto);

    if ($sentencia->fetch()) {
        $sentencia->close();


        $idCanalAnterior = '';
        $opcion = 1;

        echo '#EXTM3U';
        echo "\r\n";


        $sql = "SELECT c.idCanal, c.nombre, c.calidad, c.logo, s.url 
        FROM canales c 
        JOIN streams s ON c.idCanal=s.idCanal 
        ORDER BY c.idCanal";

        $sentencia = $link->prepare($sql);
        $sentencia->execute();
        $sentencia->bind_result($idCanal, $nombre, $calidad, $logo, $url);

        while ($sentencia->fetch()) {

            $raiz = '';

            if(!empty($calidad)){
                $nombre = $nombre . ' (' . $calidad . ')';
            }


            if($idCanalAnterior == $idCanal)
            {
                $opcion = $opcion + 1;

                $nombre = $nombre . ' (opción ' . $opcion . ')';
            }
            else
            {
                $opcion = 1;
            }
            
            $logo = $raiz . ":9091/acestream/img/" . $logo;
            $url = $raiz . ":".$puerto."/ace/getstream?id=" . $url . "&hlc=1&transcode_audio=0&transcode_mp3=0&transcode_ac3=0&preferred_audio_language=spa";

            
            echo '#EXTINF:-1 tvg-logo="'.$logo.'" tvg-name="'.$nombre.'" group-title="amateur",'.$nombre;
            echo "\r\n";
            echo $url; 
            echo "\r\n"; 
            
            
            $idCanalAnterior = $idCanal;
        }

        $sentencia->close();
    }
    else
    {
        $sentencia->close();
    }

    
}
