<?php
//set_time_limit(0);
while(1){
        $pid = pcntl_fork(); //create fork process

        if ($pid == -1) {
                        die("could not fork process");
                        // child here
        } elseif ($pid == 0) {
                try {
                        //sleep(8);
                        $ch = curl_init();
                        curl_setopt($ch,CURLOPT_URL,"http://zhibo.mimilove.com/OpenAPI/v1/qiniu/GetList");
                        curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
                        curl_setopt($ch,CURLOPT_HEADER,0);
                        curl_exec($ch);
                        curl_close($ch);
                } catch (Exception $ex) {
                        echo $ex->getMessage();
                }
                exit(); //release when the process is finished
        } elseif ($pid) {
            //echo "fork child process successfully , pid = $pid\n";

            pcntl_waitpid($pid,$status);

            //echo "child process($pid) terminated , return is $status\n";
        }
        sleep(5);
}
?>
