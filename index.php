<?php 


function compress_image($target, $newcopy, $w, $h, $ext) {
    list($w_orig, $h_orig) = getimagesize($target);
    $scale_ratio = $w_orig / $h_orig;

    // Check and change width and height vals
    if($w_orig<$w){

      $w = $w_orig;

    }
    else if ($h_orig<$h){
      $h= $h_orig;

    }
    if (($w / $h) > $scale_ratio) {
           $w = $h * $scale_ratio;
    } else {
           $h = $w / $scale_ratio;
    }
    $img = "";
    $ext = strtolower($ext);
    // Different conditions for differenet image formats
    if ($ext == "gif"){ 
      $img = imagecreatefromgif($target);
    } else if($ext =="png"){ 

      $img = imagecreatefrompng($target);
  
   
    }
    else if($ext =="webp"){

        $img = imagecreatefromwebp($target);
    }
     else{ 
      $img = imagecreatefromjpeg($target);
    }
    $tci = imagecreatetruecolor($w, $h);
    // imagecopyresampled(dst_img, src_img, dst_x, dst_y, src_x, src_y, dst_w, dst_h, src_w, src_h)
    imagecopyresampled($tci, $img, 0, 0, 0, 0, $w, $h, $w_orig, $h_orig);
    if($w_orig>4800){
    imagejpeg($tci, $newcopy, 90);
    }
    else if($w_orig>3000 && $w_orig<=4800){
     imagejpeg($tci, $newcopy, 95); 
    }
     else if($w_orig>2000 && $w_orig<=3000){
     imagejpeg($tci, $newcopy, 95); 
    }
    else {
     imagejpeg($tci, $newcopy, 95);   
    }
}

// Generate webp 

function generate_webp($file, $compression_quality = 80)
{



    // check if file exists
    if (!file_exists($file)) {
        return false;
    }

    // If output file already exists return path
    $output_file = $file . '.webp';
    if (file_exists($output_file)) {
        return $output_file;
    }

    $file_type = strtolower(pathinfo($file, PATHINFO_EXTENSION));

    if (function_exists('imagewebp')) {

        switch ($file_type) {
            case 'jpeg':
            case 'jpg':
                $image = imagecreatefromjpeg($file);
                break;

            case 'png':
                $image = imagecreatefrompng($file);
                imagepalettetotruecolor($image);
                imagealphablending($image, true);
                imagesavealpha($image, true);
                break;

            case 'gif':
                $image = imagecreatefromgif($file);
                break;

            case 'webp':
            
                $image = $file;
                break;    
            default:
                return false;
        }

        // Save the image
        $result = imagewebp($image, $output_file, $compression_quality);
        if (false === $result) {
            return false;
        }

        // Free up memory
        imagedestroy($image);

        return $output_file;
    } else if (class_exists('Imagick')) {
        $image = new Imagick();
        $image->readImage($file);

        if ($file_type === 'png') {
            $image->setImageFormat('webp');
            $image->setImageCompressionQuality($compression_quality);
            $image->setOption('webp:lossless', 'true');
        }

        $image->writeImage($output_file);
        return $output_file;
    }

    return false;
}

 ?>