<?php

$setmode27 = shell_exec("gpio -g mode 27 in");
//$setmode17 = shell_exec("gpio -g mode 17 out");

while(1){
$aaa = shell_exec('gpio -g read 27');
if($aaa == 1){
  echo '123'."\n";
} else {
  echo 'none'."\n";
}
sleep(3);
}
