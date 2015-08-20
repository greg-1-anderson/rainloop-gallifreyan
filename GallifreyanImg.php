<?php
define('PI', acos(-1));
define('TWO_PI', PI * 2);
$semirandom = false;





$count=0;



$sentenceRadius = 256;

function gallifreyan($image, $fg, $english, $sentenceRadius){
  $english=strtolower($english);
  $english=implode("-", explode(" -", $english));
  $english=implode("-", explode("- ", $english));
  $english=implode("- ", explode("-", $english));
  $english=implode("#", explode("ch", $english));
  $english=implode("$", explode("sh", $english));
  $english=implode("%", explode("th", $english));
  $english=implode("&", explode("ng", $english));
  $english=implode("q", explode("qu", $english));
  //background($bg);
  $spaces=0;
  $sentences=1;
  for ($i=0;$i<strlen($english);$i++) {

    if ($english[$i]=='c') {
      //text("ERROR: Please replace every C with $a K or an S.",15,60);
      return;
    }
    if ($english[$i]==' ') {
      $spaces++;
    }
    if (($english[$i]=='.'||$english[$i]=='!'||$english[$i]=='?')&&$i<strlen($english)-1) {
      if ($english[$i+1]==' ') {
        $sentences++;
      }
    }
  }
  if ($spaces==0) {
    _writeSentence($image, $fg, $english, $sentenceRadius, 0);
  }
  else if ($sentences==1) {
    _writeSentence($image, $fg, $english, $sentenceRadius, 1);
  }else{
    //text("ERROR: Multiple $sentences are not yet supported.",15,60);
    return;
  }
  //text("Press return again for another version.",15,60);
  //text("Hold control to animate.",15,120);
  //text("Press alt to randomize colors.",15,90);
  //text("Press tab to save $image.",15,150);
}

