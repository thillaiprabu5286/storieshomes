<?php echo phpinfo(); 
//echo getcwd();
exit;
$abc = array('Subj1'=>'Physics','Subj2'=>'Chemistry','Subj3'=>'Mathematics','Class'=>array(5,6,7,8));  
//print_r($abc);
error_log(print_r($abc,true), 3, "/home/stori5i7/public_html/var/errortest.txt");

?>