function _writeSentence($image, $fg, $english, $sentenceRadius, $type) {
  global $semirandom;
  global $count;

  $width = imagesx($image);
  $height = imagesy($image);
  $wordRadius  = array();
  $float1=0;
  $float2=0;
  $english=trim($english);
  $charCount=0;
  $Sentence = array();

  $Sentence=explode(" ", $english);
  $sentence  = array();
  $punctuation  = array();
  $apostrophes  = array();
  for ($j=0;$j<count($Sentence);$j++) {
    $word = array();

    $Sentence[$j]=implode("", explode(" ", $Sentence[$j]));
    $vowel=true;
    for ($i=0;$i<strlen($Sentence[$j]);$i++) {
      $punctuation[$j] = '';
      $apostrophes[$j][$i]=false;
      if ($i!=0) {
        if ($Sentence[$j][$i]==$Sentence[$j][$i-1]) {
          $word[count($word)-1]=$word[count($word)-1].'@';
          continue;
        }
      }
      if ($Sentence[$j][$i]=='a'||$Sentence[$j][$i]=='e'||$Sentence[$j][$i]=='i'||$Sentence[$j][$i]=='o'||$Sentence[$j][$i]=='u') {
        if ($vowel) {
          $word[]=str($Sentence[$j][$i]);
        }
        else {
          $word[count($word)-1]=$word[count($word)-1].$Sentence[$j][$i];
        }
        $vowel=true;
      }
      else if ($Sentence[$j][$i]=='.'||$Sentence[$j][$i]=='?'||$Sentence[$j][$i]=='!'||$Sentence[$j][$i]=='"'||$Sentence[$j][$i]=="'"||$Sentence[$j][$i]=='-'||$Sentence[$j][$i]==','||$Sentence[$j][$i]==';'||$Sentence[$j][$i]==':') {
        if($Sentence[$j][$i]=="'"){
          $apostrophes[$j][$i]=true;
        }else{
          $punctuation[$j]=$Sentence[$j][$i];
        }
      }
      else {
        $word[]=str($Sentence[$j][$i]);
        if ($Sentence[$j][$i]=='t'||$Sentence[$j][$i]=='$'||$Sentence[$j][$i]=='r'||$Sentence[$j][$i]=='s'||$Sentence[$j][$i]=='v'||$Sentence[$j][$i]=='w') {
          $vowel=true;
        }
        else {
          $vowel=false;
        }
      }
    }
    $sentence[$j]=$word;
    $charCount+=count($word);
  }
  //stroke($fg);
  if ($type>0) {
    //imagesetthickness($image, 3);
    imageellipse($image, $width/2, $height/2, $sentenceRadius*2, $sentenceRadius*2, $fg);
  }
  //imagesetthickness($image, 4);
  imageellipse($image, $width/2, $height/2, $sentenceRadius*2+40, $sentenceRadius*2+40, $fg);
  $pos=PI/2;
  $maxRadius=0;
  for ($i=0;$i<count($sentence);$i++) {
    $wordRadius[]=constrain($sentenceRadius*count($sentence[$i])/$charCount*1.2, 0, $sentenceRadius/2);
    if ($wordRadius[$i]>$maxRadius) {
      $maxRadius=$wordRadius[$i];
    }
  }
  $scaleFactor = $sentenceRadius/($maxRadius+($sentenceRadius/2));
  $distance=$scaleFactor*$sentenceRadius/2;
  for ($i=0;$i<count($wordRadius);$i++) {
    $wordRadius[$i]*=$scaleFactor;
  }
  $x = array();

  $y = array();

  //stroke($fg);
  for ($i=0;$i<count($sentence);$i++) {
    $x[]=$width/2+$distance*cos($pos);
    $y[]=$height/2+$distance*sin($pos);
    $nextIndex=0;
    if ($i!=count($sentence)-1) {
      $nextIndex=$i+1;
    }
    $pos-=(float)(count($sentence[$i])+count($sentence[$nextIndex]))/(2*$charCount)*TWO_PI;
    $pX = $width/2+cos($pos+(float)(count($sentence[$i])+count($sentence[$nextIndex]))/(2*$charCount)*PI)*$sentenceRadius;
    $pY = $height/2+sin($pos+(float)(count($sentence[$i])+count($sentence[$nextIndex]))/(2*$charCount)*PI)*$sentenceRadius;
    switch($punctuation[$i]){
      case '.':
        imageellipse($image, $pX,$pY,20,20, $fg);
        break;
      case '?':
        _makeDots($image, $fg, $width/2,$height/2,$sentenceRadius*1.4,2,-1.2,0.1);
        break;
      case '!':
        _makeDots($image, $fg, $width/2,$height/2,$sentenceRadius*1.4,3,-1.2,0.1);
        break;
      case '"':
        imageline($image, $width/2+cos($pos+(float)(count($sentence[$i])+count($sentence[$nextIndex]))/(2*$charCount)*PI)*$sentenceRadius,$height/2+sin($pos+(float)(count($sentence[$i])+count($sentence[$nextIndex]))/(2*$charCount)*PI)*$sentenceRadius,$width/2+cos($pos+(float)(count($sentence[$i])+count($sentence[$nextIndex]))/(2*$charCount)*PI)*($sentenceRadius+20),$height/2+sin($pos+(float)(count($sentence[$i])+count($sentence[$nextIndex]))/(2*$charCount)*PI)*($sentenceRadius+20), $fg);
        break;
      case '-':
        imageline($image, $width/2+cos($pos+(float)(count($sentence[$i])+count($sentence[$nextIndex]))/(2*$charCount)*PI)*$sentenceRadius,$height/2+sin($pos+(float)(count($sentence[$i])+count($sentence[$nextIndex]))/(2*$charCount)*PI)*$sentenceRadius,$width/2+cos($pos+(float)(count($sentence[$i])+count($sentence[$nextIndex]))/(2*$charCount)*PI)*($sentenceRadius+20),$height/2+sin($pos+(float)(count($sentence[$i])+count($sentence[$nextIndex]))/(2*$charCount)*PI)*($sentenceRadius+20), $fg);
        imageline($image, $width/2+cos($pos+(count($sentence[$i])+count($sentence[$nextIndex])+0.3)/(2*$charCount)*PI)*$sentenceRadius,$height/2+sin($pos+(count($sentence[$i])+count($sentence[$nextIndex])+0.2)/(2*$charCount)*PI)*$sentenceRadius,$width/2+cos($pos+(count($sentence[$i])+count($sentence[$nextIndex])+0.2)/(2*$charCount)*PI)*($sentenceRadius+20),$height/2+sin($pos+(count($sentence[$i])+count($sentence[$nextIndex])+0.3)/(2*$charCount)*PI)*($sentenceRadius+20), $fg);
        imageline($image, $width/2+cos($pos+(count($sentence[$i])+count($sentence[$nextIndex])-0.3)/(2*$charCount)*PI)*$sentenceRadius,$height/2+sin($pos+(count($sentence[$i])+count($sentence[$nextIndex])-0.2)/(2*$charCount)*PI)*$sentenceRadius,$width/2+cos($pos+(count($sentence[$i])+count($sentence[$nextIndex])-0.2)/(2*$charCount)*PI)*($sentenceRadius+20),$height/2+sin($pos+(count($sentence[$i])+count($sentence[$nextIndex])-0.3)/(2*$charCount)*PI)*($sentenceRadius+20), $fg);
        break;
      case ',':
        //fill($fg);
        imagefilledellipse($image, $pX,$pY,20,20, $fg);
        //noFill();
        break;
      case ';':
        //fill($fg);
        imagefilledellipse($image, $width/2+cos($pos+(float)(count($sentence[$i])+count($sentence[$nextIndex]))/(2*$charCount)*PI)*$sentenceRadius-10,$height/2+sin($pos+(float)(count($sentence[$i])+count($sentence[$nextIndex]))/(2*$charCount)*PI)*$sentenceRadius-10,10,10, $fg);
        //noFill();
        break;
      case ':':
        imageellipse($image, $pX,$pY,25,25, $fg);
        //imagesetthickness($image, 2);
        imageellipse($image, $pX,$pY,15,15, $fg);
        //imagesetthickness($image, 4);
        break;
      default:
        break;
    }
  }
  $otherIndex=0;
  $nested  = array();
  for ($i=0;$i<count($sentence);$i++) {
    $angle1=0;//angle facing onwards
    $angle2=0;//backwards
    if ($i==count($sentence)-1) {
      $otherIndex=0;
    }
    else {
      $otherIndex=$i+1;
    }
    $angle1=atan_wrapper(($y[$i]-$y[$otherIndex]),($x[$i]-$x[$otherIndex]));
    if (dist($x[$i]+(cos($angle1)*20), $y[$i]+(sin($angle1)*20), $x[$otherIndex], $y[$otherIndex])>dist($x[$i], $y[$i], $x[$otherIndex], $y[$otherIndex])) {
      $angle1-=PI;
    }
    if ($angle1<0) {
      $angle1+=TWO_PI;
    }
    if ($angle1<0) {
      $angle1+=TWO_PI;
    }
    $angle1-=PI/2;
    if ($angle1<0) {
      $angle1+=TWO_PI;
    }
    $angle1=TWO_PI-$angle1;
    $index = round(map($angle1, 0, TWO_PI, 0, (float)(count($sentence[$i]))));
    if ($index==count($sentence[$i])) {
      $index=0;
    }
    $tempChar=$sentence[$i][$index][0];
    if (($tempChar=='t'||$tempChar=='$'||$tempChar=='r'||$tempChar=='s'||$tempChar=='v'||$tempChar=='w')&&$type>0) {
      $nested[$i][$index]=true;
      $wordRadius[$i]=constrain($wordRadius[$i]*1.2, 0, $maxRadius*$scaleFactor);
      while (dist ($x[$i], $y[$i], $x[$otherIndex], $y[$otherIndex])>$wordRadius[$i]+$wordRadius[$otherIndex]) {
        $x[$i]=lerp($x[$i], $x[$otherIndex], 0.05);
        $y[$i]=lerp($y[$i], $y[$otherIndex], 0.05);
      }
    }
  }
  $lineX  = array();

  $lineY  = array();

  $arcBegin  = array();

  $arcEnd  = array();

  $lineRad  = array();

  //imagesetthickness($image, 2);
  if ($type==0) {
    $wordRadius[0]=$sentenceRadius*0.9;
    $x[0]=$width/2;
    $y[0]=$height/2;
  }
  for ($i=0;$i<count($sentence);$i++) {
    $pos=PI/2;
    $letterRadius = $wordRadius[$i]/(count($sentence[$i])+1)*1.5;
    for ($j=0;$j<count($sentence[$i]);$j++) {
      if($apostrophes[$i][$j]){
        $a=$pos+PI/count($sentence[$i])-0.1;
        $d=0;
        $tempX=$x[$i];
        $tempY=$y[$i];
        while (pow ($tempX-$width/2, 2)+pow($tempY-$height/2, 2)<pow($sentenceRadius+20, 2)) {
          $tempX=$x[$i]+cos($a)*$d;
          $tempY=$y[$i]+sin($a)*$d;
          $d+=1;
        }
        imageline($image, $x[$i]+cos($a)*$wordRadius[$i], $y[$i]+sin($a)*$wordRadius[$i], $tempX, $tempY, $fg);
        $a=$pos+PI/count($sentence[$i])+0.1;
        $d=0;
        $tempX=$x[$i];
        $tempY=$y[$i];
        while (pow ($tempX-$width/2, 2)+pow($tempY-$height/2, 2)<pow($sentenceRadius+20, 2)) {
          $tempX=$x[$i]+cos($a)*$d;
          $tempY=$y[$i]+sin($a)*$d;
          $d+=1;
        }
        imageline($image, $x[$i]+cos($a)*$wordRadius[$i], $y[$i]+sin($a)*$wordRadius[$i], $tempX, $tempY, $fg);
      }
      $vowel=true;
      $tempX=0;
      $tempY=0;

      //single vowels

      if ($sentence[$i][$j][0]=='a') {
        $tempX=$x[$i]+cos($pos)*($wordRadius[$i]+$letterRadius/2);
        $tempY=$y[$i]+sin($pos)*($wordRadius[$i]+$letterRadius/2);
        imageellipse($image, $tempX, $tempY, $letterRadius, $letterRadius, $fg);
      }
      else if ($sentence[$i][$j][0]=='e') {
        $tempX=$x[$i]+cos($pos)*($wordRadius[$i]);
        $tempY=$y[$i]+sin($pos)*($wordRadius[$i]);
        imageellipse($image, $tempX, $tempY, $letterRadius, $letterRadius, $fg);
      }
      else if ($sentence[$i][$j][0]=='i') {
        $tempX=$x[$i]+cos($pos)*($wordRadius[$i]);
        $tempY=$y[$i]+sin($pos)*($wordRadius[$i]);
        imageellipse($image, $tempX, $tempY, $letterRadius, $letterRadius, $fg);
        $lineX[]=$tempX;
        $lineY[]=$tempY;
        $arcBegin[]=$pos+PI/2;
        $arcEnd[]=$pos+3*PI/2;
        $lineRad[]=$letterRadius;
      }
      else if ($sentence[$i][$j][0]=='o') {
        $tempX=$x[$i]+cos($pos)*($wordRadius[$i]-$letterRadius/1.6);
        $tempY=$y[$i]+sin($pos)*($wordRadius[$i]-$letterRadius/1.6);
        imageellipse($image, $tempX, $tempY, $letterRadius, $letterRadius, $fg);
      }
      else if ($sentence[$i][$j][0]=='u') {
        $tempX=$x[$i]+cos($pos)*($wordRadius[$i]);
        $tempY=$y[$i]+sin($pos)*($wordRadius[$i]);
        imageellipse($image, $tempX, $tempY, $letterRadius, $letterRadius, $fg);
        $lineX[]=$tempX;
        $lineY[]=$tempY;
        $arcBegin[]=$pos-PI/2;
        $arcEnd[]=$pos+PI/2;
        $lineRad[]=$letterRadius;
      }
      else {
        $vowel=false;
      }

      if ($vowel) {
        imagearc_radians($image, $x[$i], $y[$i], $wordRadius[$i]*2, $wordRadius[$i]*2, $pos-PI/count($sentence[$i]), $pos+PI/count($sentence[$i]), $fg);
        if (strlen($sentence[$i][$j])==1) {
        }
        else {

          //double vowels

          if ($sentence[$i][$j][1]=='@') {
            imageellipse($image, $tempX, $tempY, $letterRadius*1.3, $letterRadius*1.3, $fg);
          }
        }
      }
      else {

        // consonants

        if ($sentence[$i][$j][0]=='b'||$sentence[$i][$j][0]=='#'||$sentence[$i][$j][0]=='d'||$sentence[$i][$j][0]=='f'||$sentence[$i][$j][0]=='g'||$sentence[$i][$j][0]=='h') {
          $tempX=$x[$i]+cos($pos)*($wordRadius[$i]-$letterRadius*0.95);
          $tempY=$y[$i]+sin($pos)*($wordRadius[$i]-$letterRadius*0.95);
          _makeArcs($image, $fg, $tempX, $tempY, $x[$i], $y[$i], $wordRadius[$i], $letterRadius, $pos-PI/count($sentence[$i]), $pos+PI/count($sentence[$i]), $float1, $float2);
          $lines=0;
          if ($sentence[$i][$j][0]=='#') {
            _makeDots($image, $fg, $tempX, $tempY, $letterRadius, 2, $pos, 1);
          }
          else if ($sentence[$i][$j][0]=='d') {
            _makeDots($image, $fg, $tempX, $tempY, $letterRadius, 3, $pos, 1);
          }
          else if ($sentence[$i][$j][0]=='f') {
            $lines=3;
          }
          else if ($sentence[$i][$j][0]=='g') {
            $lines=1;
          }
          else if ($sentence[$i][$j][0]=='h') {
            $lines=2;
          }
          for ($k=0;$k<$lines;$k++) {
            $lineX[]=$tempX;
            $lineY[]=$tempY;
            $arcBegin[]=$pos+0.5;
            $arcEnd[]=$pos+TWO_PI-0.5;
            $lineRad[]=$letterRadius*2;
          }
          if (strlen($sentence[$i][$j])>1) {
            $vowelIndex=1;
            if ($sentence[$i][$j][1]=='@') {
              _makeArcs($image, $fg, $tempX, $tempY, $x[$i], $y[$i], $wordRadius[$i], $letterRadius*1.3, $pos+TWO_PI, $pos-TWO_PI, $float1, $float2);
              $vowelIndex=2;
            }
            if (strlen($sentence[$i][$j])==$vowelIndex) {
              $pos-=TWO_PI/count($sentence[$i]);
              continue;
            }
            if ($sentence[$i][$j][$vowelIndex]=='a') {
              $tempX=$x[$i]+cos($pos)*($wordRadius[$i]+$letterRadius/2);
              $tempY=$y[$i]+sin($pos)*($wordRadius[$i]+$letterRadius/2);
              imageellipse($image, $tempX, $tempY, $letterRadius, $letterRadius, $fg);
            }
            else if ($sentence[$i][$j][$vowelIndex]=='e') {
              imageellipse($image, $tempX, $tempY, $letterRadius, $letterRadius, $fg);
            }
            else if ($sentence[$i][$j][$vowelIndex]=='i') {
              imageellipse($image, $tempX, $tempY, $letterRadius, $letterRadius, $fg);
              $lineX[]=$tempX;
              $lineY[]=$tempY;
              $arcBegin[]=$pos+PI/2;
              $arcEnd[]=$pos+3*PI/2;
              $lineRad[]=$letterRadius;
            }
            else if ($sentence[$i][$j][$vowelIndex]=='o') {
              $tempX=$x[$i]+cos($pos)*($wordRadius[$i]-$letterRadius*2);
              $tempY=$y[$i]+sin($pos)*($wordRadius[$i]-$letterRadius*2);
              imageellipse($image, $tempX, $tempY, $letterRadius, $letterRadius, $fg);
            }
            else if ($sentence[$i][$j][$vowelIndex]=='u') {
              imageellipse($image, $tempX, $tempY, $letterRadius, $letterRadius, $fg);
              $lineX[]=$tempX;
              $lineY[]=$tempY;
              $arcBegin[]=$pos-PI/2;
              $arcEnd[]=$pos+PI/2;
              $lineRad[]=$letterRadius;
            }
            if (strlen($sentence[$i][$j])==($vowelIndex+2)) {
              if ($sentence[$i][$j][$vowelIndex+1]=='@') {
                imageellipse($image, $tempX, $tempY, $letterRadius*1.3, $letterRadius*1.3, $fg);
              }
            }
          }
        }
        if ($sentence[$i][$j][0]=='j'||$sentence[$i][$j][0]=='k'||$sentence[$i][$j][0]=='l'||$sentence[$i][$j][0]=='m'||$sentence[$i][$j][0]=='n'||$sentence[$i][$j][0]=='p') {
          $tempX=$x[$i]+cos($pos)*($wordRadius[$i]-$letterRadius);
          $tempY=$y[$i]+sin($pos)*($wordRadius[$i]-$letterRadius);
          imageellipse($image, $tempX, $tempY, $letterRadius*1.9, $letterRadius*1.9, $fg);
          imagearc_radians($image, $x[$i], $y[$i], $wordRadius[$i]*2, $wordRadius[$i]*2, $pos-PI/count($sentence[$i]), $pos+PI/count($sentence[$i]), $fg);
          $lines=0;
          if ($sentence[$i][$j][0]=='k') {
            _makeDots($image, $fg, $tempX, $tempY, $letterRadius, 2, $pos, 1);
          }
          else if ($sentence[$i][$j][0]=='l') {
            _makeDots($image, $fg, $tempX, $tempY, $letterRadius, 3, $pos, 1);
          }
          else if ($sentence[$i][$j][0]=='m') {
            $lines=3;
          }
          else if ($sentence[$i][$j][0]=='n') {
            $lines=1;
          }
          else if ($sentence[$i][$j][0]=='p') {
            $lines=2;
          }
          for ($k=0;$k<$lines;$k++) {
            $lineX[]=$tempX;
            $lineY[]=$tempY;
            $arcBegin[]=0;
            $arcEnd[]=TWO_PI;
            $lineRad[]=$letterRadius*1.9;
          }
          if (strlen($sentence[$i][$j])>1) {
            $vowelIndex=1;
            if ($sentence[$i][$j][1]=='@') {
              imageellipse($image, $tempX, $tempY, $letterRadius*2.3, $letterRadius*2.3, $fg);
              $vowelIndex=2;
            }
            if (strlen($sentence[$i][$j])==$vowelIndex) {
              $pos-=TWO_PI/count($sentence[$i]);
              continue;
            }
            if ($sentence[$i][$j][$vowelIndex]=='a') {
              $tempX=$x[$i]+cos($pos)*($wordRadius[$i]+$letterRadius/2);
              $tempY=$y[$i]+sin($pos)*($wordRadius[$i]+$letterRadius/2);
              imageellipse($image, $tempX, $tempY, $letterRadius, $letterRadius, $fg);
            }
            else if ($sentence[$i][$j][$vowelIndex]=='e') {
              imageellipse($image, $tempX, $tempY, $letterRadius, $letterRadius, $fg);
            }
            else if ($sentence[$i][$j][$vowelIndex]=='i') {
              imageellipse($image, $tempX, $tempY, $letterRadius, $letterRadius, $fg);
              $lineX[]=$tempX;
              $lineY[]=$tempY;
              $arcBegin[]=$pos+PI/2;
              $arcEnd[]=$pos+3*PI/2;
              $lineRad[]=$letterRadius;
            }
            else if ($sentence[$i][$j][$vowelIndex]=='o') {
              $tempX=$x[$i]+cos($pos)*($wordRadius[$i]-$letterRadius*2);
              $tempY=$y[$i]+sin($pos)*($wordRadius[$i]-$letterRadius*2);
              imageellipse($image, $tempX, $tempY, $letterRadius, $letterRadius, $fg);
            }
            else if ($sentence[$i][$j][$vowelIndex]=='u') {
              imageellipse($image, $tempX, $tempY, $letterRadius, $letterRadius, $fg);
              $lineX[]=$tempX;
              $lineY[]=$tempY;
              $arcBegin[]=$pos-PI/2;
              $arcEnd[]=$pos+PI/2;
              $lineRad[]=$letterRadius;
            }
            if (strlen($sentence[$i][$j])==($vowelIndex+2)) {
              if ($sentence[$i][$j][$vowelIndex+1]=='@') {
                imageellipse($image, $tempX, $tempY, $letterRadius*1.3, $letterRadius*1.3, $fg);
              }
            }
          }
        }
        if ($sentence[$i][$j][0]=='t'||$sentence[$i][$j][0]=='$'||$sentence[$i][$j][0]=='r'||$sentence[$i][$j][0]=='s'||$sentence[$i][$j][0]=='v'||$sentence[$i][$j][0]=='w') {
          $tempX=$x[$i]+cos($pos)*($wordRadius[$i]);
          $tempY=$y[$i]+sin($pos)*($wordRadius[$i]);
          $nextIndex;
          if ($i==count($sentence)-1) {
            $nextIndex=0;
          }
          else {
            $nextIndex=$i+1;
          }
          $angle1=atan_wrapper(($y[$i]-$y[$nextIndex]),($x[$i]-$x[$nextIndex]));
          if (dist($x[$i]+(cos($angle1)*20), $y[$i]+(sin($angle1)*20), $x[$nextIndex], $y[$nextIndex])>dist($x[$i], $y[$i], $x[$nextIndex], $y[$nextIndex])) {
            $angle1-=PI;
          }
          if ($angle1<0) {
            $angle1+=TWO_PI;
          }
          if ($angle1<0) {
            $angle1+=TWO_PI;
          }
          if (isset($nested[$i][$j])) {
            _makeArcs($image, $fg, $x[$nextIndex], $y[$nextIndex], $x[$i], $y[$i], $wordRadius[$i], $wordRadius[$nextIndex]+20, $pos-PI/count($sentence[$i]), $pos+PI/count($sentence[$i]), $float1, $float2);
          }
          else {
            _makeArcs($image, $fg, $tempX, $tempY, $x[$i], $y[$i], $wordRadius[$i], $letterRadius*1.5, $pos-PI/count($sentence[$i]), $pos+PI/count($sentence[$i]), $float1, $float2);
          }
          $lines=0;
          if ($sentence[$i][$j][0]=='$') {
            if (isset($nested[$i][$j])) {
              _makeDots($image, $fg, $x[$nextIndex], $y[$nextIndex], ($wordRadius[$nextIndex]*1.4)+14, 2, $angle1, $wordRadius[$nextIndex]/500);
            }
            else {
              _makeDots($image, $fg, $tempX, $tempY, $letterRadius*2.6, 2, $pos, 0.5);
            }
          }
          else if ($sentence[$i][$j][0]=='r') {
            if (isset($nested[$i][$j])) {
              _makeDots($image, $fg, $x[$nextIndex], $y[$nextIndex], ($wordRadius[$nextIndex]*1.4)+14, 3, $angle1, $wordRadius[$nextIndex]/500);
            }
            else {
              _makeDots($image, $fg, $tempX, $tempY, $letterRadius*2.6, 3, $pos, 0.5);
            }
          }
          else if ($sentence[$i][$j][0]=='s') {
            $lines=3;
          }
          else if ($sentence[$i][$j][0]=='v') {
            $lines=1;
          }
          else if ($sentence[$i][$j][0]=='w') {
            $lines=2;
          }
          if (isset($nested[$i][$j])) {
            for ($k=0;$k<$lines;$k++) {
              $lineX[]=$x[$nextIndex];
              $lineY[]=$y[$nextIndex];
              $arcBegin[]=$float1;
              $arcEnd[]=$float2;
              $lineRad[]=$wordRadius[$nextIndex]*2+40;
            }
          }
          else {
            for ($k=0;$k<$lines;$k++) {
              $lineX[]=$tempX;
              $lineY[]=$tempY;
              $arcBegin[]=$float1;
              $arcEnd[]=$float2;
              $lineRad[]=$letterRadius*3;
            }
          }
          if (strlen($sentence[$i][$j])>1) {
            if ($sentence[$i][$j][1]=='@') {
              if (isset($nested[$i][$j])) {
                _makeArcs($image, $fg, $x[$nextIndex], $y[$nextIndex], $x[$i], $y[$i], $wordRadius[$i], ($wordRadius[$nextIndex]+20)*1.2, $pos+TWO_PI, $pos-TWO_PI, $float1, $float2);
              }
              else {
                _makeArcs($image, $fg, $tempX, $tempY, $x[$i], $y[$i], $wordRadius[$i], $letterRadius*1.8, $pos+TWO_PI, $pos-TWO_PI, $float1, $float2);
              }
            }
          }
        }
        if ($sentence[$i][$j][0]=='%'||$sentence[$i][$j][0]=='y'||$sentence[$i][$j][0]=='z'||$sentence[$i][$j][0]=='&'||$sentence[$i][$j][0]=='q'||$sentence[$i][$j][0]=='x') {
          $tempX=$x[$i]+cos($pos)*($wordRadius[$i]);
          $tempY=$y[$i]+sin($pos)*($wordRadius[$i]);
          imageellipse($image, $tempX, $tempY, $letterRadius*2, $letterRadius*2, $fg);
          imagearc_radians($image, $x[$i], $y[$i], $wordRadius[$i]*2, $wordRadius[$i]*2, $pos-PI/count($sentence[$i]), $pos+PI/count($sentence[$i]), $fg);
          $lines=0;
          if ($sentence[$i][$j][0]=='y') {
            _makeDots($image, $fg, $tempX, $tempY, $letterRadius, 2, $pos, 1);
          }
          else if ($sentence[$i][$j][0]=='z') {
            _makeDots($image, $fg, $tempX, $tempY, $letterRadius, 3, $pos, 1);
          }
          else if ($sentence[$i][$j][0]=='&') {
            $lines=3;
          }
          else if ($sentence[$i][$j][0]=='q') {
            $lines=1;
          }
          else if ($sentence[$i][$j][0]=='x') {
            $lines=2;
          }
          for ($k=0;$k<$lines;$k++) {
            $lineX[]=$tempX;
            $lineY[]=$tempY;
            $arcBegin[]=0;
            $arcEnd[]=TWO_PI;
            $lineRad[]=$letterRadius*2;
          }
          if (strlen($sentence[$i][$j])>1) {
            $vowelIndex=1;
            if ($sentence[$i][$j][1]=='@') {
              imageellipse($image, $tempX, $tempY, $letterRadius*2.3, $letterRadius*2.3, $fg);
              $vowelIndex=2;
            }
            if (strlen($sentence[$i][$j])==$vowelIndex) {
              $pos-=TWO_PI/count($sentence[$i]);
              continue;
            }
            if ($sentence[$i][$j][$vowelIndex]=='a') {
              $tempX=$x[$i]+cos($pos)*($wordRadius[$i]+$letterRadius/2);
              $tempY=$y[$i]+sin($pos)*($wordRadius[$i]+$letterRadius/2);
              imageellipse($image, $tempX, $tempY, $letterRadius, $letterRadius, $fg);
            }
            else if ($sentence[$i][$j][$vowelIndex]=='e') {
              imageellipse($image, $tempX, $tempY, $letterRadius, $letterRadius, $fg);
            }
            else if ($sentence[$i][$j][$vowelIndex]=='i') {
              imageellipse($image, $tempX, $tempY, $letterRadius, $letterRadius, $fg);
              $lineX[]=$tempX;
              $lineY[]=$tempY;
              $arcBegin[]=$pos+PI/2;
              $arcEnd[]=$pos+3*PI/2;
              $lineRad[]=$letterRadius;
            }
            else if ($sentence[$i][$j][$vowelIndex]=='o') {
              $tempX=$x[$i]+cos($pos)*($wordRadius[$i]-$letterRadius);
              $tempY=$y[$i]+sin($pos)*($wordRadius[$i]-$letterRadius);
              imageellipse($image, $tempX, $tempY, $letterRadius, $letterRadius, $fg);
            }
            else if ($sentence[$i][$j][$vowelIndex]=='u') {
              imageellipse($image, $tempX, $tempY, $letterRadius, $letterRadius, $fg);
              $lineX[]=$tempX;
              $lineY[]=$tempY;
              $arcBegin[]=$pos-PI/2;
              $arcEnd[]=$pos+PI/2;
              $lineRad[]=$letterRadius;
            }
            if (strlen($sentence[$i][$j])==($vowelIndex+2)) {
              if ($sentence[$i][$j][$vowelIndex+1]=='@') {
                imageellipse($image, $tempX, $tempY, $letterRadius*1.8, $letterRadius*1.8, $fg);
              }
            }
          }
        }
      }
      $pos-=TWO_PI/count($sentence[$i]);
    }
  }
  //imagesetthickness($image, 2);
  $lineLengths = array();

  //stroke($fg);
  $used = new UsedLines();
  for ($i=0;$i<count($lineX);$i++) {
    $indexes = array();

    $angles = array();

    for ($j=0;$j<count($lineX);$j++) {
      if (round($lineY[$i])==round($lineY[$j])&&round($lineX[$i])==round($lineX[$j])) {
        continue;
      }
      if ($used->exists($lineX[$i], $lineY[$i], $lineX[$j], $lineY[$j])) {
        continue;
      }
      $b=false;
      for ($k=0;$k<count($lineLengths);$k++) {
        if ($lineLengths[$k]==dist($lineX[$i], $lineY[$i], $lineX[$j], $lineY[$j])+$lineX[$i]+$lineY[$i]+$lineX[$j]+$lineY[$j]) {
          $b=true;
          break;
        }
      }
      if ($b) {
        continue;
      }
      $angle1=atan_wrapper(($lineY[$i]-$lineY[$j]),($lineX[$i]-$lineX[$j]));
      if (dist($lineX[$i]+(cos($angle1)*20), $lineY[$i]+(sin($angle1)*20), $lineX[$j], $lineY[$j])>dist($lineX[$i], $lineY[$i], $lineX[$j], $lineY[$j])) {
        $angle1-=PI;
      }
      if ($angle1<0) {
        $angle1+=TWO_PI;
      }
      if ($angle1<0) {
        $angle1+=TWO_PI;
      }
      if ($angle1<$arcEnd[$i]&&$angle1>$arcBegin[$i]) {
        $angle1-=PI;
        if ($angle1<0) {
          $angle1+=TWO_PI;
        }
        if ($angle1<$arcEnd[$j]&&$angle1>$arcBegin[$j]) {
          $indexes[]=$j;
          $angles[]=$angle1;
        }
      }
    }
    if (count($indexes)==0) {
      $a = 0;
      if($semirandom){
        $a=map(noise($count+$i*5),0,1,$arcBegin[$i], $arcEnd[$i]);
      }else{
        $a=random($arcBegin[$i], $arcEnd[$i]);
      }
      $d=0;
      $tempX=$lineX[$i]+cos($a)*$d;
      $tempY=$lineY[$i]+sin($a)*$d;
      while (pow ($tempX-$width/2, 2)+pow($tempY-$height/2, 2)<pow($sentenceRadius+20, 2)) {
        $tempX=$lineX[$i]+cos($a)*$d;
        $tempY=$lineY[$i]+sin($a)*$d;
        $d+=1;
      }
      imageline($image, $lineX[$i]+cos($a)*$lineRad[$i]/2, $lineY[$i]+sin($a)*$lineRad[$i]/2, $tempX, $tempY, $fg);
    }
    else {
      $r;
      if($semirandom){
        $r=0;
      }else{
        $r=floor(random(count($indexes)));
      }
      $j=$indexes[$r];
      $a=$angles[$r]+PI;
      imageline($image, $lineX[$i]+cos($a)*$lineRad[$i]/2, $lineY[$i]+sin($a)*$lineRad[$i]/2, $lineX[$j]+cos($a+PI)*$lineRad[$j]/2, $lineY[$j]+sin($a+PI)*$lineRad[$j]/2, $fg);
      $lineLengths[]=dist($lineX[$i], $lineY[$i], $lineX[$j], $lineY[$j]+$lineX[$i]+$lineY[$i]+$lineX[$j]+$lineY[$j]);
      $used->add($lineX[$i], $lineY[$i], $lineX[$j], $lineY[$j]);

      $templineX  = array();

      $templineY  = array();

      $temparcBegin  = array();

      $temparcEnd  = array();

      $templineRad  = array();

      for ($k=0;$k<count($lineX);$k++) {
        if ($k!=$j&&$k!=$i) {
          $templineX[]=$lineX[$k];
          $templineY[]=$lineY[$k];
          $temparcBegin[]=$arcBegin[$k];
          $temparcEnd[]=$arcEnd[$k];
          $templineRad[]=$lineRad[$k];
        }
      }
      $lineX=$templineX;
      $lineY=$templineY;
      $arcBegin=$temparcBegin;
      $arcEnd=$temparcEnd;
      $lineRad=$templineRad;
      $i-=1;
    }
  }
}

function _makeDots($image, $fg, $mX, $mY, $r, $amnt, $pos, $scaleFactor) {
  //noStroke();
  //fill($fg);
  if ($amnt==3) {
    imagefilledellipse($image, $mX+cos($pos+PI)*$r/1.4, $mY+sin($pos+PI)*$r/1.4, $r/3*$scaleFactor, $r/3*$scaleFactor, $fg);
  }
  imagefilledellipse($image, $mX+cos($pos+PI+$scaleFactor)*$r/1.4, $mY+sin($pos+PI+$scaleFactor)*$r/1.4, $r/3*$scaleFactor, $r/3*$scaleFactor, $fg);
  imagefilledellipse($image, $mX+cos($pos+PI-$scaleFactor)*$r/1.4, $mY+sin($pos+PI-$scaleFactor)*$r/1.4, $r/3*$scaleFactor, $r/3*$scaleFactor, $fg);
  //noFill();
  //stroke($fg);
}

$float1=0;
$float2=0;

function _makeArcs($image, $fg, $mX, $mY, $nX, $nY, $r1, $r2, $begin, $end, &$float1, &$float2) {
  $theta = 0;
  $omega=0;
  $d = dist($mX, $mY, $nX, $nY);
  $theta=acos((pow($r1, 2)-pow($r2, 2)+pow($d, 2))/(2*$d*$r1));
  if ($nX-$mX<0) {
    $omega=atan_wrapper(($mY-$nY),( $mX-$nX));
  }
  else if ($nX-$mX>0) {
    $omega=PI+atan_wrapper(($mY-$nY),( $mX-$nX));
  }
  else if ($nX-$mX==0) {
    if ($nY>$mY) {
      $omega=3*PI/2;
    }
    else {
      $omega=PI/2;
    }
  }
  if ($omega+$theta-$end>0) {
    imagearc_radians($image, $nX, $nY, $r1*2, $r1*2, ($omega+$theta), ($end+TWO_PI), $fg);
    imagearc_radians($image, $nX, $nY, $r1*2, $r1*2, ($begin+TWO_PI), ($omega-$theta), $fg);
  }
  else {
    imagearc_radians($image, $nX, $nY, $r1*2, $r1*2, ($omega+$theta), $end, $fg);
    imagearc_radians($image, $nX, $nY, $r1*2, $r1*2, ($begin+TWO_PI), ($omega-$theta+TWO_PI), $fg);
  }
  if ($omega+$theta<$end||$omega-$theta>$begin) {
    //strokeCap(SQUARE);
    //stroke($bg);
    //imagesetthickness($image, 4);
    // imagearc_radians($image, $nX, $nY, $r1*2, $r1*2, $omega-$theta,$omega+$theta, $fg);
    //imagesetthickness($image, 2);
    //stroke($fg);
    //strokeCap(ROUND);
  }
  $theta=PI-acos((pow($r2, 2)-pow($r1, 2)+pow($d, 2))/(2*$d*$r2));
  if ($nX-$mX<0) {
    $omega=atan_wrapper(($mY-$nY),( $mX-$nX));
  }
  else if ($nX-$mX>0) {
    $omega=PI+atan_wrapper(($mY-$nY),( $mX-$nX));
  }
  else if ($nX-$mX==0) {
    if ($nY>$mY) {
      $omega=3*PI/2;
    }
    else {
      $omega=PI/2;
    }
  }
  imagearc_radians($image, $mX, $mY, $r2*2, $r2*2, ($omega+$theta), ($omega-$theta+TWO_PI), $fg);
  //imagefilledellipse($image, $mX, $mY, $r2*2, $r2*2, $fg);
  //noFill()
  $float1=$omega+$theta;
  $float2=$omega-$theta+TWO_PI;
}

// Helper functions
function dist($x1, $y1, $x2, $y2) {
  return sqrt(pow($x1 - $x2, 2) + pow($y1 - $y2, 2));
}
function constrain($amt, $low, $high) {
  return max(min($high, $amt), $low);
}
function lerp($t, $v0, $v1) {
  return $v0+($v1-$v0)*$t;
}
function map($value, $start1, $stop1, $start2, $stop2) {
  $t = ($value - $start1) / ($stop1 - $start1);
  return lerp($t, $start2, $stop2);
}
function random($low, $high = NULL) {
  if (!isset($high)) {
    $high = $low;
    $low = 0;
  }
  return map(rand(0,10000), 0, 10000, $low, $high);
}
// temporary implementation
function noise($value) {
  return random(0, 1);
}
function str($s) {
  return $s;
}
function atan_wrapper($a, $b) {
  if ($b == 0) {
    return PI/2;
  }
  else {
    return atan($a / $b);
  }
}
function imagearc_radians($image, $cx, $cy, $w, $h, $start, $end, $color) {
    $start_degrees = (360 * $start) / TWO_PI;
    $end_degrees = (360 * $end) / TWO_PI;
    imagearc($image, $cx, $cy, $w, $h, $start_degrees, $end_degrees, $color);
}

class UsedLines {

  public $startX = array();
  public $startY = array();
  public $endX = array();
  public $endY = array();

  public function exists($x1, $y1, $x2, $y2) {
    for ($i=0;$i<count($this->startX);$i++) {
      // this isn't a very good test. what we really want to know is if
      // the line from (x1,y1) to (x2,y2) has the same angle as, and overlaps
      // with any of the lines recorded in this class.
      // Our simpler test probably works okay, though, because i and u
      // are centered at the same point as the larger circle / arc they
      // are grouped with, and a and o do not have rays.
      if ((round($x1) == round($this->startX[$i])) && (round($y1) == round($this->startY[$i])) && (round($x2) == round($this->endX[$i])) && (round($y2) == round($this->endY[$i]))) {
        return true;
      }
      if ((round($x2) == round($this->startX[$i])) && (round($y2) == round($this->startY[$i])) && (round($x1) == round($this->endX[$i])) && (round($y1) == round($this->endY[$i]))) {
        return true;
      }
    }
    return false;
  }

  public function add($x1, $y1, $x2, $y2) {
    $this->startX[] = $x1;
    $this->startY[] = $y1;
    $this->endX[] = $x2;
    $this->endY[] = $y2;
  }
}

